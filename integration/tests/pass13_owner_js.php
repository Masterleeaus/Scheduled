<?php
declare(strict_types=1);
$s=file_get_contents(__DIR__.'/../../assets/user/js/titan-booking-owner.js');if(!str_contains($s,'textContent')||str_contains($s,'innerHTML'))throw new Exception('Unsafe rendering');
echo "PASS ".basename(__FILE__)."\n";
