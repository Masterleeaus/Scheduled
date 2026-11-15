<?php
$root=dirname(__DIR__,2);
foreach(['default','construction','logistic','cleaning','lawyer','gym','ecommerce','car'] as $theme){
 $file=file_get_contents($root.'/core/resources/views/front/'.$theme.'/layout.blade.php');
 if(substr_count($file,"@include('front.partials.titan-booking-assets')")!==1)throw new RuntimeException('Missing or duplicate theme assets: '.$theme);
}
echo "All eight theme layouts include booking assets\n";
