<?php
namespace PracticaFinal\Model;

use PracticaFinal\Controller\DatabaseController;
use Symfony\Component\Validator\Constraints\DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class comprovacioRegister
{

    public function comprovacioRegisterModel(Application $app, Request $request)
    {
        ob_start(); //assegura que no hi haura outputs per poder fer el header
        if ($request->isMethod('POST')) {
            // Validate
            $name = $request->get('name');
            $email = $request->get('email');
            $data = $request->get('data_naixement');
            $password = $request->get('password');
            $confirm = $request->get('confirm');


            if ($this->validName($name) &&
                $this->validEmail($email) &&
                $this->validData($data) &&
                $this->validPassword($password, $confirm)
            ) {


                //ho guardem per base de dades
                $databasecontroller = new DatabaseController();
                $id = $databasecontroller->postAction($app, $request);
                if ($id == 0) { //no s'ha pujat bé
                    $linkbool = false;
                    $response = new Response();
                    $content = $app['twig']->render('main_register.twig', array(
                        'error' => "Error. No s'ha pogut accedir al server",
                        'link_activacio' => "",
                        'linkbool' => $linkbool
                    ));
                    $response->setContent($content);
                    return $response;
                }

                /*
                                    while (ob_get_status()) //neteja per poder fer el Header
                                    {
                                        ob_end_clean();
                                    }

                                    header("location: /register_amb_link/{".$id."}"); //tornem a registra amb l'id
                                    //header("location: /home");//Abans del Gran Canvi
                */
                $linkbool = true;
                $response = new Response();
                $content = $app['twig']->render('main_register.twig', array(
                    'error' => "",
                    'link_activacio' =>  $id,
                    'linkbool' => $linkbool
                )); //no envio res com a missatge d'error i fico el link d'activacio
                $response->setContent($content);

                return $response;

            }
        }

        //si no estan be

        $linkbool = false;
        $response = new Response();
        $content = $app['twig']->render('main_register.twig', array(
            'error' => "Error: has fallat algun dels camps",
            'link_activacio' => "",
            'linkbool' => $linkbool
        ));
        $response->setContent($content);

        return $response;

    }





        function validName($name)
        {
            //Trec espais pk no doni error
            $name=str_replace(' ','',$name);
            if ($name !== '' && ctype_alnum($name)) {//comprovo alfanumerics
                return true;
            } else {
                return false;
            }
        }

        function validEmail($email)
        {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                return false;
            }
        }

        function validData($data_naixement)
        {
            $now = new \DateTime();
            $d = \DateTime::createFromFormat('Y-m-d', $data_naixement);
            if ($d && $d->format('Y-m-d') == $data_naixement) {
                if($now>$d){ //comprovo si es futur
                    return true;
                }

            }
            return false;
        }




        function validPassword($password, $confirm) //s'ha de ficar numeros i majuscules
        {
            if (strcmp($password, $confirm) == 0) {//son iguals?
                $majus = 0;
                $minus = 0;
                if (strcspn($password,
                        '0123456789') !== strlen($password)
                ) { //amb strcspn retorna la llargada de la part
                    //de la contrasenya que no te numeros i ho comparo amb la llargada total. Te numeros?
                    $i = -1;
                    while ($i < strlen($password)) { //te majuscules i minusucles?
                        $i++;

                        if (ctype_upper($password[$i])) { //es majus

                            $majus++;
                        }
                        if (ctype_lower($password[$i])) {//es minus
                            $minus++;

                        }
                    }
                    if ($majus > 0 && $minus > 0) {
                        return true;

                    } else {
                        return false;
                    }

                } else {
                    return false;
                }
            } else {
                return false;
            }
        }


    }


