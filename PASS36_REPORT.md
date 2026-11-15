# Pass 36 — Installation environment & completion reliability

## Changes

- Replaced destructive `.env` regeneration with an atomic, locked, key-preserving update that retains unknown integration and payment configuration.
- Rejects newline/NUL injection into environment values, removes duplicate overwritten keys, and retains the exact existing Laravel application key.
- Fails before SQL import if `.env` is not writable, or `pdo_mysql`/`mysqli` drivers are unavailable.
- Protects installer completion retry by consuming the finalisation session flag only after an installed marker exists.
- Adds write-failure handling for the installed marker and standalone environment/installer contract tests.

## Unverified operational blockers

- No MariaDB client/server or `pdo_mysql`/`mysqli` installed in the execution environment; no live SQL import, migration or admin login acceptance.
- Legacy paid licence verification remains in place, with its existing external integration and configuration requirements.
- `core/.env` is written after schema import; a server crash in between can still leave a partially initialised database. Only fresh empty dedicated database installs are supported.
- Existing third-party payment-provider configuration and Cleanto provisioning remain independently unverified.
- Original website and landing templates remain registered; no browser acceptance.

## Release decision

**Development/staging candidate only**. Do not deploy as a production-ready or one-click installation.
