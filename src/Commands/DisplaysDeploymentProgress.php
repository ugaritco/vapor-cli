<?php

namespace Ugarit\VaporCli\Commands;

use Ugarit\VaporCli\Commands\Output\DeploymentFailure;
use Ugarit\VaporCli\Commands\Output\DeploymentSuccess;
use Ugarit\VaporCli\Helpers;
use Ugarit\VaporCli\Models\Deployment;

trait DisplaysDeploymentProgress
{
    /**
     * The deployment steps that have been displayed.
     *
     * @var array
     */
    protected $displayedSteps = [];

    /**
     * Display the server-side deployment pipeline for the given deployment.
     *
     * @param  array  $deployment
     * @return array
     */
    protected function displayDeploymentProgress(array $deployment)
    {
        $deployment = new Deployment($deployment);

        if ($deployment->hasEnded()) {
            return $deployment->toArray();
        }

        Helpers::line();

        with($deployment = $this->displayDeploymentSteps($deployment))->isFinished()
                        ? $this->displaySuccessMessage($deployment)
                        : $this->displayFailureMessage($deployment);

        return $deployment->toArray();
    }

    /**
     * Display the deployment steps until the deployment is finished.
     *
     * @param  \Ugarit\VaporCli\Models\Deployment  $deployment
     * @return \Ugarit\VaporCli\Models\Deployment
     */
    protected function displayDeploymentSteps(Deployment $deployment)
    {
        while (! $deployment->hasEnded()) {
            $this->displayActiveDeploymentSteps($deployment = new Deployment(
                $this->vapor->deployment($deployment->id)
            ));

            sleep(1);
        }

        return $deployment;
    }

    /**
     * Display any new, active deployment steps.
     *
     * @param  \Ugarit\VaporCli\Models\Deployment  $deployment
     * @return void
     */
    protected function displayActiveDeploymentSteps(Deployment $deployment)
    {
        foreach ($deployment->displayableSteps($this->displayedSteps) as $step) {
            Helpers::step("<options=bold>{$step}</>");

            $this->displayedSteps[] = $step;
        }
    }

    /**
     * Display the deployment success message.
     *
     * @param  \Ugarit\VaporCli\Models\Deployment  $deployment
     * @return void
     */
    protected function displaySuccessMessage(Deployment $deployment)
    {
        (new DeploymentSuccess())->render($deployment, $this->startedAt);
    }

    /**
     * Display the deployment failure message.
     *
     * @param  \Ugarit\VaporCli\Models\Deployment  $deployment
     * @return void
     */
    protected function displayFailureMessage(Deployment $deployment)
    {
        (new DeploymentFailure())->render($deployment);
    }
}
