<?php

namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EditaFotoController
{
    public function editaImatgeForm(Application $app, Request $request)
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
        // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $loguejat=true;
        if (!$app['session']->has('id')) { //no esta loguejat
            $loguejat = false;
        }

        $content = $app['twig']->render('edita_imatge_form.twig',array('loguejat'=>$loguejat,'id'=>$request->get('id')));

        $response->setContent($content);
        return $response;
    }

    public function updatePhotoInfo(Application $app, Request $request){
        $id = $request->get('id');
        $private = $request->get('private');
        $title = $request->get('title');
        if(strlen($title)==0){
            $sql= "UPDATE image SET  private = ? WHERE id =?";
            $info = $app['db']->executeUpdate( $sql, array ((int) $private, (int) $id));

        }
        else {

            $sql = "UPDATE image SET title = ?, private = ? WHERE id =?";
            $info = $app['db']->executeUpdate($sql, array((string)$title, (int)$private, (int)$id));
        }
            return new RedirectResponse("/edita_imatge");

    }

    public function editaImatge(Application $app)
    {


        $id=$app['session']->get('id');
        $sql= "SELECT * FROM user WHERE id = ? ";
        $sql2= "SELECT * FROM image WHERE user_id = ? ORDER BY created_at";
        $sql3= "SELECT count(id) FROM comentaris WHERE id_user = ?";


        $info = $app['db']->fetchAssoc($sql, array ((string) $id));
        $fotos = $app['db']->fetchAll($sql2, array ((string) $id));
        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        $loguejat=true;
        if (!$app['session']->has('id')) { //no esta loguejat
            $loguejat = false;
        }
        $content = $app['twig']->render('edita_imatge.twig',array('loguejat'=>$loguejat,'name'=>$info['username'],'imatge'=>$info['img_path'],'fotos'=>$fotos));

        $response->setContent($content);
        return $response;
    }
    public function deleteImage(Application $app,Request $request)
    {

        $dbc = new DatabaseController();

        $id = $request->get('id'); //id de la imatge
        $sql = "delete from image where id=?";
        $info = $app['db']->executeUpdate( $sql, array((int)$id));

        $sql = "delete from comentaris where id_imatge = ?";
        $info = $app['db']->executeUpdate( $sql, array((int)$id));

        $sql = "delete from likes where id_imatge = ?";
        $info = $app['db']->executeUpdate( $sql, array((int)$id));

        $sql = "delete from notificacions where id_imatge = ?";
        $info = $app['db']->executeUpdate( $sql, array((int)$id));

        return new RedirectResponse("/edita_imatge");
    }


}


?>