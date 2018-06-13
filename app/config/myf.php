<?php

return [
    // 调试模式
    'debug' => true,

    // 路由配置
    'route' => [
        // 静态路由
        'static' => [
            '/mongo/insert' => ['Mongo', 'insert'],
            '/mongo/find' => ['Mongo', 'find'],
            '/mongo/update' => ['Mongo', 'update'],
            '/mongo/delete' => ['Mongo', 'delete'],
            '/mongo/bulk' => ['Mongo', 'bulk'],
        ],
        // pcre正则路由
        'regex' => [
        ],
    ],
];