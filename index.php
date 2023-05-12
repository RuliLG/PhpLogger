<?php

require_once __DIR__ . '/src/Logger.php';
require_once __DIR__ . '/src/loggers/ConsoleLogger.php';

$logger = new Logger();
$logger->addLogger(new ConsoleLogger(LogLevel::WARNING));

$logger->debug('Debug message');
$logger->info('Info message');
$logger->warning('Warning message');
$logger->error('ERROR message');
