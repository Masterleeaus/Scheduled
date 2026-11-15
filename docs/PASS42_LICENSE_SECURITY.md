# Pass 42 — Installer licence security

The previous installer embedded a third-party bearer token in distributed source and directly printed remote response bodies and exception text. This pass removes that secret, requires a server-provided `INSTALLER_ENVATO_TOKEN`, verifies the Envato item and buyer response, and stores successful verification only in the session. No persistent marker is written in the vendor directory.

**Operator action:** Supply a valid Envato author API token in the server environment or `core/.env` as `INSTALLER_ENVATO_TOKEN=...` before starting the purchase-check step. Do not commit or upload that token to source control. The token must have permission to query sales from the vendor account. Licence checking remains enabled; it is not bypassed.

No external verification was attempted. Live Envato verification, a clean MariaDB installation, administrator login, and paid booking remain untested. This is not a production-certified installer.
