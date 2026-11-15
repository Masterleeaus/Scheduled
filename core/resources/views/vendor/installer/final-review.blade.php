@extends('vendor.installer.layouts.master')
@section('template_title', 'Review and complete installation')
@section('title')
    <i class="fa fa-check-circle fa-fw" aria-hidden="true"></i> Complete setup
@endsection
@section('container')
    <p>The initial database has been imported and your administrator has been created.</p>
    <p>Completing setup validates the required account tables, the administrator, encryption key and writable storage before creating the installed marker.</p>
    <p><strong>Staging first:</strong> This step does not certify live Cleanto bookings, payments, domain ownership or every website template.</p>
    <form method="post" action="{{ route('LaravelInstaller::finalComplete') }}">
        @csrf
        <div class="buttons"><button type="submit" class="button">Verify and complete installation</button></div>
    </form>
@endsection
