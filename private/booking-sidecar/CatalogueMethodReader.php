<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueMethodReader {public function __construct(private \PDO $db){} public function rows():array {return CatalogueQuery::rows($this->db,"SELECT m.id,m.service_id,m.method_title AS title FROM ct_services_method m INNER JOIN ct_services s ON s.id=m.service_id AND s.status='E' WHERE m.status='E' ORDER BY m.position,m.id LIMIT 1001",1000);}}
