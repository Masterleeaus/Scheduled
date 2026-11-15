<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueStaffReader {public function __construct(private \PDO $db){} public function rows():array {return CatalogueQuery::rows($this->db,"SELECT id,first_name,last_name FROM ct_users WHERE status='E' AND usertype='staff' ORDER BY first_name,last_name,id LIMIT 251",250);}}
