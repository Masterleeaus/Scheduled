<?php
namespace App\Http\Controllers\Booking;
use App\Booking\BookingHealth;
use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, JsonResponse};
use RuntimeException;
/** Not publicly routed until PlusAgency auth guards are verified. */
final class BookingServicesController extends Controller {
    public function __invoke(Request $request, BookingHealth $booking): JsonResponse {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);
        try { return response()->json($booking->services((int)$user->getAuthIdentifier())); }
        catch (RuntimeException $e) { report($e); return response()->json(['error' => 'Booking services unavailable'], 503); }
    }
}
