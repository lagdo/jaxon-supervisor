<?php

namespace Lagdo\Supervisor\App\Ajax;

use Jaxon\App\Component as BaseComponent;
use Jaxon\App\Dialog\DialogTrait;
use Lagdo\Supervisor\App\Ui\UiBuilderInterface;
use Lagdo\Supervisor\Client;
use Exception;

abstract class Component extends BaseComponent
{
    use DialogTrait;

    /**
     * The constructor
     *
     * @param Client $client The Supervisor client
     * @param UiBuilderInterface $ui The UI builder
     */
    public function __construct(protected Client $client, protected UiBuilderInterface $ui)
    {}

    /**
     * Set the Supervisor server to be displayed.
     *
     * @param string $server
     *
     * @return bool
     */
    protected function connect(string $server): bool
    {
        return $this->client->connect($server);
    }

    /**
     * @param Exception $e
     * @param string $message
     *
     * @return void
     */
    protected function error(Exception $e, string $message): void
    {
        $this->logger()->error($e->getMessage());
        $this->alert()->title('Error')->error($message);
    }
}
