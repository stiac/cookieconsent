<?php

namespace Plugin\CookieConsent\Controllers;

use Plugin\CookieConsent\Services\ConsentService;

/**
 * Handles persisting user preferences coming from the banner.
 */
class ConsentController
{
    public function __construct(private readonly ConsentService $consent)
    {
    }

    public function __invoke(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid payload']);
            return;
        }

        $this->consent->storePreferences(array_map('boolval', $input));

        echo json_encode(['message' => 'Preferences stored']);
    }
}
