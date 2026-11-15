# Pass 49 evidence

Source: Pass 48 staging evidence master.

- php -l /mnt/data/pass49check/tools/install-preflight.php: exit=0, No syntax errors detected in /mnt/data/pass49check/tools/install-preflight.php
- php -l /mnt/data/pass49check/tests/pass49-private-http-protection.php: exit=0, No syntax errors detected in /mnt/data/pass49check/tests/pass49-private-http-protection.php
- php /mnt/data/pass49check/tests/pass49-private-http-protection.php: exit=0, PASS: Apache and Nginx both deny core
PASS: Apache and Nginx both deny private
PASS: Apache and Nginx both deny tools
PASS: Apache and Nginx both deny tests
PASS: Apache and Nginx both deny docs
PASS: Apache and Nginx both deny integration
PASS: Apache and Nginx both deny development
PASS: Apache and Nginx both deny scripts
PASS: Apache and Nginx both deny PASS05_EVIDENCE
PASS: Apache and Nginx both deny PASS[0-9]+ root documents
PASS: Apache and Nginx both deny RECOVERY_STATUS_PASS root documents
PASS: Live HTTP probe tests /private/cleanto/vendor/composer/installed.json
PASS: Live HTTP probe tests /private/cleanto/assets/stripe/composer.json
PASS: Live HTTP probe tests /PASS48_REPORT.md
PASS: Live HTTP probe tests /PASS48_MANIFEST.json
PASS: Live HTTP probe tests /RECOVERY_STATUS_PASS62.json
PASS: Live HTTP probe tests /integration/install/pass47/compose.yaml
PASS: Preflight checks private-source protection
PASS: Probe accepts only deny/not-found for private paths
PASS: Public Laravel entrypoint preserved
PASS: Public Nginx PHP entrypoint preserved
PASS: Pass 49 source privacy checks
- bash -n /mnt/data/pass49check/integration/install/pass47/http-security-check.sh: exit=0, 
- php tests/pass47-staging-contract.php: exit=0, PASS: 19 staging acceptance contract assertions
- php tests/pass48-staging-network-evidence.php: exit=0, PASS: 22 Pass 48 configuration assertions

Modified: 5; added: 2; removed: 0.

Live Apache/Nginx enforcement and MariaDB installation: NOT VERIFIED.
