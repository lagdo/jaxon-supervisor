<?php

namespace Lagdo\Supervisor\App\Ajax;

use Jaxon\App\Component as BaseComponent;
use Jaxon\Response\AjaxResponse;
use Lagdo\Supervisor\App\Ui\UiBuilderInterface;
use Lagdo\Supervisor\Client;
use Exception;

abstract class Component extends BaseComponent
{
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
     * @return AjaxResponse
     */
    protected function error(Exception $e, string $message): AjaxResponse
    {
        $this->logger()->error($e->getMessage());
        $this->response->dialog->error($message, 'Error');
        return $this->response;
    }
}
