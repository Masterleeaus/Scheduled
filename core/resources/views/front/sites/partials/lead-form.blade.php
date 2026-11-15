<section class="site-extras site-contact" id="{{ \App\Services\Sites\VerticalBooking::enabled($site->template_key) ? 'booking' : 'enquiry' }}"><div class="wrap">
@php($verticalBooking = \App\Services\Sites\VerticalBooking::enabled($site->template_key))
@php($bookingPresentation = $verticalBooking ? \App\Services\Sites\VerticalBooking::presentation($site->template_key,$document['vertical_key'] ?? null) : null)
@if($verticalBooking)<h2>{{ \App\Services\Sites\ThemeFields::text($document,'copy_06',$bookingPresentation['title']) }}</h2><p>{{ \App\Services\Sites\ThemeFields::text($document,'copy_07',$bookingPresentation['intro']) }}</p>
@else<h2>Request a cleaning quote</h2>@endif
@if($preview)<p class="site-note" role="status">Preview: appointment requests and enquiries are disabled until this website is published.</p>
@else
@if(session('site_lead_success'))<p class="site-success" role="status">{{ session('site_lead_success') }}</p>@endif
@if($errors->has('site_lead'))<p class="site-error" role="alert">{{ $errors->first('site_lead') }}</p>@endif
<form method="post" action="{{ route('front.sites.lead',['language'=>$site->language_id,'slug'=>$site->slug]) }}" class="site-lead-form">
@csrf
<div class="site-form-grid"><label>Your name<input name="name" maxlength="120" autocomplete="name" required></label>
<label>Email address<input type="email" name="email" maxlength="160" autocomplete="email" required></label>
<label>Phone (optional)<input type="tel" name="phone" maxlength="40" autocomplete="tel"></label>
<label>Service required<select name="service"><option value="">Choose a service</option>@foreach(($document['services']??[]) as $service)<option value="{{ $service['name'] }}">{{ $service['name'] }}</option>@endforeach</select></label></div>
@foreach(['utm_source','utm_medium','utm_campaign'] as $tracking)<input type="hidden" name="{{ $tracking }}" value="{{ preg_match('/^[A-Za-z0-9_.-]{1,100}$/D',(string)request()->query($tracking,'')) ? request()->query($tracking) : '' }}">@endforeach
@if(count(\App\Services\Sites\SiteMarketing::activePromotions($document,now()->toDateString())))<label>Offer code (optional)<select name="promotion_code"><option value="">No offer</option>@foreach(\App\Services\Sites\SiteMarketing::activePromotions($document,now()->toDateString()) as $offer)<option value="{{ $offer['code'] }}">{{ $offer['code'] }}</option>@endforeach</select></label>@endif
@if($verticalBooking)
<div class="site-booking-schedule" role="group" aria-label="Appointment preferences">
<label>Preferred date<input type="date" name="preferred_date" min="{{ now()->toDateString() }}" required></label>
<label>Preferred time<input type="time" name="preferred_time" required></label>
<label>{{ $bookingPresentation['subject'] }} (optional)<input name="booking_subject" maxlength="160" placeholder="{{ $bookingPresentation['subject_hint'] }}"></label>
</div>
<p class="site-note">This form sends an appointment request. Your appointment is not confirmed until the business responds with a booking confirmation.</p>
@endif
<label>{{ $verticalBooking ? $bookingPresentation['notes'] : 'What would you like cleaned?' }}<textarea name="message" rows="4" maxlength="3000" required></textarea></label>
<div class="site-honeypot" aria-hidden="true"><label>Leave this empty<input name="website" tabindex="-1" autocomplete="off"></label></div>
<label class="site-consent"><input type="checkbox" name="consent" value="1" required> I agree to this business using these details to respond to my enquiry.</label>
<button class="site-submit" type="submit">{{ $verticalBooking ? $bookingPresentation['action'] : 'Send enquiry' }}</button></form>
@endif</div></section>
