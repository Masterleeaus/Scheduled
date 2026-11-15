<?php
$source=file_get_contents(__DIR__.'/../../core/routes/web.php');if(strpos($source,"Route::post('/titan-booking/availability'")===false)throw new RuntimeException('Missing route contract');echo 'PASS route\n';
