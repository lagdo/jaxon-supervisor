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
        if(!parent::setServer($server))
        {
            return false;
        }
        $this->response->item($this->client->getCurrentServerId());
        return true;
    }

    /**
     * Get the HTML code of the component.
     *
     * @return string
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

        return $this->view()->render('lagdo::supervisor::views::bootstrap/server', [
            'server' => $this->client->getCurrentServerName(),
            'serverId' => $this->client->getCurrentServerId(),
            'version' => $version,
            'processes' => $processes,
            'rqServer' => $this->rq(),
            'rqProcess' => $this->rq(Process::class),
            'clProcess' => $this->cl(Process::class),
        ]) . '';
    }

    /**
     * Render a given server
     *
     * @param string $server    The server name in the configuration
     *
     * @return AjaxResponse
     */
    public function renderServer($server)
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
    public function start($server)
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
            $this->logger()->error($e->getMessage());
            $this->response->dialog->error("Unable to start all processes on server $server", 'Error');
            return $this->response;
        }
    }

    /**
     * Restart all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return AjaxResponse
     */
    public function restart($server)
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
            $this->logger()->error($e->getMessage());
            $this->response->dialog->error("Unable to restart all processes on server $server", 'Error');
            return $this->response;
        }
    }

    /**
     * Stop all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return AjaxResponse
     */
    public function stop($server)
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
            $this->logger()->error($e->getMessage());
            $this->response->dialog->error("Unable to stop all processes on server $server", 'Error');
            return $this->response;
        }
    }
}
