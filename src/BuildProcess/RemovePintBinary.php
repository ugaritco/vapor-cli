<?php

namespace Ugarit\VaporCli\BuildProcess;

use Ugarit\VaporCli\Helpers;
use Ugarit\VaporCli\Path;

class RemovePintBinary
{
    use ParticipatesInBuildProcess;

    /**
     * Execute the build process step.
     *
     * @return void
     */
    public function __invoke()
    {
        if ($this->files->exists($path = Path::app().'/vendor/ugarit/pint/builds/pint')) {
            Helpers::step('<options=bold>Removing Ugarit Pint Binary</>');

            $this->files->delete($path);
        }
    }
}
