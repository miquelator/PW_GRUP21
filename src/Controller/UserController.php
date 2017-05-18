<?php
    namespace PracticaFinal\Controller;

use PracticaFinal\Controller\DatabaseController;
    use PracticaFinal\Model\comprovacioRegister;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController{

     public function edicioPerfil(Application $app){

         //obtinc path imatge perfil
         $database= new DatabaseController();
         $info=$database->retornaImatgeNomDataUsuari($app);

        $response = new Response();
        $content = $app['twig']-> render('edicio_perfil.twig',array('path_imatge'=>$info['img_path'],'nom_user'=>$info['username'],'data_naixement'=>$info['birthdate'],'error'=>"")); //mostrem per pantalla la pagina

        $response->setContent($content);
        return $response;
    }

    public function edicioPerfilError(Application $app){
        //obtinc path imatge perfil
        $database= new DatabaseController();
        $info=$database->retornaImatgeNomDataUsuari($app);

        $response = new Response();
        $content = $app['twig']-> render('edicio_perfil.twig',array('path_imatge'=>$info['img_path'],'nom_user'=>$info['username'],'data_naixement'=>$info['birthdate'],'error'=>"Revisa")); //mostrem per pantalla la pagina

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
        $dbc = new DatabaseController();
        $info = $dbc->searchTopViews($app);
        $info2 = $dbc->searchLastUploaded($app);

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
       // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $content = $app['twig']->render('home_logged.twig', array( 'info1' => $info,'info2' => $info2));

        $response->setContent($content);
        return $response;
    }
    public function comment(Application $app, Request $request){

        $dbc = new DatabaseController();
        $info1 = $dbc->searchTopViews($app);

        $info2 = $dbc->searchLastUploaded($app);
        //var_dump($app['session']->get('id'));

        $dbc->uploadComment($app,$request);



        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $content = $app['twig']->render('home_logged.twig', array('info1' => $info1,'info2' => $info2));

        $response->setContent($content);
        return $response;
    }
    public function userComments(Application $app)
    {

        $dbc = new DatabaseController();

        $comments = $dbc->searchCommentsUser($app);
        $c[0] = $comments[0]['comentari'];
        $c[1] = $comments[1]['comentari'];
        $c[2] = $comments[2]['comentari'];
        $c[3] = $comments[3]['comentari'];
        $c[4] = $comments[4]['comentari'];

        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        //$content = $app['twig']->render('user_comments.twig', array('c1' => $comments[0]['comentari'],'c2' => $comments[1]['comentari'],'c3' => $comments[2]['comentari'],'c4' => $comments[3]['comentari'],'c5' => $comments[4]['comentari']));
        $content = $app['twig']->render('user_comments.twig', array('c' => $c));

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

    public function showPhoto(Application $app, Request $request ){

        $response = new Response();

        var_dump($request);
        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('home.twig');
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
