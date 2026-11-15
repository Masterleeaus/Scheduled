<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class): void { $prefix='App\\Booking\\Public\\Payment\\'; if (str_starts_with($class,$prefix)) { $file=__DIR__.'/../../../core/app/Booking/Public/Payment/'.substr($class,strlen($prefix)).'.php'; if(is_file($file)) require $file; } });
function assert68(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }
function reject68(callable $fn, string $label): void { try {$fn();} catch (InvalidArgumentException|DomainException $e) { return; } throw new RuntimeException('Expected rejection: '.$label); }
