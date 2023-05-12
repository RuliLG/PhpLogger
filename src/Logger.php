<?php
require_once __DIR__ . '/LogLevel.php';
require_once __DIR__ . '/loggers/LoggerInterface.php';

class Logger
{
    private $loggers;

    public function __construct(array $loggers = [])
    {
        $this->loggers = $loggers;
    }

    public function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    public function debug(string $message, ?array $data = null)
    {
        $this->log($message, LogLevel::DEBUG, $data);
    }

    public function info(string $message, ?array $data = null)
    {
        $this->log($message, LogLevel::INFO, $data);
    }

    public function warning(string $message, ?array $data = null)
    {
        $this->log($message, LogLevel::WARNING, $data);
    }

    public function error(string $message, ?array $data = null)
    {
        $this->log($message, LogLevel::ERROR, $data);
    }

    protected function log(string $message, LogLevel $logLevel, ?array $data = null)
    {
        foreach ($this->loggers as $logger) {
            $logger->log($message, $logLevel, $data);
        }
    }
}
