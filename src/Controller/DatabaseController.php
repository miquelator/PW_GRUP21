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



    public function postEdicioPerfil(Application $app, Request $request)//rep de Edicio perfil
    {
        //  var_dump($request);
        ob_start(); //assegura que no hi haura outputs per poder fer el header


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

                $qb = $app['db']->createQueryBuilder();//inicio base


                //actualitzem la base de dades els camps que s'han omplert. Es fa uddate quan id=id
                if (strlen($name) != 0) { //si s'ha variat

                    //$sql= "UPDATE user SET username = ? WHERE id=?";
                    //$info = $app['db']->fetchAssoc($sql, array ((string) $name,(string)$id));

                    //actualitzo bases
                    $comprovacio= new comprovacioRegister();
                    if($comprovacio->validName($name)){
                        $sql= "UPDATE user SET user.username = ? WHERE user.id =?";
                        $info = $app['db']->fetchAssoc( $sql, array ((string) $name,(string) $id));

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
                        $info = $app['db']->fetchAssoc( $sql, array ((string) $data,(string) $id));
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
                        $info = $app['db']->fetchAssoc( $sql, array ((string) $password,(string) $id));
                    }
                    else{
                        $tot_correcte=false;
                    }

                }
                if (!is_null($perfil)) {
                    //guardem imatge a carpeta
                    $filename= 'assets/Pictures/'.$id.'.'.$perfil->getClientOriginalExtension();
                    $destdir = 'assets/Pictures/';
                    $perfil->move($destdir,$filename); //guardo imatge perfil a carpeta

                    //substituim a base de dades (original)
                    $sql= "UPDATE user SET user.img_path = ? WHERE user.id =?";
                    $info = $app['db']->fetchAssoc( $sql, array ((string) $filename,(string) $id));
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

            while (ob_get_status()) //neteja per poder fer el Header
            {
                ob_end_clean();
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
                $content = $app['twig']-> render('edicio_perfil.twig',array('path_imatge'=>$info['img_path'],'nom_user'=>$info['username'],'data_naixement'=>$info['birthdate'],'error'=>"")); //mostrem per pantalla la pagina

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





}