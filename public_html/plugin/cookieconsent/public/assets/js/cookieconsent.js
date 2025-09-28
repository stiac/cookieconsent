/**
 * Lightweight Cookie Consent manager.
 *
 * The script loads both the configuration (JSON) and the Tailwind-based HTML
 * fragment produced by the PHP controllers. It exposes a minimal API to
 * accept, reject or customize the categories without relying on bundlers.
 */
(function () {
    'use strict';

    const root = document.getElementById('cookie-consent-root');
    if (!root) {
        return;
    }

    const endpoints = {
        config: root.dataset.endpointConfig || 'api/config',
        banner: root.dataset.endpointBanner || 'api/banner',
        consent: root.dataset.endpointConsent || 'api/consent'
    };

    const state = {
        categories: {},
        preferences: {}
    };

    function request(url, options = {}) {
        return fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            ...options
        });
    }

    function hideBanner() {
        const banner = document.getElementById('cookie-consent');
        if (banner) {
            banner.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                banner.remove();
            }, 300);
        }
    }

    function syncToggles(preferences) {
        document.querySelectorAll('[data-cookie-category]').forEach((input) => {
            const key = input.getAttribute('data-cookie-category');
            if (!key) {
                return;
            }
            if (input.disabled) {
                input.checked = true;
                return;
            }
            input.checked = Boolean(preferences[key]);
        });
    }

    function collectPreferences() {
        const result = {};
        document.querySelectorAll('[data-cookie-category]').forEach((input) => {
            const key = input.getAttribute('data-cookie-category');
            if (!key || input.disabled) {
                return;
            }
            result[key] = input.checked;
        });
        return result;
    }

    function attachActions() {
        const accept = document.querySelector('.consent-accept');
        const reject = document.querySelector('.consent-reject');
        const save = document.querySelector('.consent-save');

        if (accept) {
            accept.addEventListener('click', () => {
                const preferences = {};
                Object.keys(state.categories).forEach((key) => {
                    if (!state.categories[key].readonly) {
                        preferences[key] = true;
                    }
                });
                submitPreferences(preferences);
            });
        }

        if (reject) {
            reject.addEventListener('click', () => {
                const preferences = {};
                Object.keys(state.categories).forEach((key) => {
                    if (!state.categories[key].readonly) {
                        preferences[key] = false;
                    }
                });
                submitPreferences(preferences);
            });
        }

        if (save) {
            save.addEventListener('click', () => {
                submitPreferences(collectPreferences());
            });
        }
    }

    function submitPreferences(preferences) {
        request(endpoints.consent, {
            method: 'POST',
            body: JSON.stringify(preferences)
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Impossibile salvare le preferenze.');
                }
                state.preferences = { ...state.preferences, ...preferences };
                hideBanner();
            })
            .catch((error) => {
                console.error(error);
            });
    }

    Promise.all([
        request(endpoints.config),
        fetch(endpoints.banner, { credentials: 'same-origin' })
    ])
        .then(async ([configResponse, bannerResponse]) => {
            const configJson = await configResponse.json();
            state.categories = configJson.config.categories || {};
            state.preferences = configJson.preferences || {};

            const html = await bannerResponse.text();
            root.innerHTML = html;
            syncToggles(state.preferences);
            attachActions();
        })
        .catch((error) => {
            console.error('Errore nel caricamento del banner Cookie Consent', error);
        });
})();
