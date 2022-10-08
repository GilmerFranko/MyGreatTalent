<?php
include "config.php";

$queryconfig   = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
$configsite   = mysqli_fetch_assoc($queryconfig);

if (isset($_COOKIE['eluser'])) {
  $uname = $_COOKIE['eluser'];
  $suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname'");
  $count = mysqli_num_rows($suser);
  if ($count > 0) {
    $welcome=mysqli_fetch_assoc($suser);
    $return=welcomechat($welcome['id'],$welcome['email']);
    if($return==true){
      echo '<meta http-equiv="refresh" content="0; url=messages.php?welcomechat=#92adsajr2297h2h7HUJHG7Gk" />';
      exit;
    }else{
      echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
      exit;
    }
  }
}

$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$error = 0;


if (isset($_POST['register'])) {
  $username = $connect->real_escape_string($_POST['username']);
  $password = $connect->real_escape_string(password_hash($_POST['password'],PASSWORD_DEFAULT));
  $email    = $connect->real_escape_string(strtolower($_POST['email']));
  $sexo     = $connect->real_escape_string($_POST['sexo']);

  $sqlbono  = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
  $sbono    = mysqli_fetch_assoc($sqlbono);
  $bono     = $sbono['bonoref'];

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
  }
  else
  {
  $dinero = $bono;
  }
  // COMPROBAR NOMBRE Y EMAIL
  if( preg_match("/^([a-zA-Z ]{4,30}+)$/isu", $username)  && filter_var($email, FILTER_VALIDATE_EMAIL) )
  {
    $sql = mysqli_query($connect, "SELECT username FROM `players` WHERE username='$username'");

    // SI NO SE ENCUENTRA NINGUN USUARIO REGISTRADO CON EL NOMBRE INGRESADO
    if ($sql AND $sql->num_rows <= 0)
    {
      //
      $sql2 = mysqli_query($connect, "SELECT email FROM `players` WHERE email='$email'");

      // SI NO SE ENCUENTRA NINGUN USUARIO REGISTRADO CON EL CORREO INGRESADO
      if ($sql2 AND $sql2->num_rows <= 0)
      {
        // SI LAS CONTRASEÑA COINCIDEN
        if ($_POST['password'] == $_POST['password2']) {

          // REGISTRA EL NUEVO USUARIO
          $insert = mysqli_query($connect, "INSERT INTO `players` (`username`, `password`, `email`, `creditos`, `gender`, `refcodigo`, `referer_id`, `ipaddres`)
          VALUES ('$username', '$password', '$email', '$dinero', '$sexo', '$crearcodigoreferidos', $refererid, '".get_client_ip_server()."')");
          if($insert)
          {
            $lasID = $connect->insert_id;
            // DAR REGALO SEMANAL
            $connect->query("INSERT INTO `giftcredits_weekly` (`player_id`, `credits`, `time`) VALUES ( \"". $lasID ."\", '200', \"". time() ."\" )");
            // ENVIAR NOTIFICACIÓN DE REGALO
            $connect->query("INSERT INTO `players_notifications` (`toid`, `fromid`, `not_key`, `action`, `read_time`) VALUES (\"". $lasID ."\", '0', 'giftWeekly', '200', '0')");

            if ($refererid > 0)
            {
            $refererupdate = mysqli_query($connect, "UPDATE `players` SET creditos=creditos+'$bono' WHERE id='$refererid'");
            }
            // ESTABLECER COOKIE PARA LA SESSION
            setcookie("eluser", $username, time() + 365 * 24 * 60 * 60);
            // ENVIA MENSAJE DE BIENVENIDA
            //$return=welcomechat($connect->insert_id,$email);

            // RETORNAR MENSAJE
            $message = array('Has sido registrado con éxito!','',true);
          }
        }
        else
        {
          $message = array('Las contraseñas no coinciden','Comprueba que la contraseña que anotaste en los dos campos coincidan',false);
        }
      }
      else
      {
        $message = array('El Correo Electrónico ya se encuentra registrado en el sistema','Intenta con otro correo electrónico diferente.',false);
      }
    }
    else
    {
      $message = array(' El nombre de usuario que ingresó ya está siendo utilizado por otra persona','Intenta con otro nombre diferente',false);
    }
  }
  else
  {
    $message = array('Nombre o Email no validos', 'No se aceptan caracteres extraños en los nombres');
  }
  echo json_encode($message);
}

?>
<?php if(!isset($_POST['ajax'])): ?>
  <!DOCTYPE html>
  <html lang="en">
  <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <title><?php echo $sitio['name']; ?> &rsaquo;</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link rel="stylesheet" href="assets/css/admin.min.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/favicon.png">
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/sweetalert.min.js"></script>
    <style type="text/css">
    .swal2-popup {
      font-size: 1.5rem !important;
    }
    </style>
  </head>

  <body class="hold-transition login-page">

    <div class="login-box">
      <div class="login-logo">
        <a href="index.php"><i class="fas fa-heart"></i> <?php echo $sitio['name']; ?></a>
      </div>
      <div class="login-box-body">
       <p class="login-box-msg">Registrarte<br/>
       Es rápido y fácil.</p>
       <?php



       ?>
       <form id="formRegister" action="" method="post">
        <h4>Nombre</h4>


        <div class="form-group has-feedback <?php if ($error == 1) {echo 'has-error';}?>">
          <input type="username" id="regUsername" class="form-control" placeholder="Como te llamas?" <?php
          if ($error == 1)echo 'autofocus';?> required>



          <!--sexo-->
          <h4>Sexo</h4>
          <div class="form-group has-feedback <?php if ($error == 1)echo 'has-error';?>"><br/>
            <select id="regGender" name="sexo" required>
              <option value="hombre">Soy Hombre</option>
              <option value="mujer">Soy Mujer</option>
            </select>
            <br>


            <span class="glyphicon glyphicon-user form-control-feedback fa fa-user"></span>
          </div>


          <!--correo electronico-->
          <h4>Correo Electr&oacute;nico</h4>
          <div class="form-group has-feedback <?php
          if ($error == 1) echo 'has-error';?>">

          <input id="regEmail" type="text" name="email" class="form-control" placeholder="Tu Correo Electrónico" <?php
          if ($error == 1) {
            echo 'autofocus';
          }
        ?> required>


        <span class="glyphicon glyphicon-user form-control-feedback fa fa-user"></span>
      </div>



      <!--contraseña-->

      <h4>Contraseña</h4>

      <div class="form-group has-feedback">
        <input id="regPassword" type="password" name="password" class="form-control" placeholder="Elige una Contraseña" required>
        <span class="glyphicon glyphicon-lock form-control-feedback fa fa-lock"></span>
      </div>

      <div class="form-group has-feedback">
        <input id="regPassword2" type="password" name="password2" class="form-control" placeholder="Repite la Contraseña" required>
        <span class="glyphicon glyphicon-lock form-control-feedback fa fa-lock"></span>
      </div>


      <div class="form-group">
        <h4>Código de referido (opcional)</h4>
        <div class="input-group">
          <div class="input-group-prepend">
            <div class="input-group-text"></div>
          </div>
          <input name="codigoref" type="number" id="codigoref" placeholder="opcional" class="form-control">
          <span class="glyphicon glyphicon-user form-control-feedback fa fa-user"></span>
        </div>
      </div>




      <br><br>
      <div class="row">
        <div class="col-xs-12">

         <button type="submit" name="signin" class="btn btn-primary btn-block btn-flat btn-lg"><i class="fas fa-sign-in-alt"></i>
         &nbsp;Registrarse</button>

         <span class="grey-text">
          Al hacer clic en Registrarte aceptas nuestros <a href="https://bellasgram.com/index.php?app=site&section=pages&name=terms">T&eacute;rminos del servicio</a>. Obt&eacute;n m&aacute;s informaci&oacute;n en la <a href="https://bellasgram.com/index.php?app=site&section=pages&name=privacy">Pol&iacute;tica de privacidad</a>.
        </span>
      </div>
    </div>
  </form>

</div>
<!--TRANSLATE TRADUCTOR-->
<center> <hr />
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
<?php } ?> <hr /></center>
<!--/TRANSLATE-->


<center><img src="https://bellasgram.com/chat/assets/img/insignia.png"> Recomendaciones: <img src="https://bellasgram.com/chat/assets/img/insignia.png"></center>
<br/><br/>
<img src="https://bellasgram.com/chat/assets/img/datos-del-usuario.png"> Elige una contraseña que puedas recordar, apuntala en algun lugar para que puedas volver a entrar.	 
<br/><br/>
<img src="https://bellasgram.com/chat/assets/img/navegador.png"> Recuerda la url de nuestra página web, es: bellasgram.com/chat

</div>
</div>

<!-- Javascript -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>
</html>

  <script type="text/javascript">
    $(document).ready(function (){
      $("#formRegister").submit(function(){
        var formData = new FormData();
        formData.append("username", $("#regUsername").val());
        formData.append("password", $("#regPassword").val());
        formData.append("email", $("#regEmail").val());
        formData.append("sexo", $("#regGender").val());
        formData.append("password2", $("#regPassword2").val());
        formData.append("codigoref", $("#codigored").val());
        formData.append("ajax", "true");
        formData.append("register", "true");
        password = $("#regPassword").val();
        password2 = $("#regPassword2").val();
        if(password.length>5){
          if(password == password2){
            $.ajax({
              url: "register.php",
              type: "POST",
              data: formData,
              processData: false,
              contentType: false
            }).done(function(a){
              console.log(a)
              a = $.parseJSON(a);
              if(a[2] == true)
              {
                swal.fire(a[0],a[1],'success')
                location.reload();
              }
              else
              {
                swal.fire(a[0],a[1],'error');
              }
            });
          }
          else
          {
            swal.fire('La contraseñas no son iguales','Debes ingresar de forma correcta la contraseña en los dos campos','warning');
          }
        }
        else
        {
          swal.fire('La contraseña es muy corta','Intenta ingresar una contraseña mas larga','warning');
        }
        return false;
      });
    });
  </script>
  <?php endif; ?>
<?php 
function welcomechat($id,$email){
  include "config.php";
  $wlcomecount=mysqli_query($connect,"SELECT * FROM welcomechat WHERE userid=$id")->num_rows;
  $SQLUser = getUser($id);
  $User = $SQLUser->fetch_assoc();
  if ($wlcomecount>0) {

  }else{

    $to = $email;
    $subject = "Welcome to My Great Talent";
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: My Great Talent <contacto@bellasgram.com>';
    $message = "Hi <strong>".$User['username']."</strong><br>

    <h4>Welcome to My Great Talent</h4><br>

    <span>You have successfully joined the best place.</span><br>
    <span>We are very happy that you have come to support your favorite authors.</span><br>

    <a href='https://my-great-talent.com/'>my-great-talent.com</a>


    <br>";


    $mail=mail($to, $subject, $message, $headers);

    if ($mail) {
      mysqli_query($connect,"INSERT welcomechat (userid,welcomechat) values ($id,'si')");
    }else{
      mysqli_query($connect,"INSERT welcomechat (userid,welcomechat) values ($id,'si')");
    }
    return true;
  }

}
?>
