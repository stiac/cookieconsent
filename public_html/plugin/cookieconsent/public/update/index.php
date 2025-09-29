<?php
declare(strict_types=1);

use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\DatabaseManager;
use Plugin\CookieConsent\Services\EnvironmentService;
use Plugin\CookieConsent\Services\FileLogger;
use Plugin\CookieConsent\Services\InstallerStateService;
use Plugin\CookieConsent\Services\MigrationService;

require __DIR__ . '/../../app/bootstrap.php';

$logger = new FileLogger(storage_path('logs/install.log'));
$configService = new ConfigService(config_path());
$appConfig = $configService->get('app');

$envService = new EnvironmentService(config_path('.env'), config_path('.env.example'), $logger);
$currentEnv = $envService->ensureFileExists();

$stateService = new InstallerStateService(storage_path('tmp/installer_state.json'), $logger);
$installedVersion = $stateService->getInstalledVersion();

$versionFile = base_path('../../../VERSION');
$targetVersion = is_readable($versionFile) ? trim((string) file_get_contents($versionFile)) : ($appConfig['version'] ?? '1.0.0');

$dbManager = new DatabaseManager($currentEnv, $logger);
$connection = $dbManager->createConnection();

$migrationService = new MigrationService(
    base_path('migrations'),
    storage_path('tmp/migrations.json'),
    $logger
);
$migrationReport = $migrationService->runPendingMigrations($connection);

$hasFailure = (bool) array_filter($migrationReport, static fn (array $report): bool => $report['status'] === 'failed');

$versionMessage = '';
if (!$hasFailure) {
    if ($installedVersion !== $targetVersion) {
        $stateService->persistInstalledVersion($targetVersion);
        $versionMessage = sprintf('Versione aggiornata da %s a %s.', $installedVersion ?? 'N/D', $targetVersion);
    } else {
        $versionMessage = 'Il plugin è già aggiornato all\'ultima versione disponibile.';
    }
} else {
    $versionMessage = 'Aggiornamento interrotto: correggi gli errori delle migrazioni e riprova.';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Aggiornamento Cookie Consent</title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-4xl px-4 py-16 space-y-10">
        <header class="border-b border-slate-800 pb-6">
            <h1 class="text-3xl font-bold">Aggiornamento Cookie Consent</h1>
            <p class="mt-2 text-sm text-slate-300">Versione disponibile: <?= e($targetVersion); ?></p>
            <p class="mt-1 text-sm text-slate-400">
                Questo pannello applica migrazioni incrementali e aggiorna le informazioni di versione salvate nel sistema.
            </p>
        </header>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Stato versione</h2>
            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                <div class="rounded-lg border border-slate-800 bg-slate-900/30 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Versione installata</dt>
                    <dd class="mt-2 text-lg font-semibold text-slate-100"><?= e($installedVersion ?? 'Non registrata'); ?></dd>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-900/30 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Versione target</dt>
                    <dd class="mt-2 text-lg font-semibold text-slate-100"><?= e($targetVersion); ?></dd>
                </div>
            </dl>
            <p class="mt-4 text-sm <?= !$hasFailure ? 'text-emerald-300' : 'text-rose-300'; ?>"><?= e($versionMessage); ?></p>
        </section>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Report migrazioni</h2>
            <ul class="mt-4 space-y-3 text-sm">
                <?php foreach ($migrationReport as $report): ?>
                    <li class="rounded-md border border-slate-800 bg-slate-900/30 px-4 py-3">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="font-mono text-xs text-slate-300"><?= e($report['file']); ?></span>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $report['status'] === 'applied' ? 'bg-emerald-500/10 text-emerald-400' : ($report['status'] === 'failed' ? 'bg-rose-500/10 text-rose-400' : 'bg-amber-500/10 text-amber-300'); ?>">
                                <?= e(strtoupper($report['status'])); ?>
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-slate-400"><?= e($report['message']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="mt-4 text-xs text-slate-500">
                Log dettagliati disponibili in <code>storage/logs/install.log</code>.
            </p>
        </section>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Prossimi passi</h2>
            <ul class="mt-4 list-disc space-y-2 pl-6 text-sm text-slate-300">
                <li>Verifica che il file <code>.env</code> contenga le credenziali aggiornate.</li>
                <li>Ripeti l\'aggiornamento dopo aver caricato nuovi file via FTP.</li>
                <li>Utilizza l\'endpoint <code>/public/health/</code> per monitorare lo stato dell\'applicazione.</li>
            </ul>
        </section>
    </main>
</body>
</html>
