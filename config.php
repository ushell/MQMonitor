<?php

$config = [
	'log' => [
		'level' => '1',
		'path' => '/tmp',
	],
	'mail' => [
		'default' => [
			'username' => 'monitor',
			'email' => 'admin@ushell.me',
			'title' => '业务监控通知'
		],
	],
];


return $config;