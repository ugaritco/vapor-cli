<?php

namespace Ugarit\VaporCli\Commands;

use Heritage\Filesystem\Filesystem;
use Heritage\Support\Arr;
use Ugarit\VaporCli\GitIgnore;
use Ugarit\VaporCli\Helpers;
use Ugarit\VaporCli\Manifest;
use Ugarit\VaporCli\Path;
use Symfony\Component\Console\Input\InputArgument;

class EnvCloneCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('env:clone')
            ->addArgument('from', InputArgument::REQUIRED, 'The environment to clone from')
            ->addArgument('to', InputArgument::REQUIRED, 'The name that should be assigned to the cloned environment')
            ->setDescription('Clone an existing environment');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        Helpers::ensure_api_token_is_available();

        $environment = $this->vapor->cloneEnvironment(
            Manifest::id(),
            $this->argument('from'),
            $this->argument('to')
        );

        $manifest = Manifest::current();

        Manifest::addEnvironment(
            $this->argument('to'),
            Arr::except($manifest['environments'][$this->argument('from')] ?? [], 'domain')
        );

        if (file_exists(Path::dockerfile($this->argument('from')))) {
            (new Filesystem())->copy(
                Path::dockerfile($this->argument('from')),
                Path::dockerfile($this->argument('to'))
            );
        }

        GitIgnore::add(['.env.'.$this->argument('to')]);

        Helpers::info('Environment cloned successfully.');
    }
}
