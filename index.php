<?php

use Monitor\Core\Core;

require __DIR__ . '/vendor/autoload.php';

$config = require 'config.php';

$app = new Core($config);
$app->run();
