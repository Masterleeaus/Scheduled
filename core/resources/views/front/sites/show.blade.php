<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $document['seo_title'] ?: $site->name }}</title>
<meta name="description" content="{{ $document['seo_description'] }}">
<meta property="og:type" content="website"><meta property="og:title" content="{{ $document['seo_title'] ?: $site->name }}"><meta property="og:description" content="{{ $document['seo_description'] }}">
@if(!empty($document['seo_image']))<meta property="og:image" content="{{ $document['seo_image'] }}">@endif
@if(!$preview)<link rel="canonical" href="{{ route('front.sites.show',['language'=>$site->language_id,'slug'=>$site->slug]) }}">@endif
@if($preview)<meta name="robots" content="noindex,nofollow">@endif
<link rel="stylesheet" href="{{ asset('assets/sites/site.css') }}"><link rel="stylesheet" href="{{ asset('assets/sites/marketing.css') }}"><link rel="stylesheet" href="{{ asset('assets/sites/features.css') }}">
@if(in_array($site->template_key, array_keys(\App\Services\Sites\SiteTemplates::catalog()),true))
<link rel="stylesheet" href="{{ asset('assets/sites/themes/' . $site->template_key . '.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('assets/sites/branding.css') }}">
</head>
<body style="{{ \App\Services\Sites\SiteBranding::css($document) }}">
@if($preview)<div class="preview" role="status">DRAFT PREVIEW — Not published</div>@endif
@if(in_array($site->template_key, array_keys(\App\Services\Sites\SiteTemplates::catalog()),true))
@include('front.sites.themes.' . str_replace('-', '_', $site->template_key))
@else
<header><div class="wrap"><strong>{{ $site->name }}</strong><nav aria-label="Site navigation"><a href="#services">Services</a><a href="#about">About</a><a href="#faq">FAQ</a><a href="#contact">Contact</a></nav></div></header>
<main><section class="hero"><div class="wrap"><h1>{{ $document['headline'] }}</h1><p>{{ $document['tagline'] }}</p><a class="button" href="{{ $document['cta_url'] ?: '#contact' }}">{{ $document['cta_label'] }}</a></div></section>
<section id="services" class="wrap"><h2>Services</h2><div class="tiles">@foreach($document['services'] as $service)<article><h3>{{ $service['name'] }}</h3><p>{{ $service['description'] }}</p></article>@endforeach</div></section>
<section id="about" class="wrap"><h2>About</h2><p>{{ $document['about'] }}</p></section>
<section id="faq" class="wrap"><h2>Frequently asked questions</h2>@foreach($document['faqs'] as $faq)<details><summary>{{ $faq['question'] }}</summary><p>{{ $faq['answer'] }}</p></details>@endforeach</section>
<section id="contact" class="wrap"><h2>Contact</h2>@if($document['contact_email'])<p><a href="mailto:{{ $document['contact_email'] }}">{{ $document['contact_email'] }}</a></p>@endif
@if($document['phone'])<p>{{ $document['phone'] }}</p>@endif</section></main><footer><div class="wrap">{{ $site->name }}</div></footer>@endif
@include('front.sites.partials.features')
@include('front.sites.partials.lead-form')
@if(!$preview && \App\Services\Sites\SiteBookingIntegration::supported((string)$site->template_key))
@php($verifiedBooking = \App\Services\Sites\SiteBookingIntegration::active($site,request()->getHost()))
@if($verifiedBooking)
@php($mappedCleantoServiceIds = \App\Services\Sites\SiteCleantoServiceMapping::current($site))
@php($verticalBookingLabels = \App\Services\Sites\SiteBookingIntegration::labels((string)$site->template_key, $document['vertical_key'] ?? null))
<section class="vertical-booking-availability wrap" id="live-availability" aria-label="Live appointment availability">
@include('front.partials.titan-booking-widget')
</section>
@include('front.partials.titan-booking-assets')
@endif
@endif

@if(!empty($document['image_blocks']) || !empty($placementPicker))
<script id="site-image-block-data" type="application/json">{!! json_encode($document['image_blocks'] ?? [], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_SLASHES) !!}</script>
<script src="{{ asset('assets/sites/site-image-placement.js') }}" defer></script>
@endif
@if(!empty($placementPicker))<script>window.sitePlacementPicker=true;</script>@endif
</body></html>
