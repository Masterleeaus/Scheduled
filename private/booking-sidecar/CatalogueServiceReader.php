<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueServiceReader {public function __construct(private \PDO $db){} public function rows():array {return CatalogueQuery::rows($this->db,"SELECT id,title FROM ct_services WHERE status='E' ORDER BY position,id LIMIT 251",250);}}
