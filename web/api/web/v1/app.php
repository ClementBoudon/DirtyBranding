<?php

    date_default_timezone_set('GMT');
    require_once __DIR__.'/config.php';

    require_once __DIR__.'/vendor/autoload.php';

    use PhpWhois\PhpWhois;

    $app = new Silex\Application();
    $app['debug'] = true;


    $app['PhpWhois'] = $app->share(function ($app) {
        return new Whois();
    });


    $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
        'db.options' => array(
            'driver' => 'pdo_mysql',
            'host' => $mysql_host,
            'dbhost' => $mysql_host,
            'dbname' => $mysql_database,
            'user' => $mysql_user,
            'password' => $mysql_pass
        ),
    ));

    $app->get('/', function () use ($app) {
        return 'API DirtyBranding.';
    });

    $app->mount('/ideas', include __DIR__.'/controllers/ideas.php');
    $app->mount('/brands', include __DIR__.'/controllers/brands.php');
    $app->mount('/domains', include __DIR__.'/controllers/domains.php');
    $app->mount('/extensions', include __DIR__.'/controllers/extensions.php');
    $app->mount('/ipoffices', include __DIR__.'/controllers/ipoffices.php');

    return $app;
