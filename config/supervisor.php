<?php

return [
    'directories' => [
        __DIR__ . '/../ajax/Web' => [
            'namespace' => 'Lagdo\\Supervisor\\Ajax\\Web',
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
        'set' => [
            Lagdo\Supervisor\Client::class => function($di) {
                return new Lagdo\Supervisor\Client($di->g(Lagdo\Supervisor\Package::class));
            },
        ],
    ],
];
