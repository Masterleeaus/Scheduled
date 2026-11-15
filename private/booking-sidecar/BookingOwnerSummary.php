<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** One bounded, read-only aggregate for the owner operations screen. */
final class BookingOwnerSummary {
 public function __construct(private \PDO $db){}
 public function get(array $input):array {
  $filters=BookingOwnerFilters::from($input);$params=[];$where=BookingOwnerFilters::where($filters,$params);
  $s=$this->db->prepare('SELECT b.booking_status,COUNT(*) AS total FROM ct_bookings b WHERE 1=1'.$where.' GROUP BY b.booking_status ORDER BY b.booking_status');
  foreach($params as $i=>$p)$s->bindValue($i+1,$p,\PDO::PARAM_STR);
  $s->execute();$counts=[];$total=0;
  foreach($s->fetchAll(\PDO::FETCH_ASSOC) as $row){$counts[(string)$row['booking_status']]=(int)$row['total'];$total+=(int)$row['total'];}
  return ['total'=>$total,'by_status'=>$counts,'filters'=>$filters];
 }
}
