<?php 

use App\Services\ChannelGroper;

require_once __DIR__.'/vendor/autoload.php';

[
    'token' => $token,
    'orgId' => $orgId,
    'channelId' => $channelId
] = (require __DIR__.'/config/settings.php');

$channelGroper = new ChannelGroper($token, $orgId, $channelId);
$channelGroper->initiateChannelRun();
$dataStream = $channelGroper->getChannelData();

