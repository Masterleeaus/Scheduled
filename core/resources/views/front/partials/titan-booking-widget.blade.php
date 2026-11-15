{{-- Explicitly read-only until MariaDB transactions, payment security and release gates pass. --}}
<section class="titan-booking-widget" data-titan-booking-widget data-catalogue-endpoint="/titan-booking/catalogue" data-mapped-service-ids="{{ implode(',', $mappedCleantoServiceIds ?? []) }}" data-preview-has-slots="false" data-service-prompt="{{ 'Select ' . strtolower($verticalBookingLabels['service'] ?? 'cleaning service') }}" data-staff-prompt="{{ $verticalBookingLabels['staff_prompt'] ?? 'Choose a professional' }}" data-staff-noun="{{ strtolower($verticalBookingLabels['staff'] ?? 'professional') }}" data-booking-submit-enabled="{{ !request()->routeIs('user.booking.preview') && \App\Booking\Public\Checkout\CheckoutTenantReadiness::eligibleForHost(
    config('booking_internal.public_commit_enabled', false) === true,
    app()->bound(\App\Booking\Public\Checkout\CheckoutSubmissionExecutor::class),
    extension_loaded('pdo_mysql'),
    static function () { return app(\App\Booking\Public\PublicDomainRegistry::class)->ownerForHost(request()->getHost()); },
    config('booking_internal.public_commit_tenants', [])
) ? 'true' : 'false' }}" aria-labelledby="titan-booking-title">
 <div class="titan-booking-heading"><h2 id="titan-booking-title">{{ $verticalBookingLabels['title'] ?? 'Check availability' }}</h2><span class="titan-preview-tag" data-booking-readiness-label>Availability checker</span></div>
 <p>Choose a {{ strtolower($verticalBookingLabels['service'] ?? 'service') }}, {{ strtolower($verticalBookingLabels['staff'] ?? 'professional') }} and preferred date to check available times. Availability does not guarantee a reservation.</p>
 <form data-booking-form>
  @csrf
  <label>{{ $verticalBookingLabels['service'] ?? 'Service' }}<select name="service_id" autocomplete="off" required disabled><option value="">Loading services…</option></select></label>
  <label>{{ $verticalBookingLabels['pricing'] ?? 'Pricing method' }}<select name="method_id" autocomplete="off" required disabled><option value="">Choose service first</option></select></label>
  <label>{{ $verticalBookingLabels['size'] ?? 'Service option' }}<select name="method_unit_id" autocomplete="off" required disabled><option value="">Choose method first</option></select></label>
  <label>{{ $verticalBookingLabels['units'] ?? 'Number of units' }}<input name="quantity" type="number" min="1" max="100" step="1" value="1" required></label>
  <label>{{ $verticalBookingLabels['staff'] ?? 'Professional' }}<select name="staff_ids" autocomplete="off" required disabled><option value="">Loading professionals…</option></select></label>
  <label>Preferred date<input name="date" type="date" required></label>
  <label>{{ $verticalBookingLabels['duration'] ?? 'Duration (minutes)' }}<select name="duration_minutes" required><option value="30">30</option><option value="60" selected>60</option><option value="90">90</option><option value="120">120</option><option value="180">180</option><option value="240">240</option><option value="360">360</option><option value="480">480</option></select></label>
  <label>Time zone<input name="timezone" value="Australia/Melbourne" required readonly></label>
  <div class="titan-booking-actions"><button type="submit">Check available times</button><button type="button" class="titan-booking-reset" data-booking-reset>Start again</button></div>
 </form>
 <p role="status" aria-live="polite" data-booking-status>Loading services…</p>
 <h3 id="titan-available-times">Available start times</h3><ul data-booking-slots aria-labelledby="titan-available-times"></ul>
 <section class="titan-booking-intent" data-booking-intent-panel hidden aria-labelledby="booking-intent-title">
  <h3 id="booking-intent-title">Selected appointment time</h3>
  <p data-booking-selected-time></p>
  <button type="button" data-booking-intent-button disabled>Continue with selected time</button>
  <p role="status" aria-live="polite" data-booking-intent-status>Choose one of the listed times to continue.</p>
  <form data-booking-checkout-form hidden autocomplete="on">
   <label>First name<input name="first_name" autocomplete="given-name" maxlength="80" required></label>
   <label>Last name<input name="last_name" autocomplete="family-name" maxlength="80" required></label>
   <label>Email<input name="email" type="email" autocomplete="email" maxlength="200" required></label>
   <label>Phone<input name="phone" type="tel" autocomplete="tel" maxlength="32" required></label>
   <label>{{ $verticalBookingLabels['address'] ?? 'Service location' }}<input name="address" autocomplete="street-address" maxlength="250" required></label>
   <label>City or suburb<input name="city" autocomplete="address-level2" maxlength="100" required></label>
   <label>State or territory<input name="state" autocomplete="address-level1" maxlength="100" required></label>
   <label>Postcode<input name="zip" autocomplete="postal-code" maxlength="24" required></label>
   <label>Notes (optional)<textarea name="notes" maxlength="1000"></textarea></label>
   <label><input type="checkbox" name="consent" required> I agree to be contacted about this booking.</label>
   <button type="submit">Continue with booking details</button>
   <p role="status" aria-live="polite" data-booking-checkout-status></p>
  </form>
  <p data-booking-intent-disclaimer>Availability and checkout checks do not themselves reserve a slot. Only a server-issued booking reference confirms a reservation. Payment status is separate.</p>
 </section>
 <p class="preview-warning" data-booking-readiness-message aria-live="polite">Times are indicative until a reservation has been confirmed by the business system. No payment is confirmed by this widget.</p>
</section>
