<?php

require_once __DIR__ . '/../LogLevel.php';
require_once __DIR__ . '/LoggerInterface.php';

class ConsoleLogger extends LoggerInterface
{
    public function __construct(LogLevel $minLogLevel)
    {
        parent::__construct('Console', $minLogLevel);
    }

    protected function logAction(string $message, LogLevel $logLevel, ?array $data = null)
    {
        echo $this->buildLogMessage($message, $logLevel, $data) . PHP_EOL;
    }
}
