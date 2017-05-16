<?php
namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PracticaFinal\Controller\BaseController;


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

            //converteixo per evitar sql injection
            $name = htmlentities($name, ENT_QUOTES); //faig que no es pugui fer sql injection
            $password = htmlentities($password, ENT_QUOTES);
            $email = htmlentities($email, ENT_QUOTES);


            $perfil = $request->files->get('imatge_perfil');

            $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM user ORDER BY id DESC LIMIT 1');
            $id = 1;
            if ($lastInsertedId!=false){
                $classeBaseController=new BaseController(); //Creo classe per cridar metode
                $classeBaseController->creaSession($app, $id); //crido metode
                $id =$lastInsertedId['id']+1;
            }
            $filename= 'assets/Pictures/'.$id.'.'.$perfil->getClientOriginalExtension();
            $destdir = 'assets/Pictures/';
            $perfil->move($destdir,$filename);

            try {
                $app['db']->insert('user', [
                        'username' => $name,
                        'email' => $email,
                        'birthdate'=>$data,
                        'password'=>$password,
                        'img_path'=>$filename


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

        $name = $request->get('user');
        $password = $request->get('password');
        $user=htmlentities($name, ENT_QUOTES); //faig que no es pugui fer sql injection
        $password=htmlentities($password, ENT_QUOTES);

        try {


            $sql= "SELECT * FROM user WHERE (username = ? or email = ?) and password = ?  ORDER BY id DESC LIMIT 1";
            $info = $app['db']->fetchAssoc($sql, array ((string) $name,(string) $name,(string)$password));

            $dbc = new DatabaseController();
            $info1 = $dbc->searchTopViews($app);

            $info2 = $dbc->searchLastUploaded($app);

            if ($info==false){

                $content = $app['twig']->render('home_logged.twig');
            }
            else{

                $content = $app['twig']->render('home_logged.twig',array('name' => $info['username'],'email'=> $info['email'],'image'=>$info['img_path'],'tv0' => $info1[0]['img_path'], 'tv1' => $info1[1]['img_path'], 'tv2' => $info1[2]['img_path'], 'tv3' => $info1[3]['img_path'], 'tv4' => $info1[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path']));
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

    public function dataPhoto (Application $app, Request $request){ //es crida a partir del login
        $response = new Response();
        $foto =  $request->files->get('imgInp');
        $path =  $request->get('path');
        $title = $request->get('title');
        $private = $request->get('private');






        $id = $app['session']->get('id');

        $path=htmlentities($path, ENT_QUOTES); //faig que no es pugui fer sql injection
        $title=htmlentities($title, ENT_QUOTES);
        $filename= 'assets/Pictures/No_Perfil'.'.'.$path;
        $destdir = 'assets/Pictures/No_Perfil';
        $foto->move($destdir,$filename);
        $date = date('Y/m/d h:i:s', time());
        echo "date: ".$date;
        try {

            $app['db']->insert('image', [
                    'user_id' => $id,
                    'title' => $title,
                    'img_path'=>$filename,
                    'visits'=>'0',
                    'private'=>$private,
                    'created_at'=>$date


                ]
            );

                $content = $app['twig']->render('home_logged.twig');



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

    public function searchTopViews (Application $app){
        $response = new Response();

        try {
            $sql= "SELECT * FROM image WHERE (private = 0) ORDER BY visits DESC LIMIT 5";
            $info = $app['db']->fetchAll($sql);


        }catch (Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('home.twig', [
                'errors' => [
                    'unexpected' => 'An error has occurred, please try it again later'
                ]
            ]);
        }

        return $info;

    }
    public function searchLastUploaded (Application $app){
        $response = new Response();

        try {
            $sql= "SELECT * FROM image ORDER BY id DESC LIMIT 5";
            $info = $app['db']->fetchAll($sql);


        }catch (Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('home.twig', [
                'errors' => [
                    'unexpected' => 'An error has occurred, please try it again later'
                ]
            ]);
        }

        return $info;

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
                    $st->execute(array($name));


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