<?php

$config = [
	'log' => [
		'level' => '1',
		'path' => dirname(__FILE__) . '/runtime/log/',
	],
	'mail' => [
		'default' => [
			'username' => 'monitor',
            'sender' => 'monitor@example.com',
			'email' => 'admin@example.com',
			'title' => '业务监控通知'
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
];



return $config;
