<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Durable idempotency record for signed Central events. One row per (consumer, idempotency key).
 * Forward-only: down() does nothing, because dropping this table would forget which events were handled.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('central_processed_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('consumer', 64);
            $table->string('idempotency_key', 200);
            $table->string('event_id', 36);
            $table->timestamp('created_at');
            $table->unique(['consumer', 'idempotency_key'], 'central_processed_events_consumer_key_unique');
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        // Forward-only by design. Rolling back would drop the replay record.
    }
};
