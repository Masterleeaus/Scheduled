@extends('vendor.installer.layouts.master')
@section('template_title', 'Installation completed')
@section('title')
    <i class="fa fa-check-circle fa-fw" aria-hidden="true"></i> Installation completed
@endsection
@section('container')
    <p>The account and environment checks passed, and the installation marker was written.</p>
    <p><strong>Next:</strong> Sign in with the administrator account you created and verify each configured website, booking, email, payment and tenant integration on staging before going live.</p>
    <p>No default login is provided. Secrets and the contents of <code>.env</code> are never displayed.</p>
    <p>For server prerequisites, run <code>php tools/install-preflight.php</code> from the extracted application root before installation. The separate live acceptance checks are still required.</p>
    <div class="buttons"><a href="{{ url('/') }}" class="button">Open application</a></div>
@endsection
