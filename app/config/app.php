<?php
//fuck everything in sightkk
use Silex\Application;
$app = new Application();
$app['name']='Bullshit';

$app['calc']=function(){
    return new \PracticaFinal\Model\Services\Calculator();
};

return $app;

?>

