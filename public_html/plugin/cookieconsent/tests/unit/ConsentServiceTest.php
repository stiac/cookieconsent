<?php
declare(strict_types=1);

use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\ConsentService;

define('TEST_BASE_PATH', __DIR__ . '/../../');
require TEST_BASE_PATH . 'app/bootstrap.php';

$configService = new ConfigService(TEST_BASE_PATH . 'config');
$consentService = new ConsentService($configService);

assert($consentService->getPreferences() === [], 'Expected empty preferences when cookie is missing.');

echo "ConsentService smoke test completed.\n";
