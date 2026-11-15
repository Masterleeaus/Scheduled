-- Tenant database migration (MySQL / MariaDB, InnoDB). Not applied by this repository.
-- Durable idempotency ledger for owner_booking_create. The claim row is written in the
-- same transaction as the Cleanto order/booking inserts, so a committed booking and its
-- completed ledger row can never diverge. Columns must match BookingRequestLedger.php.
CREATE TABLE IF NOT EXISTS titan_booking_requests (
 request_key VARCHAR(96) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
 fingerprint CHAR(64) CHARACTER SET ascii NOT NULL,
 state VARCHAR(16) NOT NULL,
 booking_id BIGINT UNSIGNED DEFAULT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT chk_titan_booking_requests_state CHECK (state IN ('pending','completed')),
 KEY idx_titan_booking_requests_booking_id (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
