@extends('admin.layout')
@section('content')
<div class="container"><h1>Site starters</h1><a href="{{ route('admin.sites.create',['language_id'=>$language]) }}">Create a site</a>
<table class="table"><thead><tr><th>Name</th><th>Template</th><th>Status</th><th></th></tr></thead><tbody>
@foreach($sites as $site)<tr><td>{{ $site->name }}</td><td>{{ $site->template_key }}</td><td>{{ $site->published ? 'Published':'Draft' }}</td><td><a href="{{ route('admin.sites.edit',['id'=>$site->id,'language_id'=>$language]) }}">Edit</a> · <a href="{{ route('admin.sites.preview',['id'=>$site->id,'language_id'=>$language]) }}" target="_blank" rel="noopener">Preview</a></td></tr>@endforeach
</tbody></table>{{ $sites->links() }}</div>
@endsection
