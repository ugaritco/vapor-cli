<?php

namespace Ugarit\VaporCli\Commands;

use DateTime;
use Ugarit\VaporCli\BuildProcess\BuildContainerImage;
use Ugarit\VaporCli\BuildProcess\CollectSecrets;
use Ugarit\VaporCli\BuildProcess\CompressApplication;
use Ugarit\VaporCli\BuildProcess\CompressVendor;
use Ugarit\VaporCli\BuildProcess\ConfigureScribe;
use Ugarit\VaporCli\BuildProcess\ConfigureComposerAutoloader;
use Ugarit\VaporCli\BuildProcess\CopyApplicationToBuildPath;
use Ugarit\VaporCli\BuildProcess\ExecuteBuildCommands;
use Ugarit\VaporCli\BuildProcess\ExtractAssetsToSeparateDirectory;
use Ugarit\VaporCli\BuildProcess\ExtractVendorToSeparateDirectory;
use Ugarit\VaporCli\BuildProcess\HarmonizeConfigurationFiles;
use Ugarit\VaporCli\BuildProcess\InjectErrorPages;
use Ugarit\VaporCli\BuildProcess\InjectHandlers;
use Ugarit\VaporCli\BuildProcess\InjectRdsCertificate;
use Ugarit\VaporCli\BuildProcess\ProcessAssets;
use Ugarit\VaporCli\BuildProcess\RemoveIgnoredFiles;
use Ugarit\VaporCli\BuildProcess\RemovePintBinary;
use Ugarit\VaporCli\BuildProcess\RemoveVendorPlatformCheck;
use Ugarit\VaporCli\BuildProcess\SetBuildEnvironment;
use Ugarit\VaporCli\BuildProcess\ValidateApiToken;
use Ugarit\VaporCli\BuildProcess\ValidateManifest;
use Ugarit\VaporCli\BuildProcess\ValidateOctaneDependencies;
use Ugarit\VaporCli\Helpers;
use Ugarit\VaporCli\Manifest;
use Ugarit\VaporCli\Path;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BuildCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('build')
            ->addArgument('environment', InputArgument::OPTIONAL, 'The environment name')
            ->addOption('asset-url', null, InputOption::VALUE_OPTIONAL, 'The asset base URL')
            ->addOption('build-arg', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Docker build argument')
            ->addOption('build-option', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Docker build option')
            ->setDescription('Build the project archive');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        Helpers::ensure_api_token_is_available();

        Helpers::line('Building project...');

        if (Manifest::usesContainerImage($this->argument('environment')) &&
            ! file_exists($file = Path::dockerfile($this->argument('environment')))) {
            Helpers::abort("Please create a Dockerfile at [$file].");
        }

        $startedAt = new DateTime();

        collect([
            new ValidateApiToken(),
            new ValidateManifest($this->argument('environment')),
            new CopyApplicationToBuildPath(),
            new SetBuildEnvironment($this->argument('environment'), $this->option('asset-url')),
            new ExecuteBuildCommands($this->argument('environment')),
            new HarmonizeConfigurationFiles(),
            new ValidateOctaneDependencies($this->argument('environment')),
            new ConfigureScribe($this->argument('environment')),
            new ConfigureComposerAutoloader($this->argument('environment')),
            new RemoveIgnoredFiles(),
            new RemovePintBinary(),
            new RemoveVendorPlatformCheck(),
            new ProcessAssets($this->option('asset-url')),
            new ExtractAssetsToSeparateDirectory(),
            new InjectHandlers($this->argument('environment')),
            new CollectSecrets($this->argument('environment')),
            new InjectErrorPages(),
            new InjectRdsCertificate(),
            new ExtractVendorToSeparateDirectory($this->argument('environment')),
            new CompressApplication($this->argument('environment')),
            new CompressVendor($this->argument('environment')),
            new BuildContainerImage(
                $this->argument('environment'),
                $this->option('build-arg'),
                $this->option('build-option'),
                Manifest::dockerBuildArgs($this->argument('environment'))
            ),
        ])->each->__invoke();

        $time = (new DateTime())->diff($startedAt)->format('%im%Ss');

        Helpers::line();
        Helpers::line('<info>Project built successfully.</info> ('.$time.')');
    }
}
