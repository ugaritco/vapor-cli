<?php

namespace Ugarit\VaporCli\BuildProcess;

use Heritage\Filesystem\Filesystem;
use Ugarit\VaporCli\Helpers;
use Ugarit\VaporCli\Manifest;

class ExtractVendorToSeparateDirectory
{
    use ParticipatesInBuildProcess;

    /**
     * Execute the build process step.
     *
     * @return void
     */
    public function __invoke()
    {
        if (! Manifest::shouldSeparateVendor($this->environment)) {
            return;
        }

        Helpers::step('<options=bold>Extracting Vendor Files</>');

        (new Filesystem())->move(
            $this->appPath.'/vendor',
            $this->buildPath.'/vendor'
        );
    }
}
