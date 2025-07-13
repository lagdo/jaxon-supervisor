<?php

use Lagdo\Supervisor\App\Ui\UiBuilder;
use Lagdo\Supervisor\App\Ui\UiBuilderInterface;
use Lagdo\Supervisor\App\Ui\UiBuilderProxy;
use Lagdo\Supervisor\Client;
use Lagdo\Supervisor\Package;
use Lagdo\UiBuilder\BuilderInterface;

return [
    'metadata' => 'annotations',
    'directories' => [
        [
            'path' => __DIR__ . '/../app/Ajax/Web',
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
            UiBuilder::class => function($di) {
                $builder = $di->g(BuilderInterface::class);
                return $builder === null ? null : new UiBuilder($builder);
            },
            UiBuilderProxy::class => fn($di) => new UiBuilderProxy($di->g(UiBuilder::class)),
            Client::class => fn($di) => new Client($di->g(Package::class)),
        ],
    ],
];
