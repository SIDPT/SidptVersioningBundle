<?php

namespace Sidpt\VersioningBundle;

use Claroline\KernelBundle\Bundle\ExternalPluginBundle;
use Sidpt\VersioningBundle\Installation\AdditionalInstaller;

class SidptVersioningBundle extends ExternalPluginBundle
{
    public function getAdditionalInstaller()
    {
        return new AdditionalInstaller();
    }
}
