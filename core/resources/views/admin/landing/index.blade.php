@extends('admin.layout')
@section('content')
<div class="container-fluid"><h2>Landing campaigns</h2><p><a class="btn btn-primary" href="{{ route('admin.landing.create') }}">New campaign</a></p>
<table class="table"><thead><tr><th>Campaign</th><th>Template</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@foreach($campaigns as $c)<tr><td>{{ $c->name }}</td><td>{{ $c->template }}</td><td>{{ $c->published ? 'Published' : 'Draft' }}</td><td><a href="{{ route('admin.landing.edit',$c->id) }}">Edit</a> · <a href="{{ route('admin.landing.preview',$c->id) }}" target="_blank" rel="noopener">Preview</a> · <a href="{{ route('admin.landing.leads',$c->id) }}">Leads</a></td></tr>@endforeach
</tbody></table>{{ $campaigns->links() }}</div>
@endsection
