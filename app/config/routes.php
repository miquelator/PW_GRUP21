<?php

$before=function(Request $request, Application $app){ //si es crida, fa que s'activi abans aixo que la ruta que cridem
    if(!$app['session']->has('id')){
    $response=new Response();
    $content=$app['twig']->render('home.twig', ['message'=>'Abans inicia sessió']);
    $response->setContent($content);
    return $response;
    }
};


$app->get('/', 'PracticaFinal\\Controller\\BaseController::indexAction');
$app->get('/admin', 'PracticaFinal\\Controller\\BaseController::adminAction')->before($before); //abans s'activa el before per comprovar si estem loguejats
//$app->get('add/{num1}/{num2}','PracticaFinal\\Controller\\HelloController::addAction');
$app->get('/users/get/{id}','PracticaFinal\Controller\UserController::getAction');
$app->get('/home','PracticaFinal\Controller\UserController::goHome');
$app->get('/home_log','PracticaFinal\Controller\UserController::goHomeLogged');
$app->match('/users/add','PracticaFinal\Controller\DatabaseController::postAction');
$app->get('/register/', 'PracticaFinal\\Controller\\UserController::register');
$app->get('/login', 'PracticaFinal\\Controller\\UserController::login');
$app->get('/edicio_perfil', 'PracticaFinal\\Controller\\UserController::edicio_perfil');
$app->match('/users/edit', 'PracticaFinal\\Controller\\DatabaseController::postEdicioPerfil');
$app->get('','PracticaFinal\Controller\UserController::goHome');
$app->get('/showUser', 'PracticaFinal\\Controller\\UserController::showUser');


?>
