@extends('user.layout')
@section('content')
<div class="container py-4"><h1>Booking management</h1>
<p>Bookings are handled by the private Cleanto scheduling engine. Payments and recurring booking creation require separate setup.</p>
<div id="titan-booking-notice" role="status"></div>
<form id="titan-booking-create" class="mb-4">
<h2>Create single appointment</h2>
<p>Service IDs, method IDs and staff IDs must be configured in Cleanto and linked to this business. Payment is not collected here.</p>
<div class="row">
<div class="col-md-3"><label>PlusAgency service ID<input name="service_id" type="number" min="1" required class="form-control"></label></div>
<div class="col-md-3"><label>Cleanto method ID<input name="method_id" type="number" min="1" required class="form-control"></label></div>
<div class="col-md-3"><label>Cleanto unit ID<input name="method_unit_id" type="number" min="1" required class="form-control"></label></div>
<div class="col-md-3"><label>Quantity<input name="quantity" type="number" min="1" step="1" value="1" required class="form-control"></label></div>
<div class="col-md-4"><label>Staff IDs (comma-separated)<input name="staff_ids" required class="form-control"></label></div>
<div class="col-md-4"><label>Start date/time<input name="start_at" type="datetime-local" required class="form-control"></label></div>
<div class="col-md-4"><label>Duration (minutes)<input name="duration_minutes" type="number" min="15" max="720" step="5" value="60" required class="form-control"></label></div>
<div class="col-md-4"><label>Timezone<input name="timezone" value="Australia/Melbourne" required class="form-control"></label></div>
<div class="col-md-4"><label>Customer name<input name="name" required class="form-control"></label></div>
<div class="col-md-4"><label>Customer email<input name="email" type="email" required class="form-control"></label></div>
<div class="col-md-4"><label>Customer phone<input name="phone" type="tel" required class="form-control"></label></div>
</div><button class="btn btn-primary mt-2" type="submit">Create unpaid booking</button>
</form>
<form id="titan-booking-filters" class="mb-3" aria-label="Filter bookings"><div class="row">
<div class="col-md-2"><label>From date<input class="form-control" type="date" name="from"></label></div>
<div class="col-md-2"><label>To date<input class="form-control" type="date" name="to"></label></div>
<div class="col-md-2"><label>Status<input class="form-control" name="status" maxlength="12" placeholder="All statuses"></label></div>
<div class="col-md-2"><label>Staff ID<input class="form-control" type="number" min="1" name="staff_id" placeholder="All staff"></label></div>
<div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-primary mr-2">Apply filters</button><button type="reset" class="btn btn-outline-secondary mr-2">Clear</button><a href="{{ route('user.booking.manage.export') }}" id="titan-booking-export" class="btn btn-outline-primary">Export CSV</a></div>
</div></form><div id="titan-booking-summary" role="status" aria-live="polite" class="mb-3"></div>
<button id="titan-bookings-reload" type="button" class="btn btn-primary">Refresh bookings</button>
<table class="table" id="titan-bookings-table"><thead><tr><th>ID</th><th>Customer</th><th>Date</th><th>Status</th><th>Staff</th><th>Actions</th></tr></thead><tbody></tbody></table>
</div>
<script>window.TitanOwnerBooking={list:@json(route('user.booking.manage.list')),summary:@json(route('user.booking.manage.summary')),export:@json(route('user.booking.manage.export')),base:@json(url('/user/booking/manage')),csrf:@json(csrf_token())};</script>
<script src="{{ asset('assets/user/js/titan-booking-owner.js') }}" defer></script>
@endsection
