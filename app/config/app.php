<?php

use Silex\Application;
$app = new Application();
$app['app.name']='Bullshit';
$app['calc']=function(){
    return new \PracticaFinal\Model\Services\Calculator();
};

return $app;

?>

