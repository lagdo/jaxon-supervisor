<?php

namespace Lagdo\Supervisor;

use Jaxon\Plugin\AbstractPackage;
use Lagdo\Supervisor\Ajax\Home;

use function realpath;
use function Jaxon\rq;

/**
 * Supervisor package
 */
class Package extends AbstractPackage
{
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
        return $this->view()->render('lagdo::supervisor::codes::script.js', [
            'rqHome' => rq(Home::class),
        ]);
    }

    /**
     * Get the javascript code to execute after page load
     *
     * @return string
     */
    public function getReadyScript(): string
    {
        return rq(Home::class)->refresh();
    }

    /**
     * Get the HTML code of the package home page
     *
     * @return string
     */
    public function getHtml(): string
    {
        return '';
    }
}
