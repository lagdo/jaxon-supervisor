<?php

namespace Lagdo\Supervisor;

use fXmlRpc\Client as RpcClient;
use fXmlRpc\Transport\PsrTransport;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use Supervisor\Supervisor;
use Supervisor\Process;

use function array_keys;
use function array_map;
use function preg_replace;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function trim;

/**
 * Supervisor client
 */
class Client
{
    /**
     * @var Package
     */
    protected $package;

    /**
     * @var Supervisor
     */
    protected $connection;

    /**
     * @var string
     */
    protected $server;

    /**
     * @var array
     */
    protected $options;

    /**
     * The constructor
     *
     * @param Package $package    The Supervisor package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * Get the server names from the package configuration
     *
     * @return array
     */
    public function getServerNames(): array
    {
        return array_keys($this->package->getOption('servers', []));
    }

    /**
     * Slugify a string
     *
     * @param string $server The server name
     *
     * @return string
     */
    private function getServerId($server): string
    {
        return 'supervisor-host-' .
            strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $server), '-'));
    }

    /**
     * Get the div id of the HTML element showing the data from a Supervisor server
     *
     * @return array
     */
    public function getServerIds(): array
    {
        return array_map(fn($server) => $this->getServerId($server), $this->getServerNames());
    }

    /**
     * Create an instance of the Supervisor client
     *
     * @return void
     */
    private function createConnection()
    {
        // Create GuzzleHttp client
        // $httpClient = new HttpClient(['auth' => ['user', 'password']]);
        $httpClient = isset($this->options['auth']) ?
            new HttpClient(['auth' => $this->options['auth']]) : new HttpClient();
        // Pass the url (null) and the guzzle client to the XmlRpc Client
        $url = $this->options['url'] . ':' . $this->options['port'] . '/RPC2';
        $rpcClient = new RpcClient($url, new PsrTransport(new HttpFactory(), $httpClient));
        // Pass the client to the connector
        // See the full list of connectors bellow
       $this->connection = new Supervisor($rpcClient);
    }

    /**
     * Create an instance of the Supervisor client
     *
     * @return Supervisor
     */
    private function connection(): Supervisor
    {
        return $this->connection;
    }

    /**
     * Get a given server options
     *
     * @param string $server The server name
     *
     * @return bool
     */
    public function setCurrentServer(string $server): bool
    {
        $this->server = trim($server);
        $servers = $this->package->getOption('servers', []);
        if(!isset($servers[$this->server]))
        {
            $this->server = '';
            return false; // Unable to find the server in the config.
        }

        $this->options = $servers[$this->server];
        // Set the "wait" option default value
        if(!isset($this->options['wait']))
        {
            $this->options['wait'] = $servers['wait'] ?? true;
        }
        $this->createConnection();
        return true;
    }

    /**
     * Get the current server name
     *
     * @return string
     */
    public function getCurrentServerName(): string
    {
        return $this->server;
    }

    /**
     * Get the current server id
     *
     * @return string
     */
    public function getCurrentServerId(): string
    {
        return $this->getServerId($this->server);
    }

    /**
     * Get the Supervisor version on a given server
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->connection()->getSupervisorVersion();
    }

    /**
     * @param array $process
     *
     * @return Process
     */
    private function makeProcess(array $process): Process
    {
        // Add an id in process info
        $process['id'] = $process['group'] . ':' . $process['name'];
        // Add the uptime in process info
        $process['uptime'] = '';
        if(($pos = strpos($process['description'], 'uptime ')) !== false)
        {
            $process['uptime'] = substr($process['description'], $pos + strlen('uptime '));
        }
        return new Process($process);
    }

    /**
     * Get the processes on a Supervisor server
     *
     * @return array<string,Process>
     */
    public function getProcesses(): array
    {
        return array_map(function($processInfo) {
            return $this->makeProcess($processInfo);
        }, $this->connection()->getAllProcessInfo());
    }

    /**
     * Start all the processes on a Supervisor server
     *
     * @return void
     */
    public function startAllProcesses()
    {
        $this->connection()->startAllProcesses($this->options['wait']);
    }

    /**
     * Stop all the processes on a Supervisor server
     *
     * @return void
     */
    public function stopAllProcesses()
    {
        $this->connection()->stopAllProcesses($this->options['wait']);
    }

    /**
     * Restart all the processes on a Supervisor server
     *
     * @return void
     */
    public function restartAllProcesses()
    {
        $this->connection()->stopAllProcesses($this->options['wait']);
        $this->connection()->startAllProcesses($this->options['wait']);
    }

    /**
     * Get a process on a Supervisor server
     *
     * @param string $process       The process identifier
     *
     * @return Process|null
     */
    public function getProcess(string $process): ?Process
    {
        return $this->connection()->getProcess($process);
    }

    /**
     * Start a process on a Supervisor server
     *
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function startProcess($process)
    {
        $this->connection()->startProcess($process, $this->options['wait']);
    }

    /**
     * Stop a process on a Supervisor server
     *
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function stopProcess($process)
    {
        $this->connection()->stopProcess($process, $this->options['wait']);
    }

    /**
     * Restart a process on a Supervisor server
     *
     * @param string $process       The process identifier
     *
     * @return void
     */
    public function restartProcess($process)
    {
        $this->connection()->stopProcess($process, $this->options['wait']);
        $this->connection()->startProcess($process, $this->options['wait']);
    }
}
