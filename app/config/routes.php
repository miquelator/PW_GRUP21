<?php

$before=function(Request $request, Application $app){ //si es crida, fa que s'activi abans aixo que la ruta que cridem
    if(!$app['session']->has('id')){
    $response=new Response();
    $content=$app['twig']->render('home.twig', ['message'=>'Abans inicia sessió']);
    $response->setContent($content);
    return $response;
    }
};



$app->get('/users/get/{id}','PracticaFinal\Controller\UserController::getAction');
$app->get('/home','PracticaFinal\Controller\UserController::goHome');
$app->get('/home_log','PracticaFinal\Controller\UserController::goHomeLogged');
$app->match('/home_log/comment','PracticaFinal\Controller\UserController::comment');
$app->get('/user_comments','PracticaFinal\Controller\UserController::userComments');
//$app->match('/users/add','PracticaFinal\Controller\DatabaseController::postAction');
$app->get('/register/', 'PracticaFinal\\Controller\\UserController::register');
//$app->get('/register_error/', 'PracticaFinal\\Controller\\UserController::registerError');
$app->get('/login', 'PracticaFinal\\Controller\\UserController::login');
$app->get('/edicio_perfil', 'PracticaFinal\\Controller\\UserController::edicioPerfil'); //carrega edicio perfil
$app->get('','PracticaFinal\Controller\UserController::goHome');
$app->match('/users_edit', 'PracticaFinal\\Controller\\DatabaseController::postEdicioPerfil'); //post de edicio perfil
$app->match('/users/comprovacio/register', 'PracticaFinal\\Controller\\UserController::comprovaRegister');

$app->get('/searchUser', 'PracticaFinal\\Controller\\DatabaseController::searchUser');
$app->get('/upload', 'PracticaFinal\\Controller\\UserController::uploadPhoto');
$app->get('/logout', 'PracticaFinal\\Controller\\UserController::logout');
$app->match('/dataPhoto', 'PracticaFinal\\Controller\\DatabaseController::dataPhoto');
$app->get('/bigPhoto/{path}/{titol}/{created}/{likes}/{visits}', 'PracticaFinal\\Controller\\UserController::showPhoto');
$app->get('/activacio_link/{id}', 'PracticaFinal\\Controller\\UserController::activaLink');
//$app->get('/register_amb_link/{id}', 'PracticaFinal\\Controller\\UserController::registerAmbLink');




?>
