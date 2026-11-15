<?php
$source=file_get_contents(__DIR__.'/../../assets/front/js/titan-booking-widget.js');if(strpos($source,"/titan-booking/availability")===false)throw new RuntimeException('Missing widget contract');echo 'PASS widget\n';
