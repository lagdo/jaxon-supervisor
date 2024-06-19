<?php

namespace Lagdo\Supervisor\Ajax;

use Supervisor\Process as SupervisorProcess;
use Exception;

/**
 * Jaxon component for a Supervisor process
 */
class Process extends BaseComponent
{
    /**
     * @var SupervisorProcess
     */
    private $process;

    /**
     * Set the Supervisor process to be displayed.
     *
     * @exclude
     * @param SupervisorProcess $process
     *
     * @return self
     */
    public function setProcess(SupervisorProcess $process): self
    {
        $this->process = $process;
        $this->response->item($this->client->getCurrentServerId() . '-' . $process['id']);
        return $this;
    }

    /**
     * Get the HTML code of the component.
     *
     * @return string
     */
    public function html(): string
    {
        return $this->view()->render('lagdo::supervisor::views::bootstrap/process', [
            'server' => $this->client->getCurrentServerName(),
            'process' => $this->process,
        ]);
    }

    /**
     * Start a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return \Jaxon\Response\AjaxResponse
     */
    public function start($server, $process)
    {
        if(!$this->setServer($server))
        {
            return $this->response;
        }

        try
        {
            $this->client->startProcess($process);
            return $this->setProcess($this->client->getProcess($process))->render();
        }
        catch(Exception $e)
        {
            $this->logger()->error($e->getMessage());
            $this->response->dialog->error("Unable to start process $process on server $server", 'Error');
            return $this->response;
        }
    }

    /**
     * Restart a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return \Jaxon\Response\AjaxResponse
     */
    public function restart($server, $process)
    {
        if(!$this->setServer($server))
        {
            return $this->response;
        }

        try
        {
            $this->client->restartProcess($process);
            return $this->setProcess($this->client->getProcess($process))->render();
        }
        catch(Exception $e)
        {
            $this->logger()->error($e->getMessage());
            $this->response->dialog->error("Unable to restart process $process on server $server", 'Error');
            return $this->response;
        }
    }

    /**
     * Stop a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return \Jaxon\Response\AjaxResponse
     */
    public function stop($server, $process)
    {
        if(!$this->setServer($server))
        {
            return $this->response;
        }

        try
        {
            $this->client->stopProcess($process);
            return $this->setProcess($this->client->getProcess($process))->render();
        }
        catch(Exception $e)
        {
            $this->logger()->error($e->getMessage());
            $this->response->dialog->error("Unable to stop process $process on server $server", 'Error');
            return $this->response;
        }
    }
}
