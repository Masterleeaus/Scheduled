@extends('vendor.installer.layouts.master')
@section('template_title', 'Create administrator')
@section('title')
    <i class="fa fa-user-shield fa-fw" aria-hidden="true"></i> Create the administrator
@endsection
@section('container')
    <p>The demo administrator and user accounts are not imported. Create your own secure administrator before opening the application.</p>
    @if ($errors->any())
        <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="post" action="{{ route('LaravelInstaller::administratorSave') }}" autocomplete="off">
        @csrf
        <div class="form-group"><label for="first_name">First name</label><input id="first_name" name="first_name" required maxlength="100" value="{{ old('first_name') }}"></div>
        <div class="form-group"><label for="last_name">Last name</label><input id="last_name" name="last_name" required maxlength="100" value="{{ old('last_name') }}"></div>
        <div class="form-group"><label for="email">Administrator email</label><input id="email" name="email" type="email" required maxlength="255" value="{{ old('email') }}"></div>
        <div class="form-group"><label for="password">New password (at least 14 characters)</label><input id="password" name="password" type="password" required minlength="14" maxlength="128" autocomplete="new-password"></div>
        <div class="form-group"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" required minlength="14" maxlength="128" autocomplete="new-password"></div>
        <div class="buttons"><button class="button" type="submit">Create administrator securely</button></div>
    </form>
@endsection
