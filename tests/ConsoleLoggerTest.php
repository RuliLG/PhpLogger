<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/loggers/ConsoleLogger.php';

use PHPUnit\Framework\TestCase;

final class ConsoleLoggerTest extends TestCase
{
    public function testLoggingOutputsRightMessageToConsole(): void
    {
        $logger = new ConsoleLogger(LogLevel::WARNING);
        ob_start();
        $logger->log('Test message', LogLevel::WARNING);
        $output = ob_get_clean();
        $this->assertTrue(str_starts_with($output, 'Console:'), $output);
        $this->assertTrue(str_ends_with($output, '[WARNING] Test message' . PHP_EOL), $output);
    }

    public function testDoesNotLogWrongLevels(): void
    {
        $levels = [
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::WARNING,
            LogLevel::ERROR,
        ];
        foreach ($levels as $loggerLevel) {
            $logger = new ConsoleLogger($loggerLevel);
            foreach ($levels as $messageLevel) {
                ob_start();
                $logger->log('Test message', $messageLevel);
                $output = ob_get_clean();

                if ($messageLevel->value < $loggerLevel->value) {
                    $this->assertEmpty($output);
                } else {
                    $this->assertNotEmpty($output);
                }
            }
        }
    }

    public function testLogsAdditionalDataAsJson(): void
    {
        $logger = new ConsoleLogger(LogLevel::WARNING);
        ob_start();
        $logger->log('Test message', LogLevel::WARNING, ['foo' => 'bar']);
        $output = ob_get_clean();
        $this->assertTrue(str_starts_with($output, 'Console:'), $output);
        $this->assertTrue(str_ends_with($output, '[WARNING] Test message {"foo":"bar"}' . PHP_EOL), $output);
    }
}
