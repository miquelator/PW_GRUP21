

<?php
namespace PracticaFinal\src\Model\Services;

use PracticaFinal\Controller\DatabaseController;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class comprovacioRegister
{

    public function comprovacioRegisterModel(Application $app, Request $request)
    {
        if ($request->isMethod('POST')) {
            // Validate
            $name = $request->get('name');
            $email = $request->get('email');
            $data = $request->get('data_naixement');
            $password = $request->get('password');
            var_dump($name);

            if (!isset($_POST['email']) ||
                !isset($_POST['name']) ||
                !isset($_POST['data_naixement']) ||
                !isset($_POST['password']) ||
                !isset($_POST['confirm'])
            ) {

                echo "Revisa tots els camps";
                header("/home");
                include 'main_register.php'; //tornem al registre
                exit;
            } else {

                $name = $_POST['name'];
                $email = $_POST['email'];
                $data_naixement = $_POST['data_naixement'];
                $password = $_POST['password'];
                $confirm = $_POST['confirm'];
                if (validName($name) &&
                    validEmail($email) &&
                    validData($data_naixement) &&
                    validPassword($password, $confirm)
                ) {
                    //si tot es valida
                    $name = htmlentities($name, ENT_QUOTES); //faig que no es pugui fer sql injection
                    $password = htmlentities($password, ENT_QUOTES);
                    $email = htmlentities($email, ENT_QUOTES);
                    //connectaBaseDades($name, $email, $data_naixement, $password);

                    //ho guardem per base de dades
                    $databasecontroller=new DatabaseController();
                    $databasecontroller->postAction($app, $request);


                    //include 'main_login.php';

                    exit;
                }
            }
            echo "Revisa els camps";
            include 'main_register.php'; //tornem al registre
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
            $d = DateTime::createFromFormat('Y-m-d', $data_naixement);

            if ($d && $d->format('Y-m-d') == $data_naixement) {
                return true;
            } else {
                echo "Data fallada.  ";
                return false;
            }

        }

        function validPassword($password, $confirm)
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













