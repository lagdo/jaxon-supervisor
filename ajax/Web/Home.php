<?php

namespace Lagdo\Supervisor\Ajax\Web;

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
            'rqHome' => $this->rq(),
            'rqServer' => $this->rq(Server::class),
            'servers' => $this->client->getServerIds(),
        ]);
    }

    /**
     * Refresh data from all the servers
     *
     * @return \Jaxon\Response\AjaxResponse
     */
    public function refresh()
    {
        $this->render();

        foreach($this->client->getServerNames() as $server)
        {
            // Add a request for the server in the response
            $this->response->exec($this->rq(Server::class)->renderServer($server));
        }

        // Refresh the statuses after a given interval
        $this->response->call('jaxon.supervisor.enableRefresh');
        return $this->response;
    }
}
