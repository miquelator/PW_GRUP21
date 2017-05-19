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
                    'ultim_comentari' =>" "


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



}








    function resize($newWidth, $targetFile, $originalFile) {


$info = getimagesize($originalFile);
$mime = $info['mime'];

switch ($mime) {
case 'image/jpeg':
$image_create_func = 'imagecreatefromjpeg';
$image_save_func = 'imagejpeg';
$new_image_ext = 'jpg';
break;

case 'image/png':
$image_create_func = 'imagecreatefrompng';
$image_save_func = 'imagepng';
$new_image_ext = 'png';
break;

case 'image/gif':
$image_create_func = 'imagecreatefromgif';
$image_save_func = 'imagegif';
$new_image_ext = 'gif';
break;

default:
throw new Exception('Unknown image type.');
}

$img = $image_create_func($originalFile);
list($width, $height) = getimagesize($originalFile);

$newHeight = ($height / $width) * $newWidth;
$tmp = imagecreatetruecolor($newWidth, $newHeight);
imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

if (file_exists($targetFile)) {
unlink($targetFile);
}
$image_save_func($tmp, "$targetFile.$new_image_ext");

}
?>