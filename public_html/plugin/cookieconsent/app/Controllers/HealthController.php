<?php

namespace Plugin\CookieConsent\Controllers;

/**
 * Minimal health-check endpoint used for monitoring purposes.
 */
class HealthController
{
    public function __invoke(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'ok',
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}
