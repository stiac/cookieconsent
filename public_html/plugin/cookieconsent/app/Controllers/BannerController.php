<?php

namespace Plugin\CookieConsent\Controllers;

use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\ConsentService;
use Plugin\CookieConsent\Services\TemplateService;

/**
 * Builds the banner markup from Tailwind UI templates.
 */
class BannerController
{
    public function __construct(
        private readonly TemplateService $templates,
        private readonly ConfigService $config,
        private readonly ConsentService $consent
    ) {
    }

    public function __invoke(): void
    {
        header('Content-Type: text/html; charset=utf-8');

        echo $this->templates->render('banner', [
            'config' => $this->config->get('cookieconsent'),
            'preferences' => $this->consent->getPreferences(),
        ]);
    }
}
