<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Logger.php';

use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    public function testDoesNotLogWithoutTargets(): void
    {
        $logger = new Logger();
        ob_start();
        $logger->debug('Test message');
        $logger->info('Test message');
        $logger->warning('Test message');
        $logger->error('Test message');
        $output = ob_get_clean();
        $this->assertEmpty($output);
    }

    public function testTargetCanBeAddedInRuntime(): void
    {
        $logger = new Logger();
        $logger->addLogger(new ConsoleLogger(LogLevel::WARNING));
        ob_start();
        $logger->error('Test message');
        $output = ob_get_clean();
        $this->assertTrue(str_starts_with($output, 'Console:'), $output);
    }

    public function testCanBeInitializedWithTargets(): void
    {
        $logger = new Logger([
            new ConsoleLogger(LogLevel::WARNING),
        ]);

        ob_start();
        $logger->error('Test message');
        $output = ob_get_clean();
        $this->assertTrue(str_starts_with($output, 'Console:'), $output);
    }

    public function testLevelsAreCalledCorrectly(): void
    {
        $levels = [
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::WARNING,
            LogLevel::ERROR,
        ];

        $logger = new Logger([
            new ConsoleLogger(LogLevel::DEBUG),
        ]);
        foreach ($levels as $level) {
            $method = match ($level) {
                LogLevel::DEBUG => 'debug',
                LogLevel::INFO => 'info',
                LogLevel::WARNING => 'warning',
                LogLevel::ERROR => 'error',
            };

            ob_start();
            $logger->$method('Test message');
            $output = ob_get_clean();
            $this->assertTrue(str_contains($output, '[' . $level->name . ']'));
        }
    }
}
