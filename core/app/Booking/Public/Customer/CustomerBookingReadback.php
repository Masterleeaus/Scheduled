<?php
declare(strict_types=1);
namespace App\Booking\Public\Customer;
/** Prepared, read-only source adapter. Schema-specific table/field names are supplied by the integration. */
final class CustomerBookingReadback {
    public static function withPdo(\PDO $pdo,string $table,string $tenantColumn,string $idColumn,int $tenant,int $booking):?array {
        foreach([$table,$tenantColumn,$idColumn] as $name) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]{0,63}$/D',$name)) throw new \InvalidArgumentException('Unsafe booking schema identifier');
        }
        $stmt=$pdo->prepare('SELECT * FROM `'.$table.'` WHERE `'.$tenantColumn.'` = :tenant AND `'.$idColumn.'` = :booking LIMIT 2');
        if (!$stmt) throw new \RuntimeException('Booking query unavailable');
        $stmt->bindValue(':tenant',CustomerBookingId::parse($tenant),\PDO::PARAM_INT);
        $stmt->bindValue(':booking',CustomerBookingId::parse($booking),\PDO::PARAM_INT);
        if (!$stmt->execute()) throw new \RuntimeException('Booking query failed');
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!is_array($rows) || count($rows)!==1 || !is_array($rows[0])) return null;
        return $rows[0];
    }
}
