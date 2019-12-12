<?php

namespace Lagdo\Supervisor\Ajax;

use Lagdo\Supervisor\Package as SupervisorPackage;
use Lagdo\Supervisor\Client as SupervisorClient;

use Jaxon\CallableClass;
use Exception;

/**
 * Supervisor Ajax client
 */
class Client extends CallableClass
{
    /**
     * The Jaxon Supervisor package
     *
     * @var SupervisorPackage
     */
    protected $package;

    /**
     * The Supervisor client
     *
     * @var SupervisorClient
     */
    protected $client;

    /**
     * The constructor
     *
     * @param SupervisorPackage $package    The Supervisor package
     * @param SupervisorClient  $client     The Supervisor client
     */
    public function __construct(SupervisorPackage $package, SupervisorClient $client)
    {
        $this->package = $package;
        $this->client = $client;
    }

    /**
     * Refresh data from all the servers
     *
     * @return \Jaxon\Response\Response
     */
    public function refreshAll()
    {
        $options = $this->package->getOptions();
        $servers = \array_keys($options['servers']);
        $actions = [];
        foreach($servers as $server)
        {
            // Add a request for the server in the response
            $this->response->script($this->rq()->refresh($server));
        }

        // Refresh the statuses after a given interval
        $this->response->script('jaxon.supervisor.enableRefresh()');
        return $this->response;
    }

    /**
     * Refresh data from a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return \Jaxon\Response\Response
     */
    public function refresh($server)
    {
        $server = trim($server);
        // The id of the div element for the Supervisor instance
        $divId = $this->package->divId($server);

        $options = $this->package->getOptions();
        if(!\key_exists('servers', $options) || !\key_exists($server, $options['servers']))
        {
            $this->response->dialog->error("No config entry for server with name $server", 'Error');
            $this->response->html($divId, "No config entry for server with name $server");
            return $this->response;
        }

        $serverOptions = $options['servers'][$server];
        try
        {
            $version = $this->client->getVersion($serverOptions);
            $processes = $this->client->getProcesses($serverOptions);
        }
        catch(Exception $e)
        {
            $version = 'Error';
            $processes = [];
        }
        $template = 'lagdo::supervisor::views/bootstrap/server';
        $content = $this->view()->render($template, compact('server', 'version', 'processes'));

        // Insert the server HTML content in the page
        $this->response->html($divId, $content);

        // Set handlers on server buttons
        $this->jq('.jaxon-supervisor-server>a.btn-start', "#$divId")
            ->click($this->rq()->startAll(jq()->parent()->attr('data-s')));
        $this->jq('.jaxon-supervisor-server>a.btn-stop', "#$divId")
            ->click($this->rq()->stopAll(jq()->parent()->attr('data-s')));
        $this->jq('.jaxon-supervisor-server>a.btn-restart', "#$divId")
            ->click($this->rq()->restartAll(jq()->parent()->attr('data-s')));

        // Set handlers on process buttons
        $this->jq('.jaxon-supervisor-process>a.btn-start', "#$divId")
            ->click($this->rq()->start(jq()->parent()->attr('data-s'), jq()->parent()->attr('data-p')));
        $this->jq('.jaxon-supervisor-process>a.btn-stop', "#$divId")
            ->click($this->rq()->stop(jq()->parent()->attr('data-s'), jq()->parent()->attr('data-p')));
        $this->jq('.jaxon-supervisor-process>a.btn-restart', "#$divId")
            ->click($this->rq()->restart(jq()->parent()->attr('data-s'), jq()->parent()->attr('data-p')));

        return $this->response;
    }

    /**
     * Start all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return \Jaxon\Response\Response
     */
    public function startAll($server)
    {
        $server = trim($server);
        $options = $this->package->getOptions();
        if(!\key_exists('servers', $options) || !\key_exists($server, $options['servers']))
        {
            $this->response->dialog->error("No config entry for server with name $server", 'Error');
            $this->response->html($divId, "No config entry for server with name $server");
            return $this->response;
        }

        $serverOptions = $options['servers'][$server];
        try
        {
            $this->client->startAllProcesses($serverOptions);
            return $this->refresh($server);
        }
        catch(Exception $e)
        {
            $this->response->dialog->error("Unable to start all processes on server $server", 'Error');
            return $this->response;
        }
    }

    /**
     * Restart all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return \Jaxon\Response\Response
     */
    public function restartAll($server)
    {
        $server = trim($server);
        $options = $this->package->getOptions();
        if(!\key_exists('servers', $options) || !\key_exists($server, $options['servers']))
        {
            $this->response->dialog->error("No config entry for server with name $server", 'Error');
            $this->response->html($divId, "No config entry for server with name $server");
            return $this->response;
        }

        $serverOptions = $options['servers'][$server];
        try
        {
            $this->client->restartAllProcesses($serverOptions);
            return $this->refresh($server);
        }
        catch(Exception $e)
        {
            $this->response->dialog->error("Unable to restart all processes on server $server", 'Error');
            return $this->response;
        }
    }

    /**
     * Stop all the processes on a server
     *
     * @param string $server    The server name in the configuration
     *
     * @return \Jaxon\Response\Response
     */
    public function stopAll($server)
    {
        $server = trim($server);
        $options = $this->package->getOptions();
        if(!\key_exists('servers', $options) || !\key_exists($server, $options['servers']))
        {
            $this->response->dialog->error("No config entry for server with name $server", 'Error');
            $this->response->html($divId, "No config entry for server with name $server");
            return $this->response;
        }

        $serverOptions = $options['servers'][$server];
        try
        {
            $this->client->stopAllProcesses($serverOptions);
            return $this->refresh($server);
        }
        catch(Exception $e)
        {
            $this->response->dialog->error("Unable to stop all processes on server $server", 'Error');
            return $this->response;
        }
    }

    /**
     * Start a process on a server
     *
     * @param string $server    The server name in the configuration
     * @param string $process       The process identifier
     *
     * @return \Jaxon\Response\Response
     */
    public function start($server, $process)
    {
        $server = trim($server);
        $process = trim($process);
        $options = $this->package->getOptions();
        if(!\key_exists('servers', $options) || !\key_exists($server, $options['servers']))
        {
            $this->response->dialog->error("No config entry for server with name $server", 'Error');
            $this->response->html($divId, "No config entry for server with name $server");
            return $this->response;
        }

        $serverOptions = $options['servers'][$server];
        try
        {
            $this->client->startProcess($serverOptions, $process);
            return $this->refresh($server);
        }
        catch(Exception $e)
        {
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
     * @return \Jaxon\Response\Response
     */
    public function restart($server, $process)
    {
        $server = trim($server);
        $process = trim($process);
        $options = $this->package->getOptions();
        if(!\key_exists('servers', $options) || !\key_exists($server, $options['servers']))
        {
            $this->response->dialog->error("No config entry for server with name $server", 'Error');
            $this->response->html($divId, "No config entry for server with name $server");
            return $this->response;
        }

        $serverOptions = $options['servers'][$server];
        try
        {
            $this->client->restartProcess($serverOptions, $process);
            return $this->refresh($server);
        }
        catch(Exception $e)
        {
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
     * @return \Jaxon\Response\Response
     */
    public function stop($server, $process)
    {
        $server = trim($server);
        $process = trim($process);
        $options = $this->package->getOptions();
        if(!\key_exists('servers', $options) || !\key_exists($server, $options['servers']))
        {
            $this->response->dialog->error("No config entry for server with name $server", 'Error');
            $this->response->html($divId, "No config entry for server with name $server");
            return $this->response;
        }

        $serverOptions = $options['servers'][$server];
        try
        {
            $this->client->stopProcess($serverOptions, $process);
            return $this->refresh($server);
        }
        catch(Exception $e)
        {
            $this->response->dialog->error("Unable to stop process $process on server $server", 'Error');
            return $this->response;
        }
    }
}
