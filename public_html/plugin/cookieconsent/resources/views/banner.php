<?php
/** @var array<string, mixed> $config */
/** @var array<string, bool> $preferences */
?>
<!-- Component assembled from Tailwind UI "Consent banner" block with custom switches -->
<div id="cookie-consent" class="fixed inset-x-0 bottom-0 z-50 flex w-full justify-center px-4 pb-4">
    <div class="w-full max-w-3xl rounded-2xl bg-slate-900/95 p-6 text-white shadow-2xl ring-1 ring-white/10">
        <div class="flex flex-col gap-6 md:flex-row md:items-start">
            <div class="flex-1 space-y-3">
                <div>
                    <h2 class="text-lg font-semibold tracking-tight">
                        <?= htmlspecialchars($config['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-slate-100/80">
                        <?= htmlspecialchars($config['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <?php if (!empty($config['policy_url'])): ?>
                        <a class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-emerald-300 hover:text-emerald-200" href="<?= htmlspecialchars($config['policy_url'], ENT_QUOTES, 'UTF-8'); ?>">
                            Informativa sulla privacy
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="rounded-xl bg-white/5 p-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-emerald-200">Categorie</h3>
                    <p class="mt-1 text-xs text-slate-100/70">Personalizza il consenso scegliendo le categorie da abilitare.</p>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($config['categories'] as $key => $category): ?>
                            <?php $checked = $category['readonly'] ? true : ($preferences[$key] ?? false); ?>
                            <div class="flex items-start justify-between gap-3 rounded-lg bg-slate-800/80 p-3">
                                <div>
                                    <p class="text-sm font-medium"><?= htmlspecialchars($category['label'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="mt-1 text-xs text-slate-100/60"><?= htmlspecialchars($category['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input
                                        type="checkbox"
                                        class="peer sr-only"
                                        data-cookie-category="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"
                                        <?= $checked ? 'checked' : ''; ?>
                                        <?= $category['readonly'] ? 'disabled' : ''; ?>
                                    >
                                    <span class="flex h-6 w-11 items-center rounded-full bg-slate-600 transition peer-checked:bg-emerald-400 peer-focus:outline peer-focus:outline-2 peer-focus:outline-emerald-200"></span>
                                    <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="flex w-full flex-col gap-3 md:w-64">
                <button
                    type="button"
                    class="consent-accept w-full rounded-xl bg-emerald-400 px-4 py-2 text-sm font-semibold text-emerald-950 shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-300"
                >
                    <?= htmlspecialchars($config['buttons']['accept_all'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
                <button
                    type="button"
                    class="consent-reject w-full rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-100 ring-1 ring-inset ring-white/10 transition hover:bg-slate-700"
                >
                    <?= htmlspecialchars($config['buttons']['reject_all'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
                <button
                    type="button"
                    class="consent-save w-full rounded-xl border border-emerald-200/40 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:border-emerald-200 hover:text-emerald-100"
                >
                    <?= htmlspecialchars($config['buttons']['save'], ENT_QUOTES, 'UTF-8'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
