<?php
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Booking\Public\PublicCatalogueGateway;
use App\Booking\Public\PublicCatalogueHeaders;
use App\Booking\Public\PublicCatalogEnvelope;
use Illuminate\Http\Request;
final class BookingPublicCatalogueController extends Controller {
 public function index(Request $request,PublicCatalogueGateway $gateway){
  try {$catalogue=PublicCatalogEnvelope::build($gateway->forHost($request->getHost()));return PublicCatalogueHeaders::apply(response()->json($catalogue));}
  catch(\Throwable $e){report($e);return PublicCatalogueHeaders::apply(response()->json(['error'=>'Catalogue unavailable'],404));}
 }
}
