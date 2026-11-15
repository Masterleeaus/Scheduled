@extends('admin.layout')
@section('content')
<div class="container-fluid"><h2>{{ $campaign ? 'Edit landing campaign' : 'New landing campaign' }}</h2>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ $campaign ? route('admin.landing.update',$campaign->id) : route('admin.landing.store') }}">@csrf
@if($campaign)<input type="hidden" name="revision" value="{{ $campaign->revision }}">@endif
<div class="form-group"><label>Language</label><select name="language_id" required>@foreach($languages as $l)<option value="{{ $l->id }}" {{ (int)old('language_id',$campaign->language_id??0)===$l->id ? 'selected' : '' }}>{{ $l->name }} ({{ $l->code }})</option>@endforeach</select></div>
<div class="form-group"><label>Campaign name</label><input class="form-control" name="name" maxlength="120" required value="{{ old('name',$campaign->name??'') }}"></div>
<div class="form-group"><label>Public path /campaign/</label><input class="form-control" name="slug" pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="100" required value="{{ old('slug',$campaign->slug??'') }}"></div>
<div class="form-group"><label>Template</label><select name="template" id="landing-template">@foreach($templates as $key=>$template)<option value="{{ $key }}" {{ old('template',$campaign->template??'')===$key ? 'selected' : '' }}>{{ $template['title'] }}</option>@endforeach</select></div>
<div class="form-group"><label>Headline</label><input class="form-control" name="headline" id="landing-headline" required maxlength="180" value="{{ old('headline',$campaign->headline??'') }}"></div>
<div class="form-group"><label>Subheading</label><textarea class="form-control" name="subheading" id="landing-subheading" maxlength="1000">{{ old('subheading',$campaign->subheading??'') }}</textarea></div>
<div class="form-group"><label>Button label</label><input class="form-control" name="cta_label" id="landing-cta" maxlength="80" required value="{{ old('cta_label',$campaign->cta_label??'Request a quote') }}"></div>
<div class="form-group"><label>Accent colour</label><input type="color" name="accent" value="{{ old('accent',$campaign->accent??'#173c66') }}"></div>
<div class="form-group"><label>Business phone</label><input class="form-control" name="phone" maxlength="40" value="{{ old('phone',$campaign->phone??'') }}"></div>
<div class="form-group"><label>SEO title</label><input class="form-control" name="meta_title" maxlength="160" value="{{ old('meta_title',$campaign->meta_title??'') }}"></div>
<div class="form-group"><label>SEO description</label><textarea class="form-control" name="meta_description" maxlength="300">{{ old('meta_description',$campaign->meta_description??'') }}</textarea></div>
<fieldset class="landing-design-editor" style="border:1px solid #dae2ea;padding:1.2rem;margin:1rem 0"><legend>Campaign design and imagery</legend>
<p>Each campaign has its own editable design. Paste a URL or upload an image after saving the initial campaign.</p>
<div class="form-group"><label>Hero image URL</label><input class="form-control" name="image_url" id="campaign-image" maxlength="500" value="{{ old('image_url',$design['image_url']) }}"></div>
@if($campaign)<div class="form-group"><label for="landing-hero-upload">Upload hero image (JPEG, PNG, WebP, GIF; max 5 MB)</label><input type="file" accept="image/jpeg,image/png,image/webp,image/gif" id="landing-hero-upload" data-landing-upload-target="campaign-image"><small class="text-muted">Uploading places the image URL into the form. Save the draft to keep the change.</small></div>@endif
<div class="form-group"><label>Hero image description (alt text)</label><input class="form-control" name="image_alt" maxlength="180" value="{{ old('image_alt',$design['image_alt']) }}"></div>
<div class="form-group"><label>Intro eyebrow</label><input class="form-control" name="eyebrow" maxlength="90" value="{{ old('eyebrow',$design['eyebrow']) }}"></div>
<div class="form-group"><label>Layout</label><select name="theme" class="form-control">@foreach(['split'=>'Split image','centered'=>'Centered','editorial'=>'Editorial'] as $v=>$label)<option value="{{ $v }}" @selected(old('theme',$design['theme'])===$v)>{{ $label }}</option>@endforeach</select></div>
<div class="form-group"><label>Button shape</label><select name="button_style" class="form-control">@foreach(['rounded'=>'Rounded','pill'=>'Pill','square'=>'Square'] as $v=>$label)<option value="{{ $v }}" @selected(old('button_style',$design['button_style'])===$v)>{{ $label }}</option>@endforeach</select></div>
<div class="form-group"><label>Secondary colour</label><input type="color" name="secondary_colour" value="{{ old('secondary_colour',$design['secondary_colour']) }}"></div>
<div class="form-group"><label>Trust section heading</label><input class="form-control" name="trust_heading" maxlength="120" value="{{ old('trust_heading',$design['trust_heading']) }}"></div>
<div class="form-group"><label>Trust section text</label><textarea class="form-control" name="trust_text" maxlength="1200">{{ old('trust_text',$design['trust_text']) }}</textarea></div>
<h4>Enquiry form and footer</h4>
<div class="form-group"><label>Form heading</label><input class="form-control" name="form_heading" maxlength="120" value="{{ old('form_heading',$design['form_heading']) }}"></div>
<div class="form-group"><label>Form intro</label><textarea class="form-control" name="form_intro" maxlength="400">{{ old('form_intro',$design['form_intro']) }}</textarea></div>
<div class="form-group"><label>Submit button text</label><input class="form-control" name="form_button" maxlength="80" value="{{ old('form_button',$design['form_button']) }}"></div>
<div class="form-group"><label>Consent checkbox label</label><input class="form-control" name="consent_label" maxlength="240" value="{{ old('consent_label',$design['consent_label']) }}"></div>
<div class="form-group"><label>Footer text (leave blank for business name)</label><input class="form-control" name="footer_text" maxlength="180" value="{{ old('footer_text',$design['footer_text']) }}"></div>
<label>Benefits (up to eight)</label><div class="row">@for($i=0;$i<8;$i++)<div class="col-md-6 form-group"><input class="form-control" name="benefits[{{ $i }}]" maxlength="120" placeholder="Benefit {{ $i+1 }}" value="{{ old('benefits.'.$i,$design['benefits'][$i]??'') }}"></div>@endfor</div>
<hr><h4>Additional images anywhere in the campaign</h4><p>Insert up to twelve images at supported section boundaries. Images remain editable, moveable and removable. Upload an image or paste a URL. For a new campaign, save the initial draft first to enable uploads.</p>
<div id="landing-image-blocks">
@foreach(old('image_blocks',$design['image_blocks']??[]) as $i=>$block)
<div class="landing-image-block" style="padding:12px;border:1px solid #d5dee8;margin:12px 0;border-radius:8px">
<div class="form-group"><label>Image URL</label><input class="form-control landing-block-url" name="image_blocks[{{ $i }}][url]" value="{{ $block['url']??'' }}" maxlength="500"></div>
@if($campaign)<label>Upload replacement <input type="file" class="landing-block-upload" accept="image/jpeg,image/png,image/webp,image/gif"></label>@endif
<div class="form-group"><label>Alternative text</label><input class="form-control" name="image_blocks[{{ $i }}][alt]" maxlength="180" value="{{ $block['alt']??'' }}"></div>
<div class="form-group"><label>Optional caption</label><input class="form-control" name="image_blocks[{{ $i }}][caption]" maxlength="240" value="{{ $block['caption']??'' }}"></div>
<div class="form-group"><label>Placement</label><select class="form-control" name="image_blocks[{{ $i }}][placement]">@foreach(['after-hero'=>'After hero','before-trust'=>'Before trust section','after-trust'=>'After trust section','before-form'=>'Before enquiry form'] as $position=>$label)<option value="{{ $position }}" @selected(($block['placement']??'after-trust')===$position)>{{ $label }}</option>@endforeach</select></div>
<div class="form-group"><label>Width</label><select class="form-control" name="image_blocks[{{ $i }}][width]">@foreach(['normal'=>'Normal','wide'=>'Wide','full'=>'Full width'] as $width=>$label)<option value="{{ $width }}" @selected(($block['width']??'normal')===$width)>{{ $label }}</option>@endforeach</select></div>
<button class="btn btn-outline-danger landing-remove-block" type="button">Remove image</button>
</div>
@endforeach
</div>
<button class="btn btn-outline-primary" type="button" id="landing-add-image">Insert image</button>
<p id="landing-image-message" role="status" aria-live="polite"></p>
</fieldset>
<button class="btn btn-primary">Save draft</button></form>
@if($campaign)<hr><a class="btn btn-outline-primary" href="{{ route('admin.landing.preview',$campaign->id) }}" target="_blank" rel="noopener">Preview</a>
@if($campaign->published)<a class="btn btn-outline-success" href="{{ route('front.landing.show',$campaign->published_slug ?: $campaign->slug) }}" target="_blank" rel="noopener">Published page</a>@endif
<form method="post" action="{{ route('admin.landing.publish',$campaign->id) }}" style="display:inline">@csrf<input type="hidden" name="revision" value="{{ $campaign->revision }}"><input type="hidden" name="published" value="{{ $campaign->published ? 0 : 1 }}"><button class="btn btn-warning">{{ $campaign->published ? 'Unpublish' : 'Publish' }}</button></form>
<a class="btn btn-link" href="{{ route('admin.landing.leads',$campaign->id) }}">View leads</a>
<section><h3>Duplicate campaign</h3><p>Creates an independent unpublished campaign with no existing leads.</p><form method="post" action="{{ route('admin.landing.duplicate',$campaign->id) }}">@csrf
<label>New name <input name="name" required minlength="2" maxlength="120"></label><label>New public slug <input name="slug" required maxlength="100" pattern="[a-z0-9]+(-[a-z0-9]+)*"></label><button type="submit">Duplicate as draft</button></form></section>@endif
<p><a href="{{ route('admin.landing.index') }}">Back to campaigns</a></p></div>
<script>window.landingTemplateDefaults=@json($templates);window.landingUploadUrl=@json($campaign ? route('admin.landing.upload-image',$campaign->id) : null);window.landingCsrfToken=@json(csrf_token());</script><script src="{{ asset('assets/landing/admin.js') }}" defer></script>
@endsection
