<?php

return [
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
        'auto' => [
            Lagdo\Supervisor\App\Ui\UiBuilder::class,
        ],
        'alias' => [
            Lagdo\Supervisor\App\Ui\UiBuilderInterface::class => Lagdo\Supervisor\App\Ui\UiBuilderProxy::class,
        ],
        'set' => [
            Lagdo\Supervisor\App\Ui\UiBuilderProxy::class => function($di) {
                $uiBuilder = $di->h(Lagdo\UiBuilder\BuilderInterface::class) ?
                    $di->g(Lagdo\Supervisor\App\Ui\UiBuilder::class) : null;
                return new Lagdo\Supervisor\App\Ui\UiBuilderProxy($uiBuilder);
            },
            Lagdo\Supervisor\Client::class => function($di) {
                return new Lagdo\Supervisor\Client($di->g(Lagdo\Supervisor\App\Package::class));
            },
        ],
    ],
];
