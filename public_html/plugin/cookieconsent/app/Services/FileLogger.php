<?php
declare(strict_types=1);

namespace Plugin\CookieConsent\Services;

use DateTimeImmutable;
use RuntimeException;

/**
 * Minimal file based logger used by the installer, updater and
 * health check endpoints. The logger writes structured JSON lines
 * so that administrators can quickly inspect recent operations
 * without additional tooling.
 */
class FileLogger
{
    public function __construct(private readonly string $logFile)
    {
        $directory = dirname($this->logFile);
        if (!is_dir($directory) && !mkdir($directory, 0o775, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Unable to create log directory "%s"', $directory));
        }
    }

    /**
     * Write a message with context to the log file.
     *
     * @param array<string, mixed> $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $timestamp = (new DateTimeImmutable())->format(DATE_ATOM);
        $record = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context,
        ];

        $encoded = json_encode($record, JSON_THROW_ON_ERROR);
        $result = @file_put_contents($this->logFile, $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);

        if ($result === false) {
            throw new RuntimeException(sprintf('Unable to write to log file "%s"', $this->logFile));
        }
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
}
