<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void {
    $prefix='App\\'; if(strncmp($class,$prefix,strlen($prefix))!==0)return;
    $path=dirname(__DIR__,3).'/core/app/'.str_replace('\\','/',substr($class,strlen($prefix))).'.php';
    if(is_file($path))require_once $path;
});
function check(bool $condition,string $message):void {if(!$condition)throw new RuntimeException($message);}
function rejects(callable $action,string $name):void {try {$action();}catch(InvalidArgumentException|RuntimeException $e){return;}throw new RuntimeException('Expected rejection: '.$name);}
