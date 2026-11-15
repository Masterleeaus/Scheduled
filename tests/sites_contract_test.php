<?php
$root=dirname(__DIR__);$get=fn($p)=>file_get_contents($root.'/'.$p);$assert=function($yes,$why){if(!$yes){fwrite(STDERR,"FAIL: $why\n");exit(1);}echo "PASS: $why\n";};
$c=$get('core/app/Http/Controllers/Admin/TitanSiteController.php');$f=$get('core/app/Http/Controllers/Front/TitanSiteController.php');$d=$get('core/app/Services/Sites/SiteDocument.php');$r=$get('core/routes/web.php');
foreach(['lockForUpdate()','DB::transaction','language_id','published_document','draft_document','restore_revision','revision','SiteDocument::decode','SiteTemplates::make'] as $x)$assert(str_contains($c,$x),"admin workflow $x");
foreach(['published_document','published', 'language_id','abort_unless'] as $x)$assert(str_contains($f,$x),"public scope $x");
foreach(['self::isList','FILTER_VALIDATE_EMAIL','cta_url','services','faqs'] as $x)$assert(str_contains($d,$x),"document validation $x");
foreach(['admin.sites.index','admin.sites.publish','admin.sites.restore','front.sites.show','checkpermission:Pages'] as $x)$assert(str_contains($r,$x),"route $x");
require_once $root.'/core/app/Services/Sites/SiteTemplates.php';
$assert(count(\App\Services\Sites\SiteTemplates::catalog())===19,'19 templates');
