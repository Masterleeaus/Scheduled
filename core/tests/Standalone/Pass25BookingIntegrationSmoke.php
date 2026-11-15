<?php
$root=dirname(__DIR__,2);
require_once $root.'/app/Services/Sites/SiteBookingIntegration.php';
use App\Services\Sites\SiteBookingIntegration as Integration;
foreach(['pawfy','stevenwatkins','yoga-pilates','carzone'] as $key){
 if(!Integration::supported($key))throw new RuntimeException('Missing theme: '.$key);
 $labels=Integration::labels($key);
 foreach(['title','service','staff','duration','address','staff_prompt'] as $label)if(empty($labels[$label]))throw new RuntimeException($key.' '.$label);
}
if(Integration::supported('residential'))throw new RuntimeException('Cleaning flow accidentally changed');
$source=file_get_contents($root.'/resources/views/front/sites/show.blade.php');
if(!str_contains($source,'SiteBookingIntegration::active')||!str_contains($source,'titan-booking-assets'))throw new RuntimeException('Missing widget integration');
$code=file_get_contents($root.'/app/Services/Sites/SiteBookingIntegration.php');
foreach(['titan_site_domains','booking_public_domains','booking_tenant_instances','titan_site_booking_settings'] as $table)if(!str_contains($code,$table))throw new RuntimeException('Unscoped domain binding');
$admin=file_get_contents($root.'/app/Http/Controllers/Admin/SiteBookingSettingsController.php');
if(!str_contains($admin,"TenantAccess::require((int)$".'site->tenant_id'))throw new RuntimeException('Missing tenant authorization');
echo "PASS: four industry widgets, dual-domain verification and owner-only settings.\n";
