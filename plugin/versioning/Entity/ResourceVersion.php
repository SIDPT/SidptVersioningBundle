<?php


namespace Sidpt\VersioningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Claroline\AppBundle\Entity\Identifier\Id;

use Claroline\CoreBundle\Entity\User;

use Sidpt\VersioningBundle\Entity\ResourceNodeBranch;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resource versionning (attached to a ResourceNodeBranch)
 * 
 *
 * @ORM\Entity()
 * @ORM\Table(name="sidpt__resource_version")
 */
class ResourceVersion
{
    use Id;
    use Uuid;
    

    /**
     * Branch referencing this version
     * Not following git model here :
     *     a version can only have a single branch referenced
     *     (as a resource should be uniquely bound to a node in claroline model)
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceNodeBranch")
     *
     * @var string
     */
    protected $branch;
   

    /**
     * Optional version identifier (like a git tag)
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var integer
     */
    protected $version;


    /**
     * Resource Type
     *
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\Resource\ResourceType")
     * @ORM\JoinColumn(name="resource_type_id", onDelete="CASCADE", nullable=false)
     *
     * @var [type]
     */
    protected $resourceType;

    /**
     * Abstract Resource Uuid
     *
     * @ORM\Column(type="string", length=36, nullable=false)
     *
     * @var [type]
     */
    protected $resourceId;


    /**
     * @ORM\Column(type="datetime")
     * @var [type]
     */
    protected $creationDate;


    /**
     * @ORM\Column(type="datetime")
     * @var [type]
     */
    protected $lastModificationDate;

    /**
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\User")
     * @var [type]
     */
    protected $lastModificationUser;


    /**
     * Previous versions of the Resource in the version tree
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceVersion")
     * @ORM\JoinColumn(name="previous_id", referencedColumnName="id")
     *
     * @var ResourceVersion
     */
    protected $previousVersion;

    /**
     * Next versions of the ResourceVersion in the tree
     *
     * @ORM\OneToMany(
     *     targetEntity="Sidpt\BinderBundle\Entity\ResourceVersion")
     * @ORM\JoinColumn(name="next_id", referencedColumnName="id")
     *
     * @var ResourceVersion[]|ArrayCollection
     */
    protected $nextVersions;

    public function __construct()
    {
        $this->refreshUuid();
        $this->creationDate = new \DateTime("now");
        $this->lastModificationDate = new \DateTime("now");
    }

    // GETTERS
    
    public function getBranch()
    {
        return $this->branch;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getResourceType()
    {
        return $this->resourceType;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function getLastModificationDate()
    {
        return $this->lastModificationDate;
    }

    public function getLastModificationUser()
    {
        return $this->lastModificationUser;
    }


    /**
     * [getPreviousVersion description]
     * @return [type] [description]
     */
    public function getPreviousVersion()
    {
        return $this->previousVersion;
    }

    
    public function getNextVersions()
    {
        return $this->nextVersions;
    }
    

    /**
     * @param string $locale optional locale to select the next version
     *
     * @return Document|null
     */
    public function getNextVersion(ResourceNodeBranch $branch = null) : ResourceVersion
    {
        $found = null;
        if (!isset($branch) && !$nextVersions->isEmpty()) {
            $found = $nextVersions->first();
        } else {
            foreach ($this->nextVersions as $nextVersion) {
                if ($nextVersion->getBranch() === $branch) {
                    $found = $nextVersion;
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * @param string $locale optional locale to select the next version
     *
     * @return Document|null
     */
    public function getNextVersionById($nextId) : ResourceVersion
    {
        $found = null;
        foreach ($this->nextVersions as $nextVersion) {
            if ($nextVersion->getUuid() === $nextId) {
                $found = $nextVersion;
                break;
            }
        }
        return $found;
    }


    

    // SETTERS
    
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }
    public function setVersion($version)
    {
        $this->version = $version;
    }
    public function setResourceType($resourceClass)
    {
        $this->resourceType = $resourceType;
    }
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;
    }
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }
    public function setLastModificationDate($lastModificationDate)
    {
        $this->lastModificationDate = $lastModificationDate;
    }
    public function setLastModificationUser($lastModificationUser)
    {
        $this->lastModificationUser = $lastModificationUser;
    }

    /**
     * [getPreviousVersion description]
     * @return [type] [description]
     */
    public function setPreviousVersion(ResourceVersion $previousVersion)
    {
        $this->previousVersion = $previousVersion;
    }
    
    /**
     * [getPreviousVersion description]
     * @return [type] [description]
     */
    public function setNextVersions(ArrayCollection $nextVersions)
    {
        $this->nextVersions = $nextVersions;
    }

    /**
     * @param $nextVersion
     */
    public function addNextVersion(ResourceVersion $nextVersion)
    {
        if (!$this->nextVersions->contains($nextVersion)) {
            $this->nextVersions->add($nextVersion);
        }
    }

    /**
     * @param $nextVersion
     */
    public function removeNextVersion(ResourceVersion $nextVersion)
    {
        if ($this->nextVersions->contains($nextVersion)) {
            $this->nextVersions->removeElement($nextVersion);
        }
    }

    /**
     * @param $branch
     *
    public function addBranch(ResourceNodeBranch $branch)
    {
        if (!$this->branches->contains($branch)) {
            $this->branches->add($branch);
        }
    }*/

    /**
     * @param $branch
     *
    public function removeBranch(ResourceNodeBranch $branch)
    {
        if ($this->branches->contains($branch)) {
            $this->branches->removeElement($branch);
        }
    }*/


    /**
     * @param string $locale optional locale to select the next version
     *
     * @return Document|null
     *
    public function getBranchById($branchId) : ResourceNodeBranch
    {
        $found = null;
        foreach ($this->branches as $branch) {
            if ($branch->getUuid() === $branchId) {
                $found = $branch;
                break;
            }
        }
        return $found;
    }*/
    

}