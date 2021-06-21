<?php declare(strict_types=1);

namespace App\Model;

use PDO;

class DigitalAssetsModel implements DatabaseSeedInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function seedData(array $data): void
    {
        $this->pdo->beginTransaction();
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
            $stmt->bindValue(5, @$data[$i]['salsify:asset_height'], PDO::PARAM_INT);
            $stmt->bindValue(6, @$data[$i]['salsify:asset_width'], PDO::PARAM_INT);
            $stmt->bindValue(7, $data[$i]['salsify:asset_resource_type']);
            $stmt->bindValue(8, $data[$i]['salsify:filename']);
            $stmt->bindValue(9, $data[$i]['salsify:bytes'], PDO::PARAM_INT);
            $stmt->bindValue(10, @$data[$i]['salsify:format']);
            $stmt->bindValue(11, $data[$i]['salsify:etag']);
            $stmt->bindValue(12, $data[$i]['salsify:system_id']);
            $stmt->execute();
        }
        
        $this->pdo->commit();
    }
}