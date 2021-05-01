<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'doctrine' => [
            'cache_dir' => __DIR__ . '../var/doctrine',
            'meta' => [
                'entity_path' => [
                    __DIR__ . '/Doctrine/Example/Entity'
                ],
                'auto_generate_proxies' => true,
                'proxy_dir' => __DIR__ . '/Doctrine/Example/Entity',
                'cache' => null,
                'simple_annotation_reader' => false,
            ],
            'auto_generate_proxies' => true,
            // if true, metadata caching is forcefully disabled
            'dev_mode' => true,
            'connection' => [
                'driver'   => 'pdo_pgsql',
                'host'     => 'localhost',
                'dbname'   => 'billing',
                'user'     => 'billing',
                'password' => 'billing',
            ]
        ],
    ],
];
