<?php

namespace Lagdo\Supervisor;

use Supervisor\Supervisor;
use Supervisor\Process;
use Supervisor\Connector\XmlRpc;
use fXmlRpc\Client as RpcClient;
use fXmlRpc\Transport\HttpAdapterTransport;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;
use GuzzleHttp\Client as HttpClient;

/**
 * Supervisor client
 */
class Client
{
    /**
     * The class constructor
     */
    public function __construct()
    {
        // Create GuzzleHttp client
        // $httpClient = new HttpClient(['auth' => ['user', 'password']]);
        $httpClient = new HttpClient();
        // Pass the url (null) and the guzzle client to the XmlRpc Client
        $this->rpcClient = new RpcClient(null,
            new HttpAdapterTransport(new Guzzle6HttpAdapter($httpClient))
        );
        // Pass the client to the connector
        // See the full list of connectors bellow
        $connector = new XmlRpc($this->rpcClient);
        $this->supervisor = new Supervisor($connector);
    }

    /**
     * Get the Supervisor version on a given server
     *
     * @param array $serverOptions  The server options in the configuration
     *
     * @return string
     */
    public function getVersion(array $serverOptions)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        return $this->supervisor->getSupervisorVersion();
    }

    /**
     * Get the processes on a Supervisor server
     *
     * @param array $serverOptions  The server options in the configuration
     *
     * @return array<string,Process>
     */
    public function getProcesses(array $serverOptions)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        $processes = $this->supervisor->getAllProcessInfo();
        foreach($processes as $key => $processInfo)
        {
            // Add an id in process info
            $processInfo['id'] = $processInfo['group'] . ':' . $processInfo['name'];
            // Add the uptime in process info
            $processInfo['uptime'] = '';
            if(($pos = \strpos($processInfo['description'], 'uptime ')) !== false)
            {
                $processInfo['uptime'] = \substr($processInfo['description'], $pos + strlen('uptime '));
            }

            $processes[$key] = new Process($processInfo);
        }
        return $processes;
    }

    /**
     * Start all the processes on a Supervisor server
     *
     * @param array $serverOptions  The server options in the configuration
     *
     * @return void
     */
    public function startAllProcesses(array $serverOptions)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        $this->supervisor->startAllProcesses();
    }

    /**
     * Stop all the processes on a Supervisor server
     *
     * @param array $serverOptions  The server options in the configuration
     *
     * @return void
     */
    public function stopAllProcesses(array $serverOptions)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        $this->supervisor->stopAllProcesses();
    }

    /**
     * Restart all the processes on a Supervisor server
     *
     * @param array $serverOptions  The server options in the configuration
     *
     * @return void
     */
    public function restartAllProcesses(array $serverOptions)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        $this->supervisor->stopAllProcesses();
        $this->supervisor->startAllProcesses();
    }

    /**
     * Start a process on a Supervisor server
     *
     * @param array $serverOptions  The server options in the configuration
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function startProcess(array $serverOptions, $process)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        $this->supervisor->startProcess($process);
    }

    /**
     * Stop a process on a Supervisor server
     *
     * @param array $serverOptions  The server options in the configuration
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function stopProcess(array $serverOptions, $process)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        $this->supervisor->stopProcess($process);
    }

    /**
     * Restart a process on a Supervisor server
     *
     * @param array $serverOptions  The server options in the configuration
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function restartProcess(array $serverOptions, $process)
    {
        $host = $serverOptions['url'] . ':' . $serverOptions['port'] . '/RPC2';
        $this->rpcClient->setUri($host);
        $this->supervisor->stopProcess($process);
        $this->supervisor->startProcess($process);
    }
}
