<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        /*'renderer' => [
            'template_path' => __DIR__ . '/templates/',
        ],*/

        'view' => [
            'template_path' => __DIR__ . '/../src/templates',
            'twig' => [
                'cache' => __DIR__ . '/../data/cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        'doctrine' => [
            'meta' => [
                'entity_path' => [
                    __DIR__ . '/../src/App/Example/Entity'
                ],
                'auto_generate_proxies' => true,
                'proxy_dir' => __DIR__ . '/../data/cache/proxies',
                'cache' => null,
            ],
            'connection' => [
                'driver'   => 'pdo_mysql',
                'host'     => '172.19.0.2',
                'dbname'   => 'billing',
                'user'     => 'billing',
                'password' => 'billing',
            ]
        ],

        // Monolog settings
        'logger' => [
            'name' => 'billing-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
