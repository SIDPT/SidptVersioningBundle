<?php
/**
 *
 */

namespace Sidpt\BinderBundle\Listener\Resource;

use Claroline\AppBundle\API\SerializerProvider;
use Claroline\AppBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Event\ExportObjectEvent;
use Claroline\CoreBundle\Event\ImportObjectEvent;
use Claroline\CoreBundle\Event\Tool\ConfigureToolEvent;
use Claroline\CoreBundle\Event\Tool\OpenToolEvent;
use Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 *
 */
class VersioningListener
{
    /**
     * [$authorization description]
     *
     * @var [type]
     */
    private $authorization;

    /**
     * [$om description]
     *
     * @var [type]
     */
    private $om;

    /**
     * [$config description]
     *
     * @var [type]
     */
    private $config;

    /**
     * [$serializer description]
     *
     * @var [type]
     */
    private $serializer;
    

    /**
     * [__construct description]
     *
     * @param AuthorizationCheckerInterface $authorization [description]
     * @param ObjectManager                 $om            [description]
     * @param PlatformConfigurationHandler  $config        [description]
     * @param SerializerProvider            $serializer    [description]
     */
    public function __construct(
        AuthorizationCheckerInterface $authorization,
        ObjectManager $om,
        PlatformConfigurationHandler $config,
        SerializerProvider $serializer
    ) {
        $this->authorization = $authorization;
        $this->om = $om;
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * [onLoad description]
     *
     * @param LoadResourceEvent $event [description]
     *
     * @return [type]                   [description]
     */
    public function onOpen(OpenToolEvent $event)
    {
        
        $event->stopPropagation();
    }
}
