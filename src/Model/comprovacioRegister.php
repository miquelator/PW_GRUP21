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
                    $databasecontroller=new DatabaseController();
                    $databasecontroller->postAction($app, $request);

                    while (ob_get_status()) //neteja per poder fer el Header
                    {
                        ob_end_clean();
                    }
                    header("location: /home"); //tornem a home

                    exit;
                }
            }

            //si no estan be



            while (ob_get_status()) //neteja per poder fer el Header
            {
                ob_end_clean();
            }

             header("location: /register_error");
            exit;

        }


        function validName($name)
        {
            if ($name !== '' && ctype_alnum($name)) {//comprovo alfanumerics
                return true;
            } else {
                echo "Nom fallat.  ";
                return false;
            }
        }

        function validEmail($email)
        {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                echo "Email fallat.  ";
                return false;
            }
        }

        function validData($data_naixement)
        {

            $d = \DateTime::createFromFormat('Y-m-d', $data_naixement);

            if ($d && $d->format('Y-m-d') == $data_naixement) {
                return true;
            } else {
                echo "Data fallada.  ";
                return false;
            }


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
                        echo "Contrasenya fallada.  ";
                        return false;
                    }

                } else {
                    echo "Contrasenya fallada.  ";
                    return false;
                }
            } else {
                echo "Contrasenya fallada.  ";
                return false;
            }
        }


}

/*

function connectaBaseDades($name,$email,$data_naixement,$password){

    //miro si ja existeix l'usuari
    $db = new PDO('mysql:host=localhost;dbname=Exercici1', 'nick', 'nick'); //base,user,password
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $insert = $db->query("SELECT id FROM usuari WHERE (username='".$name."' OR email='".$email."' ) "); //comete dobles entremig de cometes simples
    $id = $insert->fetchAll();

    if(!empty($id)){ //si ja existeix
        echo "Usuari o email ja agafats";
        include "main_register.php";
        exit;
    }

    else {

        $db = new PDO('mysql:host=localhost;dbname=Exercici1', 'nick', 'nick'); //base,user,password
        $insert = $db->prepare("INSERT INTO usuari (username, email, data_naixement, password) VALUES('" . $name . "','" . $email . "','" . $data_naixement . "','" . $password . "')");
        if (!$insert) {
            print_r($db->errorInfo());
            exit;
        }
        $insert->execute();

    }

}
*/













