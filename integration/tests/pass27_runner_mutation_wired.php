<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$s=file_get_contents(__DIR__.'/../mysql/lifecycle/LifecycleRunner.php');foreach(['LifecycleAcceptanceBaseline::capture','LifecycleMutationAudit::reschedule','LifecycleMutationAudit::cancel','reschedule_audit','cancellation_audit'] as $needle)if(!str_contains($s,$needle))throw new Exception('not wired '.$needle);
echo "PASS pass27_runner_mutation_wired\n";
