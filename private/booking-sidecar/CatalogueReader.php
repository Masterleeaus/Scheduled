<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueReader {public function __construct(private \PDO $db){} public function snapshot():array {$s=CatalogueRows::normalize((new CatalogueServiceReader($this->db))->rows(),[]);$m=CatalogueRows::normalize((new CatalogueMethodReader($this->db))->rows(),['service_id']);$u=CatalogueRows::normalize((new CatalogueUnitReader($this->db))->rows(),['method_id','service_id']);$staff=CatalogueStaffRows::normalize((new CatalogueStaffReader($this->db))->rows());return CatalogueResponse::make($s,$m,$u,$staff);}}
