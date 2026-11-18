<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** PostgreSQL DDL for the booking redemption claim ledger (Scheduled #15). Runs as one simple-query batch via PDO::exec. */
final class RedeemSchema { public static function ddl():string {return "CREATE TABLE IF NOT EXISTS titan_booking_intent_claims (claim_key CHAR(64) NOT NULL PRIMARY KEY, owner_id BIGINT NOT NULL CHECK (owner_id >= 0), fingerprint CHAR(64) NOT NULL, state VARCHAR(16) NOT NULL CHECK (state IN ('claimed','committed')), booking_id BIGINT NULL CHECK (booking_id IS NULL OR booking_id >= 0), created_at TIMESTAMPTZ NOT NULL DEFAULT now(), updated_at TIMESTAMPTZ NOT NULL DEFAULT now()); CREATE INDEX IF NOT EXISTS idx_titan_booking_intent_claims_owner_state ON titan_booking_intent_claims (owner_id, state);";} }
