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

    public function serialize(ResourceNodeBranch $branch, array $options = [])
    {
        return [
            'id' => $branch->getUuid(),
            'name' => $branch->getName(),
            'resourceNode' => $this->nodeSerializer->serialize(
                $branch->getResourceNode
            ),
            'parentId' => empty($branch->getParentBranch()) ?
                null :
                $branch->getParentBranch()->getUuid(),
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
        }

        if (isset($data['head'])) {
            $headVersion = $this->om->find(
                ResourceVersion::class,
                $data['head']['id']
            );
            if (empty($headVersion) && !empty($node)) {
                $headVersion = new ResourceVersion();
                $headVersion->setBranch($branch);
            }
            $branch->setHead($headVersion);
            
        }
        if (isset($data['parentId'])) {
            $parentBranch = $this->om->find(
                ResourceNodeBranch::class,
                $data['parentId']
            );
            $branch->setParentBranch($parentBranch);
        }

        return $branch;
    }

}