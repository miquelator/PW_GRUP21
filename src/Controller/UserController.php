<?php
    namespace PracticaFinal\Controller;

use PracticaFinal\Controller\DatabaseController;
    use PracticaFinal\Model\comprovacioRegister;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController{

    public function edicioPerfil(Application $app)
    {


        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if (!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setContent($content);
            return $response;
        } else { //esta loguejat


            //obtinc path imatge perfil
            $database = new DatabaseController();
            $info = $database->retornaImatgeNomDataUsuari($app);
            $response = new Response();
            $content = $app['twig']->render('edicio_perfil.twig', array(
                'path_imatge' => $info['img_path'],
                'nom_user' => $info['username'],
                'data_naixement' => $info['birthdate'],
                'error' => ""
            )); //mostrem per pantalla la pagina

            $response->setContent($content);
            return $response;
        }
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

    public function showUser(Application $app, Request $request)
    {

        $response = new Response();

        $id=$request->get('id');
        $sql= "SELECT * FROM user WHERE id = ? ";
        $sql2= "SELECT * FROM image WHERE user_id = ? and private = 0 ORDER BY created_at";
        $sql3= "SELECT count(id) FROM comentaris WHERE id_user = ?";


        $info = $app['db']->fetchAssoc($sql, array ((string) $id));
        $fotos = $app['db']->fetchAll($sql2, array ((string) $id));
        $numcom = $app['db']->fetchAll($sql3, array ((string) $id));

        $numcom2 = $numcom[0]['count(id)'];

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('showUser.twig',array('name'=>$info['username'],'email'=>$info['email'],'image'=>$info['img_path'],'fotos'=>$fotos,'numcom'=>$numcom2));



        $response->setContent($content);
        return $response;
    }

    public function goHome(Application $app)
    {

        $dbc = new DatabaseController();
        $info = $dbc->searchTopViews($app);
        $info2 = $dbc->searchLastUploaded($app);
        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('home.twig', array('info1' => $info, 'info2' => $info2));


        $response->setContent($content);
        return $response;
    }


    public function goHomeLogged(Application $app){
        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if (!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setContent($content);
            return $response;
        }

        $dbc = new DatabaseController();
        $info = $dbc->searchTopViews($app);
        $info2 = $dbc->searchLastUploaded($app);

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $content = $app['twig']->render('home_logged.twig', array('info1' => $info, 'info2' => $info2));

        $response->setContent($content);
        return $response;
    }



    public function login(Application $app, Request $request)
    {

        $response = new Response();

        $name = $request->get('name');
        $password = $request->get('password');

            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('login.twig',array('error'=>""));
            $response->setContent($content);
            return $response;


        }







    public function register(Application $app){ //pas 1 de register

        $response = new Response();
        $linkbool = false;

        $content = $app['twig']->render('main_register.twig', array(
            'error' => "",
            'link_activacio' => "",
            'linkbool' => $linkbool
        )); //no envio res com a missatge d'error
        $response->setContent($content);
        return $response;
    }

    /*
        public function registerError(Application $app){ //envio amb un missatge d'error

            $response = new Response();
            $linkbool=false;
            $content = $app['twig']-> render('main_register.twig',array('error' => "Error: Revisa els camps",'link_activacio'=>"",'linkbool'=>$linkbool)); // envio  missatge d'error
            $response->setContent($content);
            return $response;
        }
    */
    public function comprovaRegister(Application $app, Request $request)
    {
        $comprovacio = new comprovacioRegister();
        $response = $comprovacio->comprovacioRegisterModel($app, $request);

        return $response;
    }

    public function activaLink(Application $app, Request $request)
    { //pas 3 de register

        $id = $request->get('id');
        //creem una session amb l'id de l'usuari:
        $classeBaseController = new BaseController(); //Creo classe per cridar metode
        $classeBaseController->creaSession($app, $id, 'id'); //crido metode



        //redirigim a la home
        /*
        $url = '/home';
        return new RedirectResponse($url);
        */
        $dbc = new DatabaseController();
        $username = $dbc->retornaNom($app, $id);
        $classeBaseController->creaSession($app, $username, 'username'); //crido metode
        $info1 = $dbc->searchTopViews($app); //obtinc fotos
        $info2 = $dbc->searchLastUploaded($app);

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('home_logged.twig',
            array('name' => $username, 'info1' => $info1, 'info2' => $info2));
        $response->setContent($content);
        return $response;

    }

    public function logout(Application $app, Request $request)
    {
        //tanquem sessio
        $classeBaseController = new BaseController(); //Creo classe per cridar metode
        $classeBaseController->tancaSession($app); //crido metode


        $dbc = new DatabaseController();
        $info = $dbc->searchTopViews($app);
        $info2 = $dbc->searchLastUploaded($app);
        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('home.twig', array('info1' => $info, 'info2' => $info2));


        $response->setContent($content);
        return $response;
    }


}

        ?>
