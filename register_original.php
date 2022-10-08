<?php
include "config.php";

$queryconfig   = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
$configsite   = mysqli_fetch_assoc($queryconfig);

if (isset($_COOKIE['eluser'])) {
    $uname = $_COOKIE['eluser'];
    $suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname'");
    $count = mysqli_num_rows($suser);
    if ($count > 0) {
        echo '<meta http-equiv="refresh" content="0; url=galerias.php" />';
        exit;
    }
}

$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$error = 0;


if (isset($_POST['signin'])) {
$username = $_POST['username'];
    $password = hash('sha256', $_POST['password']);
    $email    = $_POST['email'];
	$sexo    = $_POST['sexo'];
    
	 $sqlbono = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
   $sbono   = mysqli_fetch_assoc($sqlbono);
   $bono    = $sbono['bonoref'];
   
   do {
    $crearcodigoreferidos = rand(111111,999999); 
	$sqlexiste = mysqli_query($connect, "SELECT * FROM `players` WHERE refcodigo='$crearcodigoreferidos'");
  
  } while (mysqli_num_rows($sqlexiste) > 0);
   
   $codigoreferer = $_POST['codigoref'];
  if ($codigoreferer > 0){
	  
	  
	  
	  $sqlref = mysqli_query($connect, "SELECT * FROM `players` WHERE refcodigo='$codigoreferer'");
        if (mysqli_num_rows($sqlref) > 0) {
     $rowreferer   = mysqli_fetch_assoc($sqlref);
			$refererid = $rowreferer['id'];
        } else{
			$refererid = 0;
		}
	  
  }else{
	  $refererid = 0;
  }
	
	if ($refererid == 0){
		$dinero = 0;
	}else{
		$dinero = $bono;
	}

    $sql = mysqli_query($connect, "SELECT username FROM `players` WHERE username='$username'");
    if (mysqli_num_rows($sql) > 0) {
        echo '<br /><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> El correo username ya está siendo utilizado por otra persona</div>';
    } else {
		
		 $sql2 = mysqli_query($connect, "SELECT email FROM `players` WHERE email='$email'");
        if (mysqli_num_rows($sql2) > 0) {
            echo '<br /><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  El correo email ya se encuentra registrado en el sistema</div>';
        } else {
        if ($_POST['password'] != $_POST['password2']) {
            echo '<br /><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Las contraseñas no coinciden</div>';
        } else { 
			
			$insert = mysqli_query($connect, "INSERT INTO `players` (`username`, `password`, `email`, `creditos`, `gender`, `refcodigo`, `referer_id`, `ipaddres`) 
				VALUES ('$username', '$password', '$email', '$dinero', '$sexo', '$crearcodigoreferidos', $refererid, '".get_client_ip_server()."')");
				
			if ($refererid > 0){
				$refererupdate = mysqli_query($connect, "UPDATE `players` SET creditos=creditos+'$bono' WHERE id='$refererid'");
			}
				
            setcookie("eluser", $username, time() + 365 * 24 * 60 * 60);
            echo '<meta http-equiv="refresh" content="0;url='.$sitio['site'].'galerias.php">';
        }
    }
}
} 
?>
<!DOCTYPE html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <title><?php echo $sitio['name']; ?> &rsaquo;</title>

        <!-- CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
        <link rel="stylesheet" href="assets/css/admin.min.css">

        <!-- Favicon -->
        <link rel="shortcut icon" href="../assets/img/favicon.png">
    </head>

    <body class="hold-transition login-page">
        
<div class="login-box">
    <div class="login-logo">
        <a href="index.php"><i class="fas fa-heart"></i> <?php echo $sitio['name']; ?></a>
        
                 <!--TRANSLATE TRADUCTOR--> 
          <hr />
<style>
body {
  top: 0 !important;
}
.goog-te-banner-frame {
  display: none;
}
.goog-te-gadget-simple{
    padding: 5px !important;
    border-radius:2px;
    background-color: #fafafa;
    border-left: 0;
    border-right: 0;
    border-top: 0;
    border-bottom: 0;
}
.goog-te-gadget-icon{
    display: none;
}
.goog-te-gadget-simple> span > a > span{
    font-size: 14px;
    color: #313131;
}
.goog-tooltip{
    display: none !important;
}
</style>
<?php if(basename($_SERVER['PHP_SELF'])!='chat.php'){ ?> 

<div id="google_translate_element"></div>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'es',includedLanguages: 'en,es,fr,it,pt', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</li>
<script type="text/javascript" src="assets/js/translate.js"></script>
<?php } ?> <hr />
        <!--/TRANSLATE-->
        
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Registrar una cuenta, es gratis</p>
<?php



?> 
        <form action="" method="post">
		<h4>Nombre</h4>
           
			
			 <div class="form-group has-feedback <?php
if ($error == 1) {
    echo 'has-error';
}
?>">
                <input type="text" name="username" class="form-control" placeholder="Como te llamas?" <?php
if ($error == 1) {
    echo 'autofocus';
}
?> required>


			
			<!--sexo-->
			<h4>Género</h4>
			<div class="form-group has-feedback <?php
if ($error == 1) {
    echo 'has-error';
}
?>"><br/>
                <select name="sexo" required> 
  <option value="hombre">Soy Hombre</option> 
  <option value="mujer">Soy Mujer</option>  
  </select> 
  <br> 


                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
			
			
			<!--correo electronico-->
			<h4>Correo Electronico</h4>
			<div class="form-group has-feedback <?php
if ($error == 1) {
    echo 'has-error';
}
?>">

<input type="email" name="email" class="form-control" placeholder="Tu Correo Electrónico" <?php
if ($error == 1) {
    echo 'autofocus';
}
?> required>


                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
			
			
				
			<!--contraseña-->
			
			<h4>Contraseña</h4>
			
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Elige una Contraseña" required>
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
			
			  <div class="form-group has-feedback">
                <input type="password" name="password2" class="form-control" placeholder="Repite la Contraseña" required>
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
			
			
		<!-- OCULTO	<div class="form-group">
                                        <h4>Código de referido</h4>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text"></div>
									</div>
                                            <input name="codigoref" type="number" id="codigoref" placeholder="opcional" class="form-control">
                                      <span class="glyphicon glyphicon-user form-control-feedback"></span>
									  
										</div>
                                    </div>
									
									
			

			<br><br>
            <div class="row">
                <div class="col-xs-12">
                   
                   <button type="submit" name="signin" class="btn btn-primary btn-block btn-flat btn-lg"><i class="fas fa-sign-in-alt"></i>
&nbsp;Registrarse</button>

                </div>
            </div>OCULTO -->
</form> 

    </div>
</div>


        <!-- Javascript -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    </body>
</html>