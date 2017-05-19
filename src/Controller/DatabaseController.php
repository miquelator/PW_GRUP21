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
    public function searchUser (Application $app, Request $request){ //es crida a partir del login
        $response = new Response();

        $name = $request->get('user');
        $password = $request->get('password');
        $user=htmlentities($name, ENT_QUOTES); //faig que no es pugui fer sql injection
        $password=htmlentities($password, ENT_QUOTES);

        try {


            $sql= "SELECT * FROM user WHERE (username = ? or email = ?) and password = ?  ORDER BY id DESC LIMIT 1";
            $info = $app['db']->fetchAssoc($sql, array ((string) $name,(string) $name,(string)$password));


            $info1 = $this->searchTopViews($app);

            $info2 = $this->searchLastUploaded($app);

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
    public function eraseComment (Application $app,  $id){
        $response = new Response();

        $app['db']->delete('comentaris', array ('id' => $id));



    }
    public function checkUserComment (Application $app, $id_img){

        $response = new Response();
        $check = true;
        $id = $app['session']->get('id');

        try {
            $sql= "SELECT * FROM comentaris";
            $info = $app['db']->fetchAll($sql);


        }catch (Exception $e) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('home.twig', [
                'errors' => [
                    'unexpected' => 'An error has occurred, please try it again later'
                ]
            ]);
        }
        //var_dump($info);
        for ($i = 0; $i < count($info); $i++) {
            echo $info[$i]['id_user'].'/';
            echo $id.'/';
            echo $info[$i]['id_imatge'].'/';
            echo $id_img.'///////////';

            if(($info[$i]['id_user'] == $id) && ($info[$i]['id_imatge'] == $id_img)){
                $check = false;
                echo 'Entra';
            }
        }


        return $check;


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

    public function retornaNomImatge(Application $app, $id){ //Li passo id de la imatge
        try {


            $sql= "SELECT * FROM image WHERE id=? ";
            $info = $app['db']->fetchAssoc($sql, array ((string) $id));


            return $info['title'];



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

    public function repNotificacions(Application $app){
        $id = $app['session']->get('id');
        try {
            $sql = "SELECT * FROM notificacions WHERE id_creador_imatge = ? ORDER BY id DESC";
            $info = $app['db']->fetchAll($sql, array((string)$id));
            return $info;
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

    public function pujaNotificacions(Application $app,$id_imatge,$title, $id_creador_imatge,$tipus){ //tipus es 'comentari' o 'like'

        //$id = $app['session']->get('id');
        $username = $app['session']->get('username');


        $app['db']->insert('notificacions', [
                'id_imatge' => $id_imatge,
                'username'=> $username, //nom del qui ha fet el comment/like
                'titol_imatge'=>$title,
                'tipus'=>$tipus,
                'id_creador_imatge'=>$id_creador_imatge


            ]
        );
    }
    public function uploadLike (Application $app,$id){ //rep id imatge
        $response = new Response();
        try {
            var_dump($id);
            $sql= "UPDATE image SET likes = (likes+1) WHERE id =?";
            $info = $app['db']->executeUpdate( $sql, array((string)$id));



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