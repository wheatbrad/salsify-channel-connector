<?php

use App\Services\ChannelGroper;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

return [
    'setting' => function () {
        return require __DIR__ . 'settings.php';
    },

    ChannelGroper::class => function (ContainerInterface $container) {
        [
            'token' => $token,
            'orgId' => $orgId,
            'channelId' => $channelId
        ] = $container->get('settings');

        $httpClient = new Client();

        return new ChannelGroper($httpClient, $token, $orgId, $channelId);
    }
];