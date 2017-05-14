<?php
    namespace PracticaFinal\Controller;

    use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController{

     public function edicio_perfil(Application $app){

        $response = new Response();
        $content = $app['twig']-> render('edicio_perfil.twig'); //mostrem per pantalla la pagina

        $response->setContent($content);
        return $response;
    }
        public function getAction(Application $app, $id)
    {
        $sql = "SELECT * FROM user WHERE id = ?";
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
    public function mostraUsuari(Application $app, Request $request) //registra usuari
    {
        //  var_dump($request);
        $response = new Response();
        if ($request->isMethod('POST')) {
            // Validate
            $name = $request->get('name');
            $password = $request->get('password');




            try {

                $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM user ORDER BY id DESC LIMIT 1');
                $id = $lastInsertedId['id'];
                //$url = '/home' . $id;
                $url = '/home';

                return new RedirectResponse($url);
            } catch (Exception $e) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('main_register.twig', [
                    'errors' => [
                        'unexpected' => 'An error has occurred, please try it again later'
                    ]
                ]);
                $response->setContent($content);
                return $response;
            }
        }
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


            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('login.twig');
            $response->setContent($content);
            return $response;


        }

    public function showUser(Application $app, Request $request ){

        $response = new Response();

        $name = $request->get('name');
        $password = $request->get('password');

        $response->setStatusCode(Response::HTTP_OK);

        $content = $app['twig']->render('showUser.twig',array('name' => $name,'password'=>$password));
        $response->setContent($content);
        return $response;


    }




    public function register(Application $app){

        $response = new Response();
        $content = $app['twig']-> render('main_register.twig');
        $response->setContent($content);
        return $response;
    }
    }

        ?>
