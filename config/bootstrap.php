<?php

use DI\ContainerBuilder;
use App\Data\SalsifyCredential;
use Psr\Container\ContainerInterface;

require_once __DIR__.'/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'settings' => (require __DIR__.'/settings.php'),
    SalsifyCredential::class => function (ContainerInterface $c) {
        $settings = $c->get('settings');

        return new SalsifyCredential(
            $settings['token'],
            $settings['orgId'],
            $settings['channelId'],
        );
    }
]);

if (isset($_ENV['PRODUCTION'])) {
    $containerBuilder->enableCompilation(__DIR__.'/../tmp');
    $containerBuilder->writeProxiesToFile(true, __DIR__.'/../tmp/proxies');
}

return $containerBuilder;