@extends('admin.layout')
@section('content')
<div class="container"><h1>Site history — {{ $site->name }}</h1>
<a href="{{ route('admin.sites.edit',['id'=>$site->id,'language_id'=>$language]) }}">Back to editor</a>
@if($errors->any())<div role="alert">{{ implode(' ',$errors->all()) }}</div>@endif
<table class="table"><thead><tr><th>Version</th><th>Action</th><th>Saved</th><th>Restore to draft</th></tr></thead><tbody>
@foreach($revisions as $version)<tr><td>{{ $version->revision }}</td><td>{{ $version->action }}</td><td>{{ $version->created_at }}</td><td>
<form method="post" action="{{ route('admin.sites.restore',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><input type="hidden" name="revision" value="{{ $site->revision }}"><input type="hidden" name="restore_revision" value="{{ $version->revision }}">
<button type="submit">Restore</button></form></td></tr>@endforeach
</tbody></table></div>
@endsection
