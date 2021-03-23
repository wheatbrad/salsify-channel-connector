<?php

// Should set these to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('America/New_York');

$settings = [];

$settings['token'] = '';
$settings['orgId'] = '';
$settings['channelId'] = '';
 
return $settings;