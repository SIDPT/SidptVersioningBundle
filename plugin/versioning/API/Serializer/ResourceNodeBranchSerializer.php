<?php

namespace Sidpt\VersioningBundle\API\Serializer;

use Claroline\AppBundle\API\Serializer\SerializerTrait;

use Claroline\CoreBundle\API\Serializer\Resource\ResourceNodeSerializer;

use Claroline\CoreBundle\Entity\Resource\ResourceNode;

use Claroline\AppBundle\Persistence\ObjectManager;

use Sidpt\VersioningBundle\Entity\ResourceVersion;
use Sidpt\VersioningBundle\Entity\ResourceNodeBranch;

use Sidpt\VersioningBundle\API\Serializer\ResourceVersionSerializer;

class ResourceNodeBranchSerializer
{

    use SerializerTrait;

    /**
     * [$om description]
     *
     * @var [type]
     */
    private $om;


    private $nodeSerializer;
    

    /**
     * DocumentSerializer constructor.
     *
     * @param ObjectManager             $om                        desc
     * @param WidgetContainerSerializer $widgetContainerSerializer desc
     */
    public function __construct(
        ObjectManager $om,
        ResourceNodeSerializer $nodeSerializer,
        ResourceVersionSerializer $versionSerializer
    ) {
        $this->om = $om;
        $this->nodeSerializer = $nodeSerializer;
        $this->versionSerializer = $versionSerializer;
    }

    /**
     * [getName description]
     *
     * @return [type] [description]
     */
    public function getName()
    {
        return 'branch';
    }

    /**
     * [getClass description]
     *
     * @return [type] [description]
     */
    public function getClass()
    {
        return ResourceNodeBranch::class;
    }

    /**
     * [getSchema description]
     *
     * @return string
     */
    public function getSchema()
    {
        return '~/sidpt/versioning-bundle/plugin/versioning/branch.json';
    }

    public function serialize(/*ResourceNodeBranch|Proxy*/ $branch, array $options = [])
    {
        return [
            'id' => $branch->getUuid(),
            'name' => $branch->getName(),
            'resourceNode' => $this->nodeSerializer->serialize(
                $branch->getResourceNode()
            ),
            'parentId' => empty($branch->getParent()) ?
                null :
                $branch->getParent()->getUuid(),
            'head' => $this->versionSerializer->serialize($branch->getHead())
        ];
    }

    public function deserialize(
        array $data,
        ResourceNodeBranch $branch = null,
        array $options = []
    ): ResourceNodeBranch {
        if (empty($branch)) {
            $branch = new ResourceNodeBranch();
        }
        $this->sipe('name', 'setName', $data, $branch);

        if (isset($data['resourceNode'])) {
            $node = $this->om->find(
                ResourceNode::class,
                $data['resourceNode']['id']
            );
            if (empty($node)) {
                $node = new ResourceNode();
                $this->nodeSerializer->deserialize(
                    $data['resourceNode'],
                    $node,
                    $options
                );
                $branch->setResourceNode($node);
            }
        }

        if (isset($data['head'])) {
            $previousHead = $branch->getHead();

            $newHead = $this->om->find(
                ResourceVersion::class,
                $data['head']['id']
            );
            // TODO : WARNING there might be a risk of error here
            if (empty($newHead)) {
                $newHead = new ResourceVersion();
                $this->versionSerializer->deserialize(
                    $data['head'],
                    $newHead,
                    $options
                );
                $newHead->setBranch($branch);
            }
            // Relink node to the new head version
            $currentResource = $this->om->find(
                $previousHead->getResourceType()->getClass(),
                $previousHead->getResourceId()
            );
            $newResource = $this->om->find(
                $newHead->getResourceType()->getClass(),
                $newHead->getResourceId()
            );
            $newResource->setResourceNode($currentResource->getResourceNode());
            $currentResource->setResourceNode(null);
            $branch->setHead($newHead);
        }
        if (isset($data['parentId'])) {
            $parent = $this->om->find(
                ResourceNodeBranch::class,
                $data['parentId']
            );
            $branch->setParent($parent);
        }

        return $branch;
    }

}