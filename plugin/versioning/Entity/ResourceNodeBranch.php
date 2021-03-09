<?php


namespace Sidpt\VersioningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Claroline\AppBundle\Entity\Identifier\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Branch of versions of a resource, attached to a ResourceNode
 *
 *
 * @ORM\Entity()
 * @ORM\Table(name="sidpt__resource_node_branch")
 */
class ResourceNodeBranch
{
    use Id;

    /**
     * Branch name for the current version.
     * Default is "main"
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $name = "main";

    /**
     * ResourceNode handled by the branch
     *
     * @ORM\ManyToOne(
     *     targetEntity="Claroline\CoreBundle\Entity\Resource\ResourceNode")
     *
     * @var ResourceNode
     */
    protected $resourceNode;


    /**
     * Optional reference to a parent branch (mainly the "main" one)
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceNodeBranch")
     *
     * @var ResourceNodeBranch
     */
    protected $parentBranch;

    /**
     * Last version of the resource for the node branch
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceVersion")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var [type]
     */
    protected $head;

    /**
     * Optional version to displayed for the branch
     * (If unspecified, the head is displayed)
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceVersion")
     *
     * @var [type]
     */
    protected $displayedVersion;

    /**
     * Specify if the branch is the default one to use for a node
     *
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $default = false;



    public function getName()
    {
        return $this->name;
    }

    public function getResourceNode()
    {
        return $this->resourceNode;
    }

    public function getParentBranch()
    {
        return $this->parentBranch;
    }

    public function getHead()
    {
        return $this->head;
    }

    public function getDisplayedVersion()
    {
        if (isset($this->displayedVersion)) {
            return $this->displayedVersion;
        } else {
            return $this->head;
        }
    }

    public function isDefault($newStatus = null)
    {
        if (isset($newStatus)) {
            $this->default = $newStatus;
        }
        return $this->default;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setResourceNode($resourceNode)
    {
        $this->resourceNode = $resourceNode;
    }

    public function setParentBranch($parentBranch)
    {
        $this->parentBranch = $parentBranch;
    }

    public function setHead($head)
    {
        $this->head = $head;
    }

    public function setDisplayedVersion($displayedVersion)
    {
        $this->displayedVersion = $displayedVersion;
    }

}