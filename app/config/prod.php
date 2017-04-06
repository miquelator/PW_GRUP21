<?php

$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path'=>__DIR__.'/../../src/View/templates',));

$app->register(new Silex\Provider\AssetServiceProvider(),array(

	'assets.vesion'=>'v1',
	'assets.version_format'=>'%s?version=%s',
	'assets.named_packages'=>array(
		'css'=>array('base_path' => '/assets/css'),
		'js' => array('base_path' => '/assets/js'),
		'images' => array('base_urls'=>array('http://silexapp.dev/assets/img')),
		),
));
$app->register(new \SilexApp\Providers\HelloServiceProvider(),array(
    'hello.default_name' => 'Bullshit2',
));



?>