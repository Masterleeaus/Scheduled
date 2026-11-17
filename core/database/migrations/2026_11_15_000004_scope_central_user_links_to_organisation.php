<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A deliberately additive migration for existing link records.
 * NULL does NOT match any verified organisation: old unscoped links deny login
 * until an authorised provisioning process validates company membership.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('central_user_links', function (Blueprint $table): void {
            $table->char('central_organisation_id', 36)->nullable();
            $table->dropUnique('central_user_links_verified_subject_unique');
            // One Central identity may belong to multiple companies; never conflate their links.
            $table->unique(
                ['issuer_sha256', 'subject_sha256', 'central_organisation_id'],
                'central_user_links_identity_org_unique'
            );
            $table->index(['central_organisation_id', 'revoked_at'], 'central_user_links_org_revoke_idx');
        });
    }

    public function down(): void
    {
        // Forward-only to avoid silently removing the tenancy boundary.
    }
};
