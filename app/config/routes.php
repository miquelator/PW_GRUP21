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
$app->match('/home_log/comment/{id}/{title}/{user_id}','PracticaFinal\Controller\InteractionController::comment');
$app->get('/user_comments','PracticaFinal\Controller\InteractionController::userComments');
$app->get('/user_comments/delete/{id}/{id_imatge}','PracticaFinal\Controller\InteractionController::deleteComment');
$app->match('/home_log/comment/{id}/{title}/{user_id}','PracticaFinal\Controller\InteractionController::comment');
$app->get('/home_log/like/{id}/{title}/{user_id}','PracticaFinal\Controller\InteractionController::like');

//$app->get('/user_comments','PracticaFinal\Controller\InteractionController::userComments');
//$app->match('/users/add','PracticaFinal\Controller\DatabaseController::postAction');
$app->get('/register/', 'PracticaFinal\\Controller\\UserController::register');
//$app->get('/register_error/', 'PracticaFinal\\Controller\\UserController::registerError');
$app->get('/login', 'PracticaFinal\\Controller\\UserController::login');
$app->get('/edicio_perfil', 'PracticaFinal\\Controller\\UserController::edicioPerfil'); //carrega edicio perfil
$app->get('','PracticaFinal\Controller\UserController::goHome');
$app->match('/users_edit', 'PracticaFinal\\Controller\\DatabaseController::postEdicioPerfil'); //post de edicio perfil
$app->match('/users/comprovacio/register', 'PracticaFinal\\Controller\\UserController::comprovaRegister');

$app->get('/searchUser', 'PracticaFinal\\Controller\\DatabaseController::searchUser');
$app->get('/upload', 'PracticaFinal\\Controller\\PhotoController::uploadPhoto');
$app->get('/logout', 'PracticaFinal\\Controller\\UserController::logout');
$app->match('/dataPhoto', 'PracticaFinal\\Controller\\PhotoController::dataPhoto');
$app->get('/bigPhoto/{path}/{titol}/{created}/{likes}/{visits}/{user}/{id}/{private}', 'PracticaFinal\\Controller\\PhotoController::showPhoto');
$app->get('/activacio_link/{id}', 'PracticaFinal\\Controller\\UserController::activaLink');
//$app->get('/register_amb_link/{id}', 'PracticaFinal\\Controller\\UserController::registerAmbLink');
$app->get('/notificacions', 'PracticaFinal\\Controller\\InteractionController::notificacions');
$app->get('/showUser/{id}', 'PracticaFinal\\Controller\\UserController::showUser');
$app->get('/edita_imatge/delete/{id}','PracticaFinal\Controller\EditaFotoController::deleteImage');
$app->get('/edita_imatge', 'PracticaFinal\\Controller\\EditaFotoController::editaImatge');
$app->get('/edita_imatge/edita/{id}', 'PracticaFinal\\Controller\\EditaFotoController::editaImatgeForm');
$app->get('/updatePhotoInfo/{id}', 'PracticaFinal\\Controller\\EditaFotoController::updatePhotoInfo');








?>
