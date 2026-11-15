@extends('admin.layout')
@section('content')
<div class="container"><h1>New site starter</h1>
@if($errors->any())<div role="alert">{{ implode(' ', $errors->all()) }}</div>@endif
<form method="post" action="{{ route('admin.sites.store') }}">@csrf<input type="hidden" name="language_id" value="{{ $language }}">
<label>Business vertical <select name="vertical_key" id="site-vertical" required>
<option value="">Select industry / service</option>
@foreach($verticalGroups as $group=>$verticals)<optgroup label="{{ $group }}">@foreach($verticals as $key=>$name)<option value="{{ $key }}" {{ old('vertical_key')===$key?'selected':'' }}>{{ $name }}</option>@endforeach</optgroup>@endforeach
</select></label>
<p id="vertical-recommendation" role="status" aria-live="polite">Select a business vertical to see a recommended starter.</p>
<label>Site name <input name="name" maxlength="120" required></label><label>URL slug <input name="slug" pattern="[a-z0-9]+(-[a-z0-9]+)*" required></label>
<label>Starter <select name="template_key" id="site-starter" required>@foreach($templates as $key=>$template)<option value="{{ $key }}" {{ !$template['available'] ? 'disabled' : '' }}>{{ $template['title'] }}{{ !$template['available'] ? ' (missing assets)' : '' }}</option>@endforeach</select></label>
<p><a id="site-template-preview" href="#" target="_blank" rel="noopener">Preview selected starter before creating</a></p>
<p id="starter-summary" role="status" aria-live="polite">Choose a starter to create an editable draft.</p>
<fieldset><legend>Business profile and website generation</legend>
<label>Business name <input name="business_name" maxlength="120" minlength="2" required value="{{ old('business_name') }}"></label>
<label>Business description <textarea name="business_summary" maxlength="1200" rows="4">{{ old('business_summary') }}</textarea></label>
<label>Service area <input name="service_area" maxlength="120" value="{{ old('service_area') }}"></label>
<label>Contact email <input type="email" name="contact_email" maxlength="160" value="{{ old('contact_email') }}"></label>
<label>Phone <input name="phone" maxlength="40" value="{{ old('phone') }}"></label>
<label>Copy tone <select name="tone">@foreach(['professional'=>'Professional','friendly'=>'Friendly','premium'=>'Premium','straightforward'=>'Straightforward'] as $tone=>$label)<option value="{{ $tone }}" {{ old('tone','professional')===$tone?'selected':'' }}>{{ $label }}</option>@endforeach</select></label>
<label>Creation mode <select name="generation_mode"><option value="manual" {{ old('generation_mode','manual')==='manual'?'selected':'' }}>Manual branded starter — no API required</option><option value="assisted" {{ old('generation_mode')==='assisted'?'selected':'' }}>AI copy generation (requires provider connection)</option></select></label>
</fieldset>
<p>Preview the selected template using the link above; create a draft to review your personalised site. AI-assisted creation sends the business profile to the configured AI provider. Publishing always remains a separate action.</p>
<script>const verticals=@json($verticalCatalog);const verticalSelect=document.getElementById("site-vertical");const verticalHint=document.getElementById("vertical-recommendation");const catalog=@json($templates);const select=document.getElementById('site-starter');const label=document.getElementById('starter-summary');function describe(){const t=catalog[select.value];label.textContent=t ? (t.headline+' — '+t.tagline) : 'Select a starter';document.getElementById('site-template-preview').href=@json(route('admin.sites.template-preview',['key'=>'__starter__','language_id'=>$language])).replace('__starter__',encodeURIComponent(select.value));}function recommend(){const vertical=verticals[verticalSelect.value];if(!vertical){verticalHint.textContent='Select a business vertical.';return;}const key=vertical.recommended_template;verticalHint.textContent='Recommended design: '+(catalog[key]?.title||key)+'. You can select any starter.';if(catalog[key]?.available){select.value=key;describe();}}verticalSelect.addEventListener('change',recommend);select.addEventListener('change',describe);describe();if(verticalSelect.value)recommend();</script>
<button type="submit" formaction="{{ route('admin.sites.creation-preview') }}" formtarget="_blank" formmethod="post">Preview personalised website</button>
<button type="submit">Create editable draft</button></form></div>
@endsection
