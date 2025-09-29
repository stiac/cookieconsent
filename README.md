# Cookie Consent Plugin

**Versione corrente:** 1.0.1

Plugin PHP pensato per ambienti condivisi con cPanel che fornisce banner e preferenze di consenso ai cookie, senza richiedere toolchain di build. Tutti i file possono essere caricati via FTP mantenendo piena compatibilità con l'alberatura `public_html/plugin/cookieconsent`.

## Requisiti
- **PHP:** 8.1 o superiore (testato su 8.1 e 8.2) con estensioni standard abilitate (`json`, `mbstring`).
- **Database:** MySQL 5.7+ o MariaDB 10.3+ per la persistenza delle preferenze (opzionale ma consigliato).
- **Accesso:** FTP o File Manager cPanel per il deploy dei file.
- **Browser:** supporto moderno a JavaScript ES6 per l'interfaccia Tailwind.

## Installazione via FTP
1. Scaricare il pacchetto del plugin e posizionare la cartella nella directory locale `public_html/plugin/cookieconsent`.
2. Caricare l'intera struttura sul server mantenendo la gerarchia:
   ```
   public_html/plugin/cookieconsent/
   ├── app/
   ├── config/
   ├── public/
   ├── resources/
   ├── storage/
   ├── install/
   ├── migrations/
   ├── tests/
   └── docs/
   ```
3. Impostare i permessi di scrittura su `storage/` e sottocartelle se il server lo richiede.
4. Visitare `https://tuodominio.tld/plugin/cookieconsent/public/install/` per verificare i prerequisiti e completare l'installazione guidata.
5. Inserire nel layout pubblico i riferimenti agli asset:
   ```html
   <link rel="stylesheet" href="/plugin/cookieconsent/public/assets/css/app.css">
   <script defer src="/plugin/cookieconsent/public/assets/js/cookieconsent.js"></script>
   <div id="cookie-consent-root"></div>
   ```
6. Configurare `config/cookieconsent.php` con i testi dei cookie, le categorie e gli URL della privacy.

## Configurazione di Tailwind
- Gli stili presenti in `public/assets/css/app.css` sono compilati e pronti all'uso, allineati ai blocchi Tailwind UI.
- Per personalizzazioni leggere, modificare le utility CSS nel file e ricaricare l'asset. In caso di ricompilazione da sorgenti Tailwind, usare un ambiente locale e caricare via FTP il CSS generato.
- I componenti UI si basano su [Tailwind UI](https://tailwindui.com/) seguendo i blocchi Catalyst; documentare l'uso nel file `docs/DEPENDENCIES.md` quando si aggiungono nuovi pattern.

## Sicurezza
- Tenere i file di configurazione sensibili fuori da `/public/` e proteggere `config/.env` con permessi restrittivi.
- Assicurarsi che `.htaccess` in `public/` imposti gli header di sicurezza (X-Frame-Options, X-Content-Type-Options, Referrer-Policy) e disabiliti il directory listing.
- Usare connessioni HTTPS per tutte le pagine che ospitano il banner di consenso.
- Validare sempre l'input utente e usare prepared statements per l'accesso al database.
- Impostare i cookie come `Secure`, `HttpOnly` e con `SameSite=Lax` o più restrittivo.

## Aggiornamento e versionamento
1. Caricare i nuovi file sovrascrivendo quelli esistenti.
2. Visitare `https://tuodominio.tld/plugin/cookieconsent/public/update/` per applicare migrazioni e verificare la versione installata.
3. Ad ogni release aggiornare coerentemente i file `README.md`, `CHANGELOG.md` e `VERSION`.
4. Consultare `CHANGELOG.md` per l'elenco delle modifiche e `ROADMAP.md` per le evoluzioni pianificate.

## Documentazione di supporto
- `docs/DEPENDENCIES.md`: librerie e licenze adottate.
- `SOFTWARE_REPORT.md`: stato del progetto, moduli e rischi.
- `ROADMAP.md`: attività pianificate e priorità.

## Licenza
Distribuito sotto licenza MIT (vedi `LICENSE`).
