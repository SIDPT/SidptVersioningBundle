<?php

namespace Sidpt\VersioningBundle\API\Controller;

// traits
use Claroline\AppBundle\Controller\RequestDecoderTrait;
use Claroline\CoreBundle\Security\PermissionCheckerTrait;

// constructor params
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\AppBundle\API\Crud;
use Claroline\AppBundle\API\Options;
use Claroline\AppBundle\API\SerializerProvider;
use Claroline\AppBundle\API\FinderProvider;
use Claroline\CoreBundle\Manager\ResourceManager;
use Claroline\AppBundle\Controller\AbstractApiController;

// Exceptions
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Other use
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// entities
use Sidpt\VersioningBundle\Entity\ResourceNodeBranch;
use Sidpt\VersioningBundle\Entity\ResourceVersion;
use Claroline\CoreBundle\Entity\Resource\ResourceNode;


// logging for debug
use Claroline\AppBundle\Log\LoggableTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Versioning controller
 * @category Controller
 *
 * @Route("/versioning")
 */
class VersioningController implements LoggerAwareInterface
{
    use LoggableTrait;

    use PermissionCheckerTrait;
    use RequestDecoderTrait;



    /**
     * [$om description]
     *
     * @var ObjectManager [desc]
     */
    private $om;

    /**
     * [$crud description]
     * @var [type]
     */
    private $crud;

    /**
     * [$serializer description]
     * @var [type]
     */
    private $serializer;

    /**
     * [$finder description]
     * @var [type]
     */
    private $finder;

    /**
     * [$manager description]
     * @var [type]
     */
    private $manager;


    /**
     * [__construct description]
     *
     * @param AuthorizationCheckerInterface $authorization [description]
     * @param ObjectManager                 $om            [description]
     * @param Crud                          $crud          [description]
     * @param SerializerProvider            $serializer    [description]
     */
    public function __construct(
        AuthorizationCheckerInterface $authorization,
        ObjectManager $om,
        Crud $crud,
        FinderProvider $finder,
        SerializerProvider $serializer,
        ResourceManager $manager
    ) {
        $this->authorization = $authorization;
        $this->om = $om;
        $this->crud = $crud;
        $this->finder = $finder;
        $this->serializer = $serializer;
        $this->manager = $manager;
    }

    /**
     *  Get all nodes that have versioning activated
     *  (that is, nodes that have a main branch associated)
     *
     * @Route("/nodes",
     *     name="sidpt_versioning_get_nodes",
     *     methods={"GET"})
     *
     */
    public function getNodesAction()
    {
        $mainBranches = $this->finder->fetch(
            ResourceNodeBranch::class,
            [ 'parent' => null ]
        );
        if (empty($mainBranches)) {
            $mainBranches = [];
        }
        return new JsonResponse(
            array_map(
                function (ResourceNodeBranch $branch) {
                    return $this->serializer->serialize(
                        $branch->getResourceNode()
                    );
                },
                $mainBranches
            )
        );
    }


    /**
     *  Get the branches associated to a versioned resource node
     *
     * @Route("/{node}",
     *     name="sidpt_versioning_get_branches",
     *     methods={"GET"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:Resource\ResourceNode",
     *     options={"mapping": {"node": "uuid"}})
     * @EXT\ParamConverter("user", converter="current_user", options={"allowAnonymous"=false})
     */
    public function getBranchesAction(ResourceNode $node)
    {
        
        // Get the node branch
        $nodeBranches = $this->finder->fetch(
            ResourceNodeBranch::class,
            [   'resourceNode' => $node->getId() ]
        );

        if (!empty($nodeBranches)) {
            // If a main branch exist, use it instead
            if (!empty($nodeBranches[0]->getParent())) {
                $nodeBranches[0] = $nodeBranches[0]->getParent();
            }
            $main = $nodeBranches[0];
            $childBranches = $this->finder->fetch(
                ResourceNodeBranch::class,
                ['parent' => $main->getId()]
            );
            if (!empty($childBranches)) {
                $nodeBranches = array_merge(
                    $nodeBranches,
                    $childBranches
                );
            }
        } else {
            $nodeBranches = array();
        }
        return new JsonResponse(
            array_map(
                function (ResourceNodeBranch $branch) {
                    return $this->serializer->serialize($branch);
                },
                $nodeBranches
            )
        );
    }



    /**
     * Add a branch to a node
     * If its a main branch : create references to the current node and resource
     * if not : create copies of both the node and its associated resource,
     *     create a new version pointing to this new resource
     *     and set the branch to point on the node copy
     * 
     * @Route("/{node}",
     *     name="sidpt_versioning_add_branch",
     *     methods={"POST"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:Resource\ResourceNode",
     *     options={"mapping": {"node": "uuid"}})
     *
     */
    public function addBranchAction(ResourceNode $node, Request $request)
    {
        $newBranch = new ResourceNodeBranch();
        $newBranchHead = null;
        $data = $this->decodeRequest($request);

        // Beware : thats an array, not a simple object
        $mainBranch = $this->finder->fetch(
            ResourceNodeBranch::class,
            [   'resourceNode_id' => $node->getId(),
                'parent' => null
            ]
        );
        
        if (empty($mainBranch)) {
            // if the node has no main branch
            if (empty($data)) {
                // if no data are provided, default config
                $newBranch->setName("main");
                $newBranch->setResourceNode($node);
                $newBranchHead = new ResourceVersion();
                $newBranchHead->setBranch($newBranch);
                $newBranchHead->setResourceType($node->getResourceType());
                // Find the resource associated to the node
                $resource = $this->manager->getResourceFromNode($node);
                $newBranchHead->setResourceId($resource->getId());
            } else {
                // try deserialization
                $this->serializer->deserialize($data, $newBranch);
                // If no node data was provided, reference the current node
                if (empty($data['resourceNode'])) {
                    $newBranch->setResourceNode($node);
                }
                // If no head version data was provided, create a new one
                // pointing to the actual resource
                if (empty($data['head'])) {
                    $newBranchHead = new ResourceVersion();
                    $newBranchHead->setBranch($newBranch);
                    $newBranchHead->setResourceType($node->getResourceType());
                    // Find the resource associated to the node
                    $resource = $this->manager->getResourceFromNode($node);
                    $newBranchHead->setResourceId($resource->getId());
                }
            }
            $mainBranch[] = $newBranch;
        } elseif (!empty($data)) {
            if (empty($data['resourceNode'])) {
                // If no node data was provided,
                // retrieve current node user
                $user = $node->getCreator();
                // Create a node copy
                // note : according the resource node crud,
                //      a copy of the resource is also created
                $newNode = $this->crud->copy(
                    $node,
                    [Options::IGNORE_RIGHTS, Crud::NO_PERMISSIONS],
                    ['user' => $user, 'parent' => $node]
                );
                $this->om->persist($newNode);
                $newBranch->setResourceNode($newNode);
            }
            // Try deserialization (at minima to retrieve branch name)
            $this->serializer->deserialize($data, $newBranch);

            // TODO : avoid persisting the branch if a one with the same name
            // already exists for the node
            // (could be resolved on the data mode)
            

            $newNode = $newBranch->getResourceNode();
            if (empty($data['head'])) {
                // If no head version data was provided,
                // create a new one that point to the new node resource
                $newBranchHead = new ResourceVersion();
                $newBranchHead->setBranch($newBranch);
                $newBranchHead->setResourceType($newNode->getResourceType());
                // Find the resource associated to the node
                $resource = $this->manager->getResourceFromNode($newNode);
                $newBranchHead->setResourceId($resource->getId());
                
                // add the new version as next version of the main head
                $newBranchHead->setPreviousVersion($mainBranch[0]->getHead());
                $mainBranch[0]->getHead()->addNextVersion($newBranchHead);
            }

            $newBranch->setParent($mainBranch[0]);
        } else { // error case, no data provided for the new branch
            return new JsonResponse(['missing_branch_data'], 500);
        }
        
        if (isset($newBranchHead)) {
            $newBranch->setHead($newBranchHead);
            $this->om->persist($newBranchHead);
        }
        $this->om->persist($newBranch);
        $this->om->flush();

        return $this->getBranchesAction($mainBranch[0]->getResourceNode());
    }

    /**
     * @Route("/branch/{branch}/versions",
     *     name="sidpt_versioning_update_branch",
     *     methods={"GET"})
     * @EXT\ParamConverter(
     *     "branch",
     *     class="SidptVersioningBundle:ResourceNodeBranch",
     *     options={"mapping": {"branch": "uuid"}})
     *
     */
    public function getVersionsAction(ResourceNodeBranch $branch)
    {
        $head = $branch->getHead();


    }

    /**
     * @Route("/update/branch/{branch}",
     *     name="sidpt_versioning_update_branch",
     *     methods={"PUT"})
     * @EXT\ParamConverter(
     *     "branch",
     *     class="SidptVersioningBundle:ResourceNodeBranch",
     *     options={"mapping": {"branch": "uuid"}})
     *
     */
    public function updateBranchAction(
        Request $request,
        ResourceNodeBranch $branch
    ) {
        //If creating branch hierarchy, add a do/while for top parent search
        $mainBranch = $branch->getParent() ?: $branch;
        $data = $this->decodeRequest($request);
        $this->serializer->deserialize($data, $branch);
        $this->om->persist($branch);
        $this->om->flush();

        return $this->getBranchesAction($mainBranch->getResourceNode());
    }

    /**
     * Note:
     * DELETE method being not allowed, replacing delete by PUT through a new route
     *
     * @Route("/delete/branch/{branch}",
     *     name="sidpt_versioning_delete_branch",
     *     methods={"PUT"})
     * @EXT\ParamConverter(
     *     "branch",
     *     class="SidptVersioningBundle:ResourceNodeBranch",
     *     options={"mapping": {"branch": "uuid"}})
     *
     */
    public function deleteBranchAction(
        Request $request,
        ResourceNodeBranch $branch
    ) {

        //If creating branch hierarchy, add a do/while for top parent search
        $mainBranch = $branch->getParent() ?: $branch;
        // versions referencing the branch
        $versions = $this->finder->fetch(
            ResourceVersion::class,
            [ 'branch' => $branch->getId() ]
        );

        // unlink all versions
        foreach ($versions as $key => $version) {
            // check if version is linked to another branch by its predecessor
            $previousVersion = $version->getPreviousVersion();
            if (!empty($previousVersion)) {
                // remove previous to current
                $previousVersion->removeNextVersion($version);
                // remove current to previous
                $version->setPreviousVersion(null);
                $this->om->persist($previousVersion);
            }
        }

        // remove resource and nodes of the child nodes
        if (!empty($branch->getParent())) {
            // delete the resource node associated with it
            $this->om->remove($branch->getResourceNode());
            // Delete the resources of each versions
            foreach ($versions as $key => $version) {
                $resource = $this->om->find(
                    $version->getResourceType()->getClass(),
                    $version->getResourceId()
                );
                if (!empty($resource)) {
                    $this->om->remove($resource);
                }
            }
        }

        // remove the link to the branch of each concerned version
        foreach ($versions as $key => $version) {
            $version->setBranch(null);
            $this->om->persist($version);
        }
        $this->om->flush();
        
        // remove the branch
        $this->om->remove($branch);
        $this->om->flush();
        
        // remove all versions
        foreach ($versions as $key => $version) {
            $this->om->remove($version);
        }
        $this->om->flush();

        // return updated branches list
        return $this->getBranchesAction($mainBranch->getResourceNode());
    }

    /**
     * Create a new version with a new resource,
     *     change branch head to the new version
     *     and make the new resource pointing the branch node
     *     instead of the current version
     *
     * TODO : add status update with commit
     *
     * @Route("/version/{afterVersion}",
     *     name="sidpt_versioning_commit",
     *     methods={"POST"})
     *
     * @EXT\ParamConverter(
     *     "version",
     *     class="SidptVersioningBundle:ResourceVersion",
     *     options={"mapping": {"afterVersion": "uuid"}})
     *
     */
    public function commitAction(ResourceVersion $version, Request $request)
    {
        
        //If creating branch hierarchy, add a do/while for top parent search
        $branch = $version->getBranch();
        $mainBranch = $branch->getParent() ?: $branch;
        $data = $this->decodeRequest($request);

        $resource = $this->om->find(
            $version->getResourceType()->getClass(),
            $version->getResourceId()
        );

        $newVersion = new ResourceVersion();
        $newVersion->setBranch($version->getBranch());
        $newVersion->setResourceType($version->getResourceType());
            
        $newResource = $this->crud->copy($resource, [Options::REFRESH_UUID]);
        // transfer node link to the new resource
        $newResource->setResourceNode($resource->getResourceNode());
        $resource->setResourceNode(null);
        
        // Create version pointing to the new resource
        $newVersion->setResourceId($newResource->getId());

        // linking versions
        $newVersion->setPreviousVersion($version);
        $version->addNextVersion($newVersion);
        
        // Set the branch head to the new version
        $version->getBranch()->setHead($newVersion);

        // If additionnal version data are provided with the request
        if (!empty($data)) {
            $this->serializer->deserialize($data, $newVersion);
        }

        $this->om->persist($resource);
        $this->om->persist($newResource);
        $this->om->persist($version);
        $this->om->persist($newVersion);
        $this->om->persist($version->getBranch());
        $this->om->flush();

        // return the updated branches list
        return $this->getBranchesAction($mainBranch->getResourceNode());
    }

    /**
     *
     * @Route("/version/{versionId}",
     *     name="sidpt_versioning_get_version",
     *     methods={"GET"})
     * @EXT\ParamConverter(
     *     "version",
     *     class="SidptVersioningBundle:ResourceVersion",
     *     options={"mapping": {"versionId": "uuid"}})
     *
     */
    public function getVersionAction(ResourceVersion $version)
    {
        return new JsonResponse(
            $this->serializer->serialize($version)
        );
    }

    /**
     *
     *
     * @Route("/version/{version}",
     *     name="sidpt_versioning_update_version",
     *     methods={"PUT"})
     * @EXT\ParamConverter(
     *     "version",
     *     class="SidptVersioningBundle:ResourceVersion",
     *     options={"mapping": {"version": "uuid"}})
     *
     */
    public function updateVersionAction(ResourceVersion $version, Request $request)
    {
        
        //If creating branch hierarchy, add a do/while for top parent search
        $branch = $version->getBranch();
        $mainBranch = $branch->getParent() ?: $branch;
        $data = $this->decodeRequest($request);

        // If additionnal version data are provided with the request
        if (!empty($data)) {
            $this->serializer->deserialize($data, $newVersion);
        }

        $this->om->persist($version);
        $this->om->flush();

        // return the updated branches list
        return $this->getBranchesAction($mainBranch->getResourceNode());
    }



    /**
     * @Route("/version/{version}",
     *     name="sidpt_versioning_delete_version",
     *     methods={"DELETE"})
     * @EXT\ParamConverter(
     *     "version",
     *     class="SidptVersioningBundle:ResourceVersion",
     *     options={"mapping": {"version": "uuid"}})
     *
     */
    public function deleteVersionAction(
        ResourceVersion $version
    ) {
        $branch = $version->getBranch();
        $mainBranch = $branch->getParent() ?: $branch;
        
        // Default behavior :
        // if there is a previous version, relink next versions with it
        $previous = $version->getPreviousVersion();
        if (!empty($previous)) {
            // if previous is on the same branch
            // change branch head to the previous
            // if not :
            //  if the deleted version has a next version on the same branch
            //      change head to this next
            //  if not :
            //      raise an error saying to remove branch instead of version
            $previous->removeNextVersion($version);
            foreach ($version->getNextVersions() as $next) {
                $previous->addNextVersion($next);
                $next->setPreviousVersion($previous);
            }

        }

        // remove the version
        $this->om->remove($version);
        $this->om->flush();

        return $this->getBranchesAction($mainBranch->getResourceNode());
    }

    
}
