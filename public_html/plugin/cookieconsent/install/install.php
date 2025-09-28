<?php
declare(strict_types=1);

use Plugin\CookieConsent\Services\ConfigService;

require __DIR__ . '/../app/bootstrap.php';

$configService = new ConfigService(config_path());
$app = $configService->get('app');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Installazione Cookie Consent</title>
    <link rel="stylesheet" href="../public/assets/css/app.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-2xl px-4 py-24">
        <h1 class="text-3xl font-bold">Installazione</h1>
        <p class="mt-4">Versione: <?= htmlspecialchars($app['version'] ?? '1.0.0', ENT_QUOTES, 'UTF-8'); ?></p>
        <ol class="mt-6 space-y-3 text-sm leading-6 text-slate-100/80">
            <li><strong>1.</strong> Verifica che la versione di PHP sia <?= htmlspecialchars($app['support_php'] ?? '>=8.1', ENT_QUOTES, 'UTF-8'); ?>.</li>
            <li><strong>2.</strong> Copia la cartella <code>public_html/plugin/cookieconsent</code> via FTP nel server.</li>
            <li><strong>3.</strong> Aggiorna <code>config/cookieconsent.php</code> per personalizzare testi e categorie.</li>
            <li><strong>4.</strong> Visita <code>/public/install/</code> dopo l\'upload per confermare la configurazione.</li>
        </ol>
        <p class="mt-6 text-sm text-slate-100/70">Tutte le impostazioni sono file PHP modificabili direttamente dal file manager di cPanel.</p>
    </main>
</body>
</html>
