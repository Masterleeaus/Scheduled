<?php
$root=dirname(__DIR__,2);
$routes=file_get_contents($root.'/routes/web.php');
$view=file_get_contents($root.'/resources/views/front/partials/titan-booking-widget.blade.php');
$widget=file_get_contents(dirname($root).'/assets/front/js/titan-booking-catalogue.js');
$controller=file_get_contents($root.'/app/Http/Controllers/Admin/SiteCleantoServiceMapController.php');
foreach(['admin.sites.booking-services.edit','admin.sites.booking-services.update'] as $name)if(!str_contains($routes,$name))throw new RuntimeException('Missing route: '.$name);
foreach(['TenantAccess::require','SiteBookingIntegration::availableHosts','BookingCatalogueGateway','validateSelection'] as $token)if(!str_contains($controller,$token))throw new RuntimeException('Missing security contract '.$token);
if(!str_contains($view,'data-mapped-service-ids')||!str_contains($widget,'data-mapped-service-ids'))throw new RuntimeException('Missing mapping bridge');
echo "Service mapping source contract passed\n";
