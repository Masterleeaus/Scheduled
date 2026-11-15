<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Booking\BookingCatalogueGateway;
use Illuminate\Support\Facades\Auth;
final class BookingCatalogueController extends Controller {
 public function index(BookingCatalogueGateway $gateway) {
  if (!Auth::check()) return response()->json(['error'=>'Authentication required'],401);
  try {return response()->json($gateway->forOwner((int)Auth::id()))->header('Cache-Control','private, no-store');}
  catch (\Throwable $e) {report($e);return response()->json(['error'=>'Booking catalogue unavailable'],503)->header('Cache-Control','no-store');}
 }
}
