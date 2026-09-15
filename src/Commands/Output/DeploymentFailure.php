<?php

namespace Ugarit\VaporCli\Commands\Output;

use Ugarit\VaporCli\Commands\HookOutputCommand;
use Ugarit\VaporCli\ConsoleVaporClient;
use Ugarit\VaporCli\Helpers;
use Ugarit\VaporCli\Models\Deployment;

class DeploymentFailure
{
    /**
     * Render the output.
     *
     * @return void
     */
    public function render(Deployment $deployment)
    {
        Helpers::line('');
        Helpers::danger('    <bg=red;options=bold> Deployment Failed </>');

        if ($deployment->status_message) {
            Helpers::line('');
            Helpers::line($deployment->formattedStatusMessage());
        }

        $deployment->solutions()->whenNotEmpty(function ($solutions) {
            Helpers::line('');
            Helpers::line('    <fg=white;options=bold>Possible solutions:</>');

            $solutions->each(function ($solution) {
                Helpers::line(sprintf(
                    '    <fg=blue;options=bold>✓ </>%s',
                    $solution
                ));
            });
        });

        Helpers::line('');

        if ($deployment->hasFailedHooks() && ($hook = collect($deployment->hooks)->where('status', 'failed')->first())) {
            $output = Helpers::app(ConsoleVaporClient::class)->deploymentHookOutput($hook['id'])['output'];

            Helpers::line("<fg=red>Hook:</> {$hook['command']}");
            HookOutputCommand::writeOutput($output);

            Helpers::line();
            Helpers::line('<fg=red>Logs:</> You may review its logs using the `hook:log '.$hook['id'].'` command.');
        }
    }
}
