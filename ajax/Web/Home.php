<?php

namespace Lagdo\Supervisor\Ajax\Web;

use Jaxon\Response\AjaxResponse;
use Lagdo\Supervisor\Ajax\Component;

/**
 * Jaxon component for the Supervisor server list
 */
class Home extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->view()->render('lagdo::supervisor::views::bootstrap/servers', [
            'rqServer' => $this->rq(Server::class),
            'serverItemIds' => $this->client->getServerItemIds(),
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

        foreach($this->client->getServerIds() as $server)
        {
            // Add a request for the server in the response
            $this->response->exec($this->rq(Server::class)->renderServer($server));
        }

        $enable && $this->response->js('jaxon.supervisor')->enableRefresh();
        return $this->response;
    }
}
