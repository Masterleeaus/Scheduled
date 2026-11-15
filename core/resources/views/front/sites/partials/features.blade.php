@include('front.sites.partials.marketing')
@if(!empty($document['gallery']))
<section class="site-extras wrap" id="gallery"><h2>Our work</h2><div class="site-gallery">
@foreach($document['gallery'] as $photo)<figure><img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}" loading="lazy" referrerpolicy="no-referrer"></figure>@endforeach
</div></section>@endif
@if(!empty($document['testimonials']))
<section class="site-extras wrap" id="testimonials"><h2>Customer feedback</h2><div class="site-feature-grid">@foreach($document['testimonials'] as $item)<blockquote><p>{{ $item['quote'] }}</p><footer>{{ $item['name'] }}</footer></blockquote>@endforeach</div></section>@endif
@if(!empty($document['team']))
<section class="site-extras wrap" id="team"><h2>Meet the team</h2><div class="site-feature-grid">@foreach($document['team'] as $person)<article><h3>{{ $person['name'] }}</h3><p>{{ $person['role'] }}</p></article>@endforeach</div></section>@endif
