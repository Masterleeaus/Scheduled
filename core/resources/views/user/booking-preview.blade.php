@extends('user.layout')
@section('content')
<p><a href="{{ route('user.booking.manage') }}">Manage bookings</a></p>
<div class="container py-4"><h2>Booking availability preview</h2>
<p>Owner-only preview. No appointment is reserved or paid for.</p>
@include('front.partials.titan-booking-assets')
@include('front.partials.titan-booking-widget')
</div>
<script>document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('[data-titan-booking-widget]').forEach(function(el){el.setAttribute('data-preview-endpoint',@json(route('user.booking.preview')));el.setAttribute('data-catalogue-endpoint',@json(route('user.booking.catalogue')));});});</script>
@endsection
