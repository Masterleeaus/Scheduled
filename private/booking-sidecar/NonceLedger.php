<?php
namespace Titan\BookingSidecar;
use RuntimeException;
/** Atomic nonce reservation across PHP workers using SQLite WAL; must use a persistent private volume. */
final class NonceLedger {
    public function __construct(private \PDO $db) {
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->exec('CREATE TABLE IF NOT EXISTS booking_nonce (instance_key TEXT NOT NULL, nonce TEXT NOT NULL, expires_at INTEGER NOT NULL, PRIMARY KEY(instance_key, nonce))');
    }
    public function reserve(string $key, string $nonce, int $now): void {
        $this->db->beginTransaction();
        try {
            $purge = $this->db->prepare('DELETE FROM booking_nonce WHERE expires_at < ?');
            $purge->execute([$now]);
            $insert = $this->db->prepare('INSERT INTO booking_nonce(instance_key, nonce, expires_at) VALUES (?, ?, ?)');
            $insert->execute([$key, $nonce, $now + 120]);
            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw new RuntimeException('Replay detected or replay ledger unavailable.', 0, $e);
        }
    }
}
