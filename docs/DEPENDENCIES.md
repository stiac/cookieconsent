# Dipendenze e licenze

| Componente              | Versione/Note                         | Licenza                     | Fonte/URL                          | Uso nel progetto |
|-------------------------|---------------------------------------|-----------------------------|------------------------------------|-----------------|
| PHP                     | 8.1+ (runtime)                        | PHP License                  | https://www.php.net/               | Linguaggio backend |
| MySQL / MariaDB         | 5.7+ / 10.3+                          | GPLv2 (Server)               | https://www.mysql.com/             | Storage preferenze cookie |
| Tailwind CSS            | 3.x (CSS precompilato)                | MIT                          | https://tailwindcss.com/           | Stili del banner e pannello preferenze |
| Tailwind UI (Catalyst)  | Componenti marketing/app              | Licenza commerciale Tailwind | https://tailwindui.com/            | Blocchi UI pronti per banner e modali |
| Alpine.js               | 3.x                                   | MIT                          | https://alpinejs.dev/              | Interazioni lato client del banner |

## Note aggiuntive
- I file CSS e JS distribuiti nel repository sono già compilati per evitare dipendenze da build-tool. Qualsiasi ricompilazione deve avvenire in locale e gli output caricati via FTP.
- L'utilizzo di Tailwind UI richiede una licenza valida; assicurarsi che il team disponga delle credenziali adeguate prima di distribuire componenti aggiuntivi.
- Non sono presenti dipendenze con clausole copyleft che impongano rilascio del codice sorgente oltre ai termini MIT del progetto.
