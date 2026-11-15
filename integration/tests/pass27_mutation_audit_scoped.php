<?php
declare(strict_types=1);
require __DIR__.'/../mysql/lifecycle/bootstrap.php';
$s=file_get_contents(__DIR__.'/../mysql/lifecycle/LifecycleMutationAudit.php');foreach(['LifecycleSiblingIntegrity::verify','LifecycleOrderIntegrity::verify','LifecycleImmutableIdentity::verify'] as $n)if(substr_count($s,$n)<2)throw new Exception('incomplete audit');
echo "PASS pass27_mutation_audit_scoped\n";
