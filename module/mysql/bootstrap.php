<?php
/**
 * module command
 * 
 * @var [type]
 */
$module = [
    'mysql.heartbeat' => [
        'command' => 'Monitor\module\mysql\heartbeat\HeartbeatCommand',
        'description' => 'mysql心跳检测'
    ],
];

return $module;