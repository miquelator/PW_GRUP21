<?php
namespace PracticaFinal\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BaseController{

    public function creaSession(Application $app, $nomaguardar,$tipus){ //creem sessio amb l'id de l'usuari loguejat. tipus es 'id', 'username' o 'img_path'

        if($app['session']->has($tipus)) { //si ja existeix, la borro
            $app['session']->remove($tipus);
        }


       //la creo
        $app['session']->set($tipus,$nomaguardar);


    }


    public function tancaSession(Application $app){
        $app['session']->remove('id');
        $app['session']->remove('username');
        $app['session']->remove('img_path');
    }



}