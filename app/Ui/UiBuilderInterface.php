<?php

namespace Lagdo\Supervisor\App\Ui;

use Supervisor\Process as SupervisorProcess;

interface UiBuilderInterface
{
    /**
     * @return string
     */
    public function wrapper();

    /**
     * @param array $serverItemIds
     *
     * @return string
     */
    public function servers(array $serverItemIds);

    /**
     * @param string $serverName
     * @param string $serverVersion
     *
     * @return string
     */
    public function server(string $server, string $serverName, string $serverVersion, array $processes);

    /**
     * @param SupervisorProcess $process
     *
     * @return string
     */
    public function process(string $server, SupervisorProcess $process);
}
