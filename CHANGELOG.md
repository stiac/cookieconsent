# Changelog

## [1.1.0] - 2025-09-30
### Added
- Installatore web in `public/install/` con verifica prerequisiti, editor `.env` e applicazione automatica delle migrazioni con log in `storage/logs/install.log`.
- Updater incrementale in `public/update/` che confronta la versione installata con il file `VERSION` ed esegue solo le migrazioni mancanti.
- Endpoint JSON `public/health/` con report su versione, PHP, permessi cartelle runtime e stato database.
- Servizi di supporto (`FileLogger`, `EnvironmentService`, `SystemCheckService`, `MigrationService`, `DatabaseManager`, `InstallerStateService`) per condividere logica tra installatore, updater e health check.
- Test di base per installatore e health check sotto `tests/`.

### Changed
- README aggiornato con istruzioni su installazione guidata, aggiornamenti incrementali e monitoraggio.
- `.env.example` arricchito con variabili di connessione al database.
- `.gitignore` adeguato per tracciare i placeholder delle nuove directory di cache e tmp.

### Fixed
- N/D.

### Removed
- N/D.

## [1.0.1] - 2025-09-29
### Added
- `SOFTWARE_REPORT.md` con stato progetto, rischi e rendicontazione sintetica.
- `ROADMAP.md` per pianificazione evoluzioni del plugin.
- `docs/DEPENDENCIES.md` per documentare librerie di terze parti e licenze.

### Changed
- `README.md` aggiornato con versione 1.0.1, requisiti, istruzioni FTP, configurazione Tailwind e note di sicurezza.
- Processo di documentazione esplicitato per garantire aggiornamento di README, CHANGELOG e VERSION ad ogni release.

### Fixed
- N/D.

### Removed
- N/D.

## [1.0.0] - 2025-09-28
### Added
- Nuova struttura `public_html/plugin/cookieconsent` conforme alle linee guida Codex.
- Banner cookie basato su Tailwind UI con asset già compilati e pronti per l'uso.
- Controller e servizi PHP modulari per configurazione, template e gestione consenso.
- Script web di installazione, aggiornamento e health-check per ambienti cPanel.
- Documentazione aggiornata (README, ROADMAP, SOFTWARE_REPORT, DEPENDENCIES).

### Changed
- Rimosse toolchain Rollup e dipendenze che richiedono build.

### Fixed
- N/D (prima release).

### Removed
- Vecchio codice sorgente e asset non compatibili con l'ambiente condiviso.
