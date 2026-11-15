# Pass 14 — Tenant-safe mutation locking

Cancellation now uses the selected authenticated MySQL database to derive a tenant-qualified booking lock. The rescheduling lock plan supports explicit tenant-qualified booking + union of old/new staff locks; the original two/three-argument compatibility path is retained for legacy consumers and MUST NOT be used to expose new public mutation endpoints.

All public writes remain disabled. New lock-boundary, tenant authorization, request scoping and launch gate components are unit-testable; full MySQL transaction and concurrency tests are pending. Cleanto vendor source files have not been edited.

The MySQL advisory lock manager now retains unreleased lock keys when release fails instead of assuming a failed release succeeded. Any affected DB session must be discarded on persistent release failure.
