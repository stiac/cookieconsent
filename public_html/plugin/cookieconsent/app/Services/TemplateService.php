<?php

namespace Plugin\CookieConsent\Services;

use RuntimeException;

/**
 * Simple PHP template renderer for server-side fragments.
 */
class TemplateService
{
    public function __construct(private readonly string $viewDirectory)
    {
    }

    /**
     * Render a PHP template with a set of variables.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $file = rtrim($this->viewDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $template . '.php';

        if (!is_readable($file)) {
            throw new RuntimeException(sprintf('View "%s" not found', $file));
        }

        extract($data, EXTR_OVERWRITE);

        ob_start();
        include $file;

        return (string) ob_get_clean();
    }
}
