<?php
declare(strict_types=1);
require_once __DIR__.'/../mysql/lifecycle/bootstrap.php';
use Titan\BookingIntegration\{LifecycleConsent,LifecycleTestScope,LifecycleFixtureLoader,LifecycleFixtureValidator,LifecycleCustomerSafety,LifecycleMutationKeys,LifecycleBookingResult,LifecycleConflictAssertion,LifecycleEvidence,LifecycleReport,LifecycleDatabaseReadiness,LifecyclePreflight,LifecycleFailureRedactor};
function check(bool $b):void {if(!$b)throw new RuntimeException('Assertion failed');}
function expectFailure(callable $fn):void {try{$fn();}catch(Throwable){return;}throw new RuntimeException('Expected exception not raised');}
function validFixture():array{return ['database'=>'titan_booking_test_alpha','environment'=>'disposable','tenant'=>['authenticated_owner_id'=>1,'verified_pricing_settings'=>[]], 'create'=>['customer'=>['email'=>'qa@example.invalid','name'=>'QA','phone'=>'0000000000'],'service'=>['service_id'=>1,'method_id'=>1,'method_unit_id'=>1,'quantity'=>1],'staff_ids'=>[1],'duration_minutes'=>60,'timezone'=>'Australia/Melbourne','start_at'=>'2027-01-10 09:00:00'], 'reschedule'=>['staff_ids'=>[1],'duration_minutes'=>60,'timezone'=>'Australia/Melbourne','start_at'=>'2027-01-11 09:00:00']];}

foreach(['LifecycleConsent','LifecycleTestScope','LifecycleFixtureLoader','LifecycleFixtureValidator','LifecycleCustomerSafety','LifecycleMutationKeys','LifecycleBookingResult','LifecycleConflictAssertion','LifecycleEvidence','LifecycleReport','LifecycleDatabaseReadiness','LifecyclePreflight','LifecycleFailureRedactor'] as $short){class_alias('Titan\\BookingIntegration\\'.$short,$short);}
