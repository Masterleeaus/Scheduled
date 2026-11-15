@extends('admin.layout')
@section('content')
<div class="container"><h1>Enquiries · {{ $site->name }}</h1><p><a href="{{ route('admin.sites.edit',['id'=>$site->id,'language_id'=>$language]) }}">Back to editor</a></p>
<table class="table"><thead><tr><th>Received</th><th>Name</th><th>Email</th><th>Phone</th><th>Service</th><th>Request</th><th>Preferred date/time</th><th>Details</th><th>Campaign</th><th>Offer</th><th>Message</th></tr></thead>
<tbody>@forelse($leads as $lead)<tr><td>{{ $lead->created_at }}</td><td>{{ $lead->name }}</td><td>{{ $lead->email }}</td><td>{{ $lead->phone }}</td><td>{{ $lead->service }}</td><td>{{ $lead->request_kind ?? "enquiry" }}</td><td>{{ $lead->preferred_date ?? "" }} {{ $lead->preferred_time ?? "" }}</td><td>{{ isset($lead->booking_details) ? (json_decode($lead->booking_details,true)["subject"] ?? "") : "" }}</td><td>{{ $lead->utm_campaign ?? "" }}</td><td>{{ $lead->promotion_code ?? "" }}</td><td style="white-space:pre-wrap">{{ $lead->message }}</td></tr>
@empty<tr><td colspan="11">No enquiries yet.</td></tr>@endforelse</tbody></table>{{ $leads->links() }}</div>
@endsection
