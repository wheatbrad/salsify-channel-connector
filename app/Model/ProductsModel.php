<?php declare(strict_types=1);

namespace App\Model;

use PDO;

class ProductsModel implements DatabaseSeedInterface
{
    /**
     * PDO database connection
     */
    private PDO $pdo;

    protected array $ignoredProps;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->ignoredProps = [
            'salsify:id',
            'salsify:created_at',
            'salsify:updated_at',
            'salsify:version',
            'salsify:profile_asset_id',
            'salsify:system_id'
        ];
    }

    public function seedData(array $data): void
    {
        $this->pdo->beginTransaction();
        $stmtMeta = $this->pdo->prepare('INSERT
            INTO product_meta (
                salsify_id,
                salsify_created_at,
                salsify_updated_at,
                salsify_system_id
            ) 
            VALUES (?,?,?,?)');

        $stmtData = $this->pdo->prepare('INSERT
            INTO products (
                salsify_id,
                attribute,
                attribute_value,
                attribute_type
            )
            VALUES (?,?,?,?)');

        for ($i = 0, $l = count($data); $i < $l; $i++) {
            $stmtMeta->execute([
                $data[$i]['salsify:id'],
                $data[$i]['salsify:created_at'],
                $data[$i]['salsify:updated_at'],
                $data[$i]['salsify:system_id']
            ]);

            foreach ($data[$i] as $prop => $value) {
                if (in_array($prop, $this->ignoredProps)) continue;

                $stmtData->bindValue(1, $data[$i]['salsify:id']);
                $stmtData->bindValue(2, $prop);
                
                if (is_array($value)) {
                    $stmtData->bindValue(3, implode(', ', $value));
                }
                elseif (is_bool($value)) {
                    $stmtData->bindValue(3, (string) $value);
                }
                else {
                    $stmtData->bindValue(3, $value);
                }

                $prop = addslashes($prop);
                // Make call to attributes table to get attribute type
                $result = $this->pdo->query("SELECT salsify_data_type FROM attributes WHERE salsify_id = '$prop'");
                $type = $result->fetchColumn(0);
                $stmtData->bindValue(4, $type);
                $stmtData->execute();
            }
        }
        
        $this->pdo->commit();
    }
}