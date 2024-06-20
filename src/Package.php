<?php

namespace Lagdo\Supervisor;

use Jaxon\Plugin\AbstractPackage;
use Lagdo\Supervisor\Ajax\Web\Home;

use function realpath;
use function Jaxon\cl;
use function Jaxon\rq;

/**
 * Supervisor package
 */
class Package extends AbstractPackage
{
    /**
     * @inheritDoc
     */
    public static function config()
    {
        return realpath(__DIR__ . '/../config/supervisor.php');
    }

    /**
     * @inheritDoc
     */
    public function getCss(): string
    {
        return '
<style>
        ' . $this->view()->render('lagdo::supervisor::codes::style.css') . '
</style>
';
    }

    /**
     * @inheritDoc
     */
    public function getScript(): string
    {
        return $this->view()->render('lagdo::supervisor::codes::script.js', [
            'rqHome' => rq(Home::class),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getReadyScript(): string
    {
        return rq(Home::class)->refresh(true);
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        return $this->view()->render('lagdo::supervisor::views::bootstrap/wrapper', [
            'rqHome' => rq(Home::class),
            'clHome' => cl(Home::class),
        ]);
    }
}
