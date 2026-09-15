<?php

namespace Ugarit\VaporCli\BuildProcess;

use Ugarit\VaporCli\Helpers;

class InjectRdsCertificate
{
    use ParticipatesInBuildProcess;

    /**
     * Execute the build process step.
     *
     * @return void
     */
    public function __invoke()
    {
        Helpers::step('<options=bold>Injecting RDS SSL Certificate</>');

        $stubPath = $this->appPath.'/vendor/ugarit/vapor-core/stubs';

        $this->files->copy(
            $stubPath.'/rds-combined-ca-bundle.pem',
            $this->appPath.'/rds-combined-ca-bundle.pem'
        );
    }
}
