<?php

/*
|--------------------------------------------------------------------------
| Titan Central integration
|--------------------------------------------------------------------------
|
| Master switch for the Central wiring. It defaults to OFF, so production
| behaviour is unchanged until an operator sets CENTRAL_INTEGRATION_ENABLED=true.
|
| When OFF:
|   - App\Central\Providers\CentralServiceProvider is not registered
|     (see the conditional entry in config/app.php);
|   - routes/central.php is not loaded, so no /api/central/* route exists.
|
| When ON:
|   - the provider registers the event store binding and loads routes/central.php;
|   - GET  /api/central/status   reports that the integration is enabled;
|   - POST /api/central/events   accepts Central's signed outbox events.
|     No event handlers are registered yet, so every verified event is
|     rejected with 422 unsupported_event and no side effect runs.
|
| CONFIG NOTE: SINGLE-INSTANCE ONLY. The stores used here are in-memory and
| process-local:
|   - the event store (App\Central\Events\InMemoryProcessedEventStore) keeps
|     idempotency claims in process memory. Under PHP-FPM each request is a
|     new process, so it does not deduplicate across requests at all;
|   - the link nonce store (scheduled/src/nonce-store.js) is also process-local.
| Do not enable this flag on more than one app instance until a shared,
| durable store with the same semantics is in place (tracked in the PR notes).
|
| The flag is read from the environment only. Central's remote flag read
| endpoint is not live yet, so it is not consulted here.
|
*/

return [

    'integration_enabled' => filter_var(env('CENTRAL_INTEGRATION_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

];
