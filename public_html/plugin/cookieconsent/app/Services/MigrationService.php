<?php
declare(strict_types=1);

namespace Plugin\CookieConsent\Services;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Executes SQL migration files located in the `/migrations` directory.
 */
class MigrationService
{
    public function __construct(
        private readonly string $migrationDirectory,
        private readonly string $stateFile,
        private readonly FileLogger $logger
    ) {
    }

    /**
     * Run all migrations that have not been executed yet.
     *
     * @return array<int, array{file: string, status: string, message: string}>
     */
    public function runPendingMigrations(?PDO $connection): array
    {
        if ($connection === null) {
            $this->logger->warning('Database connection unavailable, skipping migrations.');
            return [[
                'file' => 'N/A',
                'status' => 'skipped',
                'message' => 'Connessione al database non configurata.',
            ]];
        }

        $executed = $this->getExecutedMigrations();
        $files = glob($this->migrationDirectory . DIRECTORY_SEPARATOR . '*.sql') ?: [];
        sort($files);

        $reports = [];
        foreach ($files as $file) {
            $filename = basename($file);
            if (in_array($filename, $executed, true)) {
                $reports[] = [
                    'file' => $filename,
                    'status' => 'skipped',
                    'message' => 'Migrazione già applicata.',
                ];
                continue;
            }

            try {
                $sql = file_get_contents($file);
                if ($sql === false) {
                    throw new RuntimeException('Impossibile leggere la migrazione.');
                }

                $connection->exec($sql);
                $this->markAsExecuted($filename, $executed);

                $reports[] = [
                    'file' => $filename,
                    'status' => 'applied',
                    'message' => 'Migrazione eseguita con successo.',
                ];

                $this->logger->info('Migration executed.', ['file' => $filename]);
            } catch (PDOException | RuntimeException $exception) {
                $reports[] = [
                    'file' => $filename,
                    'status' => 'failed',
                    'message' => $exception->getMessage(),
                ];
                $this->logger->error('Migration failed.', [
                    'file' => $filename,
                    'error' => $exception->getMessage(),
                ]);
                break;
            }
        }

        if ($reports === []) {
            $reports[] = [
                'file' => 'N/A',
                'status' => 'skipped',
                'message' => 'Nessuna migrazione trovata.',
            ];
        }

        return $reports;
    }

    /**
     * @return string[]
     */
    private function getExecutedMigrations(): array
    {
        if (!is_file($this->stateFile)) {
            return [];
        }

        $content = file_get_contents($this->stateFile);
        if ($content === false) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_map(static fn ($value) => (string) $value, $decoded);
    }

    /**
     * @param string[] $executed
     */
    private function markAsExecuted(string $filename, array $executed): void
    {
        $executed[] = $filename;
        $directory = dirname($this->stateFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0o775, true);
        }

        file_put_contents($this->stateFile, json_encode(array_values($executed), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), LOCK_EX);
    }
}
