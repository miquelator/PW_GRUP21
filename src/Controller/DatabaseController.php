<?php
namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DatabaseController{

    public function postAction(Application $app, Request $request) //registra usuari
    {
        //  var_dump($request);
        $response = new Response();
        if ($request->isMethod('POST')) {
            // Validate
            $name = $request->get('name');
            $email = $request->get('email');
            $data = $request->get('data_naixement');
            $password = $request->get('password');

            $perfil = $request->files->get('imatge_perfil');
            var_dump($perfil);

            $filename= $name.'.'.$perfil->getClientOriginalExtension();
            $destdir = 'assets/Pictures/';
            $perfil->move($destdir,$filename);


            try {
                $app['db']->insert('user', [
                        'username' => $name,
                        'email' => $email,
                        'birthdate'=>$data,
                        'password'=>$password,


                    ]
                );
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






    public function postEdicioPerfil(Application $app, Request $request)//rep de Edicio perfil
    {
        //  var_dump($request);
        $response = new Response();
        if ($request->isMethod('POST')) {
            // Validate
            $name = $request->get('name');

            $data = $request->get('data_naixement');
            $password = $request->get('password');

            $perfil = $request->files->get('imatge_perfil');
            var_dump($data);

            if(!is_null($perfil)) {
                $nom = $perfil->getClientOriginalName;
                echo $nom;
                $filename = $perfil->getClientOriginalExtension();
                $destdir = '/../../web/assets/Pictures/';
                $perfil->move($destdir, $filename);
            }


            try {
                //actualitzem la base de dades els camps que s'han omplert. Es fa uddate quan id=id
                if(strlen($name)!=0){ //si s'ha variat
                    $app['db']->update('user', [
                            'username' => $name,
                        ]
                    );

                    //FALTA; guardar la id per cookies
                    $st = $app['db']->prepare("UPDATE user SET username='".$name. "' WHERE id='".$id. "'");
                    $st->execute(array($username));


                }
                if(strlen($data)!=0){
                    $app['db']->update('user', [
                            'birthdate' => $data,
                        ]
                    );
                }
                if(strlen($password)!=0){
                    $app['db']->update('user', [
                            'password' => $password,
                        ]
                    );
                }
                if(!is_null($perfil)){
                    $app['db']->update('user', [
                            'img_path' => $perfil,
                        ]
                    );
                }

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