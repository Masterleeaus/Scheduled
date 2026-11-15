<?php
$base=dirname(__DIR__);$get=function($p)use($base){return file_get_contents($base.'/'.$p);};$checks=[
 ['site marketing validator','core/app/Services/Sites/SiteMarketing.php','activePromotions'],
 ['duplicate codes rejected','core/app/Services/Sites/SiteMarketing.php','Duplicate promotion code'],
 ['draft publishing remains snapshot','core/app/Http/Controllers/Admin/TitanSiteController.php','published_document'],
 ['published site uses snapshot','core/app/Http/Controllers/Front/TitanSiteController.php','published_document'],
 ['document marketing validation','core/app/Services/Sites/SiteDocument.php','SiteMarketing::normalize'],
 ['site renderer marketing','core/resources/views/front/sites/partials/features.blade.php',"partials.marketing"],
 ['site social metadata','core/resources/views/front/sites/show.blade.php','og:description'],
 ['canonical only published','core/resources/views/front/sites/show.blade.php','rel="canonical"'],
 ['editor promotion collection','core/public/assets/sites/site-editor.js','promotions:'],
 ['editor blog collection','core/public/assets/sites/site-editor.js','blog_posts:'],
 ['lead capture offer validation','core/app/Services/Sites/SiteLeadValidator.php','promotion_code'],
 ['lead attribution persistence','core/app/Http/Controllers/Front/TitanSiteController.php',"'utm_campaign'=>"],
 ['lead attribution schema','core/database/migrations/2026_11_10_000006_add_attribution_to_titan_site_leads.php','utm_campaign'],
 ['campaign code in form','core/resources/views/front/sites/partials/lead-form.blade.php','utm_campaign'],
 ['marketing styles','core/public/assets/sites/marketing.css','site-article-body'],
];$fail=0;foreach($checks as $c){$ok=strpos($get($c[1]),$c[2])!==false;echo ($ok?'PASS':'FAIL').': '.$c[0]."\n";if(!$ok)$fail++;}exit($fail?1:0);
