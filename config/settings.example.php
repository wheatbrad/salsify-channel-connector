<?php

// Should set these to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', '1');

$settings = [];

$settings['token'] = '';
$settings['orgId'] = '';
$settings['channelId'] = '';
 
return $settings;