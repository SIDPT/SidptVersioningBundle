<?php


namespace Sidpt\VersioningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Claroline\AppBundle\Entity\Identifier\Id;
use Claroline\AppBundle\Entity\Identifier\Uuid;


use Claroline\CoreBundle\Entity\Resource\ResourceNode;

use Sidpt\VersioningBundle\Entity\ResourceVersion;

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
    use Uuid;



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
     * We assume each branch to uniquely handle a node,
     * with sub branches holding children of the main branch ResourceNode
     * (for translations)
     *
     * @ORM\OneToOne(
     *     targetEntity="Claroline\CoreBundle\Entity\Resource\ResourceNode")
     *
     * @var ResourceNode
     */
    protected $resourceNode;


    /**
     * Optional reference to a parent branch
     *
     * Parent is assumed to be the "main" branch, acting both
     * as a version repository attached to a 'real' resource node
     * and as a versionning branch
     *
     * @ORM\ManyToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceNodeBranch")
     *
     * @var ResourceNodeBranch
     */
    protected $parent;

    /**
     * Displayed version of the resource for the node branch
     * (should default to the last version)
     *
     * @ORM\OneToOne(
     *     targetEntity="Sidpt\VersioningBundle\Entity\ResourceVersion",
     *     cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @var [type]
     */
    protected $head;



    public function __construct()
    {
        $this->refreshUuid();
    }


    public function getName()
    {
        return $this->name;
    }

    public function getResourceNode()
    {
        return $this->resourceNode;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getHead()
    {
        return $this->head;
    }


    public function setName($name)
    {
        $this->name = $name;
    }

    public function setResourceNode(ResourceNode $resourceNode)
    {
        $this->resourceNode = $resourceNode;
    }

    public function setParent(ResourceNodeBranch $parent)
    {
        $this->parent = $parent;
    }

    public function setHead(ResourceVersion $head)
    {
        $this->head = $head;
    }

}