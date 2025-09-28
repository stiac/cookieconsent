<?php
/**
 * Global helper functions used across the Cookie Consent plugin.
 * They keep the bootstrap light-weight and avoid the need for
 * a dependency injection container while still being explicit.
 */

declare(strict_types=1);

if (!function_exists('base_path')) {
    /**
     * Resolve a path relative to the project root.
     */
    function base_path(string $path = ''): string
    {
        $root = dirname(__DIR__, 2);
        return $path === '' ? $root : $root . DIRECTORY_SEPARATOR . $path;
    }
}

if (!function_exists('app_path')) {
    /**
     * Resolve a path within the application directory.
     */
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('config_path')) {
    /**
     * Resolve a path inside the configuration directory.
     */
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('resource_path')) {
    /**
     * Resolve a path inside the resources directory.
     */
    function resource_path(string $path = ''): string
    {
        return base_path('resources' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('storage_path')) {
    /**
     * Resolve a path inside the storage directory.
     */
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? DIRECTORY_SEPARATOR . $path : ''));
    }
}
