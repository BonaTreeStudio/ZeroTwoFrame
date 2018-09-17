<?php
require_once dirname(__FILE__) . '/define.php';
return [
    LOADER_PATH_CONFIG => [
        'components',
        'controllers',
        'extends',
        'models',
    ],
    DATABASE_CONFIG => [
        DEFAULT_DATABASE_CONFIG => [
            'host' => 'localhost',
            'user' => 'user',
            'password' => '*****',
            'database' => 'database'
        ],
    ],
    CORE_CONFIG => [
        'url_nesting' => 2,
    ]
];
