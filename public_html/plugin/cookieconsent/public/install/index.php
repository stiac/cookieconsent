<?php
declare(strict_types=1);

use Plugin\CookieConsent\Services\ConfigService;
use Plugin\CookieConsent\Services\DatabaseManager;
use Plugin\CookieConsent\Services\EnvironmentService;
use Plugin\CookieConsent\Services\FileLogger;
use Plugin\CookieConsent\Services\InstallerStateService;
use Plugin\CookieConsent\Services\MigrationService;
use Plugin\CookieConsent\Services\SystemCheckService;

require __DIR__ . '/../../app/bootstrap.php';

$logger = new FileLogger(storage_path('logs/install.log'));
$configService = new ConfigService(config_path());
$appConfig = $configService->get('app');

$systemCheck = new SystemCheckService($logger);
$phpCheck = $systemCheck->checkPhpVersion($appConfig['support_php'] ?? '8.1');

$directoriesToCheck = [
    storage_path('logs') => true,
    storage_path('cache') => true,
    storage_path('tmp') => true,
];
$directoryChecks = $systemCheck->ensureDirectories($directoriesToCheck);

$envService = new EnvironmentService(config_path('.env'), config_path('.env.example'), $logger);
$currentEnv = $envService->ensureFileExists();
$exampleEnv = $envService->loadExample();
$envKeys = array_keys(array_merge($exampleEnv, $currentEnv));

$formMessage = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submitted = [];
    foreach ($envKeys as $key) {
        $value = trim((string) ($_POST[$key] ?? ($currentEnv[$key] ?? '')));
        $submitted[$key] = $value;
    }

    $envService->write($submitted);
    $currentEnv = $submitted;
    $formMessage = 'File .env aggiornato con successo.';
}

$dbManager = new DatabaseManager($currentEnv, $logger);
$connection = $dbManager->createConnection();

$migrationService = new MigrationService(
    base_path('migrations'),
    storage_path('tmp/migrations.json'),
    $logger
);
$migrationReport = $migrationService->runPendingMigrations($connection);

$stateService = new InstallerStateService(storage_path('tmp/installer_state.json'), $logger);
$versionFile = base_path('../../../VERSION');
$currentVersion = is_readable($versionFile) ? trim((string) file_get_contents($versionFile)) : ($appConfig['version'] ?? '1.0.0');
$stateService->persistInstalledVersion($currentVersion);

$allChecksPassed = $phpCheck['status']
    && !array_filter($directoryChecks, static fn (array $check): bool => $check['status'] === false)
    && !array_filter($migrationReport, static fn (array $report): bool => $report['status'] === 'failed');

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Installazione Cookie Consent</title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-4xl px-4 py-16 space-y-10">
        <header class="border-b border-slate-800 pb-6">
            <h1 class="text-3xl font-bold">Installazione Cookie Consent</h1>
            <p class="mt-2 text-sm text-slate-300">Versione pacchetto: <?= e($currentVersion); ?></p>
            <p class="mt-1 text-sm text-slate-400">
                Questo pannello verifica i prerequisiti, consente di aggiornare il file <code>.env</code>
                e applica le migrazioni SQL disponibili.
            </p>
        </header>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Verifica prerequisiti</h2>
            <dl class="mt-4 divide-y divide-slate-800">
                <div class="flex items-center justify-between py-3">
                    <dt class="text-sm font-medium text-slate-300">Versione PHP</dt>
                    <dd class="text-sm">
                        <span class="font-semibold <?= $phpCheck['status'] ? 'text-emerald-400' : 'text-rose-400'; ?>">
                            <?= e($phpCheck['current']); ?>
                        </span>
                        <span class="ml-2 text-slate-400">Richiesta: <?= e($phpCheck['required']); ?></span>
                    </dd>
                </div>
            </dl>
            <p class="mt-3 text-xs text-slate-400"><?= e($phpCheck['message']); ?></p>
        </section>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Permessi cartelle</h2>
            <div class="mt-4 overflow-hidden rounded-md border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800 text-left text-sm">
                    <thead class="bg-slate-900/60 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Percorso</th>
                            <th class="px-4 py-3">Stato</th>
                            <th class="px-4 py-3">Messaggio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/20">
                        <?php foreach ($directoryChecks as $check): ?>
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-slate-300"><?= e($check['path']); ?></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold <?= $check['status'] ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'; ?>">
                                        <?= $check['status'] ? 'OK' : 'Errore'; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-400"><?= e($check['message']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Configurazione .env</h2>
            <?php if ($formMessage !== null): ?>
                <p class="mb-4 rounded-md border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-300">
                    <?= e($formMessage); ?>
                </p>
            <?php endif; ?>
            <form method="post" class="grid gap-4 sm:grid-cols-2">
                <?php foreach ($envKeys as $key): ?>
                    <label class="flex flex-col gap-1 text-sm">
                        <span class="font-medium text-slate-300"><?= e($key); ?></span>
                        <input type="text" name="<?= e($key); ?>" value="<?= e($currentEnv[$key] ?? ''); ?>" class="rounded-md border border-slate-700 bg-slate-900/60 px-3 py-2 text-sm text-slate-100 focus:border-emerald-500 focus:outline-none focus:ring-emerald-500" autocomplete="off">
                    </label>
                <?php endforeach; ?>
                <div class="sm:col-span-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-900 shadow hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                        Salva configurazione
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Migrazioni database</h2>
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
        </section>

        <section class="rounded-lg border border-slate-800 bg-slate-900/40 p-6">
            <h2 class="text-xl font-semibold">Esito installazione</h2>
            <p class="mt-3 text-sm text-slate-300">
                <?= $allChecksPassed ? 'Tutti i controlli sono stati superati. È possibile procedere con l\'utilizzo del plugin.' : 'Verifica le segnalazioni sopra riportate prima di procedere.'; ?>
            </p>
            <p class="mt-2 text-xs text-slate-400">
                I dettagli dell\'esecuzione sono disponibili nel log <code>storage/logs/install.log</code>.
            </p>
        </section>
    </main>
</body>
</html>
