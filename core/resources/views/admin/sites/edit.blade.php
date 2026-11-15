@extends('admin.layout')
@section('content')
@php($businessVertical = \App\Services\Sites\BusinessVerticals::find($document['vertical_key'] ?? null))
@if($businessVertical)<div class="alert alert-info">Business vertical: {{ $businessVertical['name'] }}. Site content can be edited below.</div>@endif

<div class="container"><h1>Edit site: {{ $site->name }}</h1>
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
@if($errors->any())<div role="alert">{{ implode(' ', $errors->all()) }}</div>@endif
<p>Current revision {{ $site->revision }} · {{ $site->published ? 'Published':'Draft only' }}. Draft edits do not change the public site until published.</p>
<a href="{{ route('admin.sites.preview',['id'=>$site->id,'language_id'=>$language]) }}" target="_blank" rel="noopener">Preview draft</a>
<a href="{{ route('admin.sites.leads',['id'=>$site->id,'language_id'=>$language]) }}">Customer enquiries</a>
<a href="{{ route('admin.sites.history',,['id'=>$site->id,'language_id'=>$language]) }}">Version history</a>
@if($site->published)<a href="{{ route('front.sites.show',['language'=>$language,'slug'=>$site->slug]) }}" target="_blank" rel="noopener">Public page</a>@endif
<form data-site-editor data-media-url="{{ route('admin.sites.media',['id'=>$site->id]) }}" data-placement-url="{{ route('admin.sites.preview',['id'=>$site->id,'language_id'=>$language,'placement_picker'=>1]) }}" data-theme-fields='{!! json_encode(\App\Services\Sites\ThemeFields::forTemplate($site->template_key),JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_TAG) !!}' method="post" action="{{ route('admin.sites.update',,['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><input type="hidden" name="revision" value="{{ $site->revision }}">
<label>Site name <input name="name" maxlength="120" value="{{ old('name',$site->name) }}" required></label>
<label>Slug <input name="slug" value="{{ old('slug',$site->slug) }}" required></label>
<input type="hidden" name="template_key" value="{{ $site->template_key }}">
<p><a href="{{ route('admin.sites.brand-agent',['id'=>$site->id,'language_id'=>$language]) }}">Open AI Website Assistant, Branding Agent and image studio</a></p>
<h2>Site branding</h2>
<p>Global brand colours, type, spacing and button shape apply across pages. Preview the draft before publishing.</p>
@php($brandAudit = \App\Services\Sites\SiteBrandAudit::inspect($document,$site->template_key))
<section class="site-brand-audit" aria-label="Brand readiness audit">
<h3>Brand readiness review</h3><p>{{ $brandAudit['high'] }} high-priority and {{ $brandAudit['medium'] }} medium-priority issues found.</p>
<ul>@foreach($brandAudit['issues'] as $issue)<li><strong>{{ ucfirst($issue['level']) }}:</strong> {{ $issue['message'] }}</li>@endforeach</ul>
@if(empty($brandAudit['issues']))<p>No issues found by the automated checks.</p>@endif
</section>
@if(\App\Services\Sites\SiteBookingIntegration::supported((string)$site->template_key))
<section aria-labelledby="site-booking-integration-heading"><h2 id="site-booking-integration-heading">Live Cleanto availability</h2>
@php($bookingProfile = \App\Services\Sites\BookingVerticalPresentation::forVertical($document['vertical_key'] ?? null, (string)$site->template_key))
<div class="alert alert-info" role="note">
<strong>Industry booking profile:</strong> {{ $businessVertical['name'] ?? ucfirst((string)$site->template_key) }}.
This site's live catalogue, staff availability, prices and durations are read from its verified Cleanto instance, not from the website theme.
<ul>
<li>Customer-facing service: {{ $bookingProfile['service'] }}</li>
<li>Available professional: {{ $bookingProfile['staff'] }}</li>
<li>Booking quantity: {{ $bookingProfile['units'] }}</li>
<li>Session type: {{ $bookingProfile['schedule'] }}</li>
</ul>
<p>Before enabling the availability checker, configure the actual services, pricing methods, service units, staff and appointment durations in the Cleanto business administration.</p>
</div>
<p>Booking requests always remain available. An additional live availability checker is shown only after this site's activated custom domain and the Cleanto booking domain independently verify the same hostname. Actual reservation writes remain governed by Cleanto's separate release gates.</p>
<p><a href="{{ route('admin.sites.booking-services.edit',['id'=>$site->id,'language_id'=>$language]) }}">Configure website services from the real Cleanto catalogue</a></p>
@php($bookingHosts = $site->tenant_id ? \App\Services\Sites\SiteBookingIntegration::availableHosts($site) : [])
@php($bookingActive = $site->tenant_id && \App\Services\Sites\SiteBookingIntegration::active($site,request()->getHost()))
<p>Booking domain status: <strong>{{ count($bookingHosts) ? 'Verified domains available' : 'No verified matching site and booking domains' }}</strong>. Checker: <strong>{{ $bookingActive ? 'Enabled' : 'Not enabled' }}</strong>.</p>
@if($site->tenant_id && \App\Services\Tenants\TenantAccess::permitted((int)$site->tenant_id,'owner'))
<form method="post" action="{{ route('admin.sites.booking-availability',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><label>Verified booking hostname <select name="hostname">
<option value="">Select a verified shared site/booking domain</option>
@foreach($bookingHosts as $host => $owner)<option value="{{ $host }}">{{ $host }}</option>@endforeach
</select></label><label>Availability checker
<select name="enabled"><option value="0" {{ !$bookingActive ? 'selected' : '' }}>Use appointment requests only</option><option value="1" {{ $bookingActive ? 'selected' : '' }}>Show verified Cleanto availability checker</option></select></label>
<button type="submit">Save booking settings</button></form>
@else<p>Assign this site to an active business tenant and sign in as its owner to configure booking availability.</p>@endif</section>
@endif
<h2>Edit website content</h2><p>Upload images directly, or paste URLs, and insert additional images before or after any visible page element. All template headings, labels and imported images listed below can be overridden and saved per site. Save draft, then publish to update the public website.</p><div class="site-editor-toolbar" data-site-toolbar></div><div data-site-fields class="site-editor-fields"></div><label>Site content (JSON document) <textarea rows="24" cols="95" name="document" maxlength="262144" required>{{ old('document',json_encode($document,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)) }}</textarea></label>
<button type="submit">Save draft</button></form>
<form method="post" action="{{ route('admin.sites.publish',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><input type="hidden" name="revision" value="{{ $site->revision }}">
<button type="submit" name="publish" value="1">Publish current draft</button><button type="submit" name="publish" value="0">Unpublish</button></form>
<section aria-labelledby="site-duplicate-heading"><h2 id="site-duplicate-heading">Duplicate this website</h2><p>Create an independent unpublished draft with all of this site's current draft content.</p>
<form method="post" action="{{ route('admin.sites.duplicate',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><label>New name <input name="name" required minlength="2" maxlength="120" placeholder="Copy of {{ $site->name }}"></label>
<label>New slug <input name="slug" required maxlength="100" pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="new-site-slug"></label><button type="submit">Duplicate as draft</button></form></section>
<style>.site-editor-toolbar{display:flex;gap:.5rem;flex-wrap:wrap;margin:1rem 0}.site-editor-toolbar button{padding:.5rem}.site-editor-fields{display:grid;gap:1rem;max-width:850px}.site-editor-row img{max-width:220px;max-height:140px;object-fit:contain}.site-editor-image-row{border:1px solid #cbd5e1;padding:12px;margin:8px 0}.site-editor-error{color:#ae2424}.site-editor-fields fieldset{border:1px solid #cbd5e1;padding:1rem;border-radius:8px}.site-editor-row{margin:1rem 0;padding:1rem;border:1px solid #ddd;border-radius:8px}.site-editor-field{display:grid;gap:.3rem;margin:.5rem 0}.site-editor-field input,.site-editor-field textarea{width:100%;padding:.5rem;box-sizing:border-box}</style>
<script src="{{ asset('assets/sites/site-editor.js') }}" defer></script>
</div>
@endsection
