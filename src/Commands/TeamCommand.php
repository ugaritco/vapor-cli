<?php

namespace Ugarit\VaporCli\Commands;

use Ugarit\VaporCli\Helpers;
use Symfony\Component\Console\Input\InputArgument;

class TeamCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('team')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the team')
            ->setDescription('Create a new team');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        Helpers::ensure_api_token_is_available();

        $this->vapor->createTeam($this->argument('name'));

        Helpers::info('Team created successfully.');
    }
}
