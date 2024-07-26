<?php

namespace Lagdo\Supervisor\Ajax\Web;

use Jaxon\Response\AjaxResponse;
use Lagdo\Supervisor\Ajax\Component;
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
        $this->response->item($this->client->getCurrentServerItemId());
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
     * @return AjaxResponse
     */
    public function renderServer(string $server)
    {
        if(!$this->setServer($server))
        {
            return $this->response;
        }

        return $this->render();
    }

    /**
     * Start all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return AjaxResponse
     */
    public function start(string $server)
    {
        if(!$this->setServer($server))
        {
            return $this->response;
        }

        try
        {
            $this->client->startAllProcesses();
            return $this->render();
        }
        catch(Exception $e)
        {
            return $this->error($e, "Unable to start all processes on server $server");
        }
    }

    /**
     * Restart all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return AjaxResponse
     */
    public function restart(string $server)
    {
        if(!$this->setServer($server))
        {
            return $this->response;
        }

        try
        {
            $this->client->restartAllProcesses();
            return $this->render();
        }
        catch(Exception $e)
        {
            return $this->error($e, "Unable to restart all processes on server $server");
        }
    }

    /**
     * Stop all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return AjaxResponse
     */
    public function stop(string $server)
    {
        if(!$this->setServer($server))
        {
            return $this->response;
        }

        try
        {
            $this->client->stopAllProcesses();
            return $this->render();
        }
        catch(Exception $e)
        {
            return $this->error($e, "Unable to stop all processes on server $server");
        }
    }
}
