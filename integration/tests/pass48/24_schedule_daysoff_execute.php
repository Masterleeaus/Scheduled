<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\{BookingLiveWriteSnapshot,BookingPriceReader,BookingConflictReader,BookingSchemaColumnsReader,BookingMutationCustomerScope,BookingLegacyStatusWriter,CatalogueQuery,ServiceCatalog,ScheduleReader};
$db=new class extends Pass47PDO {
 public bool $failSecondPrepare=false;private int $calls=0;
 public function prepare(string $query,array $options=[]):PDOStatement|false { $this->calls++;if($this->failSecondPrepare && $this->calls===2)return false;return parent::prepare($query,$options);}
};
$s=new P48Statement;
$s->ok=false;$db->queue=[$s];rejects48(fn()=>(new ScheduleReader($db))->daysOff(1,'2026-11-15'),'Days-off query failed');
echo "PASS48 24 schedule_daysoff_execute OK\n";
