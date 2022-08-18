<?php

namespace Lagdo\Supervisor;

use Jaxon\Plugin\Package as JaxonPackage;
use Lagdo\Supervisor\Ajax\Client as AjaxClient;

use function strtolower;
use function trim;
use function preg_replace;
use function realpath;
use function array_keys;
use function compact;
use function Jaxon\jaxon;

/**
 * Supervisor package
 */
class Package extends JaxonPackage
{
    /**
     * Slugify a string
     *
     * @param string $string    The string to be slugified
     *
     * @return string
     */
    public function slugify($string): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }

    /**
     * Get the div id of the HTML element showing the data from a Supervisor server
     *
     * @param string $server    The server name in the configuration
     *
     * @return string
     */
    public function divId($server): string
    {
        return 'supervisor-host-' . $this->slugify($server);
    }

    /**
     * Get the path to the config file
     *
     * @return string
     */
    public static function config()
    {
        return realpath(__DIR__ . '/../config/supervisor.php');
    }

    /**
     * Get the HTML tags to include javascript code and files into the page
     *
     * @return string
     */
    public function getScript(): string
    {
        return $this->view()->render('lagdo::supervisor::codes/script')
            ->with('refreshCall', jaxon()->request(AjaxClient::class)->refreshAll());
    }

    /**
     * Get the javascript code to execute after page load
     *
     * @return string
     */
    public function getReadyScript(): string
    {
        return jaxon()->request(AjaxClient::class)->refreshAll();
    }

    /**
     * Get the HTML code of the package home page
     *
     * @return string
     */
    public function getHtml(): string
    {
        // Add an HTML container block for each server in the config file
        $servers = array_keys($this->getOption('servers', []));
        $divIds = [];
        foreach($servers as $server)
        {
            $divIds[] = $this->divId($server);
        }
        return $this->view()->render('lagdo::supervisor::views/bootstrap/home', compact('divIds'));
    }
}
