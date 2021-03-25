<?php

use App\Data\SalsifyCredential;
use App\Services\ChannelGroper;

$container = (require_once __DIR__ .'/config/bootstrap.php')->build();
$channelGroper = new ChannelGroper(
    $container->get(SalsifyCredential::class)
);
$channelGroper->initiateChannelRun();
$dataStream = $channelGroper->getChannelData();

