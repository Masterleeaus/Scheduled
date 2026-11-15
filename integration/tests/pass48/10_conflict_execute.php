<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\{BookingLiveWriteSnapshot,BookingPriceReader,BookingConflictReader,BookingSchemaColumnsReader,BookingMutationCustomerScope,BookingLegacyStatusWriter,CatalogueQuery,ServiceCatalog,ScheduleReader};
$db=new class extends Pass47PDO {
 public bool $failSecondPrepare=false;private int $calls=0;
 public function prepare(string $query,array $options=[]):PDOStatement|false { $this->calls++;if($this->failSecondPrepare && $this->calls===2)return false;return parent::prepare($query,$options);}
};
$s=new P48Statement;
$s->ok=false;$db->queue=[$s];rejects48(fn()=>(new BookingConflictReader($db))->busy(2,'2026-11-15 10:00:00','2026-11-15 12:00:00','Australia/Melbourne'),'Conflict query failed');
echo "PASS48 10 conflict_execute OK\n";
