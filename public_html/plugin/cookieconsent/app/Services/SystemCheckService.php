<?php
declare(strict_types=1);

namespace Plugin\CookieConsent\Services;

use RuntimeException;

/**
 * Performs system level checks required by the installer and updater.
 */
class SystemCheckService
{
    public function __construct(private readonly FileLogger $logger)
    {
    }

    /**
     * Validate the PHP version against the configured requirement string.
     *
     * @return array{required: string, current: string, status: bool, message: string}
     */
    public function checkPhpVersion(string $requirement): array
    {
        $normalizedRequirement = $this->normalizeVersionRequirement($requirement);
        $status = version_compare(PHP_VERSION, $normalizedRequirement, '>=');

        $message = $status
            ? 'Versione PHP compatibile.'
            : sprintf('Versione PHP minima richiesta %s, rilevata %s.', $normalizedRequirement, PHP_VERSION);

        $this->logger->info('PHP version check executed.', [
            'required' => $normalizedRequirement,
            'current' => PHP_VERSION,
            'status' => $status ? 'ok' : 'failed',
        ]);

        return [
            'required' => $normalizedRequirement,
            'current' => PHP_VERSION,
            'status' => $status,
            'message' => $message,
        ];
    }

    /**
     * Ensure that the given directories exist and are writable when required.
     *
     * @param array<string, bool> $directories map of path => shouldBeWritable
     * @return array<int, array{path: string, status: bool, message: string}>
     */
    public function ensureDirectories(array $directories): array
    {
        $results = [];

        foreach ($directories as $path => $writable) {
            $status = true;
            $message = 'Disponibile.';

            if (!is_dir($path)) {
                if (!mkdir($path, 0o775, true) && !is_dir($path)) {
                    $status = false;
                    $message = 'Impossibile creare la directory.';
                } else {
                    $message = 'Directory creata.';
                    $this->logger->info('Created runtime directory.', ['path' => $path]);
                }
            }

            if ($status && $writable && !is_writable($path)) {
                $status = false;
                $message = 'Directory non scrivibile. Aggiorna i permessi a 775/777.';
            }

            $results[] = [
                'path' => $path,
                'status' => $status,
                'message' => $message,
            ];

            $this->logger->info('Directory check executed.', [
                'path' => $path,
                'writable' => $writable,
                'status' => $status ? 'ok' : 'failed',
            ]);
        }

        return $results;
    }

    private function normalizeVersionRequirement(string $requirement): string
    {
        if (preg_match('/(\d+\.\d+(?:\.\d+)?)/', $requirement, $matches) === 1) {
            return $matches[1];
        }

        throw new RuntimeException('Unsupported PHP version requirement format.');
    }
}
