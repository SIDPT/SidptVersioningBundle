<?php

namespace Sidpt\VersioningBundle\API\Serializer;

use Claroline\AppBundle\API\Serializer\SerializerTrait;
use Claroline\AppBundle\Persistence\ObjectManager;

use Claroline\CoreBundle\API\Serializer\Resource\ResourceTypeSerializer;
use Claroline\CoreBundle\API\Serializer\User\UserSerializer;

use Claroline\CoreBundle\Entity\Resource\ResourceType;
use Claroline\CoreBundle\Entity\User;

use Sidpt\VersioningBundle\Entity\ResourceNodeBranch;
use Sidpt\VersioningBundle\Entity\ResourceVersion;

class ResourceVersionSerializer
{

    use SerializerTrait;

    /**
     * [$om description]
     *
     * @var [type]
     */
    private $om;

    private $typeSerializer;

    private $userSerializer;
    

    /**
     * DocumentSerializer constructor.
     *
     * @param ObjectManager             $om                        desc
     * @param WidgetContainerSerializer $widgetContainerSerializer desc
     */
    public function __construct(
        ObjectManager $om,
        ResourceTypeSerializer $typeSerializer,
        UserSerializer $userSerializer
    ) {
        $this->om = $om;
        $this->typeSerializer = $typeSerializer;
        $this->userSerializer = $userSerializer;
    }

    /**
     * [getName description]
     *
     * @return [type] [description]
     */
    public function getName()
    {
        return 'version';
    }

    /**
     * [getClass description]
     *
     * @return [type] [description]
     */
    public function getClass()
    {
        return ResourceVersion::class;
    }

    /**
     * [getSchema description]
     *
     * @return string
     */
    public function getSchema()
    {
        return '~/sidpt/versioning-bundle/plugin/versioning/version.json';
    }

    public function serialize(ResourceVersion $version, array $options = [])
    {
        $user = $version->getLastModificationUser();
        $next = [];
        if (!(in_array('without_next', $options) || $version->getNextVersions()->isEmpty())) {
            foreach ($version->getNextVersions() as $nextVersion) {
                $next[] = $this->serialize($nextVersion, $options);
            }
        }
        $branch = $version->getBranch();
        return [
            'id' => $version->getUuid(),
            'branchId' => !empty($branch) ? $branch->getUuid() : null,
            'version' => $version->getVersion(),
            'resourceType' => $this->typeSerializer->serialize(
                $version->getResourceType()
            ),
            'resourceId' => $version->getResourceId(),
            'creationDate' => $version->getCreationDate(),
            'lastModificationDate' => $version->getLastModificationDate(),
            'lastModificationUser' => !empty($user) ?
                $this->userSerializer->serialize($user) : null,
            'previous' => empty($version->getPreviousVersion()) ? null :
                $this->serialize(
                    $version->getPreviousVersion(),
                    array_merge($options, ['without_next'])
                ),
            'next' => $next
        ];
    }

    public function deserialize(
        array $data,
        ResourceVersion $version = null, // should be created by a branch
        array $options = []
    ): ResourceVersion {
        if (empty($version)) {
            $version = new ResourceVersion();
        }
        if (isset($data['version'])) {
            $this->sipe('version', 'setVersion', $data, $version);
        }
        if (isset($data['resourceType'])) {
            $type = $this->om->find(
                ResourceType::class,
                $data['resourceType']['id']
            );
            $version->setResourceType($type);
        }

        if (isset($data['lastModificationUser'])) {
            $user = $this->om->find(
                User::class,
                $data['lastModificationUser']['id']
            );
            $version->setLastModificationUser($user);
        }

        $this->sipe('resourceId', 'setResourceId', $data, $version);
        
        if (isset($data['updated']) && $data['updated'] == true) {
            $version->setLastModificationDate(new \DateTime("now"));
        }
        
        // not sure yet if using multiple branches reference or single
        // branch reference for version
        /*if (isset($data['branches'])) {
            $currentBranches = $version->getBranches()->toArray();
            $branchesIds = [];
            foreach ($data['branches'] as $key => $branchId) {
                $branch = $version->getBranch(
                    $branchId
                );
            
                if (empty($branch)) {
                    $branch = $this->om->find(
                        ResourceNodeBranch::class,
                        $branchId
                    );
                }
                // only keep persisted branches
                if (!empty($branch)) {
                    $version->addBranch($branch);
                    $branchesIds[] = $branch->getUuid();
                }
            }
            foreach ($currentBranches as $key => $branch) {
                if (!in_array($branch->getUuid(), $versionsIds)) {
                    $version->removeBranch($branch);
                }
            }
        }*/

        if (isset($data['branchId'])) {
            $version->setBranch(
                $this->om->find(
                    ResourceNodeBranch::class,
                    $data['branchId']
                )
            );
        }
        
        if (isset($data['previous'])) {
            $previousVersion = $this->om->find(
                ResourceVersion::class,
                $data['previous']['id']
            );
        }

        if (isset($data['next'])) {
            $currentNextVersions = $version->getNextVersions()->toArray();
            $versionsIds = [];
            foreach ($data['next'] as $key => $nextVersionData) {
                if (isset($nextVersionData['id'])) {
                    $nextVersion = $version->getNextVersionById(
                        $nextVersionData['id']
                    );
                }
                if (empty($nextVersion)) {
                    $nextVersion = new ResourceVersion();
                    // propagrate branch
                    $nextVersion->setBranch($version->getBranch());
                    $version->addNextVersion($nextVersion);
                    $nextVersion->setPreviousVersion($version);
                }
                $this->deserialize($nextVersionData, $nextVersion, $options);
                $versionsIds[] = $nextVersion->getUuid();
            }
            foreach ($currentNextVersions as $key => $nextVersion) {
                if (!in_array($nextVersion->getUuid(), $versionsIds)) {
                    $version->removeNextVersion($nextVersion);
                    $this->om->remove($nextVersion);
                }
            }
        }
        return $version;
    }




}