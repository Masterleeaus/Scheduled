# Pass 113 — Real MariaDB import preflight

This is a read-only probe **executed inside the disposable database container network**, after authentic schema and synthetic seed import, before the booking acceptance scripts. It queries actual MariaDB metadata and rows and fails closed if table engines, row counts or service/pricing/staff joins differ. It does not create or confirm any appointment.

Run the component regression with `php integration/staging/pass113/test_live_contract.php`. For an actual database run, follow `integration/staging/pass101/README.md` and run its controlled Docker launcher with reviewed Cleanto SQL and synthetic inputs. The current environment lacks Docker, MariaDB and pdo_mysql, so this pass cannot claim real acceptance.

The baseline still lacks the Pass 58 source and verified historical Cleanto upgrades. Do not enable public booking or payments on the basis of this probe.
