@extends('admin.layout')
@section('content')
<div class="container"><h1>Branding Agent — {{ $site->name }}</h1>
<p><a href="{{ route('admin.sites.edit',['id'=>$site->id,'language_id'=>$language]) }}">Return to website editor</a> · <a href="{{ route('admin.sites.preview',['id'=>$site->id,'language_id'=>$language]) }}" target="_blank" rel="noopener">Preview current draft</a></p>
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
@if($errors->any())<div role="alert">{{ implode(' ', $errors->all()) }}</div>@endif
<p>Draft revision {{ $site->revision }}. Generated changes remain private until approved, saved as a new draft revision, and published separately.</p>
<h2>Automated brand review</h2><p>{{ $audit['high'] }} high-priority and {{ $audit['medium'] }} medium-priority findings.</p>
<ul>@foreach($audit['issues'] as $issue)<li><strong>{{ $issue['level'] }}:</strong> {{ $issue['message'] }}</li>@endforeach</ul>
@if(!\App\Services\Sites\BrandProvider::ready())<p role="alert">AI provider is not configured. Set SITE_BRAND_AI_KEY on the server to enable generation.</p>@endif
<h2>Propose new brand styling</h2>
<form method="post" action="{{ route('admin.sites.brand-propose',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><input type="hidden" name="revision" value="{{ $site->revision }}">
<label>Brand instructions <textarea name="instruction" minlength="5" maxlength="1200" required rows="3" cols="75" placeholder="Professional navy and teal commercial cleaning brand with high contrast"></textarea></label>
<button type="submit">Generate brand proposal</button></form>
<h2>AI Website Assistant</h2>
<p>Ask for page text, template headings, service descriptions, FAQs, testimonials or team information to be edited. AI produces a typed, reviewable proposal, never a direct public edit. Other layout and image changes remain in the visual editor and image studio.</p>
<form method="post" action="{{ route('admin.sites.site-edit-propose',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><input type="hidden" name="revision" value="{{ $site->revision }}">
<label>What should the AI change? <textarea name="instruction" minlength="5" maxlength="1200" required rows="4" cols="75" placeholder="Rewrite the hero heading and add a helpful FAQ about booking"></textarea></label>
<button type="submit">Propose website edits</button></form>
<h2>Generate a new image</h2>
<form method="post" action="{{ route('admin.sites.brand-image',['id'=>$site->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><input type="hidden" name="revision" value="{{ $site->revision }}">
<label>Image brief <textarea name="prompt" minlength="10" maxlength="1000" required rows="3" cols="75" placeholder="Sunlit modern office being professionally cleaned"></textarea></label>
<label>Use for <select name="target"><option value="seo_image">Social sharing image</option><option value="theme_image">Imported theme image</option></select></label>
<label>Theme image field (required when targeting a theme image) <select name="field"><option value="">Choose a field</option>@foreach((\App\Services\Sites\ThemeFields::forTemplate($site->template_key)['images']??[]) as $field=>$default)<option value="{{ $field }}">{{ $field }}</option>@endforeach</select></label>
<button type="submit">Generate image proposal</button></form>
<h2>Proposals to review</h2>
@forelse($proposals as $proposal)
<article style="padding:12px;border:1px solid #ccc;margin:12px 0"><h3>{{ ucfirst($proposal->kind) }} #{{ $proposal->id }} — {{ $proposal->status }}</h3>
<p>{{ $proposal->rationale }}</p>
@php($changes=json_decode($proposal->changes,true))
@if(isset($changes['operations']))<h4>Proposed website changes</h4><ol>@foreach($changes['operations'] as $operation)<li><code>{{ $operation['action'] }}</code> — {{ $operation['field'] ?? $operation['collection'] ?? '' }} @if(isset($operation['value'])): {{ $operation['value'] }}@elseif(isset($operation['item'])): {{ json_encode($operation['item']) }}@endif</li>@endforeach</ol>@endif
@if(isset($changes['brand']))<dl>@foreach($changes['brand'] as $key=>$value)<dt>{{ $key }}</dt><dd>{{ $value }}</dd>@endforeach</dl>@endif
@if(isset($changes['seo_image']))<img alt="Proposed social visual" src="{{ $changes['seo_image'] }}" style="max-width:320px"></img>@endif
@if(isset($changes['theme_image']))<p>Replace {{ $changes['theme_image']['field'] }}</p><img alt="Proposed theme visual" src="{{ $changes['theme_image']['url'] }}" style="max-width:320px"></img>@endif
@if($proposal->status==='pending')
<form method="post" action="{{ route('admin.sites.brand-decide',['id'=>$site->id,'proposalId'=>$proposal->id]) }}">@csrf
<input type="hidden" name="language_id" value="{{ $language }}"><input type="hidden" name="revision" value="{{ $site->revision }}">
<button name="decision" value="approve">Approve into draft</button><button name="decision" value="reject">Reject</button></form>
@endif</article>
@empty<p>No proposals yet.</p>@endforelse
</div>
@endsection
