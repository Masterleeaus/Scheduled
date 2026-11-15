<?php
declare(strict_types=1);

namespace App\Central\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Handlers for routes/central.php. Only reachable when the Central flag is on.
 * Serves the status endpoint only. Inbound events are handled in App\Http\Controllers\Central.
 */
final class CentralController extends Controller
{
    public function status(): JsonResponse
    {
        return response()->json([
            'integration' => 'central',
            'enabled' => true,
            'scope' => 'single-instance',
        ]);
    }
}
