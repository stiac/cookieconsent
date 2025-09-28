<?php
/**
 * Application bootstrap file.
 *
 * Sets up error handling, loads configuration values and registers a
 * lightweight PSR-4 style autoloader for the plugin namespace.
 */

declare(strict_types=1);

require_once __DIR__ . '/Helpers/functions.php';

// Configure runtime environment: explicit timezone and strict error reporting in dev mode.
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Share application metadata from configuration files.
$appConfig = require config_path('app.php');

spl_autoload_register(static function (string $class): void {
    $prefix = 'Plugin\\CookieConsent\\';
    $baseDir = app_path();

    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $path = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

        if (is_readable($path)) {
            require_once $path;
        }
    }
});

return [
    'name' => $appConfig['name'] ?? 'Cookie Consent Plugin',
    'version' => $appConfig['version'] ?? '1.0.0',
    'env' => $appConfig['env'] ?? 'production',
];
