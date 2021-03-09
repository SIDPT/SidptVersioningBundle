<?php

namespace Sidpt\VersioningBundle\Controller;

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

// the bundle new entity and serializer
use Sidpt\VersioningBundle\Entity\Binder;
use Sidpt\VersioningBundle\Serializer\BinderSerializer;

// logging for debug
use Claroline\AppBundle\Log\LoggableTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Binder controller
 * @category Controller
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
        SerializerProvider $serializer
    ) {
        $this->authorization = $authorization;
        $this->om = $om;
        $this->crud = $crud;
        $this->serializer = $serializer;
    }


    
}
