<?php
namespace Titan\BookingSidecar;
final class ServiceCatalog {
    public function __construct(private \PDO $db) {}
    /** Read-only Cleanto catalogue; no legacy PHP endpoint or global connection required. */
    public function list(): array {
        $statement = $this->db->prepare("SELECT id, title, description, color, image, position FROM ct_services WHERE status = ? ORDER BY position, id LIMIT 250");
        if($statement===false||$statement->execute(['E'])!==true)throw new \RuntimeException('Service catalogue query failed');
        $result = [];
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = [
                'id' => (int)$row['id'], 'title' => (string)$row['title'],
                'description' => (string)$row['description'], 'color' => (string)$row['color'],
                'image' => (string)$row['image'], 'position' => (int)$row['position'],
            ];
        }
        return $result;
    }
}
