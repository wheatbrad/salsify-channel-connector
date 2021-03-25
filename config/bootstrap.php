<?php

use App\Services\ChannelGroper;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

require_once __DIR__.'/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'settings' => function () {
        return require __DIR__.'/settings.php';
    },

    ChannelGroper::class => function (ContainerInterface $container) {
        $httpClient = new Client();

        [
            'token' => $token,
            'orgId' => $orgId,
            'channelId' => $channelId
        ] = $container->get('settings');

        return new ChannelGroper($httpClient, $token, $orgId, $channelId);
    }
]);

return $containerBuilder;