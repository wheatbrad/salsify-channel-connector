<?php

use DI\ContainerBuilder;
use App\Data\SalsifyCredential;
use App\Model\FlattenEntityModel;
use App\Model\RefreshModel;
use App\Services\ObjectListener;
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
            $settings['channelId']
        );
    },

    PDO::class => function (ContainerInterface $c) {
        $settings = $c->get('settings')['db'];
        
        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        
        return new PDO($dsn, $username, $password, $flags);
    },

    FlattenEntityModel::class => function (ContainerInterface $c) {
        return new FlattenEntityModel($c->get(PDO::class));
    },

    RefreshModel::class => function (ContainerInterface $c) {
        return new RefreshModel($c->get(PDO::class));
    },

    ObjectListener::class => function (ContainerInterface $c) {
        return new ObjectListener(
            $c->get(RefreshModel::class),
            $c->get(FlattenEntityModel::class)
        );
    }
]);

if (isset($_ENV['PRODUCTION'])) {
    $containerBuilder->enableCompilation(__DIR__.'/../var/cache');
}

return $containerBuilder;