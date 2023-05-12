<?php

require_once __DIR__ . '/../LogLevel.php';
require_once __DIR__ . '/LoggerInterface.php';

class ConsoleLogger extends LoggerInterface
{
    public function __construct($minLogLevel)
    {
        parent::__construct('Console', $minLogLevel);
    }

    protected function logAction($message, $logLevel, $data = null)
    {
        echo $this->buildLogMessage($message, $logLevel, $data) . PHP_EOL;
    }
}
