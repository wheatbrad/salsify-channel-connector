<?php

use App\Services\ChannelGroper;
use App\Services\ObjectListener;
use JsonStreamingParser\Parser;

$container = (require_once __DIR__ .'/../config/bootstrap.php')->build();

$channelGroper = $container->get(ChannelGroper::class);
$dataStream = $channelGroper->getChannelData();

try {
    $parser = new Parser($dataStream, new ObjectListener());
    $parser->parse();
} catch (Exception $e) {
    fclose($dataStream);
    throw $e;
}
