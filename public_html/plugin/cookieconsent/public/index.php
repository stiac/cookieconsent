<?php
/**
 * Front controller used on shared hosting environments.
 *
 * The script delegates the request to dedicated controllers based on the
 * requested URI without relying on mod_php specific routing.
 */

declare(strict_types=1);

use Plugin\CookieConsent\Controllers\BannerController;
use Plugin\CookieConsent\Controllers\ConfigController;
use Plugin\CookieConsent\Controllers\ConsentController;
use Plugin\CookieConsent\Controllers\HealthController;
use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\ConsentService;
use Plugin\CookieConsent\Services\TemplateService;

$app = require __DIR__ . '/../app/bootstrap.php';

$configService = new ConfigService(config_path());
$consentService = new ConsentService($configService);
$templateService = new TemplateService(resource_path('views'));

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$path = $base !== '' && str_starts_with($uri, $base) ? substr($uri, strlen($base)) : $uri;
$path = '/' . ltrim($path, '/');

switch ($path) {
    case '/api/config':
        (new ConfigController($configService, $consentService))();
        break;
    case '/api/banner':
        (new BannerController($templateService, $configService, $consentService))();
        break;
    case '/api/consent':
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Method Not Allowed';
            break;
        }
        (new ConsentController($consentService))();
        break;
    case '/health':
        (new HealthController())();
        break;
    default:
        header('Content-Type: text/html; charset=utf-8');
        echo $templateService->render('home', [
            'app' => $app,
        ]);
        break;
}
