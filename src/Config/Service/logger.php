<?php

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

$logger = new Logger('app');
if (isset($this->config['log']['level'])) {
    $level = $this->config['log']['level'];
} else {
    $level = Logger::ERROR;
}
$logDir = $this->config['log']['path'];
$logFile = $logDir . '/log.txt';
$handler = new RotatingFileHandler(
        $logFile, 0, $level, true, 0775
);
$logger->pushHandler($handler);

return $logger;
