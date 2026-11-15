<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Read-only operational listing from the authenticated owner's tenant database. */
final class BookingOwnerListing {
 public function __construct(private \PDO $db){}
 public function get(array $input):array {
  $limit=filter_var($input['limit']??30,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>100]]);
  $offset=filter_var($input['offset']??0,FILTER_VALIDATE_INT,['options'=>['min_range'=>0,'max_range'=>1000000]]);
  if($limit===false||$offset===false)throw new \InvalidArgumentException('Invalid pagination');
  $filters=BookingOwnerFilters::from($input);$params=[];$where=BookingOwnerFilters::where($filters,$params);
  $sql='SELECT b.id,b.order_id,b.booking_date_time,b.booking_status,b.staff_ids,b.service_id,b.method_id,b.method_unit_id,b.method_unit_qty,b.client_id,o.order_duration,o.client_name,o.client_email,o.client_phone FROM ct_bookings b INNER JOIN ct_order_client_info o ON o.order_id=b.order_id WHERE 1=1'.$where.' ORDER BY b.booking_date_time DESC,b.id DESC LIMIT ? OFFSET ?';
  $s=$this->db->prepare($sql);$i=1;foreach($params as $value)$s->bindValue($i++,$value,\PDO::PARAM_STR);
  $s->bindValue($i++,(int)$limit,\PDO::PARAM_INT);$s->bindValue($i,(int)$offset,\PDO::PARAM_INT);$s->execute();
  return ['bookings'=>$s->fetchAll(\PDO::FETCH_ASSOC),'limit'=>$limit,'offset'=>$offset,'filters'=>$filters];
 }
}
