<?php declare(strict_types=1);

namespace App\Model;

use PDO;

class AttributesModel implements DatabaseSeedInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function seedData(array $data): void
    {
        $this->pdo->beginTransaction();
        $this->pdo->query('DELETE from attributes');
        
        $stmt = $this->pdo->prepare('INSERT
            INTO attributes (
                salsify_id,
                salsify_name,
                salsify_data_type,
                salsify_attribute_group,
                salsify_created_at,
                salsify_updated_at,
                salsify_system_id
            ) 
            VALUES (?,?,?,?,?,?,?)');

        for ($i = 0, $l = count($data); $i < $l; $i++) {
            $stmt->execute([
                $data[$i]['salsify:id'],
                $data[$i]['salsify:name'],
                $data[$i]['salsify:data_type'],
                $data[$i]['salsify:attribute_group'],
                $data[$i]['salsify:created_at'],
                $data[$i]['salsify:updated_at'],
                $data[$i]['salsify:system_id']
            ]);

        }
        
        $this->pdo->commit();
    }
}