<?php
declare(strict_types=1);

namespace App\Central\Http;

use App\Central\CentralException;
use App\Central\Config\CentralConfig;
use App\Central\Events\EventConsumer;
use App\Central\Events\ProcessedEventStore;
use App\Central\Events\SignedEventVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Handlers for routes/central.php. Only reachable when the Central flag is on.
 *
 * SINGLE-INSTANCE ONLY: events are de-duplicated through the process-local store bound by the provider.
 * No handlers are registered, so every verified event is rejected as unsupported and nothing is applied.
 */
final class CentralController extends Controller
{
    public function status(): JsonResponse
    {
        return response()->json([
            'integration' => 'central',
            'enabled' => true,
            'scope' => 'single-instance',
            'eventStore' => 'in-memory',
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $body = $request->getContent();

        try {
            $secret = (new CentralConfig())->eventsSecret();
            $consumer = new EventConsumer(
                new SignedEventVerifier($secret),
                app(ProcessedEventStore::class),
                'scheduled',
                [],
            );
            $result = $consumer->handle($body, time());
        } catch (CentralException $e) {
            return match ($e->reason) {
                'config_missing', 'config_invalid' => response()->json(['error' => 'not_configured'], 503),
                'body_too_large', 'payload_too_large' => response()->json(['error' => 'too_large'], 413),
                'unsupported_event' => response()->json(['error' => 'unsupported_event'], 422),
                default => response()->json(['error' => 'rejected'], 401),
            };
        }

        return response()->json(['result' => $result]);
    }
}
