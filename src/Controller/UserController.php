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
/*
    public function edicioPerfilError(Application $app){
        //obtinc path imatge perfil
        $database= new DatabaseController();
        $info=$database->retornaImatgeNomDataUsuari($app);

        $response = new Response();
        $content = $app['twig']-> render('edicio_perfil.twig',array('path_imatge'=>$info['img_path'],'nom_user'=>$info['username'],'data_naixement'=>$info['birthdate'],'error'=>"Revisa")); //mostrem per pantalla la pagina

        $response->setContent($content);
        return $response;
    }
*/

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

        for ($i = 0; $i < 5; $i++) {

            $tv[$i] = $info[$i]['img_path'];
            $lu[$i] = $info2[$i]['img_path'];

            $titles1[$i] = $info[$i]['title'];
            $titles2[$i] = $info2[$i]['title'];

//            $users1[i] = $info[i]['user'];
//            $users2[i] = $info2[i]['user'];
            $data1 = substr($info[$i]['created_at'], 0, 10);
            $data2 = substr($info2[$i]['created_at'], 0, 10);

            $dates1[$i] = $data1;
            $dates2[$i] = $data2;

            $likes1[$i] = $info[$i]['likes'];
            $likes2[$i] = $info2[$i]['likes'];

            $views1[$i] = $info[$i]['visits'];

        }
//        $tv[0] = $info[0]['img_path'];
//        $tv[1] = $info[1]['img_path'];
//        $tv[2] = $info[2]['img_path'];
//        $tv[3] = $info[3]['img_path'];
//        $tv[4] = $info[4]['img_path'];
//
//        $lu[0] = $info2[0]['img_path'];
//        $lu[1] = $info2[1]['img_path'];
//        $lu[2] = $info2[2]['img_path'];
//        $lu[3] = $info2[3]['img_path'];
//        $lu[4] = $info2[4]['img_path'];
//
//        $titles1[0] = $info[0]['title'];
//        $titles1[1] = $info[1]['title'];
//        $titles1[2] = $info[2]['title'];
//        $titles1[3] = $info[3]['title'];
//        $titles1[4] = $info[4]['title'];
//


        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
       // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $content = $app['twig']->render('home_logged.twig', array('tv' => $tv, 'lu' => $lu, 't1' => $titles1, 't2' => $titles2,'d1' => $dates1, 'd2' => $dates2, 'l1' => $likes1, 'l2' => $likes2, 'v1' => $views1));

        $response->setContent($content);
        return $response;
    }
    public function comment(Application $app){

        $dbc = new DatabaseController();
        $info1 = $dbc->searchTopViews($app);

        $info2 = $dbc->searchLastUploaded($app);
        //var_dump($app['session']->get('id'));
        $dbc->uploadComment($app);


        for ($i = 0; $i < count($info1); $i++) {

            $tv[$i] = $info1[$i]['img_path'];

            $titles1[$i] = $info1[$i]['title'];

            $data1 = substr($info1[$i]['created_at'], 0, 10);

            $dates1[$i] = $data1;

            $likes1[$i] = $info1[$i]['likes'];

            $views1[$i] = $info1[$i]['visits'];

        }

        for ($i = 0; $i < count($info2); $i++) {

            $lu[$i] = $info2[$i]['img_path'];

            $titles2[$i] = $info2[$i]['title'];

            $data2 = substr($info2[$i]['created_at'], 0, 10);

            $dates2[$i] = $data2;

            $likes2[$i] = $info2[$i]['likes'];


        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $content = $app['twig']->render('home_logged.twig', array('tv' => $tv, 'lu' => $lu, 't1' => $titles1, 't2' => $titles2,'d1' => $dates1, 'd2' => $dates2, 'l1' => $likes1, 'l2' => $likes2, 'v1' => $views1));

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
            $content = $app['twig']->render('login.twig',array('error'=>""));
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
