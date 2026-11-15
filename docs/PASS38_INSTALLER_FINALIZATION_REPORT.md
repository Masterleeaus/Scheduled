# Pass 38 — Installer finalisation verification

## Source
Pass 37 cumulative master: `Master-Copy-PlusAgency-Cleanto-Pass37-INSTALLATION-PREFLIGHT-CUMULATIVE.zip`.

## Corrected defects
1. The first-administrator step was clearing `installer_schema_imported` before finalisation. This made final verification of schema provenance impossible. The token is now kept through the final step and both tokens are cleared only after the installed marker is verified.
2. The legacy `FinalInstallManager` swallowed exceptions from key generation and vendor asset publishing, potentially allowing completion despite command failure. It now rejects those dangerous settings and validates the existing application key, without running mutating Artisan commands.
3. Finalisation now verifies identity tables, exactly one active administrator, a password hash, no inherited customer identities, valid key, readable environment, writable storage, and absence of an installed marker before committing completion.
4. Installer finalisation reports a generic public failure rather than database or configuration details; exception errors preserve the session state for retry.

## Verification performed
- 23 executable PHP assertions passed in `tests/pass38_installer_completion.php`.
- All four modified/added application PHP files passed `php -l`.
- Source preservation and ZIP integrity checks are performed during package creation.
- No existing archive paths intentionally deleted.

## Important limits
- No MariaDB server, `pdo_mysql`, or `mysqli` extension is available locally; therefore no live clean installation, admin login or HTTP/browser test was performed.
- Booking payments, template editing and live staff schedule changes are still not operationally certified.
- This work is a source-level installer reliability patch, **not production installation certification**.

## Baseline counts
- 50 vertical catalogue entries; 19 website starters (15 cleaning-focused and 4 newly adapted); 10 landing templates.
- 29 templates partially editable; 0/29 fully browser-certified.
