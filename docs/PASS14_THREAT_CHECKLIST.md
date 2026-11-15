# Threat checklist — Pass 14

- [x] New tenant ownership requires admin membership.
- [x] Domain uniqueness database enforced.
- [x] Hostnames validated, not arbitrary URLs.
- [x] DNS TXT challenge used rather than HTTP request (avoids SSRF).
- [x] DNS ownership proof required for domain activation.
- [x] Active host bindings restrict published site/campaign requests.
- [x] Booking write controls are unchanged.
- [ ] Legacy records are NOT tenant-isolated; migration policy needed.
- [ ] Cross-guard and legacy roles need MariaDB/browser verification.
- [ ] Reverse-proxy trusted-host middleware/TLS must be configured.
- [ ] Tenant media file access and all AI tools need full isolation audit.
- [ ] Tests on actual production-like database are pending.
