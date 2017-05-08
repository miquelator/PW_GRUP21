<?php

$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path'=>__DIR__.'/../../src/View/templates',));

$app->register(new Silex\Provider\DoctrineServiceProvider(),array(
	'db.options'=>array(
		'driver'=>'pdo_mysql',
		'dbname'=>'practica1',
		'user'=>'root',
		'password'=>'root'),
	));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir'=>__DIR__.'/../../var/cache/',
    'http_cache.esi'=>null,
));

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1',
    'assets.version_format' => '%s?version=%s',
    'assets.named_packages' => array(
        'css' => array('base_path' => '/assets/css'),
        'js' => array('base_path' => '/assets/js'),
        'images' => array('base_urls' => array('http://silexapp.dev/assets/img')),
    ),
));
$app->register(new \PracticaFinal\Providers\HelloServiceProvider(),array(
    'hello.default_name' => 'Bullshit2',
));
use Silex\Provider\FormServiceProvider;

$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(),array(
    'translator.domains' => array(),
));

$app->register(new Silex\Provider\LocaleServiceProvider());

?>