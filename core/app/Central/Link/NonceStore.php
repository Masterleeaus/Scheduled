<?php
declare(strict_types=1);

namespace App\Central\Link;

/** Single-use nonce registry. consume() returns false when the nonce was already used. */
interface NonceStore
{
    public function consume(string $nonce, int $expiresAt): bool;
}
