<?php
declare(strict_types=1);

namespace Plugin\CookieConsent\Services;

use PDO;
use PDOException;

/**
 * Lightweight factory responsible for creating PDO connections from
 * environment configuration.
 */
class DatabaseManager
{
    /**
     * @param array<string, string> $environment
     */
    public function __construct(private readonly array $environment, private readonly FileLogger $logger)
    {
    }

    public function createConnection(): ?PDO
    {
        $dsn = $this->environment['DB_DSN'] ?? '';
        if ($dsn === '') {
            $this->logger->warning('DB_DSN missing; database connection skipped.');
            return null;
        }

        $username = $this->environment['DB_USER'] ?? '';
        $password = $this->environment['DB_PASSWORD'] ?? '';

        try {
            $pdo = new PDO($dsn, $username === '' ? null : $username, $password === '' ? null : $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $this->logger->info('Database connection established.', ['driver' => $this->extractDriver($dsn)]);

            return $pdo;
        } catch (PDOException $exception) {
            $this->logger->error('Database connection failed.', ['error' => $exception->getMessage()]);
            return null;
        }
    }

    private function extractDriver(string $dsn): string
    {
        return (string) strstr($dsn, ':', true);
    }
}
