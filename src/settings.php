<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',

        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'CLDB',
            'username' => 'root',
            'password' => 'XYZ456',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        'roles' => [
            'admin' => '100',
            'organization' => '101',
            'staff' => '102',
            'student' => '103',
            'parent' => '104',

        ]
    ],
];
