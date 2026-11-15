# Pass 111 — Date-relative, provenance-preserving booking fixtures

Generate synthetic fixture JSON for an upcoming business day from the Pass 108 reviewed source package. The schema and seed bytes are copied unchanged; only the two appointment fixture dates are updated. The output includes a fresh SHA-256 manifest and is checked by the existing Pass 110 validator. The generated output is **review-only** and **not approved for import or production release**.

From the application root:

```bash
python3 integration/staging/pass111/refresh_fixtures.py --out /tmp/pa111-fresh-fixtures
python3 integration/staging/pass111/test_refresh.py
```

Once a qualified operator has reviewed the schema, migration order and fixture data on a Docker-capable disposable staging host, use the existing Pass 101 runner with `PA101_FIXTURES_DIR` pointing to the generated directory, `PA101_SCHEMA_SQL` and `PA101_SEED_SQL` pointing to its two SQL files, and `PA101_REVIEW_MANIFEST` pointing to the generated manifest. The runner still requires explicit disposable-database consent and credentials. It is not possible to infer migration approval from a passing date preflight.

Pass 58 and real MariaDB booking acceptance remain unresolved.
