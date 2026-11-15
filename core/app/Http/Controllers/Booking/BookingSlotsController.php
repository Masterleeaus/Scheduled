<?php

namespace App\Http\Controllers\Booking;

use App\Booking\BookingFacade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use RuntimeException;

/** Not routed until existing PlusAgency auth/tenant roles are formally mapped. */
final class BookingSlotsController extends Controller
{
    public function __invoke(Request $request, BookingFacade $bookings): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        try {
            $data = $bookings->slots((int) $user->getAuthIdentifier(), $request->only(['date', 'service_id']));
            return response()->json($data);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            report($e);
            return response()->json(['error' => 'Booking service temporarily unavailable'], 503);
        }
    }
}
