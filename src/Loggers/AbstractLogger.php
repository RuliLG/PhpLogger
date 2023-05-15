<?php

require_once __DIR__ . '/../LogLevel.php';

abstract class AbstractLogger
{
    public function __construct(
        public readonly string $name,
        public readonly LogLevel $minLogLevel
    ) {
        //
    }

    protected function enabledFor(LogLevel $logLevel): bool
    {
        return $this->minLogLevel->value <= $logLevel->value;
    }

    protected function buildLogMessage(string $message, LogLevel $logLevel, ?array $data = null)
    {
        $logMessage = $this->name . ": " . date("Y-m-d\TH:i:s\Z") . " [" . $logLevel->name . "] " . $message;
        if ($data !== null) {
            $logMessage .= " " . json_encode($data);
        }

        return $logMessage;
    }

    abstract protected function logAction(string $message, LogLevel $logLevel, ?array $data = null);

    public function log(string $message, LogLevel $logLevel, ?array $data = null)
    {
        if (!$this->enabledFor($logLevel)) {
            return;
        }

        $this->logAction($message, $logLevel, $data);
    }
}
