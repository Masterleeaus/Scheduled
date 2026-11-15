<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\{BookingLiveWriteSnapshot,BookingPriceReader,BookingConflictReader,BookingSchemaColumnsReader,BookingMutationCustomerScope,BookingLegacyStatusWriter,CatalogueQuery,ServiceCatalog,ScheduleReader};
$db=new class extends Pass47PDO {
 public bool $failSecondPrepare=false;private int $calls=0;
 public function prepare(string $query,array $options=[]):PDOStatement|false { $this->calls++;if($this->failSecondPrepare && $this->calls===2)return false;return parent::prepare($query,$options);}
};
$s=new P48Statement;
$db->failPrepare=true;rejects48(fn()=>(new BookingMutationCustomerScope($db))->assertBookingOwner(1,2),'Customer ownership query failed');
echo "PASS48 13 customer_scope_prepare OK\n";
