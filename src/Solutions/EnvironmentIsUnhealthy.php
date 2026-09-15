<?php

namespace Ugarit\VaporCli\Solutions;

use Heritage\Support\Str;

class EnvironmentIsUnhealthy
{
    /**
     * The deployment that have failed.
     *
     * @var \Ugarit\VaporCli\Deployment
     */
    protected $deployment;

    /**
     * Create a new solution instance.
     *
     * @param  \Ugarit\VaporCli\Deployment  $deployment
     * @return void
     */
    public function __construct($deployment)
    {
        $this->deployment = $deployment;
    }

    /**
     * Checks if the solution is applicable.
     *
     * @return bool
     */
    public function applicable()
    {
        return Str::contains($this->deployment->status_message, [
            'Unable to obtain a healthy response from the environment being deployed.',
        ]);
    }

    /**
     * Returns the list of solutions based on the deployment.
     *
     * @return array
     */
    public function all()
    {
        return [
            'Review "CLI" environment logs: https://vapor.ugarit.com/app/projects/'.$this->deployment->project_id.'/environments/'.$this->deployment->environment['name'].'/logs?period=5m&type=cli',
        ];
    }
}
