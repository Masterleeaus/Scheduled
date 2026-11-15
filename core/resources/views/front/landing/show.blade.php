@php($design = \App\Services\Landing\CampaignDesign::decode($campaign->design_json??null,$campaign->template))
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="{{ $preview ? 'noindex,nofollow' : 'index,follow' }}"><title>{{ $campaign->meta_title ?: $campaign->headline }}</title><meta name="description" content="{{ $campaign->meta_description ?: $campaign->subheading }}"><link rel="stylesheet" href="{{ asset('assets/landing/campaign.css') }}"></head><body class="campaign-layout-{{ $design['theme'] }} campaign-button-{{ $design['button_style'] }}" style="--accent:{{ $campaign->accent }};--campaign-secondary:{{ $design['secondary_colour'] }}">
<header class="top"><span class="brand">{{ $campaign->name }}</span>@if($campaign->phone)<a href="tel:{{ preg_replace('/[^+0-9]/','',$campaign->phone) }}">{{ $campaign->phone }}</a>@endif</header>
<main><section class="hero"><div class="hero-copy"><div class="tag">{{ $design['eyebrow'] }}</div><h1>{{ $campaign->headline }}</h1><p>{{ $campaign->subheading }}</p><a href="#enquire" class="cta">{{ $campaign->cta_label }}</a></div>@if($design['image_url'])<div class="hero-art"><img src="{{ $design['image_url'] }}" alt="{{ $design['image_alt'] }}" loading="eager"></div>@endif</section>
@foreach(['after-hero','before-trust'] as $position)
@foreach($design['image_blocks'] as $block)@if($block['placement']===$position)<figure class="campaign-inserted-image campaign-image-{{ $block['width'] }}"><img src="{{ $block['url'] }}" alt="{{ $block['alt'] }}" loading="lazy">@if($block['caption']!=='')<figcaption>{{ $block['caption'] }}</figcaption>@endif</figure>@endif @endforeach
@endforeach
<section class="campaign-trust"><h2>{{ $design['trust_heading'] }}</h2><p>{{ $design['trust_text'] }}</p>@if(count($design['benefits']))<ul>@foreach($design['benefits'] as $benefit)<li>{{ $benefit }}</li>@endforeach</ul>@endif</section>
@foreach(['after-trust','before-form'] as $position)
@foreach($design['image_blocks'] as $block)@if($block['placement']===$position)<figure class="campaign-inserted-image campaign-image-{{ $block['width'] }}"><img src="{{ $block['url'] }}" alt="{{ $block['alt'] }}" loading="lazy">@if($block['caption']!=='')<figcaption>{{ $block['caption'] }}</figcaption>@endif</figure>@endif @endforeach
@endforeach
<section class="panel" id="enquire"><h2>{{ $design['form_heading'] }}</h2><p>{{ $design['form_intro'] }}</p>
@if($preview)<p class="notice">Draft preview — lead submission is disabled.</p>
@elseif(session('landing_received'))<p role="status" class="notice">Thank you. Your enquiry has been received.</p>@else
<form action="{{ route('front.landing.submit',$campaign->slug) }}" method="post">@csrf
<label>Full name <input name="name" required maxlength="120" value="{{ old('name') }}" autocomplete="name"></label>
<label>Email <input type="email" name="email" required maxlength="254" value="{{ old('email') }}" autocomplete="email"></label>
<label>Phone (optional) <input type="tel" name="phone" maxlength="40" value="{{ old('phone') }}"></label>
<label>How can we help? <textarea name="message" maxlength="2000">{{ old('message') }}</textarea></label>
<div class="trap" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
@foreach(['utm_source','utm_medium','utm_campaign'] as $utm)<input type="hidden" name="{{ $utm }}" value="{{ old($utm, request()->query($utm)) }}">@endforeach
<label class="consent"><input type="checkbox" name="consent" value="1" required> {{ $design['consent_label'] }}</label>
@if($errors->any())<div role="alert" class="error">Please correct the form: {{ $errors->first() }}</div>@endif
<button type="submit">{{ $design['form_button'] }}</button></form>@endif</section></main><footer>© {{ date('Y') }} {{ $design['footer_text'] ?: $campaign->name }}</footer>
<script src="{{ asset('assets/landing/campaign.js') }}" defer></script></body></html>
