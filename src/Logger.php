<?php
require_once __DIR__ . '/LogLevel.php';
require_once __DIR__ . '/loggers/LoggerInterface.php';

class Logger
{
    private $loggers;

    public function __construct($loggers = [])
    {
        $this->loggers = $loggers;
    }

    public function addLogger($logger)
    {
        $this->loggers[] = $logger;
    }

    public function debug($message, $data = null)
    {
        $this->log($message, LogLevel::DEBUG, $data);
    }

    public function info($message, $data = null)
    {
        $this->log($message, LogLevel::INFO, $data);
    }

    public function warning($message, $data = null)
    {
        $this->log($message, LogLevel::WARNING, $data);
    }

    public function error($message, $data = null)
    {
        $this->log($message, LogLevel::ERROR, $data);
    }

    protected function log($message, $logLevel, $data = null)
    {
        foreach ($this->loggers as $logger) {
            $logger->log($message, $logLevel, $data);
        }
    }
}
