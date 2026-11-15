# Pass 40: Disposable MySQL booking acceptance

This runbook is a **test environment**, not a production deployment or a claim that bookings work. The current local runtime lacks both Docker and `pdo_mysql`; real tests must be run on a separate trusted test machine.

1. Install Docker Engine and Compose, and obtain a properly licensed application distribution. Do not expose the test MariaDB port publicly.
2. Set `BOOKING_TEST_ROOT_PASSWORD` and `BOOKING_TEST_PASSWORD` to different randomly generated secrets in a local environment file, never committed to source control.
3. From the project root run `docker compose -f integration/mysql/pass40/docker/compose.yaml build` and `docker compose -f integration/mysql/pass40/docker/compose.yaml up -d db`.
4. **The disposable database starts empty.** Import a reviewed Cleanto test schema and separately generated non-personal seed fixtures, plus the integration claim-ledger schema. Never import production data or unknown SQL dumps.
5. Run `docker compose -f integration/mysql/pass40/docker/compose.yaml run --rm php integration/mysql/pass40/bin/validate_setup.php`, then `docker compose -f integration/mysql/pass40/docker/compose.yaml run --rm php integration/mysql/pass40/bin/assert_schema.php` and `... bin/assert_lock_contention.php` before attempting writes.
6. Create a JSON fixture specifically for `titan_booking_test_acceptance`. Inspect its contents; read the source requirements in `integration/mysql/lifecycle/LifecycleFixtureValidator.php`.
7. Set the explicit acknowledgment variables only for the disposable environment: `BOOKING_LIFECYCLE_ACK=I_UNDERSTAND_THIS_WRITES_TEST_DATA`, `BOOKING_ACCEPT_TEST_ONLY=YES_DISPOSABLE_ONLY`, and an absolute container fixture path. Run the lifecycle and concurrency commands via `bin/acceptance.sh`.
8. Preserve redacted results, separately verify the committed Cleanto rows, then destroy the test database. **Never** use these overrides on a production host.

The included Compose stack is a reproducible prerequisite, not proof that application migration, concurrency safety, or checkout succeeds. Booking writes in the application must remain disabled until those results are verified.

## Pass 60: Corrected environment mapping
The `php` container now exports both the original Pass 40 `BOOKING_TEST_DB_*` settings and the acceptance runtime’s canonical `BOOKING_TEST_HOST`, `BOOKING_TEST_DATABASE`, `BOOKING_TEST_USER`, and `BOOKING_TEST_PASSWORD`. The database is **`titan_booking_test_acceptance`**, matching `DisposableName`, and the isolated MariaDB service exposes the `mysql-test` alias, matching `MysqlTestConnection`. Do not alter this to use production database hosts.

The Docker database is deliberately empty on first boot. Obtain and review the official Cleanto schema and load only synthetic test data into this isolated database before running lifecycle tests. The example fixture is a template; service IDs, owner IDs and staff IDs must correspond to those synthetic seeded records. The fixture path must be within the read-only `/workspace` bind mount.

The shell wrapper requires two explicit acknowledgements and does **not** enable production booking writes. To destroy all disposable data: `docker compose -f integration/mysql/pass40/docker/compose.yaml down --volumes` (confirm this is only the isolated acceptance project).
