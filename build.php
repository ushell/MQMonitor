<?php
echo "Build...".PHP_EOL;

$phar = new Phar('monitor.phar');
$phar->buildFromDirectory(__DIR__ . '/src');
$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();
$phar->setStub($phar->createDefaultStub('index.php'));

echo "Build monitor.phar ok".PHP_EOL;
