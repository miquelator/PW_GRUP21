<?php
    namespace PracticaFinal\Controller;

    use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController
    {
    public function edicio_perfil(Application $app){

        $response = new Response();
        $content = $app['twig']-> render('edicio_perfil.twig'); //mostrem per pantalla la pagina

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
    }

        ?>
