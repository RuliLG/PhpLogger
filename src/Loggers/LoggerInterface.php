<?php

require_once __DIR__ . '/../LogLevel.php';

abstract class LoggerInterface
{
    public string $name;

    public LogLevel $minLogLevel;

    public function __construct(string $name, LogLevel $minLogLevel)
    {
        $this->name = $name;
        $this->minLogLevel = $minLogLevel;
    }

    protected function enabledFor(LogLevel $logLevel): bool
    {
        return $this->minLogLevel->value <= $logLevel->value;
    }

    protected function buildLogMessage($message, $logLevel, $data = null)
    {
        $logMessage = $this->name . ": " . date("Y-m-d\TH:i:s\Z") . " [" . $logLevel->name . "] " . $message;
        if ($data !== null) {
            $logMessage .= " " . json_encode($data);
        }

        return $logMessage;
    }

    abstract protected function logAction($message, $logLevel, $data = null);

    public function log($message, $logLevel, $data = null)
    {
        if (!$this->enabledFor($logLevel)) {
            return;
        }

        $this->logAction($message, $logLevel, $data);
    }
}
