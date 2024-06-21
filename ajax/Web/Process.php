<?php

namespace Lagdo\Supervisor\Ajax\Web;

use Jaxon\Response\AjaxResponse;
use Lagdo\Supervisor\Ajax\Component;
use Supervisor\Process as SupervisorProcess;
use Exception;

use function trim;

/**
 * Jaxon component for a Supervisor process
 */
class Process extends Component
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
        return $this;
    }

    /**
     * Get the component item id for a process on the current server
     *
     * @exclude
     *
     * @return string
     */
    public function getItemId(): string
    {
        return $this->client->getProcessItemId($this->process);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->view()->render('lagdo::supervisor::views::bootstrap/process', [
            'server' => $this->client->getCurrentServerId(),
            'process' => $this->process,
            'rqProcess' => $this->rq(),
        ]) . '';
    }

    /**
     * Set the Supervisor process and render the component.
     *
     * @param string $process
     *
     * @return AjaxResponse
     */
    private function renderProcess(string $process): AjaxResponse
    {
        $this->setProcess($this->client->getProcess($process));
        $this->response->item($this->getItemId());
        return $this->render();
    }

    /**
     * Start a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return AjaxResponse
     */
    public function start(string $server, string $process)
    {
        if(!$this->connect($server))
        {
            return $this->response;
        }

        $process = trim($process);
        try
        {
            $this->client->startProcess($process);
            return $this->renderProcess($process);
        }
        catch(Exception $e)
        {
            return $this->error($e, "Unable to start process $process on server $server");
        }
    }

    /**
     * Restart a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return AjaxResponse
     */
    public function restart(string $server, string $process)
    {
        if(!$this->connect($server))
        {
            return $this->response;
        }

        $process = trim($process);
        try
        {
            $this->client->restartProcess($process);
            return $this->renderProcess($process);
        }
        catch(Exception $e)
        {
            return $this->error($e, "Unable to restart process $process on server $server");
        }
    }

    /**
     * Stop a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return AjaxResponse
     */
    public function stop(string $server, string $process)
    {
        if(!$this->connect($server))
        {
            return $this->response;
        }

        $process = trim($process);
        try
        {
            $this->client->stopProcess($process);
            return $this->renderProcess($process);
        }
        catch(Exception $e)
        {
            return $this->error($e, "Unable to stop process $process on server $server");
        }
    }
}
