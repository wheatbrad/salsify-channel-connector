<?php declare(strict_types=1);

namespace App\Services;

use PDO;

/**
 * Service to read data streams. 
 */
final class StreamReader
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;        
    }

    public function readStream(): void {}


}

/**
 * NOTES:
 * Does this class write to the database, or delegate to another class?
 */