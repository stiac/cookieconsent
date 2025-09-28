<?php
/** @var array<string, mixed> $app */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($app['name'] ?? 'Cookie Consent', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script defer src="/assets/js/cookieconsent.js"></script>
</head>
<body class="min-h-screen bg-slate-950 bg-gradient-to-b from-slate-900 via-slate-950 to-slate-950 text-slate-100">
    <!-- Layout inspired by Tailwind UI marketing call-to-action block -->
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-24">
        <div class="max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.4em] text-emerald-300">CookieConsent <?= htmlspecialchars($app['version'], ENT_QUOTES, 'UTF-8'); ?></p>
            <h1 class="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl">
                Banner modulare per il consenso ai cookie
            </h1>
            <p class="mt-6 text-lg leading-8 text-slate-300">
                Questo esempio dimostra l\'utilizzo dei componenti Tailwind UI per creare un banner accessibile
                che può essere incluso in qualsiasi installazione PHP senza toolchain di build.
            </p>
            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="#" class="inline-flex items-center gap-2 rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-emerald-950 shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-300">
                    Documentazione
                </a>
                <a href="#" class="inline-flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:border-white/40">
                    Vedi il banner
                </a>
            </div>
        </div>
    </main>
    <div id="cookie-consent-root" data-endpoint-config="api/config" data-endpoint-banner="api/banner" data-endpoint-consent="api/consent"></div>
</body>
</html>
