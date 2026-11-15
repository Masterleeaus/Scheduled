# Pass 43 — Booking conflict readback hardening

Based on the complete Pass 42 cumulative master.

## Changes
- Defensive validation of booking IDs, staff CSV, statuses, durations and local timestamps.
- DST-safe occupied intervals and fail-closed invalid legacy rows.
- Read-only bounded Cleanto conflict SQL and staff-specific busy window integration.
- Updated historical duration rejection test to accept DomainException.

## Tests
- 735/735 executable PHP regression scripts passed.
- Live MySQL transactions: 0 (environment lacks pdo_mysql).
- Original Cleanto source changed: 0.
- Public booking confirmations remain disabled.

## Status
Source integration and regression checks only; not production-ready.
