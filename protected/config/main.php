<?php
require_once dirname(__FILE__).'/define.php';
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
            'user' => 'todo-desk',
            'password' => 'Mh9aqBTQXeIO70Cy',
            'database' => 'todo-desk'
        ],
    ],
    CORE_CONFIG => [
        'url_nesting' => 4,
    ]
];
