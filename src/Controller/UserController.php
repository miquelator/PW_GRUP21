<?php
    namespace PracticaFinal\Controller;

    use PracticaFinal\Model\comprovacioRegister;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController{

     public function edicio_perfil(Application $app){

        $response = new Response();
        $content = $app['twig']-> render('edicio_perfil.twig'); //mostrem per pantalla la pagina

        $response->setContent($content);
        return $response;
    }
        public function getAction(Application $app, $id)
    {
        $sql = "SELECT username FROM user WHERE id = ?";
        $user = $app['db']->fetchAssoc($sql, array((int)$id));
        $response = new Response();
        if (!$user) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'User not found'
                ]
            );

        } else {
            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('user.twig', [
                    'user' => $user
                ]
            );
        }
        $response->setContent($content);
        return $response;
    }
    public function uploadPhoto(Application $app)
    {

        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('upload.twig');



        $response->setContent($content);
        return $response;
    }

    public function goHome(Application $app)
    {

        $response = new Response();

            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('home.twig');



        $response->setContent($content);
        return $response;
    }
    public function goHomeLogged(Application $app)
    {

        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('home_logged.twig');


        $dbc = new DatabaseController();
        $dbc->searchTopViews($app);

        $response->setContent($content);
        return $response;
    }


        public function login(Application $app, Request $request ){

            $response = new Response();

            $name = $request->get('name');
            $password = $request->get('password');

            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('login.twig');
            $response->setContent($content);
            return $response;





        }






    public function register(Application $app){

        $response = new Response();
        $content = $app['twig']-> render('main_register.twig',array('error' => "")); //no envio res com a missatge d'error
        $response->setContent($content);
        return $response;
    }

    public function registerError(Application $app){ //envio amb un missatge d'error

        $response = new Response();
        $content = $app['twig']-> render('main_register.twig',array('error' => "Error: Revisa els camps")); // envio  missatge d'error
        $response->setContent($content);
        return $response;
    }

    public function comprovaRegister(Application $app, Request $request){
        $comprovacio= new comprovacioRegister();
        $comprovacio->comprovacioRegisterModel($app, $request);
    }
    }

        ?>
