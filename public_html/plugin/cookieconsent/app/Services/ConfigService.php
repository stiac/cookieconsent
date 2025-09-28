<?php

namespace Plugin\CookieConsent\Services;

use RuntimeException;

/**
 * Loads configuration arrays from the config directory and keeps a cached
 * instance to avoid repeated file access in the same request.
 */
class ConfigService
{
    /** @var array<string, array<mixed>> */
    private array $cache = [];

    public function __construct(private readonly string $configDirectory)
    {
    }

    /**
     * Read a configuration file by name, returning a flattened array.
     */
    public function get(string $name): array
    {
        if (!isset($this->cache[$name])) {
            $file = rtrim($this->configDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name . '.php';

            if (!is_readable($file)) {
                throw new RuntimeException(sprintf('Configuration file "%s" not found', $file));
            }

            $config = require $file;
            if (!is_array($config)) {
                throw new RuntimeException(sprintf('Configuration file "%s" must return an array', $file));
            }

            $this->cache[$name] = $config;
        }

        return $this->cache[$name];
    }
}
