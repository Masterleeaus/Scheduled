<?php

namespace App\Http\Controllers\Central;

use App\Central\CentralException;
use App\Central\Config\CentralConfig;
use App\Central\Events\DatabaseProcessedEventStore;
use App\Central\Events\EventConsumer;
use App\Central\Events\EventIngress;
use App\Central\Events\SignedEventVerifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Receives signed Central outbox envelopes. The route is registered only when CENTRAL_EVENTS_ENABLED is set.
 * With no handlers registered yet, every verified event is refused as unsupported_event.
 */
class CentralEventController extends Controller
{
    public function receive(Request $request): JsonResponse
    {
        $config = new CentralConfig();
        if (!$config->eventsEnabled()) {
            return response()->json(['error' => 'not_found'], 404);
        }
        try {
            $ingress = new EventIngress(
                new EventConsumer(
                    new SignedEventVerifier($config->eventsSecret()),
                    new DatabaseProcessedEventStore(DB::connection()->getPdo()),
                    CentralConfig::SCHEDULED_PRODUCT_ID,
                    [],
                    $config->eventsOrganisationId(),
                ),
                true,
            );
        } catch (CentralException $e) {
            return response()->json(['error' => $e->reason], 503);
        }
        [$status, $body] = $ingress->handle($request->getContent(), time());

        return response()->json($body, $status);
    }
}
