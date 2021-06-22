<?php declare(strict_types=1);

namespace App\Model;

use PDO;

class AttributeValuesModel implements DatabaseSeedInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function seedData(array $data): void
    {
        $this->pdo->beginTransaction();
        $this->pdo->query('DELETE from attribute_values');
        
        $stmt = $this->pdo->prepare('INSERT
            INTO attribute_values (
                salsify_id,
                salsify_attribute_id,
                salsify_name,
                salsify_created_at,
                salsify_updated_at
            ) 
            VALUES (?,?,?,?,?)');

        for ($i = 0, $l = count($data); $i < $l; $i++) {
            $stmt->execute([
                $data[$i]['salsify:id'],
                $data[$i]['salsify:attribute_id'],
                $data[$i]['salsify:name'],
                $data[$i]['salsify:created_at'],
                $data[$i]['salsify:updated_at']
            ]);

        }
        
        $this->pdo->commit();
    }
}