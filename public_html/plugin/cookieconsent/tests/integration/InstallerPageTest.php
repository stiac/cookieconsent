<?php
declare(strict_types=1);

require __DIR__ . '/../../app/bootstrap.php';

ob_start();
require __DIR__ . '/../../public/install/index.php';
$output = ob_get_clean();

if (strpos($output, 'Installazione Cookie Consent') === false) {
    throw new RuntimeException('Installer page did not render expected heading.');
}

if (strpos($output, 'Configurazione .env') === false) {
    throw new RuntimeException('Installer page missing environment configuration form.');
}

echo "Installer page integration test completed.\n";
