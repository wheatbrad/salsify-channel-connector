<?php

use App\Services\ChannelGroper;

$container = (require_once __DIR__.'/config/bootstrap.php')->build();

$channelGroper = $container->get(ChannelGroper::class);
$channelGroper->initiateChannelRun();
$dataStream = $channelGroper->getChannelData();
