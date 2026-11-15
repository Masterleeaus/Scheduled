<?php
declare(strict_types=1);

namespace App\Central\Link;

/**
 * Process-local nonce store, for tests and single-process use only. It does not survive a restart and is
 * not shared between workers, so production use needs a durable store behind NonceStore.
 */
final class InMemoryNonceStore implements NonceStore
{
    /** @var array<string,int> nonce => expiry timestamp */
    private array $used = [];

    public function consume(string $nonce, int $expiresAt): bool
    {
        $now = time();
        foreach ($this->used as $seen => $expiry) {
            if ($expiry <= $now) {
                unset($this->used[$seen]);
            }
        }
        if (isset($this->used[$nonce])) {
            return false;
        }
        $this->used[$nonce] = $expiresAt;

        return true;
    }
}
