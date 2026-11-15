<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Booking\BookingWidgetPreviewGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
final class BookingWidgetPreviewController extends Controller {
 public function preview(Request $request, BookingWidgetPreviewGateway $gateway) {
  $owner=Auth::id();
  if (!$owner) return response()->json(['error'=>'Authentication required'],401);
  try {return response()->json($gateway->forOwner((int)$owner,$request->all()));}
  catch (\InvalidArgumentException $e) {return response()->json(['error'=>'Invalid booking preview request'],422);}
  catch (\Throwable $e) {report($e);return response()->json(['error'=>'Availability preview unavailable'],503);}
 }
}
