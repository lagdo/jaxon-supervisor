<?php

namespace Lagdo\Supervisor\Ajax;

use Lagdo\Supervisor\Client;
use Jaxon\App\Component;

abstract class BaseComponent extends Component
{
    /**
     * The constructor
     *
     * @param Client $client The Supervisor client
     */
    public function __construct(protected Client $client)
    {}

    /**
     * Set the Supervisor server to be displayed.
     *
     * @param string $server
     *
     * @return bool
     */
    protected function setServer(string $server): bool
    {
        return $this->client->setCurrentServer($server);
    }
}
