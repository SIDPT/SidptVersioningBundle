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
        $nodeBranches = $this->finder->search(
            ResourceNodeBranch::class,
            [   'filters' => [
                    'resourceNode' => $node,
                    'parent' => null,
                ]
            ]
        );
        if (!empty($nodeBranches)) {
            $main = $nodeBranches[0];
            $nodeBranches = array_merge(
                $nodeBranches,
                $this->finder->search(
                    ResourceNodeBranch::class,
                    [   'filters' => [
                            'parent' => $main,
                        ]
                    ]
                )
            );
        }
        // Check if other branch are linked to this main branch
        
        return new JsonResponse(
            [   'branches' => $nodeBranches ]
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
        $data = $this->decodeRequest($request);
        if (isset($data['branch'])) {
            
        } else {
            // Create a default "main" branch
        }
    }

    /**
     * @Route("/{nodeId}/{branchId}",
     *     name="sidpt_versioning_update_branch",
     *     methods={"PUT"})
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
    public function updateBranchAction(
        Request $request,
        ResourceNode $node,
        ResourceNodeBranch $branch
    ) {

    }

    /**
     * @Route("/{nodeId}/{branchId}",
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
        ResourceNode $node,
        ResourceNodeBranch $branch
    ) {
        // Delete the selected branch from the node
        // if this is a children branch,
        //   also delete the resource node associated with it
    }

    /**
     * @Route("/{nodeId}/{branchId}",
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

    }

    /**
     * @Route("/{nodeId}/{branchId}/{versionId}", name="sidpt_versioning_delete_version", methods={"DELETE"})
     * @EXT\ParamConverter(
     *     "node",
     *     class="ClarolineCoreBundle:ResourceNode",
     *     options={"mapping": {"versionId": "id"}})
     *
     */
    public function deleteVersionAction(ResourceNode $node)
    {

    }

    
}
