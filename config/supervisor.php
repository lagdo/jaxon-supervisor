<?php

return [
    'directories' => [
        __DIR__ . '/../ajax' => [
            'namespace' => 'Lagdo\\Supervisor\\Ajax',
            'autoload' => false,
        ],
    ],
    'views' => [
        'lagdo::supervisor' => [
            'directory' => __DIR__ . '/../templates',
            'extension' => '.latte',
            'renderer' => 'latte',
        ],
    ],
    'container' => [
        Lagdo\Supervisor\Client::class => function() {
            return new Lagdo\Supervisor\Client();
        },
    ],
];
