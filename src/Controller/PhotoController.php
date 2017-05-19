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
        $title = $request->get('title');
        $private = $request->get('private');
        $id = $app['session']->get('id');

        $path = htmlentities($path, ENT_QUOTES); //faig que no es pugui fer sql injection
        $title = htmlentities($title, ENT_QUOTES);
        $filename = $path;
        $destdir = 'assets/Pictures/No_Perfil';
        $foto->move($destdir, $filename);

        //canvio tamany imatge
        //$this->resizeImage($filename);

        $date = date('Y/m/d h:i:s', time());
        try {

            $app['db']->insert('image', [
                    'user_id' => $id,
                    'title' => $title,
                    'img_path' => $filename,
                    'visits' => '0',
                    'private' => $private,
                    'created_at' => $date,
                    'user_nom' => $app['session']->get('username'),
                    'ultim_comentari' =>""


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
        $content = $app['twig']->render('home_logged.twig', array('info1' => $info1,'info2' => $info2));
        $response->setContent($content);
        //return $response;
        return new RedirectResponse("/home_log");


    }


    public function showPhoto(Application $app, Request $request)
    {
        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if (!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setContent($content);
            return $response;
        }


        $response = new Response();
        $imatge = $request->get('path');
        $titol = $request->get('titol');
        $created = $request->get('created');
        $likes = $request->get('likes');
        $visits = $request->get('visits');
        $user = $request->get('user');
        $id = $request->get('id');


        $sql = "UPDATE image SET visits=?+1 WHERE id=?";

        $app['db']->executeUpdate($sql, array((int) $visits, (int) $id));

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('showPhoto.twig', array('imatge'=> $imatge, 'titol' => $titol, 'created' => $created, 'likes' => $likes, 'visits' => $visits+1, 'user'=>$user));
        $response->setContent($content);
        return $response;


    }


    public function uploadPhoto(Application $app)
    {

        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('upload.twig');



        $response->setContent($content);
        return $response;
    }


    public function resizeImage($filename){ //rep l'image path

        $img = imagecreatefromjpeg($filename);
        return imagescale($img, 660, 384);

        /*
        //The blur factor where &gt; 1 is blurry, &lt; 1 is sharp.
        $imagick = new \Imagick(realpath("assets/Pictures/No_Perfil/".$filename));

        $imagick->resizeImage(400, 400, imagick::FILTER_LANCZOS, 1);
        $imagick->writeImage( "assets/Pictures/No_Perfil/".$filename );
*/
    }
    public function editaImatgeForm(Application $app, Request $request)
    {
        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if (!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');

            $response->setContent($content);
            return $response;
        }
        $dbc = new DatabaseController();
        //guardo la notificacio
        $private = $request->get('private'); //id de la imatge
        $title = $request->get('title');


        if (strlen($title) != 0) {


                $sql= "UPDATE image SET title = ? WHERE id =?";
                $info = $app['db']->executeUpdate( $sql, array ((string) $password,(string) $id));


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





        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $content = $app['twig']->render('home_logged.twig', array('info1' => $info1, 'info2' => $info2));

        $response->setContent($content);
        return $response;
    }

    public function editaImatge(Application $app)
    {


        $id=$app['session']->get('id');
        $sql= "SELECT * FROM user WHERE id = ? ";
        $sql2= "SELECT * FROM image WHERE user_id = ? and private = 0 ORDER BY created_at";
        $sql3= "SELECT count(id) FROM comentaris WHERE id_user = ?";


        $info = $app['db']->fetchAssoc($sql, array ((string) $id));
        $fotos = $app['db']->fetchAll($sql2, array ((string) $id));
        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('edita_imatge.twig',array('name'=>$info['username'],'imatge'=>$info['img_path'],'fotos'=>$fotos));

        $response->setContent($content);
        return $response;
    }

    public function deleteImage(Application $app,Request $request)
    {

        $dbc = new DatabaseController();

        $request->get('id'); //id de la imatge

        //$dbc->searchCommentsUser($app);
        $comments = $dbc->searchCommentsUser($app);
        for ($i = 0; $i < count($comments); $i++) {
            $c[$i] = $comments[$i]['comentari'];
            $ids[$i] = $comments[$i]['id'];
        }
        $id = $request->get('id');
        $dbc->eraseComment($app,$id);

        return new RedirectResponse("/user_comments");
    }


}






?>