<?php

declare(strict_types=1);

namespace Tests;

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Neucore\Application;

date_default_timezone_set('UTC');

require __DIR__ . '/../vendor/autoload.php';

// Setup error handler
$config = (new Application())->loadSettings(true);
error_reporting((int)$config['error_reporting']);
$handler = new StreamHandler($config['monolog']['path'], Logger::DEBUG);
$log = new \Neucore\Log\Logger('Test');
$log->pushHandler($handler);
ErrorHandler::register($log);
ini_set('log_errors', '0');

// Create DB schema
(new Helper())->updateDbSchema();
