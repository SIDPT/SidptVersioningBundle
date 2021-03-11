<?php

namespace Sidpt\VersioningBundle\API\Controller;

// traits
use Claroline\AppBundle\Controller\RequestDecoderTrait;
use Claroline\CoreBundle\Security\PermissionCheckerTrait;

// constructor params
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\AppBundle\API\Crud;
use Claroline\AppBundle\API\SerializerProvider;

// Exceptions
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Other use
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Sidpt\VersioningBundle\Entity\ResourceNodeBranch;

// logging for debug
use Claroline\AppBundle\Log\LoggableTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Versioning controller
 * @category Controller
 *
 * @Route("/versioning")
 */
class VersioningController extends AbstractApiController implements LoggerAwareInterface
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

    private $finder;


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
        SerializerProvider $serializer
    ) {
        $this->authorization = $authorization;
        $this->om = $om;
        $this->crud = $crud;
        $this->finder = $finder;
        $this->serializer = $serializer;
    }

    /**
     *  Get all nodes that have versioning activated
     *  (that is, nodes that have a main branch associated)
     *
     * @Route("",
     *     name="sidpt_versioning_get_nodes",
     *     methods={"GET"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:ResourceNode",
     *     options={"mapping": {"nodeId": "uuid"}})
     *
     */
    public function getNodesAction()
    {
        $mainBranches = $this->finder->fetch(
            ResourceNodeBranch::class,
            [   'filters' => [
                    'parent' => null,
                ]
            ]
        );
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
     * @Route("/{nodeId}",
     *     name="sidpt_versioning_get_branches",
     *     methods={"GET"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:ResourceNode",
     *     options={"mapping": {"nodeId": "uuid"}})
     *
     */
    public function getBranchesAction(ResourceNode $node)
    {
        // Get the main branch
        $nodeBranches = $this->finder->fetch(
            ResourceNodeBranch::class,
            [   'filters' => [
                    'resourceNode' => $node,
                    'parent' => null,
                ]
            ]
        );
        if (!empty($nodeBranches)) {
            $main = $nodeBranches[0];
            // get the child branches
            $nodeBranches = array_merge(
                $nodeBranches,
                $this->finder->fetch(
                    ResourceNodeBranch::class,
                    [   'filters' => [
                            'parent' => $main,
                        ]
                    ]
                )
            );
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
     * @Route("/{nodeId}",
     *     name="sidpt_versioning_add_branch",
     *     methods={"POST"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:ResourceNode",
     *     options={"mapping": {"nodeId": "uuid"}})
     *
     */
    public function addBranchAction(ResourceNode $node, Request $request)
    {
        $newBranch = new ResourceNodeBranch();
        $data = $this->decodeRequest($request);

        if (empty($data)) {
            $mainBranch = $this->finder->fetch(
                ResourceNodeBranch::class,
                [   'filters' => [
                        'resourceNode' => $node,
                        'parent' => null,
                    ]
                ]
            );

            if (empty($mainBranch)) {
                $newBranch->setName("main");
                $newBranch->setResourceNode($node);
                $version = new ResourceVersion();
                $version->setBranch($newBranch);
                $version->setResourceType($node->getResourceType());
                // Find the resource associated to the node
                $resource = $this->finder->fetch(
                    $node->getResourceType()->getClass(),
                    [   'filters' => [
                        'resourceNode' => $node
                        ]
                    ]
                );
                $version->setResourceId($resource[0]->getUuid());
                $newBranch->setHead($version);

            } else { // create a subbranch
                
                // Create resource node copy
                // make a new version
                // create resource copy
            }
        } else {
            $this->serializer->deserialize($data, $newBranch);
        }
        
        return new JsonResponse($this->serializer->serialize($newBranch));
    }

    /**
     * @Route("/branch/{branchId}",
     *     name="sidpt_versioning_update_branch",
     *     methods={"PUT"})
     * @EXT\ParamConverter(
     *     "branch",
     *     class="SidptVersioningBundle:ResourceNodeBranch",
     *     options={"mapping": {"branchId": "uuid"}})
     *
     */
    public function updateBranchAction(
        Request $request,
        ResourceNodeBranch $branch
    ) {
        $data = $this->decodeRequest($request);

        $this->serializer->deserialize($data, $branch);

        return new JsonResponse($this->serializer->serialize($branch));
    }

    /**
     * @Route("/branch/{branchId}",
     *     name="sidpt_versioning_delete_branch",
     *     methods={"DELETE"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:ResourceNode",
     *     options={"mapping": {"nodeId": "uuid"}})
     * @EXT\ParamConverter(
     *     "branch",
     *     class="SidptVersioningBundle:ResourceNodeBranch",
     *     options={"mapping": {"branchId": "uuid"}})
     *
     */
    public function deleteBranchAction(
        Request $request,
        ResourceNodeBranch $branch
    ) {
        // if this is a child branch,
        //   delete the resource node associated with it*
        if (!empty($branch->getParentBranch())) {
            $this->om->remove($branch->getResourceNode());
        }
        // remove all versions that are linked to the branch
        $versions = $this->om->fetch(
            ResourceVersion::class,
            [   'filters' => [
                    'branch' => $branch,
                ]
            ]
        );
        foreach ($versions as $version) {
            $this->om->remove($version);
        }

        // remove the branch
        $this->om->remove($branch);

        
    }

    /**
     * @Route("/branch/{branchId}",
     *     name="sidpt_versioning_add_version",
     *     methods={"POST"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:ResourceNode",
     *     options={"mapping": {"nodeId": "uuid"}})
     * @EXT\ParamConverter(
     *     "branch",
     *     class="SidptVersioningBundle:ResourceNodeBranch",
     *     options={"mapping": {"branchId": "uuid"}})
     *
     */
    public function addVersionAction(Request $request, ResourceVersion $version)
    {
        $data = $this->decodeRequest($request);
        // copy the resource from


    }

    public function getVersionAction()
    {

    }



    /**
     * @Route("/branch/{branchId}/{versionId}", name="sidpt_versioning_delete_version", methods={"DELETE"})
     * @EXT\ParamConverter(
     *     "branch",
     *     class="SidptVersioningBundle:ResourceNodeBranch",
     *     options={"mapping": {"branchId": "uuid"}})
     * @EXT\ParamConverter(
     *     "version",
     *     class="SidptVersioningBundle:ResourceVersion",
     *     options={"mapping": {"versionId": "uuid"}})
     *
     */
    public function deleteVersionAction(
        Request $request,
        ResourceNodeBranch $branch,
        ResourceVersion $version
    ) {

    }

    
}
