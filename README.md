# Cookie Consent Plugin

**Versione corrente:** 1.0.0

Plugin PHP modulare per la gestione del consenso ai cookie su hosting condivisi con cPanel. Tutti gli asset (CSS e JS) sono pronti all'uso e non richiedono toolchain di build.

## Requisiti
- PHP 8.1 o superiore (testato su PHP 8.1 e 8.2).
- Estensioni PHP standard abilitate (json, mbstring).
- Accesso FTP o File Manager per il deploy.

## Struttura principale
```
public_html/plugin/cookieconsent/
├── app/                # Servizi, controller e bootstrap
├── config/             # Configurazioni in PHP e .env.example
├── public/             # Entry point, asset Tailwind precompilati e .htaccess
├── resources/          # Template (Tailwind UI), lingue, email
├── storage/            # Cartelle runtime con .gitkeep
├── install/            # Script web install/update
├── migrations/         # File SQL versionati
├── tests/              # Smoke test manuali
└── docs/               # Documentazione dipendenze
```

## Installazione via FTP
1. Caricare l'intera cartella `public_html/plugin/cookieconsent` sul server.
2. Verificare che `public/.htaccess` sia riconosciuto da Apache (Rewrite e Headers abilitati).
3. Personalizzare `config/cookieconsent.php` con testi, link e categorie.
4. Visitare `https://tuodominio.tld/plugin/cookieconsent/public/install/` per confermare i prerequisiti.
5. Includere lo script nel layout pubblico con:
   ```html
   <link rel="stylesheet" href="/plugin/cookieconsent/public/assets/css/app.css">
   <script defer src="/plugin/cookieconsent/public/assets/js/cookieconsent.js"></script>
   ```
   e aggiungere il container `<div id="cookie-consent-root" ...></div>` nella pagina.

## Aggiornamento
1. Caricare i nuovi file sovrascrivendo la versione precedente.
2. Visitare `https://tuodominio.tld/plugin/cookieconsent/public/update/` per controllare la versione installata.
3. Consultare `CHANGELOG.md` per eventuali note specifiche.

## Routing e sicurezza
- `.htaccess` reindirizza tutte le richieste a `public/index.php`, mantenendo accessibili asset e script di install/update.
- Intestazioni di sicurezza base (X-Frame-Options, X-Content-Type-Options, Referrer-Policy) impostate via mod_headers.

## Test manuali
Eseguire i test di fumo forniti lanciando gli script PHP nella directory `tests/`:
```
php public_html/plugin/cookieconsent/tests/unit/ConsentServiceTest.php
php public_html/plugin/cookieconsent/tests/integration/BannerRenderTest.php
```

## Licenza
Distribuito sotto licenza MIT (vedi `LICENSE`).
