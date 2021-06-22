<?php declare(strict_types=1);

namespace App\Model;

use PDO;

final class HydrateModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function refreshAttributesTable(array $data): void
    {
        $this->pdo->beginTransaction();
        $this->pdo->query('DELETE FROM attributes');
        
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

    public function refreshAttributeValuesTable(array $data): void
    {
        $this->pdo->beginTransaction();
        $this->pdo->query('DELETE FROM attribute_values');

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

    public function refreshDigitalAssetsTable(array $data): void
    {
        $this->pdo->beginTransaction();
        $this->pdo->query('DELETE FROM digital_assets');

        $stmt = $this->pdo->prepare('INSERT
            INTO digital_assets (
                salsify_id,
                salsify_url,
                salsify_created_at,
                salsify_updated_at,
                salsify_asset_height,
                salsify_asset_width,
                salsify_asset_resource_type,
                salsify_filename,
                salsify_bytes,
                salsify_format,
                salsify_etag,
                salsify_system_id
            )
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');

        for ($i = 0, $l = count($data); $i < $l; $i++) {
            $stmt->bindValue(1, $data[$i]['salsify:id']);
            $stmt->bindValue(2, $data[$i]['salsify:url']);
            $stmt->bindValue(3, $data[$i]['salsify:created_at']);
            $stmt->bindValue(4, $data[$i]['salsify:updated_at']);
            $stmt->bindValue(5, $data[$i]['salsify:asset_height'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(6, $data[$i]['salsify:asset_width'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(7, $data[$i]['salsify:asset_resource_type']);
            $stmt->bindValue(8, $data[$i]['salsify:filename']);
            $stmt->bindValue(9, $data[$i]['salsify:bytes'], PDO::PARAM_INT);
            $stmt->bindValue(10, $data[$i]['salsify:format'] ?? null);
            $stmt->bindValue(11, $data[$i]['salsify:etag']);
            $stmt->bindValue(12, $data[$i]['salsify:system_id']);
            $stmt->execute();
        }

        $this->pdo->commit();
    }

    public function refreshProductsTable(array $data): void
    {
        $propsToIgnore = ['salsify:id','salsify:created_at','salsify:updated_at','salsify:version','salsify:profile_asset_id','salsify:system_id'];

        $this->pdo->beginTransaction();
        $this->pdo->query('DELETE FROM product_meta');
        $this->pdo->query('DELETE FROM products');

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
                if (in_array($prop, $propsToIgnore)) continue;
                
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