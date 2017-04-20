<!--fgf-->
<!DOCTYPE html>
<html lang="en">
    <head> 
    <meta charset="utf-8" author="Nick van der Graaf ls31188" title="">
		<meta name="viewport" content="width=device-width, initial-scale=1">



		<link rel="stylesheet" type="text/css" href="../../../Practica1/Practica1/register.css">



		<link href="../../../Practica1/Practica1/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet"><!--css bootstrap-->
		<link href='https://fonts.googleapis.com/css?family=Passion+One' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>

		<title>Practica1 Informatica Gestio</title>
	</head>
	<body>
		<div class="container">
			<div class="row main">
				<div class="panel-heading">
	               <div class="panel-title text-center">
	               		<h2 class="title">Registrar-se</h2>
	               		
	               	</div>
	            </div> 
				<div class="main-login main-center">
					<form id=formulari name="formulari" class="form-horizontal" onsubmit="return validateForm()" method="POST" action="../../../Practica1/Practica1/register.php">
						
						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">Nom</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" class="form-control" name="name" id="name"  placeholder="Escriu nom" maxlength="20"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">Email</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
									<input type="email" class="form-control" name="email" id="email"  placeholder="Escriu Email"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="data_naixement" class="cols-sm-2 control-label">Data naixement</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="date" class="form-control" name="data_naixement" id="data_naixement" placeholder="AAAA-MM-DD. Ficar 0 davant dia o mes si cal">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="password" class="cols-sm-2 control-label">Password</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
									<input type="password" class="form-control" name="password" id="password"  placeholder="Escriu Password" maxlength="12"
									minlength="6"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="confirm" class="cols-sm-2 control-label">Confirm Password</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
									<input type="password" class="form-control" name="confirm" id="confirm"  placeholder="Confirma Password"/>
								</div>
							</div>
						</div>

						<div class="form-group ">
							<button type="submit" class="btn btn-primary btn-lg btn-block login-button">Registra't</button>
						</div>
						<div class="login-register">
				            <a href="../../../Practica1/Practica1/main_login.php">Ja tinc usuari</a>
				         </div>
						<div class="login-register">

							<a href="../../../Practica1/Practica1/practica1.html">Anar a pàgina principal</a>
						</div>
					</form>
				</div>
			</div>
		</div>
		<script src="../../../Practica1/Practica1/jquery.js"></script>
		<script src="../../../Practica1/Practica1/bootstrap-3.3.7-dist/js/bootstrap.js"></script>
	</body>
</html>