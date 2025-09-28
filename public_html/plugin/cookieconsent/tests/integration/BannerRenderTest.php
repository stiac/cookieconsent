<?php
declare(strict_types=1);

use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\ConsentService;
use Plugin\CookieConsent\Services\TemplateService;

require __DIR__ . '/../../app/bootstrap.php';

$config = new ConfigService(config_path());
$consent = new ConsentService($config);
$templates = new TemplateService(resource_path('views'));

$html = $templates->render('banner', [
    'config' => $config->get('cookieconsent'),
    'preferences' => $consent->getPreferences(),
]);

if (strpos($html, 'cookie-consent') === false) {
    throw new RuntimeException('Banner markup missing expected container.');
}

echo "Banner render smoke test completed.\n";
