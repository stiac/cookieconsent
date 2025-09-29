<?php
declare(strict_types=1);

ob_start();
require __DIR__ . '/../../public/health/index.php';
$output = ob_get_clean();

$data = json_decode($output, true);
if (!is_array($data)) {
    throw new RuntimeException('Health endpoint did not return valid JSON.');
}

foreach (['status', 'application', 'database'] as $key) {
    if (!array_key_exists($key, $data)) {
        throw new RuntimeException(sprintf('Health payload missing "%s" section.', $key));
    }
}

echo "Health endpoint integration test completed.\n";
