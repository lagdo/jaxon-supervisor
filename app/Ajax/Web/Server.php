<?php

namespace Lagdo\Supervisor\App\Ajax\Web;

use Lagdo\Supervisor\App\Ajax\Component;
use Exception;

/**
 * Jaxon component for a Supervisor server
 */
class Server extends Component
{
    /**
     * Set the Supervisor server to be displayed.
     *
     * @param string $server
     *
     * @return bool
     */
    protected function setServer(string $server): bool
    {
        if(!$this->connect($server))
        {
            return false;
        }

        $this->item($this->client->getCurrentServerItemId());
        return true;
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        try
        {
            $version = $this->client->getVersion();
            $processes = $this->client->getProcesses();
        }
        catch(Exception $e)
        {
            $this->logger()->error($e->getMessage());
            $version = 'Error';
            $processes = [];
        }

        return $this->ui->server($this->client->getCurrentServerId(),
            $this->client->getCurrentServerName(), $version, $processes);
    }

    /**
     * Render a given server
     *
     * @param string $server    The server name in the configuration
     *
     * @return void
     */
    public function renderServer(string $server): void
    {
        if($this->setServer($server))
        {
            $this->render();
        }
    }

    /**
     * Start all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return void
     */
    public function start(string $server): void
    {
        if(!$this->setServer($server))
        {
            return;
        }

        try
        {
            $this->client->startAllProcesses();
            $this->render();
        }
        catch(Exception $e)
        {
            $this->error($e, "Unable to start all processes on server $server");
        }
    }

    /**
     * Restart all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return void
     */
    public function restart(string $server): void
    {
        if(!$this->setServer($server))
        {
            return;
        }

        try
        {
            $this->client->restartAllProcesses();
            $this->render();
        }
        catch(Exception $e)
        {
            $this->error($e, "Unable to restart all processes on server $server");
        }
    }

    /**
     * Stop all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return void
     */
    public function stop(string $server): void
    {
        if(!$this->setServer($server))
        {
            return;
        }

        try
        {
            $this->client->stopAllProcesses();
            $this->render();
        }
        catch(Exception $e)
        {
            $this->error($e, "Unable to stop all processes on server $server");
        }
    }
}
