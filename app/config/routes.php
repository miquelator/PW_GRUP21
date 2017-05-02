<?php

$app->get('/hello/', 'PracticaFinal\\Controller\\HelloController::indexAction');
$app->get('add/{num1}/{num2}','PracticaFinal\\Controller\\HelloController::addAction');
$app->get('/users/get/{id}','PracticaFinal\Controller\UserController::getAction');
$app->get('/home','PracticaFinal\Controller\UserController::goHome');
$app->match('/users/add','PracticaFinal\Controller\UserController::postAction');
$app->get('/register/', 'PracticaFinal\\Controller\\UserController::register');
$app->get('/login/', 'PracticaFinal\\Controller\\UserController::login');



?>
