<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueUnitReader {public function __construct(private \PDO $db){} public function rows():array {return CatalogueQuery::rows($this->db,"SELECT u.id,u.methods_id AS method_id,u.services_id AS service_id,u.units_title AS title,u.minlimit,u.maxlimit FROM ct_service_methods_units u INNER JOIN ct_services_method m ON m.id=u.methods_id AND m.service_id=u.services_id AND m.status='E' INNER JOIN ct_services s ON s.id=u.services_id AND s.status='E' WHERE u.status='E' ORDER BY u.position,u.id LIMIT 3001",3000);}}
