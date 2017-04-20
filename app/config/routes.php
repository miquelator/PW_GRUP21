<?php
//prova nickkiyotfututyruftftu
$app->get('/hello/', 'PracticaFinal\\Controller\\HelloController::indexAction');
$app->get('add/{num1}/{num2}','SilexApp\\Controller\\HelloController::addAction');
$app->get('/users/get/{id}','PracticaFinal\Controller\UserController::getAction');
$app->match('/users/add','PracticaFinal\Controller\UserController::postAction');
//Leleleleell OSTIA PUTA
//convis miquel
?>
