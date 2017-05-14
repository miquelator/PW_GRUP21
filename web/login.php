<?php




if (empty($_POST)) {

    header("Location: main_login.php");
    include 'main_login.php'; //tornem al login


    exit;
}




if ( !isset($_POST['user']) || !isset($_POST['password'])) {
    echo "Revisa tots els camps";
    header("Location: main_login.php");
    include 'main_login.php'; //tornem al login
    exit;
} else {

    $user= $_POST['user'];
    $password = $_POST['password'];


        //si tot es valida
        $user=htmlentities($user, ENT_QUOTES); //faig que no es pugui fer sql injection
        $password=htmlentities($password, ENT_QUOTES);

        if (!connectaBaseDades($user,$password)){

            setcookie("usuari",$user,time()+3600*7*24); //guardem l'usuari en una cookie per una setmana

            echo "Usuari connectat";
            include 'practica1.html'; //DESCOMENTAR

            //usuari connectat. Falta guardar-lo a la pagina principal
        }else{
            echo "Contrasenya o usuari erronis";
            include'main_login.php';
        }


        exit;

}
//password i user de base de dades: nick i nick
echo "Revisa els camps";
include 'main_login.php'; //tornem al login
exit;





function connectaBaseDades($name,$password){
    $db = new PDO('mysql:host=localhost;dbname=Exercici1', 'nick', 'nick'); //base,user,password
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //user:
    $insert = $db->query("SELECT id FROM usuari WHERE (username='".$name."' OR email='".$name."' ) AND password='".$password."'"); //comete dobles entremig de cometes simples
    $dades = $insert->fetchAll();








    if(!empty($dades)){ //vol dir que s'ha trobat l'usuari
        //accedeixo a l'id

        $id=$dades[0]['id'];
        //$nom=$dades[0]['username'];


        //guardem la id en sessio
        session_start();
        $_SESSION["id_usuari"]=$id;
       // $_SESSION["nom_usuari"]=$nom;



        return FALSE;
    }
    else{
        return TRUE;//NO S'HA TROBAT USUARI
    }

}













