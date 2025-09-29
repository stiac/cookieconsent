<?php
declare(strict_types=1);

use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\DatabaseManager;
use Plugin\CookieConsent\Services\EnvironmentService;
use Plugin\CookieConsent\Services\FileLogger;
use Plugin\CookieConsent\Services\InstallerStateService;
use Plugin\CookieConsent\Services\SystemCheckService;

require __DIR__ . '/../../app/bootstrap.php';

$logger = new FileLogger(storage_path('logs/install.log'));
$configService = new ConfigService(config_path());
$appConfig = $configService->get('app');

$envService = new EnvironmentService(config_path('.env'), config_path('.env.example'), $logger);
$environment = $envService->ensureFileExists();

$systemCheck = new SystemCheckService($logger);
$phpCheck = $systemCheck->checkPhpVersion($appConfig['support_php'] ?? '8.1');

$directories = [
    storage_path('logs') => true,
    storage_path('cache') => true,
    storage_path('tmp') => true,
];
$directoryChecks = $systemCheck->ensureDirectories($directories);

$dbManager = new DatabaseManager($environment, $logger);
$connection = $dbManager->createConnection();
$databaseConfigured = ($environment['DB_DSN'] ?? '') !== '';
$databaseStatus = [
    'configured' => $databaseConfigured,
    'connected' => $connection !== null,
    'message' => $connection !== null
        ? 'Connessione al database attiva.'
        : ($databaseConfigured ? 'Connessione fallita, verificare le credenziali.' : 'Database non configurato.'),
];

$stateService = new InstallerStateService(storage_path('tmp/installer_state.json'), $logger);
$installedVersion = $stateService->getInstalledVersion();

$versionFile = base_path('../../../VERSION');
$currentVersion = is_readable($versionFile) ? trim((string) file_get_contents($versionFile)) : ($appConfig['version'] ?? '1.0.0');

$directorySummaries = [];
$directoryFailures = false;
foreach ($directoryChecks as $check) {
    $directorySummaries[] = [
        'path' => $check['path'],
        'status' => $check['status'] ? 'ok' : 'error',
        'message' => $check['message'],
    ];

    if ($check['status'] === false) {
        $directoryFailures = true;
    }
}

$status = 'ok';
if (!$phpCheck['status'] || $directoryFailures || ($databaseConfigured && $connection === null)) {
    $status = 'error';
} elseif (!$databaseConfigured) {
    $status = 'warning';
}

$response = [
    'status' => $status,
    'checked_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
    'application' => [
        'name' => $appConfig['name'] ?? 'Cookie Consent Plugin',
        'target_version' => $currentVersion,
        'installed_version' => $installedVersion,
        'php' => [
            'required' => $phpCheck['required'],
            'current' => $phpCheck['current'],
            'status' => $phpCheck['status'] ? 'ok' : 'error',
        ],
    ],
    'directories' => $directorySummaries,
    'database' => $databaseStatus,
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
