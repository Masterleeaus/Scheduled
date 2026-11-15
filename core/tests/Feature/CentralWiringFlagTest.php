<?php

namespace Tests\Feature;

use App\Central\Providers\CentralServiceProvider;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

/**
 * Proves the Central wiring is gated by CENTRAL_INTEGRATION_ENABLED (default off).
 *
 * Flag off: the provider is not registered and no request is handled by a Central controller. The app has a
 * pre-existing catch-all fallback route, so an /api/central path may still be answered by that unchanged route.
 * Flag on: the provider is registered and the Central routes respond.
 */
class CentralWiringFlagTest extends TestCase
{
    private const FLAG = 'CENTRAL_INTEGRATION_ENABLED';
    private const SECRET = 'CENTRAL_EVENTS_HMAC_SECRET';

    protected function tearDown(): void
    {
        $this->setEnv(self::FLAG, null);
        $this->setEnv(self::SECRET, null);
        parent::tearDown();
    }

    public function test_flag_unset_defaults_to_off(): void
    {
        $this->bootWith(null, str_repeat('s', 40));

        $this->assertFalse(config('central.integration_enabled'));
        $this->assertFalse(app()->providerIsLoaded(CentralServiceProvider::class));
    }

    /** @dataProvider offValues */
    public function test_flag_off_registers_no_provider_and_no_named_routes(string $flag): void
    {
        $this->bootWith($flag, str_repeat('s', 40));

        $this->assertFalse(app()->providerIsLoaded(CentralServiceProvider::class));
        $this->assertFalse(app('router')->getRoutes()->hasNamedRoute('central.status'));
    }

    /** @dataProvider centralRequests */
    public function test_flag_off_no_request_reaches_a_central_handler(string $method, string $path): void
    {
        $this->bootWith(null, str_repeat('s', 40));

        $this->assertNull($this->matchedCentralAction($method, $path));
    }

    /** @dataProvider centralRequests */
    public function test_flag_on_request_reaches_its_central_handler(string $method, string $path): void
    {
        $this->bootWith('true', str_repeat('s', 40));

        $this->assertTrue(app()->providerIsLoaded(CentralServiceProvider::class));
        $this->assertNotNull($this->matchedCentralAction($method, $path));
    }

    public function test_flag_on_status_route_responds_over_http(): void
    {
        $this->bootWith('true', str_repeat('s', 40));

        $this->getJson('/api/central/status')
            ->assertOk()
            ->assertJson([
                'integration' => 'central',
                'enabled' => true,
                'scope' => 'single-instance',
            ]);
    }

    public function offValues(): array
    {
        return [['false'], ['0'], ['']];
    }

    public function centralRequests(): array
    {
        return [['GET', '/api/central/status']];
    }

    /**
     * Returns the action name of the route that would handle the request if it is a Central route,
     * or null when the request is matched by something else or by nothing.
     */
    private function matchedCentralAction(string $method, string $path): ?string
    {
        try {
            $route = app('router')->getRoutes()->match(Request::create($path, $method));
        } catch (NotFoundHttpException|MethodNotAllowedHttpException) {
            return null;
        }
        $action = $route->getActionName();

        return str_contains((string) $action, 'CentralController') ? $action : null;
    }

    private function bootWith(?string $flag, ?string $secret): void
    {
        $this->setEnv(self::FLAG, $flag);
        $this->setEnv(self::SECRET, $secret);
        $this->refreshApplication();
        // The test database has no storage/installed marker, so the installer redirect would answer every
        // request with 302. Skip only that global gate; the Central routes are what these tests exercise.
        $this->withoutMiddleware(\App\Http\Middleware\RedirectUninstalledToInstaller::class);
    }

    private function setEnv(string $name, ?string $value): void
    {
        if ($value === null) {
            unset($_ENV[$name], $_SERVER[$name]);
            putenv($name);

            return;
        }
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        putenv($name.'='.$value);
    }
}
