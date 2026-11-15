<?php
declare(strict_types=1);
/** Independent structural gate for the *actual* output of three disposable MariaDB acceptance programs. */
final class Pass102EvidenceGate {
    public static function verify(array $infra, array $life, array $race): array {
        if (($infra['scope'] ?? null) !== 'database_infrastructure_only'
            || ($infra['decision']['infrastructure_ready'] ?? null) !== true
            || ($infra['decision']['booking_writes_enabled'] ?? null) !== false
            || ($infra['booking_creation_verified'] ?? null) !== false
            || ($infra['schema']['valid'] ?? null) !== true
            || ($infra['lock_contention']['isolated'] ?? null) !== true
            || ($infra['lock_contention']['released'] ?? null) !== true
            || ($infra['transaction']['rollback'] ?? null) !== true) {
            throw new RuntimeException('Database infrastructure evidence incomplete');
        }
        if (($life['scope'] ?? null) !== 'disposable_mysql_test_database'
            || ($life['production_booking_writes_authorized'] ?? null) !== false
            || !is_array($life['details'] ?? null)) {
            throw new RuntimeException('Lifecycle evidence scope or booking state invalid');
        }
        foreach (['real_insert','idempotency','sequential_overlap','reschedule','cancel'] as $stage) {
            if (($life['evidence'][$stage] ?? null) !== true) {
                throw new RuntimeException('Unproven booking lifecycle stage: '.$stage);
            }
        }
        if (($race['scope'] ?? null) !== 'disposable_mysql_test_database'
            || ($race['production_booking_writes_authorized'] ?? null) !== false
            || ($race['actual_two_process_attempts'] ?? null) !== true
            || ($race['one_winner_verified'] ?? null) !== true
            || ($race['database_evidence']['persisted'] ?? null) !== true
            || ($race['database_evidence']['overlap_scan_passed'] ?? null) !== true
            || ($race['database_evidence']['overlapping_booking_count'] ?? null) !== 1) {
            throw new RuntimeException('Unproven persisted two-worker concurrency evidence');
        }
        if (($race['database'] ?? null) !== 'titan_booking_test_acceptance') {
            throw new RuntimeException('Unexpected database in concurrency report');
        }
        return ['disposable_database_infrastructure'=>true,
            'booking_lifecycle_evidence'=>true,
            'persisted_concurrency_evidence'=>true,
            'production_release_authorized'=>false,
            'payment_settlement_verified'=>false];
    }
    public static function decode(string $path): array {
        if (!is_file($path) || filesize($path) === 0 || filesize($path) > 1048576) {
            throw new RuntimeException('Acceptance report missing or too large');
        }
        $value=json_decode((string)file_get_contents($path),true,64,JSON_THROW_ON_ERROR);
        if (!is_array($value) || array_keys($value) === range(0,count($value)-1)) {
            throw new RuntimeException('Acceptance report must be a JSON object');
        }
        return $value;
    }
}
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    if (PHP_SAPI !== 'cli' || $argc !== 4) {fwrite(STDERR,"Usage: verify_reports.php INFRA LIFECYCLE CONCURRENCY\n");exit(78);}
    try {
        $result=Pass102EvidenceGate::verify(
            Pass102EvidenceGate::decode($argv[1]),
            Pass102EvidenceGate::decode($argv[2]),
            Pass102EvidenceGate::decode($argv[3]));
        echo json_encode(['pass102_gate'=>'disposable_acceptance_evidence_complete','checks'=>$result],JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;
    } catch (Throwable $ex) {fwrite(STDERR,'FAIL: '. $ex->getMessage().PHP_EOL);exit(1);}
}
