<?php

// Should set these to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', '1');

$settings = [];

$settings['token'] = '';
$settings['orgId'] = '';
$settings['channelId'] = '';

$settings['db'] = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'username' => '',
    'database' => '',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'flags' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        // Set character set
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
    ]
];
 
return $settings;