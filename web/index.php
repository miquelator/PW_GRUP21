<?php
//ini_set('display_errors',1);
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../app/config/app.php';
require __DIR__.'/../app/config/prod.php';
require __DIR__ .'/../app/config/routes.php';
$app['debug']=false;
$app->run();


?>