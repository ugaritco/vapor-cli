<?php

namespace Ugarit\VaporCli\BuildProcess;

use Ugarit\VaporCli\Helpers;

class InjectErrorPages
{
    use ParticipatesInBuildProcess;

    /**
     * Execute the build process step.
     *
     * @return void
     */
    public function __invoke()
    {
        Helpers::step('<options=bold>Injecting Error Pages</>');

        $stubPath = $this->appPath.'/vendor/ugarit/vapor-core/stubs';

        if (! file_exists($this->appPath.'/503.html')) {
            $this->files->copy($stubPath.'/503.html', $this->appPath.'/503.html');
        }
    }
}
