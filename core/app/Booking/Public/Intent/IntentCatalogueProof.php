<?php
declare(strict_types=1);
namespace App\Booking\Public\Intent;

/** Enforce that all selected identifiers belong to one current tenant catalogue. */
final class IntentCatalogueProof {
    public static function validate(array $selection, array $catalogue): void {
        if (($catalogue['preview_only'] ?? null) !== true || ($catalogue['booking_writes_enabled'] ?? null) !== false) {
            throw new \RuntimeException('Untrusted public booking catalogue');
        }
        $relations = [
            'services' => ['field' => 'service_id', 'parent' => null],
            'methods' => ['field' => 'method_id', 'parent' => ['service_id', 'service_id']],
            'units' => ['field' => 'unit_id', 'parent' => ['method_id', 'method_id']],
            'staff' => ['field' => 'staff_id', 'parent' => null],
        ];
        foreach ($relations as $collection => $rule) {
            $rows = $catalogue[$collection] ?? null;
            if (!is_array($rows) || !array_is_list($rows) || count($rows) > 1000) {
                throw new \RuntimeException('Invalid booking catalogue');
            }
            $matches = 0;
            foreach ($rows as $row) {
                if (!is_array($row) || !is_int($row['id'] ?? null)) {
                    throw new \RuntimeException('Malformed booking catalogue');
                }
                if ($row['id'] !== $selection[$rule['field']]) continue;
                $matches++;
                if ($rule['parent'] !== null) {
                    [$parentField, $selectedField] = $rule['parent'];
                    if (!is_int($row[$parentField] ?? null) || $row[$parentField] !== $selection[$selectedField]) {
                        throw new \InvalidArgumentException('Booking catalogue relationship mismatch');
                    }
                }
            }
            if ($matches !== 1) throw new \InvalidArgumentException('Selected booking item unavailable');
        }
    }
}
