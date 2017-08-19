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
            'password' => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'cldb_',
        ],

        's3' => [
            'bucket' => 'coloborativelearning',
            'key'   => 'AKIAJRFPB2J7GWHF2SXQ',
            'secret' => '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'
        ]
    ],
];
