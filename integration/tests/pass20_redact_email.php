<?php
declare(strict_types=1);
require __DIR__.'/pass20_support.php';
$text=LifecycleFailureRedactor::message(new RuntimeException('example user@test.example.org error'));check(!str_contains($text,'user@test.example.org'));
