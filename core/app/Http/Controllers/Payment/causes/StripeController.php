<?php

namespace App\Http\Controllers\Payment\causes;

use App\DonationDetail;
use App\Http\Controllers\Front\CausesController;
use App\Http\Controllers\Front\EventController;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\Donation;
use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
use Illuminate\Support\Facades\Session;
use PDF;
use Symfony\Component\HttpFoundation\Response;

class StripeController extends Controller
{
    public function __construct($payment_method)
    {
        // Resolve database-backed gateway/site configuration only for actual HTTP requests.
        // Route inspection and fresh-install Artisan commands must remain database-free.
        $this->middleware(function ($request, $next) {
        $stripe = PaymentGateway::where('name', $payment_method)->first();
        $stripeConf = json_decode($stripe->information, true);
        Config::set('services.stripe.key', $stripeConf["key"]);
        Config::set('services.stripe.secret', $stripeConf["secret"]);
    
            return $next($request);
        });
    }

    public function processPayment(Request $request, $amount, $actualAmount, $description, $bex, $be)
    {
        $stripe = Stripe::make(Config::get('services.stripe.secret'));
        try {
            $token = $request->stripeToken;
            if (!isset($token)) {
                return back()->with('error', 'Token Problem With Your Token.');
            }
            $charge = $stripe->charges()->create([
                'source' => $token,
                'currency' => "USD",
                'amount' => $amount,
                'description' => $description,
                'receipt_email' => $request->billing_email,
                'metadata' => [
                    'customer_name' => $request->billing_fname != null ? $request->billing_fname : '',
                ]
            ]);
            if ($charge['status'] == 'succeeded') {
                $paymentFor = Session::get('paymentFor');
                $amount = $amount * $bex->base_currency_rate;
                if ($paymentFor == "Cause") {
                    $cause = new CausesController;
                    $donation = $cause->store($request->all(), $charge["id"], json_encode($charge), $actualAmount, $bex);
                    if (!is_null($request->email)) {
                        $file_name = $cause->makeInvoice($donation);
                        $cause->sendMailPHPMailer($request, $file_name, $be);
                    }
                    session()->flash('success', __('Payment completed!'));
                    Session::forget('paymentFor');
                    return redirect()->route('front.cause_details', [$request->donation_slug]);
                } elseif ($paymentFor == "Event") {
                    $event = new EventController;
                    $event_details = $event->store($request->all(), $charge["id"], json_encode($charge), $actualAmount, $bex);
                    $file_name = $event->makeInvoice($event_details);
                    $event->sendMailPHPMailer($request, $file_name, $be);
                    session()->flash('success', __('Payment completed! We send you an email'));
                    Session::forget('paymentFor');
                    return redirect()->route('front.event_details', [$request->event_slug]);
                }
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Cartalyst\Stripe\Exception\CardErrorException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Cartalyst\Stripe\Exception\MissingParameterException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
        return redirect()->back()->with('error', __('Something went wrong.Please recheck'))->withInput();
    }
}
