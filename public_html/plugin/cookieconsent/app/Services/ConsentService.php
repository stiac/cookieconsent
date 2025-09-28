<?php

namespace Plugin\CookieConsent\Services;

/**
 * Manages storing and retrieving the visitor consent preferences.
 *
 * The service writes the data in a cookie compatible with shared hosting
 * (no SameSite=None requirement) and keeps helpers for controllers.
 */
class ConsentService
{
    public const COOKIE_NAME = 'cookieconsent_preferences';
    private const COOKIE_TTL_DAYS = 180;

    public function __construct(private readonly ConfigService $config)
    {
    }

    /**
     * Retrieve the preferences from the incoming request.
     *
     * @return array<string, bool>
     */
    public function getPreferences(): array
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return [];
        }

        $decoded = json_decode((string) $_COOKIE[self::COOKIE_NAME], true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_map(static fn ($value) => (bool) $value, $decoded);
    }

    /**
     * Persist consent preferences for the visitor.
     *
     * @param array<string, bool> $preferences
     */
    public function storePreferences(array $preferences): void
    {
        $allowed = array_keys($this->config->get('cookieconsent')['categories']);
        $filtered = array_intersect_key($preferences, array_flip($allowed));

        $cookieValue = json_encode($filtered, JSON_THROW_ON_ERROR);
        setcookie(
            self::COOKIE_NAME,
            $cookieValue,
            [
                'expires' => time() + (self::COOKIE_TTL_DAYS * 24 * 60 * 60),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }
}
