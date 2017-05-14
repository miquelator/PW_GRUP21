<?php
namespace PracticaFinal\Controller;

use PracticaFinal\Controller\BaseController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DatabaseController{

    public function postAction(Application $app, Request $request) //registra usuari. Cridat a Comprovacioregistre
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

            $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM user ORDER BY id DESC LIMIT 1');
            $id = 1;
            if ($lastInsertedId!=false){
                $classeBaseController=new BaseController(); //Creo classe per cridar metode
                $classeBaseController->creaSession($app, $id); //crido metode
                $id =$lastInsertedId['id']+1;
            }
            $filename= $name.$id.'.'.$perfil->getClientOriginalExtension();
            $destdir = 'assets/Pictures/';
            $perfil->move($destdir,$filename);


            try {
                $app['db']->insert('user', [
                        'username' => $name,
                        'email' => $email,
                        'birthdate'=>$data,
                        'password'=>$password,
                        'img_path'=>'assets/Pictures/'.$filename


                    ]
                );


                //creem una session amb l'id de l'usuari:
                $classeBaseController=new BaseController(); //Creo classe per cridar metode
                $classeBaseController->creaSession($app, $id); //crido metode
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

    public function searchUser (Application $app, Request $request){ //es crida a partir del login
        $response = new Response();

        $name = $request->get('name');
        $password = $request->get('password');
        try {


            $sql= "SELECT * FROM user WHERE (username = ? or email = ?) and password = ?  ORDER BY id DESC LIMIT 1";
            $info = $app['db']->fetchAssoc($sql, array ((string) $name,(string) $name,(string)$password));
            if ($info==false){
                $content = $app['twig']->render('home.twig');
            }
            else{
                $content = $app['twig']->render('showUser.twig',array('name' => $info['username'],'email'=> $info['email'],'image'=>$info['img_path']));
                $classeBaseController=new BaseController(); //Creo classe per cridar metode
                $classeBaseController->creaSession($app, $info['id']); //crido metode

            }

        }catch (Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('home.twig', [
                'errors' => [
                    'unexpected' => 'An error has occurred, please try it again later'
                ]
            ]);
        }
        $response->setStatusCode(Response::HTTP_OK);

        $response->setContent($content);
        return $response;

    }

    public function searchTopViews (Application $app, Request $request){
        $response = new Response();

        try {


            $sql= "SELECT * FROM image WHERE (private = 0) ORDER BY visits DESC";
            $info = $app['db']->fetchAssoc($sql);
//            if ($info==false){
//                $content = $app['twig']->render('home.twig');
//            }
//            else{
//                $content = $app['twig']->render('showUser.twig',array('name' => $info['username'],'email'=> $info['email']));
//
//            }

            var_dump($info);
            echo('Patata');
        }catch (Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('home.twig', [
                'errors' => [
                    'unexpected' => 'An error has occurred, please try it again later'
                ]
            ]);
        }
//        $response->setStatusCode(Response::HTTP_OK);
//
//        $response->setContent($content);
        return $response;

    }







    public function postEdicioPerfil(Application $app, Request $request)//rep de Edicio perfil
    {
        //  var_dump($request);



        $id= $app['session']->get('id'); //guardo id d'usuari actual

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