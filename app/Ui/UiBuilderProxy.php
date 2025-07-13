<?php

namespace Lagdo\Supervisor\App\Ui;

use Lagdo\Supervisor\App\Ajax\Web\Home;
use Lagdo\Supervisor\App\Ajax\Web\Process;
use Lagdo\Supervisor\App\Ajax\Web\Server;
use Supervisor\Process as SupervisorProcess;

use function Jaxon\cl;
use function Jaxon\jaxon;
use function Jaxon\rq;

class UiBuilderProxy implements UiBuilderInterface
{
    /**
     * @param UiBuilderInterface|null $ui
     */
    public function __construct(protected ?UiBuilderInterface $ui)
    {}

    /**
     * @return string
     */
    public function wrapper(): string
    {
        if($this->ui !== null)
        {
            return $this->ui->wrapper();
        }

        return jaxon()->view()->render('lagdo::supervisor::views::wrapper', [
            'rqHome' => rq(Home::class),
        ]) . '';
    }

    /**
     * @param array $serverItemIds
     *
     * @return string
     */
    public function servers(array $serverItemIds): string
    {
        if($this->ui !== null)
        {
            return $this->ui->servers($serverItemIds);
        }

        return jaxon()->view()->render('lagdo::supervisor::views::servers', [
            'rqServer' => rq(Server::class),
            'serverItemIds' => $serverItemIds,
        ]) . '';
    }

    /**
     * Get the HTML code of a simple form
     *
     * @param string $server
     * @param string $serverName
     * @param string $serverVersion
     * @param array $processes
     *
     * @return string
     */
    public function server(string $server, string $serverName, string $serverVersion, array $processes): string
    {
        if($this->ui !== null)
        {
            return $this->ui->server($server, $serverName, $serverVersion, $processes);
        }

        return jaxon()->view()->render('lagdo::supervisor::views::server', [
            'server' => $server,
            'serverName' => $serverName,
            'serverVersion' => $serverVersion,
            'processes' => $processes,
            'rqServer' => rq(Server::class),
            'rqProcess' => rq(Process::class),
            'clProcess' => cl(Process::class),
        ]) . '';
    }

    /**
     * @param string $serverName
     * @param SupervisorProcess $process
     *
     * @return string
     */
    public function process(string $server, SupervisorProcess $process): string
    {
        if($this->ui !== null)
        {
            return $this->ui->process($server, $process);
        }

        return jaxon()->view()->render('lagdo::supervisor::views::process', [
            'server' => $server,
            'process' => $process,
            'rqProcess' => rq(Process::class),
        ]) . '';
    }
}
