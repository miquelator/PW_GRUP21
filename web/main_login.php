<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" author="Nick van der Graaf ls31188" title="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet"><!--css bootstrap-->

    <link rel="stylesheet" type="text/css" href="entrada.css">


    <title>Practica1 Informatica Gestio</title>
</head>
<body>





<div class="container">
    <div class="row">
        <div class="container cols-sm-2 control-label" id="formContainer">

            <form class="form-signin" id="login"  method="POST" action="login.php">
                <h3 class="form-signin-heading">Iniciar sessió</h3>
                <a href="#" id="flipToRecover" class="flipLink">

                </a>
                <input type="text" class="form-control" name="user" id="user"  placeholder="Email o usuari" autofocus required/>
                <input type="password" class="form-control" id="password" name="password" maxlength="12"
                       minlength="6" placeholder="Contrasenya" required>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Inicia</button>

                <div class="login-register">
                    <a href="main_register.php">No tinc usuari</a>

                </div>
                <div >

                    <a href="practica1.html">Anar a pàgina principal</a>
                </div>
            </form>



        </div> <!-- /container -->
    </div>
</div>


<script src="jquery.js"></script>
<script src="bootstrap-3.3.7-dist/js/bootstrap.js"></script>
</body>
</html>