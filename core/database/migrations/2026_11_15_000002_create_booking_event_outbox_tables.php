<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Transactional outbox for booking.* events sent to Central. Forward-only: there is no down().
 * A row is written in the same transaction as the booking change. booking_event_streams gives each tenant a
 * sequence whose row lock is held until that transaction commits, so a tenant's events are sent in commit order.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_event_streams', function (Blueprint $table) {
            $table->char('organisation_id', 36)->primary();
            $table->unsignedBigInteger('last_seq')->default(0);
        });

        Schema::create('booking_event_outbox', function (Blueprint $table) {
            $table->id();
            $table->char('event_id', 36)->unique();
            $table->char('organisation_id', 36);
            $table->unsignedBigInteger('stream_seq');
            $table->string('event_type', 64);
            $table->unsignedInteger('schema_version')->default(1);
            $table->string('correlation_id', 128);
            $table->string('idempotency_key', 200)->unique();
            $table->dateTime('occurred_at', 3);
            $table->text('payload_json');
            $table->char('payload_digest', 64);
            $table->string('status', 16)->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->dateTime('next_attempt_at', 3);
            $table->string('last_error', 255)->nullable();
            $table->dateTime('published_at', 3)->nullable();
            $table->unique(['organisation_id', 'stream_seq']);
            $table->index(['status', 'next_attempt_at']);
        });
    }
};
