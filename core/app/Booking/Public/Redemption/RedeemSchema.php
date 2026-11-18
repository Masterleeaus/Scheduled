<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemSchema { public static function ddl():string {return "CREATE TABLE IF NOT EXISTS titan_booking_intent_claims (claim_key CHAR(64) NOT NULL PRIMARY KEY, owner_id BIGINT NOT NULL CHECK (owner_id > 0), fingerprint CHAR(64) NOT NULL, state TEXT NOT NULL CHECK (state IN ('claimed','committed')), booking_id BIGINT NULL, created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP); CREATE INDEX IF NOT EXISTS idx_titan_booking_intent_claims_owner_state ON titan_booking_intent_claims (owner_id, state);";} }
