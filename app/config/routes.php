<?php


$app->get('/users/get/{id}','PracticaFinal\Controller\ServerController::getAction');
$app->get('/home','PracticaFinal\Controller\ServerController::goHome');
$app->get('','PracticaFinal\Controller\ServerController::goHome');
$app->match('/users/add','PracticaFinal\Controller\ServerController::postAction');
$app->get('/register/', 'PracticaFinal\\Controller\\ServerController::register');
$app->get('/login/', 'PracticaFinal\\Controller\\ServerController::login');



?>
