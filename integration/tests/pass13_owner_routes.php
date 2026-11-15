<?php
declare(strict_types=1);
$s=file_get_contents(__DIR__.'/../../core/routes/web.php');foreach(['user.booking.manage.list','user.booking.manage.create','user.booking.manage.cancel','user.booking.manage.reschedule'] as $name){if(!str_contains($s,$name))throw new Exception('Missing route '.$name);}
echo "PASS ".basename(__FILE__)."\n";
