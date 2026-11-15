<?php
require_once __DIR__.'/../../core/app/Services/Tenants/DomainHostname.php';
use App\Services\Tenants\DomainHostname;
foreach(['example.com'=>'example.com','WWW.Example.COM.'=>'www.example.com','client.example.com'=>'client.example.com'] as $s=>$expected){if(DomainHostname::normalize($s)!==$expected)throw new RuntimeException('Mismatch');}
foreach(['https://example.com','example.com:443','127.0.0.1','bad..domain','-bad.example.com','example.com/path','localhost'] as $invalid){try{DomainHostname::normalize($invalid);throw new RuntimeException('Invalid hostname accepted: '.$invalid);}catch(InvalidArgumentException $e){}}
echo "domain hostname checks passed\n";
