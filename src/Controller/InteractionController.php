<?php
namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PracticaFinal\Controller\BaseController;
use PracticaFinal\Model\comprovacioRegister;


class InteractionController{
    public function notificacions(Application $app, Request $request)
    {

        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if (!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setContent($content);
            return $response;
        }


        $data=new DatabaseController();
        $info=$data->repNotificacions($app);
        $size=count($info);
        for ($i = 0; $i < count($info); $i++) {
            $nom_user[$i] = $info[$i]['username'];
            $titol_imatge[$i] = $info[$i]['titol_imatge'];
            $tipus[$i] = $info[$i]['tipus'];
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('notificacions.twig', array('size'=>$size,'nom_user' => $nom_user, 'titol_imatge' => $titol_imatge,'tipus' => $tipus));


        $response->setContent($content);
        return $response;

    }
    public function comment(Application $app, Request $request)
    {
        $response = new Response();
        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if (!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');

            $response->setContent($content);
            return $response;
        }
        $dbc = new DatabaseController();

        //guardo la notificacio
        $id = $request->get('id'); //id de la imatge
        if($dbc->checkUserComment($app, $id)){ //comprova que no ha fet comentaris per aqeulla foto
        $title = $request->get('title');
        $user_id = $request->get('user_id');//id del creador de la imatge
        $dbc->pujaNotificacions($app,$id,$title, $user_id,'Comentari:');



            $info1 = $dbc->searchTopViews($app);

            $info2 = $dbc->searchLastUploaded($app);
            //var_dump($app['session']->get('id'));

            $dbc->uploadComment($app,$request,$id);


            $response->setStatusCode(Response::HTTP_OK);
            // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
            $content = $app['twig']->render('home_logged.twig', array('info1' => $info1,'info2' => $info2));
            $response->setContent($content);
            echo 'true';
            return new RedirectResponse("/home_log");
            //return $response;
        }else{
            $info1 = $dbc->searchTopViews($app);
            $info2 = $dbc->searchLastUploaded($app);
            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('home_logged.twig', array('info1' => $info1,'info2' => $info2));
            $response->setContent($content);
            echo 'false';

            return new RedirectResponse("/home_log");
            //return $response;
        }

    }
    public function userComments(Application $app)
    {
        //comprovo que l'usuari estigui loguejat. Si no ho esta, el redirigeixo
        if(!$app['session']->has('id')) { //no esta loguejat
            $response = new Response();
            $content = $app['twig']->render('error.twig');
            $response->setContent($content);
            return $response;
        }

        $dbc = new DatabaseController();

        $comments = $dbc->searchCommentsUser($app);
        for ($i = 0; $i < count($comments); $i++) {
            $c[$i] = $comments[$i]['comentari'];
            $ids[$i] = $comments[$i]['id'];
        }
        $size = count($comments);

        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);
        //$content = $app['twig']->render('user_comments.twig', array('c1' => $comments[0]['comentari'],'c2' => $comments[1]['comentari'],'c3' => $comments[2]['comentari'],'c4' => $comments[3]['comentari'],'c5' => $comments[4]['comentari']));
        $content = $app['twig']->render('user_comments.twig', array('c' => $c,'ids' => $ids, 'size' => $size));

        $response->setContent($content);
        return $response;
    }
    public function deleteComment(Application $app,Request $request)
    {

        $dbc = new DatabaseController();

        var_dump($request->get('id'));

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



    public function like(Application $app, Request $request)
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
        $id = $request->get('id'); //id de la imatge
        $title = $request->get('title');
        $user_id = $request->get('user_id');//id del creador de la imatge
        $dbc->pujaNotificacions($app,$id,$title, $user_id,'Like:');



        //var_dump($app['session']->get('id'));

        $dbc->uploadLike($app, $id); //actualitzo base de dades

        $info1 = $dbc->searchTopViews($app);

        $info2 = $dbc->searchLastUploaded($app);

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        // $content = $app['twig']->render('home_logged.twig', array('tv0' => $info[0]['img_path'], 'tv1' => $info[1]['img_path'], 'tv2' => $info[2]['img_path'], 'tv3' => $info[3]['img_path'], 'tv4' => $info[4]['img_path'], 'lu0' => $info2[0]['img_path'], 'lu1' => $info2[1]['img_path'], 'lu2' => $info2[2]['img_path'], 'lu3' => $info2[3]['img_path'], 'lu4' => $info2[4]['img_path'],));
        $content = $app['twig']->render('home_logged.twig', array('info1' => $info1, 'info2' => $info2));

        $response->setContent($content);
        return $response;
    }






}