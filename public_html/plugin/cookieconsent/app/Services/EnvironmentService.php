<?php
declare(strict_types=1);

namespace Plugin\CookieConsent\Services;

use RuntimeException;

/**
 * Handles reading and writing the configuration `.env` file located
 * in the config directory. The service is intentionally lightweight
 * to avoid requiring third-party dependencies on shared hosting.
 */
class EnvironmentService
{
    /** @var string[] */
    private array $sensitiveKeys = ['APP_KEY', 'DB_PASSWORD'];

    public function __construct(
        private readonly string $envPath,
        private readonly string $examplePath,
        private readonly FileLogger $logger
    ) {
    }

    /**
     * Ensure that the `.env` file exists by copying from the example template.
     *
     * @return array<string, string>
     */
    public function ensureFileExists(): array
    {
        if (!is_file($this->envPath)) {
            $directory = dirname($this->envPath);
            if (!is_dir($directory) && !mkdir($directory, 0o775, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Unable to create config directory "%s"', $directory));
            }

            if (!is_readable($this->examplePath)) {
                throw new RuntimeException('Environment example file is missing.');
            }

            $copied = copy($this->examplePath, $this->envPath);
            if ($copied === false) {
                throw new RuntimeException('Unable to create environment file from template.');
            }

            $this->logger->info('Environment file created from template.', ['path' => $this->envPath]);
        }

        return $this->load();
    }

    /**
     * Load the environment variables as an associative array.
     *
     * @return array<string, string>
     */
    public function load(): array
    {
        if (!is_readable($this->envPath)) {
            return [];
        }

        $lines = file($this->envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new RuntimeException('Unable to read environment file.');
        }

        $variables = [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $variables[trim($key)] = trim($value);
        }

        return $variables;
    }

    /**
     * Persist the provided environment variables to disk.
     *
     * @param array<string, string> $values
     */
    public function write(array $values): void
    {
        ksort($values);
        $content = "";
        foreach ($values as $key => $value) {
            $content .= $key . '=' . $value . PHP_EOL;
        }

        $result = file_put_contents($this->envPath, $content, LOCK_EX);
        if ($result === false) {
            throw new RuntimeException('Unable to persist environment file.');
        }

        $this->logger->info('Environment file updated.', ['keys' => $this->maskSensitiveKeys(array_keys($values))]);
    }

    /**
     * Load the example environment file to pre-populate installer forms.
     *
     * @return array<string, string>
     */
    public function loadExample(): array
    {
        if (!is_readable($this->examplePath)) {
            return [];
        }

        $lines = file($this->examplePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $variables = [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $variables[trim($key)] = trim($value);
        }

        return $variables;
    }

    /**
     * Mask sensitive keys before logging to avoid leaking secrets.
     *
     * @param string[] $keys
     * @return array<string, string>
     */
    private function maskSensitiveKeys(array $keys): array
    {
        $masked = [];
        foreach ($keys as $key) {
            $masked[$key] = in_array($key, $this->sensitiveKeys, true) ? '***' : 'visible';
        }

        return $masked;
    }
}
