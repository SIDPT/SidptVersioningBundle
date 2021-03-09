<?php


namespace Sidpt\VersioningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Claroline\AppBundle\Entity\Identifier\Id;
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
    

    /**
     * (Mandatory) branch of the resource version
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceNodeBranch")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     *
     * @var string
     */
    protected $branch;

    /**
     * TODO
     * Optional status reference of the resource version
     * (Note : to be used for revisions and editorial management)
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceStatus")
     *
     * @var string
     
    protected $status;
    */
   

    /**
     * Optional version identifier (like a git tag)
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var integer
     */
    protected $version;

    

    /**
     * Resource class (that is, extending an AbtractResource)
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var [type]
     */
    protected $resourceClass;

    /**
     * [$resourceId description]
     * @var [type]
     */
    protected $resourceId;

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


    /**
     * [getPreviousVersion description]
     * @return [type] [description]
     */
    public function getPreviousVersion()
    {
        return $this->previousVersion;
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
     * @param string $locale optional locale to select the next version
     *
     * @return Document|null
     */
    public function getNextVersion(string $branch = null) : ResourceVersion
    {
        $found = null;
        if (!isset($branch) && !$nextVersion->isEmpty()) {
            $found = $nextVersion->first();
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
}