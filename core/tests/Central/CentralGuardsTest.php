<?php

declare(strict_types=1);

namespace Tests\Central;

use App\Central\CentralException;
use App\Central\Config\CentralConfig;
use App\Central\Flags\FlagReader;
use App\Central\Service\ServiceTokenClient;
use PHPUnit\Framework\TestCase;

/**
 * Fail-closed checks for the Central integration (Scheduled issues #4 and #6). These tests need no database and
 * no Laravel boot: configuration and flag reads are injected through closures.
 */
final class CentralGuardsTest extends TestCase
{
    private const UUID = '0f8fad5b-d9cb-469f-a165-70867728950e';

    /** @param array<string,string> $env */
    private function config(array $env): CentralConfig
    {
        return new CentralConfig(static fn (string $name): ?string => $env[$name] ?? null);
    }

    private function fullEnv(): array
    {
        return [
            'CENTRAL_ORIGIN' => 'https://central.example.test',
            'CENTRAL_OIDC_DISCOVERY_URL' => 'https://central.example.test/.well-known/openid-configuration',
            'CENTRAL_OIDC_CLIENT_ID' => 'client-id-value',
            'CENTRAL_OIDC_CLIENT_SECRET' => 'client-secret-value',
            'CENTRAL_OIDC_AUDIENCE' => 'urn:titan-central:product:scheduled',
            'CENTRAL_OIDC_REDIRECT_URI' => 'https://scheduled.example.test/auth/titan/callback',
            'CENTRAL_SERVICE_CLIENT_ID' => 'service-id-value',
            'CENTRAL_SERVICE_CLIENT_SECRET' => 'service-secret-value',
            'CENTRAL_EVENTS_HMAC_SECRET' => str_repeat('k', 32),
            'CENTRAL_EVENTS_ORGANISATION_ID' => self::UUID,
        ];
    }

    public function test_missing_required_variable_fails_and_names_only_the_variable(): void
    {
        $env = $this->fullEnv();
        unset($env['CENTRAL_SERVICE_CLIENT_SECRET']);

        try {
            $this->config($env)->serviceClientSecret();
            $this->fail('A missing secret must throw.');
        } catch (CentralException $e) {
            $this->assertSame('config_missing', $e->reason);
            $this->assertStringContainsString('CENTRAL_SERVICE_CLIENT_SECRET', $e->getMessage());
        }
    }

    public function test_blank_variable_counts_as_missing(): void
    {
        $env = $this->fullEnv();
        $env['CENTRAL_OIDC_CLIENT_ID'] = "   ";

        $this->expectException(CentralException::class);
        $this->config($env)->oidcClientId();
    }

    public function test_service_audience_is_built_from_the_scheduled_product_id(): void
    {
        $this->assertSame(
            'urn:titan-central:product:scheduled',
            $this->config($this->fullEnv())->serviceAudience(),
        );
    }

    public function test_short_events_secret_is_refused(): void
    {
        $env = $this->fullEnv();
        $env['CENTRAL_EVENTS_HMAC_SECRET'] = str_repeat('k', CentralConfig::MIN_SECRET_LENGTH - 1);

        try {
            $this->config($env)->eventsSecret();
            $this->fail('A short HMAC secret must throw.');
        } catch (CentralException $e) {
            $this->assertSame('config_invalid', $e->reason);
            $this->assertStringNotContainsString(str_repeat('k', 8), $e->getMessage());
        }
    }

    public function test_sign_in_is_off_unless_exactly_one_or_true(): void
    {
        $on = fn (string $v): bool => $this->config(['CENTRAL_SIGNIN_ENABLED' => $v])->signInEnabled();

        $this->assertTrue($on('1'));
        $this->assertTrue($on('TRUE'));
        $this->assertTrue($on(' true '));
        foreach (['', '0', 'yes', 'on', '2', 'enabled'] as $value) {
            $this->assertFalse($on($value), "Sign-in must stay off for [{$value}].");
        }
        $this->assertFalse($this->config([])->signInEnabled());
    }

    public function test_events_organisation_must_be_a_uuid(): void
    {
        $env = $this->fullEnv();
        $env['CENTRAL_EVENTS_ORGANISATION_ID'] = 'not-a-uuid';

        $this->expectException(CentralException::class);
        $this->config($env)->eventsOrganisationId();
    }

    public function test_events_route_is_off_by_default(): void
    {
        $this->assertFalse($this->config([])->eventsEnabled());
        $this->assertFalse($this->config(['CENTRAL_EVENTS_ENABLED' => 'yes'])->eventsEnabled());
        $this->assertTrue($this->config(['CENTRAL_EVENTS_ENABLED' => 'true'])->eventsEnabled());
    }

    public function test_discovery_url_must_be_on_the_central_origin(): void
    {
        $env = $this->fullEnv();
        $env['CENTRAL_OIDC_DISCOVERY_URL'] = 'https://attacker.example.test/.well-known/openid-configuration';

        try {
            $this->config($env)->oidcDiscoveryUrl();
            $this->fail('A discovery URL on another origin must throw.');
        } catch (CentralException $e) {
            $this->assertSame('origin_mismatch', $e->reason);
        }
    }

    public function test_plain_http_is_refused_except_for_localhost_in_development(): void
    {
        $env = $this->fullEnv();
        $env['CENTRAL_ORIGIN'] = 'http://central.example.test';

        try {
            $this->config($env)->centralOrigin();
            $this->fail('http must be refused for a remote host.');
        } catch (CentralException $e) {
            $this->assertSame('insecure_url', $e->reason);
        }

        $local = ['CENTRAL_ORIGIN' => 'http://localhost:9000'];
        $this->assertSame(
            'http://localhost:9000',
            $this->config($local + ['TITAN_ALLOW_INSECURE_LOCALHOST' => '1'])->centralOrigin(),
        );

        $this->expectException(CentralException::class);
        $this->config($local + ['TITAN_ALLOW_INSECURE_LOCALHOST' => '0'])->centralOrigin();
    }

    public function test_declared_default_is_returned_without_a_remote_source(): void
    {
        $reader = new FlagReader(['beta' => ['type' => 'boolean', 'default' => false]]);

        $this->assertFalse($reader->read('beta'));
    }

    public function test_undeclared_flag_is_refused(): void
    {
        $reader = new FlagReader(['beta' => ['type' => 'boolean', 'default' => false]]);

        $this->expectException(CentralException::class);
        $reader->read('unknown');
    }

    public function test_remote_error_timeout_or_fallback_returns_the_default(): void
    {
        $declarations = ['limit' => ['type' => 'integer', 'default' => 5, 'min' => 1, 'max' => 50]];

        $throws = new FlagReader($declarations, static function (): array {
            throw new \RuntimeException('timeout');
        });
        $this->assertSame(5, $throws->read('limit'));

        $fallback = new FlagReader($declarations, static fn (): array => ['value' => 20, 'source' => 'fallback']);
        $this->assertSame(5, $fallback->read('limit'));

        $noValue = new FlagReader($declarations, static fn (): array => ['source' => 'override']);
        $this->assertSame(5, $noValue->read('limit'));
    }

    public function test_remote_value_of_the_wrong_type_or_out_of_range_returns_the_default(): void
    {
        $declarations = [
            'limit' => ['type' => 'integer', 'default' => 5, 'min' => 1, 'max' => 50],
            'mode' => ['type' => 'enum', 'default' => 'off', 'allowed' => ['off', 'on']],
        ];

        $wrongType = new FlagReader($declarations, static fn (): array => ['value' => '20', 'source' => 'override']);
        $this->assertSame(5, $wrongType->read('limit'));

        $outOfRange = new FlagReader($declarations, static fn (): array => ['value' => 99, 'source' => 'override']);
        $this->assertSame(5, $outOfRange->read('limit'));

        $notAllowed = new FlagReader($declarations, static fn (): array => ['value' => 'maybe', 'source' => 'rollout']);
        $this->assertSame('off', $notAllowed->read('mode'));
    }

    public function test_valid_remote_value_is_used(): void
    {
        $reader = new FlagReader(
            ['mode' => ['type' => 'enum', 'default' => 'off', 'allowed' => ['off', 'on']]],
            static fn (): array => ['value' => 'on', 'source' => 'override', 'revision' => 3],
        );

        $this->assertSame('on', $reader->read('mode'));
    }

    public function test_a_declared_default_that_does_not_match_its_type_is_refused_at_construction(): void
    {
        $this->expectException(CentralException::class);
        new FlagReader(['limit' => ['type' => 'integer', 'default' => 'five']]);
    }

    public function test_tenant_or_cohort_fields_are_never_sent_to_central(): void
    {
        foreach (['organisation_id', 'tenant', 'cohorts', 'titan_cohorts'] as $field) {
            try {
                ServiceTokenClient::assertNoTenantParams(['query' => ['a' => 1, $field => 'x']]);
                $this->fail("Field {$field} must be refused.");
            } catch (CentralException $e) {
                $this->assertSame('tenant_param_forbidden', $e->reason);
            }
        }

        ServiceTokenClient::assertNoTenantParams(['limit' => 10, 'filter' => ['status' => 'open']]);
        $this->addToAssertionCount(1);
    }

    public function test_service_token_for_another_product_is_refused(): void
    {
        $audience = 'urn:titan-central:product:scheduled';
        $now = 1_800_000_000;
        $token = $this->jwt(['aud' => 'urn:titan-central:product:sorted', 'exp' => $now + 300]);

        try {
            ServiceTokenClient::assertAudience($token, $audience, $now);
            $this->fail('A token for another product must be refused.');
        } catch (CentralException $e) {
            $this->assertSame('wrong_audience', $e->reason);
        }
    }

    public function test_service_token_with_scheduled_audience_and_future_expiry_is_accepted(): void
    {
        $audience = 'urn:titan-central:product:scheduled';
        $now = 1_800_000_000;

        ServiceTokenClient::assertAudience(
            $this->jwt(['aud' => [$audience], 'exp' => $now + 300]),
            $audience,
            $now,
        );
        $this->addToAssertionCount(1);
    }

    public function test_expired_or_opaque_service_token_is_refused(): void
    {
        $audience = 'urn:titan-central:product:scheduled';
        $now = 1_800_000_000;

        $expired = $this->jwt(['aud' => $audience, 'exp' => $now]);
        try {
            ServiceTokenClient::assertAudience($expired, $audience, $now);
            $this->fail('An expired token must be refused.');
        } catch (CentralException $e) {
            $this->assertSame('token_expired', $e->reason);
        }

        try {
            ServiceTokenClient::assertAudience('opaque-token-value', $audience, $now);
            $this->fail('An opaque token must be refused.');
        } catch (CentralException $e) {
            $this->assertSame('token_not_jwt', $e->reason);
        }
    }

    /** @param array<string,mixed> $payload */
    private function jwt(array $payload): string
    {
        $b64 = static fn (string $s): string => rtrim(strtr(base64_encode($s), '+/', '-_'), '=');

        return $b64('{"alg":"none","typ":"JWT"}').'.'.$b64((string) json_encode($payload)).'.sig';
    }
}
