-- Per-tenant database. Not auto-applied. Requires migration review and MySQL testing.
CREATE TABLE IF NOT EXISTS titan_booking_requests (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 request_key VARCHAR(96) NOT NULL,
 command_hash CHAR(64) NOT NULL,
 status ENUM('pending','completed','failed') NOT NULL DEFAULT 'pending',
 booking_id BIGINT UNSIGNED DEFAULT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_request_key (request_key),
 KEY idx_booking_id (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Atomic booking inserts must also acquire a tenant-scoped staff lock, re-query conflicts
-- inside the transaction, commit all Cleanto order/booking records together, and handle retries.
