<?php
require_once __DIR__.'/../../core/app/Services/Tenants/DomainHostname.php';
require_once __DIR__.'/../../core/app/Services/Tenants/DomainVerification.php';
use App\Services\Tenants\DomainVerification;
assert(DomainVerification::txtName('WWW.Example.COM')==='_titan-verify.www.example.com');
if(!DomainVerification::matches('example.com','abc',[['txt'=>'titan-site=abc']]))throw new RuntimeException('Correct token rejected');
if(DomainVerification::matches('example.com','abc',[['txt'=>'titan-site=abcd']]))throw new RuntimeException('Wrong token accepted');
echo "domain proof checks passed\n";
