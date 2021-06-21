<?php

namespace App\Model;

interface DatabaseSeedInterface
{
    public function seedData(array $data): void;
}