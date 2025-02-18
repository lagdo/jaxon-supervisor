<?php

namespace Lagdo\Supervisor\App\Ajax\Web;

use Lagdo\Supervisor\App\Ajax\Component;
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
        return $this->ui->process($this->client->getCurrentServerId(), $this->process);
    }

    /**
     * Set the Supervisor process and render the component.
     *
     * @param string $process
     *
     * @return void
     */
    private function renderProcess(string $process): void
    {
        $this->setProcess($this->client->getProcess($process));
        $this->item($this->getItemId())->render();
    }

    /**
     * Start a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function start(string $server, string $process): void
    {
        if(!$this->connect($server))
        {
            return;
        }

        $process = trim($process);
        try
        {
            $this->client->startProcess($process);
            $this->renderProcess($process);
        }
        catch(Exception $e)
        {
            $this->error($e, "Unable to start process $process on server $server");
        }
    }

    /**
     * Restart a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function restart(string $server, string $process): void
    {
        if(!$this->connect($server))
        {
            return;
        }

        $process = trim($process);
        try
        {
            $this->client->restartProcess($process);
            $this->renderProcess($process);
        }
        catch(Exception $e)
        {
            $this->error($e, "Unable to restart process $process on server $server");
        }
    }

    /**
     * Stop a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function stop(string $server, string $process): void
    {
        if(!$this->connect($server))
        {
            return;
        }

        $process = trim($process);
        try
        {
            $this->client->stopProcess($process);
            $this->renderProcess($process);
        }
        catch(Exception $e)
        {
            $this->error($e, "Unable to stop process $process on server $server");
        }
    }
}
