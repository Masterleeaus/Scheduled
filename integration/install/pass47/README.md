# Pass 47: Disposable clean-install HTTP + MariaDB test rig

This is a **test-only** deployment harness; it does not certify production readiness or enable booking writes. Requires Docker Engine + Compose, Bash, and curl on a Docker-capable host. No external HTTP/DB ports other than `127.0.0.1:18080` (configurable).

From the application root, supply two **distinct, randomly generated secrets of at least 16 characters** to your terminal environment (do not store them in a committed `.env`): `PA47_DB_PASSWORD`, `PA47_ROOT_PASSWORD`. Set `PA47_DISPOSABLE_ACK=YES_DESTROY_DISPOSABLE_DB`. Then run `bash integration/install/pass47/run.sh`. All database and application data are destroyed when the runner exits. Do not use actual client data, production secrets, a public host, or production licence credentials.

Use the browser to open the loopback URL. During the install, specify MySQL host `db`, database `pa47_fresh_install`, user `pa47_installer`, and your installer DB password. Verify schema migrations, creation of a fresh administrator, login, dashboard access and template creation/publishing. Licence verification requires separately authorised credentials; do not bypass that check.

This image ships no vendor-issued licence token; the installer may legitimately block until authorised server-side licence verification is configured. If login, installer or media paths fail, preserve a redacted error report; never post database credentials or application keys.

After setup manually verify all 19 website starters and 10 landing starters; separately test Cleanto native service configuration and booking with synthetic data. **Do not enable production reservation writes or Stripe live mode** based on this harness alone.

The runner executes PHP/MySQL-driver checks and HTTP leak probes. It does *not* automatically populate the application, assert payment success, or prove all booking flows. The Docker image and runtime have not been executed in the chat environment where Docker is absent.

## Pass 48: Connectivity and evidence safeguards

The app container joins **two** Docker networks: a database-only `private` network
(`internal: true`) and an egress network. The database joins **only** `private`.
This permits the application to contact authorised external licence endpoints
while preventing any database container egress or host-published MariaDB port.
The staging HTTP port remains explicitly bound to `127.0.0.1` on the host.
No external domain or internet access is required for the basic preflight probes.

The `run.sh` script now verifies Docker Compose availability and the HTTP port,
and refuses unattended operation without an evidence folder. For noninteractive
*pre-install* probes only, set `PA47_NONINTERACTIVE=1` and
`PA47_EVIDENCE_DIR=/path/outside/source` (alongside the two random passwords and
disposable acknowledgement). It records `preflight.json` and `http-security.txt`
then automatically destroys the test volumes. A successful noninteractive run is
**not** evidence that installation, admin login, booking or payments work.

The HTTP security probe additionally exercises the `integration/` directory,
project manifest, development documents, vendor autoloader and Laravel logs.
All must return HTTP 403 or 404. Do not put real credentials in saved reports.

## Pass 49: Sensitive source HTTP deny coverage

The root Apache rules and Nginx deployment example now deny `private/` (the Cleanto source and vendor libraries), `integration/`, `development/`, `scripts/`, `PASS05_EVIDENCE/`, and release/recovery reports and manifests. These are not public assets. The HTTP probe explicitly requests real private Cleanto paths and root release documents, accepting only 403/404. An HTTP status returned by a live web server is required to verify enforcement; source tests alone cannot certify it. For Nginx, manually install the included security snippet within the virtual host before using the site.
