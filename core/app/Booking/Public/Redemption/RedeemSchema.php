<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemSchema { public static function ddl():string {return "CREATE TABLE IF NOT EXISTS titan_booking_intent_claims (claim_key CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY, owner_id BIGINT UNSIGNED NOT NULL, fingerprint CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL, state VARCHAR(16) NOT NULL, booking_id BIGINT UNSIGNED NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_owner_state(owner_id,state)) ENGINE=InnoDB";} }
