<?php

namespace Plugin\CookieConsent\Controllers;

use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\ConsentService;

/**
 * Returns the banner configuration and visitor status as JSON.
 */
class ConfigController
{
    public function __construct(
        private readonly ConfigService $config,
        private readonly ConsentService $consent
    ) {
    }

    public function __invoke(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            [
                'config' => $this->config->get('cookieconsent'),
                'preferences' => $this->consent->getPreferences(),
            ],
            JSON_THROW_ON_ERROR
        );
    }
}
