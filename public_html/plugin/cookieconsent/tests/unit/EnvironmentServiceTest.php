<?php
declare(strict_types=1);

use Plugin\CookieConsent\Services\EnvironmentService;
use Plugin\CookieConsent\Services\FileLogger;

require __DIR__ . '/../../app/bootstrap.php';

$testDirectory = storage_path('tmp/test_env_' . uniqid('', true));
if (!is_dir($testDirectory) && !mkdir($testDirectory, 0o775, true) && !is_dir($testDirectory)) {
    throw new RuntimeException('Unable to create test directory.');
}

$envPath = $testDirectory . '/.env';
$examplePath = config_path('.env.example');
$logger = new FileLogger($testDirectory . '/log.json');

$service = new EnvironmentService($envPath, $examplePath, $logger);
$data = $service->ensureFileExists();

if (!isset($data['APP_ENV'])) {
    throw new RuntimeException('Expected APP_ENV key from template.');
}

$data['APP_ENV'] = 'testing';
$service->write($data);

$reloaded = $service->load();
if (($reloaded['APP_ENV'] ?? '') !== 'testing') {
    throw new RuntimeException('EnvironmentService::write failed to persist changes.');
}

echo "EnvironmentService unit test completed.\n";
