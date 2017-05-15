<?php
namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BaseController{
    /*
    public function indexAction(Application $app){ //comprovem si existeixla variable sessio d'usuari

        if($app['session']->has('id')){ //si ja existeix, la borro
            $app['session']->remove('id');
            return new Response('Session finished');
        }

        //si no existeix, la creo
        $app['session']->set('id','Nick');
        $content=$app['session']->get('id');
        return new Response($content);
    }
    */
    public function  adminAction(Application $app){
        return new Response("fef");

    }

    public function creaSession(Application $app, $id){ //creem sessio amb l'id de l'usuari loguejat

        if($app['session']->has('id')){ //si ja existeix, la borro
            $app['session']->remove('id');
        }

       //la creo
        $app['session']->set('id',$id);

    }

    public function tancaSession(Application $app){
        $app['session']->remove('id');
    }



}