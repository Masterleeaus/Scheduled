<?php
declare(strict_types=1);

namespace App\Central;

/**
 * Failure in the Central integration. The message never contains a token, secret, signature or URL
 * query. The reason is a stable code for logs and tests.
 */
final class CentralException extends \RuntimeException
{
    public function __construct(public readonly string $reason, string $message)
    {
        parent::__construct($message);
    }
}
