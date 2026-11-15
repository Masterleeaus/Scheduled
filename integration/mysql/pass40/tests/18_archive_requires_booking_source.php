<?php
declare(strict_types=1);
require_once __DIR__.'/../bin/scan_archive.php';
if(Pass40Archive::check(['private/cleanto/book.php'])['complete'])throw new Exception('missing runtime');
echo 'PASS 18_archive_requires_booking_source'.PHP_EOL;
