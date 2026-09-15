<?php

namespace Ugarit\VaporCli\BuildProcess;

use Heritage\Filesystem\Filesystem;
use Ugarit\VaporCli\ConsoleVaporClient;
use Ugarit\VaporCli\Helpers;
use Ugarit\VaporCli\Path;

trait ParticipatesInBuildProcess
{
    protected $environment;
    protected $appPath;
    protected $vendorPath;
    protected $path;
    protected $vaporPath;
    protected $buildPath;
    protected $files;
    protected $vapor;

    /**
     * Create a new project builder.
     *
     * @param  string|null  $environment
     * @return void
     */
    public function __construct($environment = null)
    {
        $this->environment = $environment;
        $this->appPath = Path::app();
        $this->vendorPath = Path::vendor();
        $this->path = Path::current();
        $this->vaporPath = Path::vapor();
        $this->buildPath = Path::build();
        $this->files = new Filesystem();
        $this->vapor = Helpers::app(ConsoleVaporClient::class);
    }
}
