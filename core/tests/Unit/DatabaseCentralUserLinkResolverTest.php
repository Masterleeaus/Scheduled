<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Central\Link\DatabaseCentralUserLinkResolver;
use PHPUnit\Framework\TestCase;

final class DatabaseCentralUserLinkResolverTest extends TestCase
{
    public function test_uses_only_exact_verified_issuer_and_subject_digests(): void
    {
        $seen = [];
        $resolver = new DatabaseCentralUserLinkResolver(static function (string $issuer, string $subject, string $org) use (&$seen): int {
            $seen = [$issuer, $subject, $org];
            return 42;
        });
        self::assertSame(42, $resolver->localUserId('https://central.example.test', 'subject-123', '0f8fad5b-d9cb-469f-a165-70867728950e'));
        self::assertSame([
            hash('sha256', 'https://central.example.test'),
            hash('sha256', 'subject-123'),
            '0f8fad5b-d9cb-469f-a165-70867728950e',
        ], $seen);
        self::assertCount(3, $seen);
    }

    public function test_unlinked_or_revoked_identity_fails_closed(): void
    {
        $resolver = new DatabaseCentralUserLinkResolver(static fn (): ?int => null);
        self::assertNull($resolver->localUserId('https://central.example.test', 'unknown', '0f8fad5b-d9cb-469f-a165-70867728950e'));
    }

    public function test_invalid_subjects_never_reach_the_link_store(): void
    {
        $lookup = static function (): never {
            throw new \LogicException('invalid input reached lookup');
        };
        $resolver = new DatabaseCentralUserLinkResolver($lookup);
        self::assertNull($resolver->localUserId('', 'sub', '0f8fad5b-d9cb-469f-a165-70867728950e'));
        self::assertNull($resolver->localUserId('issuer', '', '0f8fad5b-d9cb-469f-a165-70867728950e'));
        self::assertNull($resolver->localUserId(str_repeat('i', 2049), 'sub'));
        self::assertNull($resolver->localUserId('issuer', str_repeat('s', 2049)));
    }

    public function test_malformed_local_ids_are_not_accepted(): void
    {
        foreach ([false, null, 0, -3, '0', '4.5', '1 OR 1=1', 'bad@example.test', [], new \stdClass()] as $candidate) {
            $resolver = new DatabaseCentralUserLinkResolver(static fn () => $candidate);
            self::assertNull($resolver->localUserId('issuer', 'subject', '0f8fad5b-d9cb-469f-a165-70867728950e'));
        }
        $resolver = new DatabaseCentralUserLinkResolver(static fn (): string => '25');
        self::assertSame(25, $resolver->localUserId('issuer', 'subject', '0f8fad5b-d9cb-469f-a165-70867728950e'));
    }

    public function test_issuer_and_subject_are_not_normalised_into_another_identity(): void
    {
        $digests = [];
        $resolver = new DatabaseCentralUserLinkResolver(static function ($issuer, $subject) use (&$digests) {
            $digests[] = [$issuer, $subject];
            return null;
        });
        $resolver->localUserId('https://central.example.test', 'sub', '0f8fad5b-d9cb-469f-a165-70867728950e');
        $resolver->localUserId('https://central.example.test/', 'sub', '0f8fad5b-d9cb-469f-a165-70867728950e');
        $resolver->localUserId('https://central.example.test', 'SUB', '0f8fad5b-d9cb-469f-a165-70867728950e');
        self::assertNotSame($digests[0][0], $digests[1][0]);
        self::assertNotSame($digests[0][1], $digests[2][1]);
    }
}
