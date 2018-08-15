<?php

$config = [
	'log' => [
		'level' => '1',
		'path' => '/tmp',
	],
	'mail' => [
		'default' => [
			'username' => 'monitor',
			'email' => 'admin@example.com',
			'title' => '业务监控通知'
		],
	],
];


return $config;
