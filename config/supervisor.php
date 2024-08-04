<?php

use Lagdo\UiBuilder\Jaxon\Builder as JaxonBuilder;
use Lagdo\Supervisor\App\Package;
use Lagdo\Supervisor\App\Ui\UiBuilder;
use Lagdo\Supervisor\App\Ui\UiBuilderInterface;
use Lagdo\Supervisor\App\Ui\UiBuilderProxy;
use Lagdo\Supervisor\Client;

return [
    'metadata' => 'annotations',
    'directories' => [
        __DIR__ . '/../app/Ajax/Web' => [
            'namespace' => 'Lagdo\\Supervisor\\App\\Ajax\\Web',
            'autoload' => false,
        ],
    ],
    'views' => [
        'lagdo::supervisor::views' => [
            'directory' => __DIR__ . '/../templates/views',
            'extension' => '.latte',
            'renderer' => 'latte',
        ],
        'lagdo::supervisor::codes' => [
            'directory' => __DIR__ . '/../templates/codes',
            'extension' => '',
            'renderer' => 'jaxon',
        ],
    ],
    'container' => [
        'alias' => [
            UiBuilderInterface::class => UiBuilderProxy::class,
        ],
        'set' => [
            UiBuilder::class => function() {
                return JaxonBuilder::isDefined() ? new UiBuilder() : null;
            },
            UiBuilderProxy::class => function($di) {
                return new UiBuilderProxy($di->g(UiBuilder::class));
            },
            Client::class => function($di) {
                return new Client($di->g(Package::class));
            },
        ],
    ],
];
