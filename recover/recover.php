<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="assets/js/sweetalert.min.js"></script>
<?php

include "config.php";
if (isset($_COOKIE['eluser'])) {
    $uname = $_COOKIE['eluser'];
    $suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname'");
    $count = mysqli_num_rows($suser);
    if ($count > 0) {
        echo '<meta http-equiv="refresh" content="0; url=galerias.php" />';
        exit;
    }
}
if(isset($_COOKIE['session']) and !empty($_COOKIE['session']) ){
    $session = $_COOKIE['session'];
    echo '<meta http-equiv="refresh" content="0; url=/bellasgram/includes/third-party-auth.php?site=chat&session='.$session.'">';
}


$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$error = 0;

if (isset($_POST['signin'])) {
    ?>
    <script type="text/javascript">
        $(document).ready(function (){
            var formData = new FormData();
            formData.append("recover", "<?php echo $_POST['email']; ?>");
            formData.append("recoverBtn", " ");
            $.ajax({
                url: "/bellasgram/index.php?app=members&section=login", 
                type: "POST",
                data: formData, 
                processData: false,
                contentType: false
            }).done(function(response){
                a=0;
                console.log(response);
                posicion=response.indexOf("No hemos podido enviarte un email con la nueva contrase&ntilde;a, intentalo mas tarde");
                if(posicion!==-1){
                    swal.fire("No hemos podido enviarte un email con la contraseña, intentalo mas tarde","","error");
                    a=1;
                }
                posicion=response.indexOf("No se ha podido actualizar la contrase&ntilde;a");
                if(posicion!==-1){
                    swal.fire("No se ha podido actualizar la contraseña","","error");
                    a=1;
                }
                posicion=response.indexOf("No se ha podido registrar la recuperaci&oacute;n");
                if(posicion!==-1){
                    swal.fire("No se ha podido registrar la recuperacion",'','error');
                    a=1;
                }
                if(a==0){
                    swal.fire("Te hemos enviado por email la nueva contraseña por favor verifica tambien en la carpeta spam de tu correo",'','succes');
                    window.locationf="http://bellasgram/chat/";
                }
            })           
        });
     </script>
     <?php 
    /*$username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = hash('sha256', $_POST['password']);
    $check    = mysqli_query($connect, "SELECT username, password FROM `players` WHERE `username`='$username' AND password='$password'");
    if (mysqli_num_rows($check) > 0) {
		$connect->query("UPDATE `players` SET ipaddres='".get_client_ip_server()."' WHERE username='{$username}'");
		setcookie("eluser", $username, time() + 365 * 24 * 60 * 60);
        echo '<meta http-equiv="refresh" content="0;url=galerias.php">';
    } else {
        echo '<br />
		<div class="callout callout-danger">
              <i class="fas fa-exclamation-circle"></i> The entered <strong>Usuario</strong> o <strong>Contraseña</strong> incorrecta.
        </div>';
        $error = 1;
    }*/
}


?>
<!DOCTYPE html>
<html lang="en">
    <head><meta charset="euc-jp">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <title>Chat</title>

        <!-- CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
        <link rel="stylesheet" href="assets/css/admin.min.css">

        <!-- Favicon -->
        <link rel="shortcut icon" href="../assets/img/favicon.png">
    </head>

    <body class="hold-transition login-page">
        

<div class="login-box">
    <div class="login-logo">
        <a href="index.php"> <strong><?php echo $sitio['name']; ?></strong></a>
  BellasGram
  
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
  new google.translate.TranslateElement({pageLanguage: 'es',includedLanguages: 'en,es,fr,it,pt,ar,de,ru,tr', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</li>
<script type="text/javascript" src="assets/js/translate.js"></script>
<?php } ?> <hr />
        <!--/TRANSLATE-->
    </div>
    <div class="login-box-body">
    	<div class="col-lg-6">
        <p class="login-box-msg">Recuperar Contraseña</p>
<?php

?> 
        <form action="" method="post">
            <div class="form-group has-feedback <?php
if ($error == 1) {
    echo 'has-error';
}
?>">
                <input type="email" name="email" class="form-control" placeholder="Correo Electronico" <?php
if ($error == 1) {
    echo 'autofocus';
}
?> required>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <a href="https://bellasgram.com/chat/index.php">Volver</a>
                </div>
            </div>
       <div class="row">
                <div class="col-xs-12">
                    <button type="submit" name="signin" class="btn btn-primary btn-block btn-flat btn-lg"><i class="fas fa-sign-in-alt"></i>
&nbsp;Recuperar</button><hr>
                </div>
                
            </div>
        </form> 
    </div>
</div>

        <!-- Javascript -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    </body>
</html>
