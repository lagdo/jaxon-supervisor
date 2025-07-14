<?php

namespace Lagdo\Supervisor\App\Ajax\Web;

use Lagdo\Supervisor\App\Ajax\Component;

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
        return $this->ui->servers($this->client->getServerItemIds());
    }

    /**
     * Refresh data from all the servers
     *
     * @param bool $enable
     *
     * @return void
     */
    public function refresh(bool $enable): void
    {
        $this->render();

        foreach($this->client->getServerIds() as $server)
        {
            // Add a request for the server in the response
            $this->response->exec($this->rq(Server::class)->renderServer($server));
        }

        $enable && $this->response->jo('jaxon.supervisor')->enableRefresh();
    }
}
