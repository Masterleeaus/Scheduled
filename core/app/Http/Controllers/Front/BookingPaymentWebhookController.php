<?php
declare(strict_types=1);
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Booking\Public\Payment\StripeBookingWebhookGuard;
use App\Booking\Public\Payment\PaymentEventStorage;
use Illuminate\Http\Request;
final class BookingPaymentWebhookController extends Controller {
    public function stripe(Request $request, PaymentEventStorage $storage) {
        try {
            $cfg=config('booking_payment',[]);
            StripeBookingWebhookGuard::enabled($cfg);
            $raw=$request->getContent();
            $signed=(string)$request->header('Stripe-Signature','');
            $candidate=StripeBookingWebhookGuard::verify($raw,$signed,$cfg['webhook_secret'],time());
            $result=$storage->record($candidate);
            return response()->json(['received'=>true,'receipt'=>$result['receipt'],'settled'=>false,'booking_confirmed'=>false],200)
                ->header('Cache-Control','no-store');
        } catch(\InvalidArgumentException|\DomainException $e) {
            return response()->json(['received'=>false],400)->header('Cache-Control','no-store');
        } catch(\Throwable $e) {
            report($e);
            // Let Stripe retry transient storage and configuration failures.
            return response()->json(['received'=>false],503)->header('Cache-Control','no-store');
        }
    }
}
