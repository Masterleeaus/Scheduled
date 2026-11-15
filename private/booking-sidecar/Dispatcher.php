<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** All operations use server-selected tenant connection; writes separately gated. */
final class Dispatcher {
 public function __construct(private \PDO $db,private array $tenantConfig=[]){}
 public function dispatch(string $operation,array $payload):array {
  if(in_array($operation,['owner_staff_overview','owner_staff_service_assignment','owner_staff_schedule_update'],true))return (new CleantoStaffAdministration($this->db,$this->tenantConfig))->execute($operation,$payload);
  if($operation==='booking_catalogue')return (new CatalogueReader($this->db))->snapshot();
  if(in_array($operation,['owner_service_units','owner_service_unit_update'],true))return (new CleantoServiceUnitAdministration($this->db,$this->tenantConfig))->execute($operation,$payload);
  if(in_array($operation,['owner_service_list','owner_service_create','owner_service_update'],true))return (new CleantoServiceAdministration($this->db,$this->tenantConfig))->execute($operation,$payload);
  if($operation==='services')return ['services'=>(new ServiceCatalog($this->db))->list()];
  if($operation==='busy_intervals'){$p=ConflictPayload::parse($payload);return ['busy'=>(new BookingConflictReader($this->db))->busy($p['staff_id'],$p['from'],$p['until'],$p['timezone'])];}
  if($operation==='availability')return (new AvailabilityReader($this->db))->slots($payload);
  if($operation==='booking_preview_capabilities')return BookingPreviewReadiness::response();
  if($operation==='booking_preview')return (new BookingPreviewReader($this->db,$this->tenantConfig['verified_pricing_settings']??[]))->preview($payload);
  if($operation==='health')return ['booking_database'=>'connected','booking_writes_enabled'=>BookingWriteFeatureGate::enabled($this->tenantConfig),'capabilities'=>BookingOperationStatus::forTenant($this->tenantConfig),'release_requirements'=>BookingTenantMutationDocumentation::requirements()];
  if(in_array($operation,['owner_booking_list','owner_booking_get','owner_booking_create','owner_booking_cancel','owner_booking_reschedule'],true))return (new BookingOwnerOperations($this->db,$this->tenantConfig))->dispatch($operation,$payload);
  throw new \InvalidArgumentException('Unsupported booking operation');
 }
}
