<?php

namespace Lagdo\Supervisor\Ajax\Web;

use Jaxon\Response\AjaxResponse;
use Lagdo\Supervisor\Ajax\Component;

/**
 * Home component
 */
class Home extends Component
{
    /**
     * Get the HTML code of the component.
     *
     * @return string
     */
    public function html(): string
    {
        return $this->view()->render('lagdo::supervisor::views::bootstrap/home', [
            'rqServer' => $this->rq(Server::class),
            'servers' => $this->client->getServerIds(),
        ]);
    }

    /**
     * Refresh data from all the servers
     *
     * @param bool $enable
     *
     * @return AjaxResponse
     */
    public function refresh(bool $enable)
    {
        $this->render();

        foreach($this->client->getServerNames() as $server)
        {
            // Add a request for the server in the response
            $this->response->exec($this->rq(Server::class)->renderServer($server));
        }

        if($enable)
        {
            $this->response->js('jaxon.supervisor')->enableRefresh();
        }
        return $this->response;
    }
}
