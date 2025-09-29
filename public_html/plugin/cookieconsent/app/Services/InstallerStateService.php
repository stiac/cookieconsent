<?php
declare(strict_types=1);

namespace Plugin\CookieConsent\Services;

/**
 * Persists installer metadata such as the last installed version to the
 * storage directory so that updates can operate incrementally.
 */
class InstallerStateService
{
    public function __construct(private readonly string $stateFile, private readonly FileLogger $logger)
    {
    }

    public function getInstalledVersion(): ?string
    {
        if (!is_file($this->stateFile)) {
            return null;
        }

        $content = file_get_contents($this->stateFile);
        if ($content === false) {
            return null;
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded) || !isset($decoded['installed_version'])) {
            return null;
        }

        return (string) $decoded['installed_version'];
    }

    public function persistInstalledVersion(string $version): void
    {
        $directory = dirname($this->stateFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0o775, true);
        }

        $payload = [
            'installed_version' => $version,
            'updated_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ];

        file_put_contents($this->stateFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), LOCK_EX);
        $this->logger->info('Installer state updated.', ['installed_version' => $version]);
    }
}
