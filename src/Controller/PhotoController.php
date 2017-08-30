<?php

namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
class PhotoController
{
    public function dataPhoto(Application $app, Request $request)
    { //es crida a partir del login
        $dbc = new DatabaseController();
        $info1 = $dbc->searchTopViews($app);

        $info2 = $dbc->searchLastUploaded($app);


        $response = new Response();
        $foto = $request->files->get('imgInp');


        $path = $request->get('path');
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $title = $request->get('title');
        $private = $request->get('private');

        if(strlen($title)==0||strlen($path)==0){
            $response = new Response();

            $response->setStatusCode(Response::HTTP_OK);
            $loguejat=true;
            if (!$app['session']->has('id')) { //no esta loguejat
                $loguejat = false;
            }
            $content = $app['twig']->render('upload.twig', array('loguejat'=>$loguejat, 'error'=>"Error. Revisa algun dels camps"));



            $response->setContent($content);
            return $response;
        }
        $id = $app['session']->get('id');

        $path = htmlentities($path, ENT_QUOTES); //faig que no es pugui fer sql injection
        $title = htmlentities($title, ENT_QUOTES);
        $filename = $path;
        $destdir = 'assets/Pictures/No_Perfil';
        $foto->move($destdir, $filename);

        //canvio tamany imatge
        //$this->resizeImage($filename);

        $date = date('Y/m/d h:i:s', time());

        if(strcmp($ext,"jpeg")==0||strcmp($ext,"png")==0||strcmp($ext,"jpg")==0) {
            try {
                $this->resizeImage($destdir + $filename); //

                $app['db']->insert('image', [
                        'user_id' => $id,
                        'title' => $title,
                        'img_path' => $filename,
                        'visits' => '0',
                        'private' => $private,
                        'created_at' => $date,
                        'user_nom' => $app['session']->get('username'),
                        'ultim_comentari' => ""


                    ]
                );

                //$content = $app['twig']->render('home_logged.twig', array('info1' => $info1, 'info2' => $info2));

                //$content = $app['twig']->render('home_logged.twig',array('name' => $app['session']->get('username'),$app['session']->get('img_path'),'info1' => $info1, 'info2' => $info2));


            } catch (Exception $e) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('home.twig', [
                    'errors' => [
                        'unexpected' => 'An error has occurred, please try it again later'
                    ]
                ]);
            }
            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('home_logged.twig', array('info1' => $info1, 'info2' => $info2));
            $response->setContent($content);
            //return $response;
            return new RedirectResponse("/home_log");

        }
        else{
            $response->setStatusCode(Response::HTTP_OK);
            $loguejat=true;
            if (!$app['session']->has('id')) { //no esta loguejat
                $loguejat = false;
            }
            $content = $app['twig']->render('upload.twig', array('loguejat'=>$loguejat, 'error'=>"Error. Revisa algun dels camps"));



            $response->setContent($content);
            return $response;
        }
    }


    public function showPhoto(Application $app, Request $request)
    {
        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
//        if (!$app['session']->has('id')) { //no esta loguejat
//            $response = new Response();
//            $content = $app['twig']->render('error.twig');
//            $response->setContent($content);
//            return $response;
//        }


        ob_start();
        $response = new Response();
        $imatge = $request->get('path');
        $titol = $request->get('titol');
        $created = $request->get('created');
        $likes = $request->get('likes');
        $visits = $request->get('visits');
        $user = $request->get('user');
        $id = $request->get('id');
        $privada = $request->get('private');

        if($privada==1){ //es privada, dono error
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setContent($content);
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            return $response;

        }
        ob_start();
        //comprovo que sigui privada i , si ho es , que nomes ho pugui mirar l'autor
        $dbc = new DatabaseController();
        $info = $dbc->retornaInfoImatge($app,$id);
        $privada=$info['private'];
        var_dump($privada);
        $user_id=$info['user_id'];
        var_dump($user_id);
        $visualitzar=true;
        ob_start();
        if($privada==1 ) {

            $visualitzar=false;
            if($app['session']->get('id')) {
                $id_user2 = $app['session']->get('id');

                if (strcmp($id_user2, $user_id) == 0) {
                    $visualitzar = true;

                }
            }
        }

        if($visualitzar==false){
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setContent($content);
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            return $response;
        }
        $sql = "UPDATE image SET visits=?+1 WHERE id=?";

        $app['db']->executeUpdate($sql, array((int) $visits, (int) $id));

        $response->setStatusCode(Response::HTTP_OK);
        $loguejat=true;
        $id_user="";

        if (!$app['session']->has('id')) { //no esta loguejat
            $loguejat = false;

        }
        else{
            $id_user=$app['session']->get('id');
        }
        $content = $app['twig']->render('showPhoto.twig', array('loguejat'=>$loguejat,'imatge'=> $imatge, 'id_img'=>$id, 'id_user'=>$id_user, 'titol' => $titol, 'created' => $created, 'likes' => $likes, 'visits' => $visits+1, 'user'=>$user));
        $response->setContent($content);
        return $response;


    }


    public function uploadPhoto(Application $app)
    {

        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if (!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            $response->setContent($content);
            return $response;
        }

        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $loguejat=true;
        if (!$app['session']->has('id')) { //no esta loguejat
            $loguejat = false;
        }
        $content = $app['twig']->render('upload.twig', array('loguejat'=>$loguejat, 'error'=>""));



        $response->setContent($content);
        return $response;
    }







    public function resizeImage($path){
        $rutaImagenOriginal = $path;

        $original = imagecreatefromjpeg($path);



        list($ancho,$alto)=getimagesize($path);

        $tmp=imagecreatetruecolor(400,400);

        imagecopyresampled($tmp,$original,0,0,0,0,400, 400,400,400);

        imagedestroy($original);



        imagejpeg($tmp, $path, 100);

    }
}






?>