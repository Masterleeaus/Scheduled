@extends('admin.layout')
@section('content')
<div class="container">
<h1>Map real Cleanto services: {{ $site->name }}</h1>
<p>This page reads the configured Cleanto instance. It does not create placeholder services or modify booking prices or staff schedules.</p>
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
@if($errors->any())<div role="alert">{{ implode(' ', $errors->all()) }}</div>@endif
<p>Services available: {{ count($catalogue['services'] ?? []) }}. Pricing methods: {{ count($catalogue['methods'] ?? []) }}. Service units: {{ count($catalogue['units'] ?? []) }}. Staff: {{ count($catalogue['staff'] ?? []) }}.</p>
@if($selected && count($valid)!==count($selected))<p role="alert">Some previously mapped services are no longer present in Cleanto. Review and save a new selection.</p>@endif

<section aria-labelledby="cleanto-service-readiness">
<h2 id="cleanto-service-readiness">Service configuration assessment</h2>
<p><strong>{{ $readiness['ready_for_admin_review'] ? 'Catalogue configuration checks passed' : 'Configuration review required' }}</strong> — catalogue checks do not authorise booking writes or payments.</p>
@if($readiness['issues'])<div role="alert"><h3>Issues to resolve in Cleanto</h3><ul>@foreach($readiness['issues'] as $issue)<li>{{ $issue }}</li>@endforeach</ul></div>@endif
<table><caption>Verified Cleanto service configuration evidence</caption><thead><tr><th scope="col">Service</th><th scope="col">Pricing methods</th><th scope="col">Service units</th><th scope="col">Catalogue assessment</th></tr></thead><tbody>
@forelse($readiness['services'] as $row)
<tr><th scope="row">{{ $row['name'] }}</th><td>{{ $row['method_count'] }}</td><td>{{ $row['unit_count'] }}</td><td>{{ $row['status'] === 'catalogue_complete' ? 'Catalogue structure complete' : implode('; ', $row['issues']) }}</td></tr>
@empty<tr><td colspan="4">No service mappings saved yet.</td></tr>@endforelse
</tbody></table>
<h3>What still needs live Cleanto verification</h3><ul>@foreach($readiness['limitations'] as $limitation)<li>{{ $limitation }}</li>@endforeach</ul>
<p>Open your verified Cleanto administration to create or edit services, assign eligible staff, set working hours, durations and prices. Refresh this page after making changes to re-read the live catalogue.</p>
</section>

<form method="post" action="{{ route('admin.sites.booking-services.update',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $input['language_id'] }}">
<fieldset><legend>Select the actual Cleanto services offered on this website</legend>
@foreach(($catalogue['services'] ?? []) as $service)
<label style="display:block"><input type="checkbox" name="service_ids[]" value="{{ $service['id'] }}" {{ in_array((int)$service['id'],$valid,true)?'checked':'' }}> {{ $service['label'] }} (ID {{ $service['id'] }})</label>
@endforeach
</fieldset><button type="submit">Save service mapping</button></form>
<section aria-labelledby="native-service-management">
<h2 id="native-service-management">Native Cleanto service details</h2>
<p>These forms write to the actual tenant Cleanto database via the signed private connector. They are disabled by default until a per-tenant administrator enables service management. New services start <strong>disabled</strong>; prices, methods, units, staff assignments and working hours must be configured and checked before activation.</p>
<form method="post" action="{{ route('admin.sites.cleanto-service-admin.save',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $site->language_id }}">
<label>Service name <input type="text" name="title" maxlength="120" required></label>
<label>Description <textarea name="description" maxlength="2000"></textarea></label>
<button type="submit">Create disabled Cleanto service</button>
</form>
<h3>Edit existing service descriptions</h3>
@if(!$serviceManagementAvailable)<p role="status">Native service management is disabled for this tenant.</p>@endif
@foreach(($managed ?? []) as $service)
<form method="post" action="{{ route('admin.sites.cleanto-service-admin.save',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $site->language_id }}">
<input type="hidden" name="service_id" value="{{ $service['id'] }}">
<label>Service #{{ $service['id'] }} <input type="text" name="title" maxlength="120" required value="{{ $service['title'] }}"></label>
<button type="submit">Update service name</button>
</form>
@endforeach
</section>
<section aria-labelledby="native-unit-management">
<h2 id="native-unit-management">Native Cleanto pricing units and durations</h2>
<p>Base price and duration editing is intentionally restricted to units belonging to <strong>disabled services with no booking history</strong>. Quantity rate rules, taxes, staff eligibility and opening hours remain managed in Cleanto. These changes do not activate booking.</p>
@if(!$unitManagementAvailable)<p role="status">Unit administration is unavailable for this tenant. Enable both tenant service_management_enabled and unit_management_enabled after database acceptance.</p>@else
<table><thead><tr><th>Service ID</th><th>Method</th><th>Unit</th><th>Price / duration</th><th>Action</th></tr></thead><tbody>
@forelse($managedUnits as $unit)
<tr><td>{{ $unit['service_id'] }}</td><td>{{ $unit['method_name'] }}</td><td>{{ $unit['title'] }}</td><td>{{ $unit['base_price'] }} / {{ $unit['duration_minutes'] }} min</td><td>
@if($unit['editable'])
<form method="post" action="{{ route('admin.sites.cleanto-unit-admin.save',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $site->language_id }}"><input type="hidden" name="unit_id" value="{{ $unit['id'] }}">
<label>Base price <input name="base_price" inputmode="decimal" value="{{ $unit['base_price'] }}" required></label>
<label>Duration (minutes) <input name="duration_minutes" type="number" step="5" min="5" max="1440" value="{{ $unit['duration_minutes'] }}" required></label>
<button type="submit">Save unit</button></form>
@else Active parent service: editing locked @endif
</td></tr>
@empty <tr><td colspan="5">No Cleanto units found.</td></tr> @endforelse
</tbody></table>
@endif
</section>
<section aria-labelledby="cleanto-staff-scheduling">
<h2 id="cleanto-staff-scheduling">Cleanto professionals and working hours</h2>
<p>These are native staff-to-service assignments and weekly schedule rows. Changing an assignment is permitted only for a disabled service; weekly shift changes are permitted only for disabled staff with no booking history, breaks or time off, and with a separate tenant schedule-write permission. Live schedule acceptance remains outstanding.</p>
@if(!$staffManagementAvailable)<p role="status">Staff administration is disabled or unavailable for this tenant. Both service and staff management must be explicitly enabled.</p>@else
@forelse($staffOverview as $person)
<article><h3>{{ $person['name'] }} (ID {{ $person['id'] }})</h3>
<p>Account: {{ $person['status'] }}. Booking flag: {{ $person['booking_enabled'] }}. Assigned service IDs: {{ implode(', ', $person['service_ids']) ?: 'none' }}.</p>
<table><thead><tr><th>Week</th><th>Weekday</th><th>Start</th><th>End</th><th>Day off</th></tr></thead><tbody>
@forelse($person['shifts'] as $shift)<tr><td>{{ $shift['week'] }}</td><td>{{ $shift['weekday'] }}</td><td>{{ $shift['start'] }}</td><td>{{ $shift['end'] }}</td><td>{{ $shift['off'] }}</td></tr>
@empty<tr><td colspan="5">No native working-hour records; do not assume availability.</td></tr>@endforelse
</tbody></table>
@if(($person['status'] ?? '') === 'D')
<details><summary>Edit weekly schedule for disabled staff (restricted)</summary>
<p>Requires separate tenant schedule-write permission, no booking history, no existing breaks or time off. Only existing weekly shift rows can be edited.</p>
@foreach($person['shifts'] as $shift)
@if($shift['week'] === 1 && $shift['weekday'] >= 1 && $shift['weekday'] <= 7 && ($shift['type'] ?? '') === 'weekly')
<form method="post" action="{{ route('admin.sites.cleanto-staff-schedule.save',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $site->language_id }}"><input type="hidden" name="staff_id" value="{{ $person['id'] }}"><input type="hidden" name="shift_id" value="{{ $shift['id'] }}">
<strong>Weekday {{ $shift['weekday'] }}</strong>
<label>From <input type="time" name="start" step="300" value="{{ substr($shift['start'],0,5) }}" required></label>
<label>To <input type="time" name="end" step="300" value="{{ substr($shift['end'],0,5) }}" required></label>
<label>Day off <select name="off"><option value="0" @if($shift['off']!=='Y') selected @endif>No</option><option value="1" @if($shift['off']==='Y') selected @endif>Yes</option></select></label>
<button type="submit">Save restricted weekly shift</button>
</form>
@endif
@endforeach
</details>
@endif
<form method="post" action="{{ route('admin.sites.cleanto-staff-admin.save',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $site->language_id }}"><input type="hidden" name="staff_id" value="{{ $person['id'] }}">
<label>Disabled service ID <select name="service_id" required>
@foreach(($managed ?? []) as $service)@if($service['status']==='D')<option value="{{ $service['id'] }}">{{ $service['title'] }} ({{ $service['id'] }})</option>@endif @endforeach
</select></label>
<label>Assignment <select name="assigned"><option value="1">Assign</option><option value="0">Remove</option></select></label>
<button type="submit">Update disabled-service eligibility</button>
</form></article>
@empty<p>No staff accounts returned by Cleanto.</p>@endforelse
@endif
</section>
<p>Available staff and appointment times are still determined by the Cleanto engine. Configure staff assignments, durations, service options and prices in Cleanto before allowing booking submissions.</p>
<p><a href="{{ route('admin.sites.edit',['id'=>$site->id,'language_id'=>$input['language_id']]) }}">Return to website editor</a></p>
</div>
@endsection
