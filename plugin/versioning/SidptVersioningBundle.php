<?php

namespace Sidpt\VersioningBundle;

use Claroline\CoreBundle\Library\DistributionPluginBundle;
use Sidpt\VersioningBundle\Installation\AdditionalInstaller;

class SidptVersioningBundle extends DistributionPluginBundle
{
    public function getAdditionalInstaller()
    {
        return new AdditionalInstaller();
    }
}
