<?php declare(strict_types=1);

namespace App\Model;

use PDO;
use Throwable;

final class FlattenEntityModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Vendor Number
     * Product Name                 description
     * Bullet Points                features
     * Faucet Installation Type     installation
     * Cartridge                    cartridge
     * Flow Rate                    flow_rate
     * Finish                       finish
     * HWW Model #                  model_number
     */
    
    public function flattenFaucets(): void
    {
        $this->pdo->query('CREATE TEMPORARY TABLE tmp_faucets SELECT salsify_id FROM products WHERE attribute = \'Product Group\' AND attribute_value = \'Faucets\'');
        $this->pdo->query('DROP TABLE IF EXISTS faucets');
        $this->pdo->query('CREATE TABLE faucets (
            `salsify_id` varchar(60) NOT NULL,
            `retailer` varchar(80) DEFAULT NULL,
            `collection_name` varchar(100) DEFAULT NULL,
            `brand` varchar(80) DEFAULT NULL,
            `group` varchar(80) DEFAULT NULL,
            `sub_group` varchar(80) DEFAULT NULL,
            `description` varchar(500) DEFAULT NULL,
            `features` varchar(2500) DEFAULT NULL,
            `installation` varchar(191) DEFAULT NULL,
            `flow_rate` varchar(191) DEFAULT NULL,
            `finish` varchar(191) DEFAULT NULL,
            `certification` varchar(191) DEFAULT NULL,
            `model_number` varchar(191) DEFAULT NULL,
            `upc` varchar(80) DEFAULT NULL,
            `salsify_bytes` varchar(40) DEFAULT NULL,
            `salsify_url` varchar(191) DEFAULT NULL,
            `image_filename` varchar(191) DEFAULT NULL,
            KEY `salsify_id` (`salsify_id`),
            KEY `retailer` (`retailer`),
            KEY `collection_name` (`collection_name`),
            KEY `brand` (`brand`),
            KEY `group` (`group`),
            KEY `sub_group` (`sub_group`),
            KEY `flow_rate` (`flow_rate`),
            KEY `finish` (`finish`),
            KEY `certification` (`certification`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->pdo->query('INSERT INTO faucets (
                `salsify_id`,
                `retailer`,
                `collection_name`,
                `brand`,
                `group`,
                `sub_group`,
                `description`,
                `features`,
                `installation`,
                `flow_rate`,
                `finish`,
                `certification`,
                `model_number`,
                `upc`,
                `salsify_bytes`,
                `salsify_url`,
                `image_filename`
            )
            SELECT salsify_id,
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Retailer\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Collection Name\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Brand\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Product Sub-Group\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Product Sub-Sub-Group\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Product Name\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Bullet Points\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Faucet Installation Type\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Flow Rate (GPM|LPM)\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Finish\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Certifications\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'HWW Model #\'),
            (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'UPC\'),
            (SELECT salsify_bytes FROM digital_assets WHERE salsify_id = (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Hero Image 1\')),
            (SELECT salsify_url FROM digital_assets WHERE salsify_id = (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Hero Image 1\')),
            (SELECT salsify_filename FROM digital_assets WHERE salsify_id = (SELECT attribute_value FROM products WHERE products.salsify_id = tmp_faucets.salsify_id AND attribute = \'Hero Image 1\'))
            FROM tmp_faucets');
        $this->pdo->query('DROP TEMPORARY TABLE tmp_faucets');
    }
}