<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
use Titan\BookingSidecar\{BookingLiveWriteSnapshot,BookingPriceReader,BookingConflictReader,BookingSchemaColumnsReader,BookingMutationCustomerScope,BookingLegacyStatusWriter,CatalogueQuery,ServiceCatalog,ScheduleReader};
$db=new class extends Pass47PDO {
 public bool $failSecondPrepare=false;private int $calls=0;
 public function prepare(string $query,array $options=[]):PDOStatement|false { $this->calls++;if($this->failSecondPrepare && $this->calls===2)return false;return parent::prepare($query,$options);}
};
$s=new P48Statement;
$s->records=[['id'=>1,'services_id'=>1,'methods_id'=>1,'minlimit'=>1,'maxlimit'=>10,'base_price'=>'1.00','status'=>'E']];$db->queue=[$s];$db->failSecondPrepare=true;rejects48(fn()=>(new BookingPriceReader($db))->inspect(1),'Pricing rule query failed');
echo "PASS48 7 price_rule_prepare OK\n";
