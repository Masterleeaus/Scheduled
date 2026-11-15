<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Explicit local-user links for verified Central OIDC subjects.
 *
 * Populate only through an authorised provisioning process after verifying
 * ownership/membership in Central. Never bootstrap links by email.
 * Deliberately non-destructive rollback: dropping this table would erase
 * revocation history and could enable unsafe re-linking during recovery.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('central_user_links', function (Blueprint $table): void {
            $table->bigIncrements('id');
            // Fixed-size SHA-256 digests keep uniqueness portable between MySQL and PostgreSQL.
            $table->char('issuer_sha256', 64);
            $table->char('subject_sha256', 64);
            $table->unsignedBigInteger('user_id');
            $table->timestamp('approved_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->unique(['issuer_sha256', 'subject_sha256'], 'central_user_links_verified_subject_unique');
            $table->index(['user_id', 'revoked_at'], 'central_user_links_user_revoke_idx');
            $table->foreign('user_id', 'central_user_links_user_fk')->references('id')->on('users')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // Forward-only: preserve authorised identity mappings and revocations.
    }
};
