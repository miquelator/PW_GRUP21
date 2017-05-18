<?php
namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PracticaFinal\Controller\BaseController;
use PracticaFinal\Model\comprovacioRegister;


class DatabaseController{

    public function postAction(Application $app, Request $request) //registra usuari. Cridat a Comprovacioregistre
    {


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
            //si

            if ($lastInsertedId!=false){

                $classeBaseController=new BaseController(); //Creo classe per cridar metode
                $classeBaseController->creaSession($app, $id,'id'); //crido metode
                $id =$lastInsertedId['id']+1;
            }
            //no
            $filename= $id.'.'.$perfil->getClientOriginalExtension();
            $destdir = 'assets/Pictures/';
            $perfil->move($destdir,$filename);


            try {
                //inserim a bd
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
                $classeBaseController->creaSession($app, $id,'id'); //crido metode
                return $id;

            } catch (Exception $e) {
               //error
                var_dump("no");
                return 0;
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


                //si no ha pogut entrar
                $content = $app['twig']->render('login.twig',array('error'=>"Usuari o contrasenya erronis"));
            }
            else{

                $content = $app['twig']->render('home_logged.twig',array('name' => $info['username'],'email'=> $info['email'],'image'=>$info['img_path'],'info1' => $info1, 'info2' => $info2));
                $classeBaseController=new BaseController(); //Creo classe per cridar metode
                $classeBaseController->creaSession($app, $info['id'],'id'); //crido metode
                $classeBaseController->creaSession($app, $info['username'],'username'); //crido metode
                $classeBaseController->creaSession($app, $info['img_path'],'img_path'); //crido metode

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
        $dbc = new DatabaseController();
        $info1 = $dbc->searchTopViews($app);

        $info2 = $dbc->searchLastUploaded($app);


        $response = new Response();
        $foto =  $request->files->get('imgInp');
        $path =  $request->get('path');
        $title = $request->get('title');
        $private = $request->get('private');

        $id = $app['session']->get('id');

        $path=htmlentities($path, ENT_QUOTES); //faig que no es pugui fer sql injection
        $title=htmlentities($title, ENT_QUOTES);
        $filename= $path;
        $destdir = 'assets/Pictures/No_Perfil';
        $foto->move($destdir,$filename);
        $date = date('Y/m/d h:i:s', time());
        try {
            var_dump($private);
            $app['db']->insert('image', [
                    'user_id' => $id,
                    'title' => $title,
                    'img_path'=>$filename,
                    'visits'=>'0',
                    'private'=>$private,
                    'created_at'=>$date


                ]
            );

            //$content = $app['twig']->render('home_logged.twig', array('info1' => $info1, 'info2' => $info2));

            $content = $app['twig']->render('home_logged.twig',array('name' => $app['session']->get('username'),$app['session']->get('img_path'),'info1' => $info1, 'info2' => $info2));


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
    public function searchCommentsUser (Application $app){
        $id = $app['session']->get('id');
        $response = new Response();

        try {


            $sql= "SELECT * FROM comentaris WHERE id_user = ? ORDER BY id DESC";
            $info = $app['db']->fetchAll($sql, array ((string) $id));




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

    public function uploadComment (Application $app, Request $request){
        $response = new Response();
        $id = $app['session']->get('id');
        try {
            $app['db']->insert('comentaris', [
                    'id_user' => $id,
                    'id_imatge' => 9,
                    'comentari'=> $request->get('comentari1'),

                ]
            );

            $url = '/home_log';

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
        return $info;

    }





    public function postEdicioPerfil(Application $app, Request $request)//rep de Edicio perfil
    {
        //  var_dump($request);


        $id = $app['session']->get('id'); //guardo id d'usuari actual

        $response = new Response();
        if ($request->isMethod('POST')) {
            // Validate
            $name = $request->get('name');

            $data = $request->get('data_naixement');
            $password = $request->get('password');
            $confirm=$request->get('confirm');
            $perfil = $request->files->get('imatge_perfil');
            //borrar:
            var_dump($name);
            var_dump($data);
            var_dump($password);
            var_dump($perfil);

            $tot_correcte=true;

            try {



                //actualitzem la base de dades els camps que s'han omplert. Es fa uddate quan id=id
                if (strlen($name) != 0) { //si s'ha variat

                    //$sql= "UPDATE user SET username = ? WHERE id=?";
                    //$info = $app['db']->fetchAssoc($sql, array ((string) $name,(string)$id));

                    //actualitzo bases
                    $comprovacio= new comprovacioRegister();
                    if($comprovacio->validName($name)){
                        //$qb = $app['db']->createQueryBuilder();//inicio base

                        $sql= "UPDATE user SET user.username = ? WHERE user.id =?";
                         $app['db']->executeUpdate( $sql, array ((string) $name,(string) $id));

                        /*
                        $qb = $app['db']->createQueryBuilder();
                        $qb->update('user')
                            ->set('user.username', $name)
                            ->where('user.id =id')
                            ->setParameter('id', $id);
                        $qb->execute();
                        */
                    }
                    else{
                        $tot_correcte=false;
                    }



                }

                if (strlen($data) != 0) {
                    //comprovo que es correcte
                    $comprovacio= new comprovacioRegister();
                    if($comprovacio->validData($data)){


                        $sql= "UPDATE user SET user.birthdate = ? WHERE user.id =?";
                        $info = $app['db']->executeUpdate( $sql, array ((string) $data,(string) $id));
                        /*
                        $qb->update('user')
                            ->set('user.birthdate', $data)
                            ->where('user.id = :id')
                            ->setParameter('id', $id);
                        $qb->execute();
                        */
                    }
                    else{
                        $tot_correcte=false;
                    }

                }
                if (strlen($password) != 0) {
                    $comprovacio= new comprovacioRegister();
                    if($comprovacio->validPassword($password,$confirm)){

                        $sql= "UPDATE user SET user.password = ? WHERE user.id =?";
                        $info = $app['db']->executeUpdate( $sql, array ((string) $password,(string) $id));
                    }
                    else{
                        $tot_correcte=false;
                    }

                }
                if (!is_null($perfil)) { //img_path
                    //guardem imatge a carpeta
                    $filename= $id.'.'.$perfil->getClientOriginalExtension();
                    $destdir = 'assets/Pictures/';
                    $perfil->move($destdir,$filename); //guardo imatge perfil a carpeta

                    //substituim a base de dades (original)

                    $sql= "UPDATE user SET user.img_path = ? WHERE user.id =?";
                    $info = $app['db']->executeUpdate( $sql, array ((string) $filename,(string) $id));


                    //la guardem a session
                    $classeBaseController=new BaseController(); //Creo classe per cridar metode
                    $classeBaseController->creaSession($app, $filename,'img_path'); //crido metode

                }
/*
                $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM user ORDER BY id DESC LIMIT 1');
                $id = $lastInsertedId['id'];
                //$url = '/home' . $id;
                $url = '/home';

                return new RedirectResponse($url);
                */
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


            if(!$tot_correcte){ //si algun dels camps que l'usuari ha posat no els ha posat bé
                //header("location: /edicio_perfil_error");
                //obtinc path imatge perfil
                $info=$this->retornaImatgeNomDataUsuari($app);


                $response = new Response();
                $content = $app['twig']-> render('edicio_perfil.twig',array('path_imatge'=>$info['img_path'],'nom_user'=>$info['username'],'data_naixement'=>$info['birthdate'],'error'=>"Error: Revisa tots els camps")); //mostrem per pantalla la pagina

                $response->setContent($content);
                return $response;
            }
            else{ //si s'ha enviat tot bé
               // header("location: /home");
                //obtinc path imatge perfil

                $info=$this->retornaImatgeNomDataUsuari($app);

                $response = new Response();
                $content = $app['twig']-> render('edicio_perfil.twig',array('path_imatge'=>$info['img_path'],'nom_user'=>$info['username'],'data_naixement'=>$info['birthdate'],'error'=>"Dades canviades correctament")); //mostrem per pantalla la pagina

                $response->setContent($content);
                return $response;
            }

        }
        return new Response();
    }


    public function retornaImatgeNomDataUsuari(Application $app){
        $id= $app['session']->get('id');
          $sql = "SELECT * FROM user WHERE id=? ";
            $info = $app['db']->fetchAssoc($sql, array((string)$id));

            return $info;



    }

    public function retornaNom(Application $app, $id){ //cridat a ActivaLink, userscontroller. No se li pot passar id per session
        try {


            $sql= "SELECT * FROM user WHERE id=? ";
            $info = $app['db']->fetchAssoc($sql, array ((string) $id));


            return $info['username'];



        }catch (Exception $e) {
            $response=new Response();
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('home.twig', [
                'errors' => [
                    'unexpected' => 'An error has occurred, please try it again later'
                ]
            ]);
        }
    }


    public function diesPassats(Application $app, $created_at){ //retorna el num de dies passats de que es va crear la imatge
        $now = new \DateTime();
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $created_at);

        $interval = $now->diff($d);

    }




}