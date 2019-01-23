<?php

$config = [
    'log' => [
        'level' => '1',
        'path' => dirname(__FILE__) . '/runtime/log/',
    ],
    'mail' => [
        'default' => [
            'title'     => '业务监控通知',
            //发送者
            'sender'    => 'monitor@163.com',
            //接收用户
            'email'     => ['demo@163.com']
        ],
        'is_smtp'   => false,
        'smtp' => [
            'username'  => '',
            'password'  => '',
            'host'      => '',
            'port'      => 587,
            'encryption'=> 'tls',
        ],
    ],
    'mysql' => [
        [
            'host' => '127.0.0.1',
            'port' => '3306',
            'username' => 'root',
            'password' => 'root',
            'database' => 'monitor',
            'table' => 'io',
        ],
    ],
    'supervisor' => [
        [
            'name' => '127.0.0.1',
            'log'  => '/tmp/supervisord.log',
        ],
    ],
];

return $config;