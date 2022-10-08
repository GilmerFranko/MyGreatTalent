<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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


$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$error = 0;

if(isset($_POST['recover']))
{

  // SI SE HA GENERADO
  if(true)
  {
    // SI SE INGRESO UN EMAIL
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    {
      $WHERE = 'email = \''. $_POST['email'] .'\'';
    }
    // SINO, SE INGRESO UN USERNAME
    else
    {
      $WHERE = 'username = \''. $_POST['email'] .'\'';
    }

    $SQLUser = $connect->query('SELECT `id`,`username`,`email` FROM `players` WHERE '.$WHERE);

    // SI EXISTE EL USUARIO
    if($SQLUser AND $SQLUser->num_rows > 0)
    {
      $User = $SQLUser->fetch_assoc();

      // GENERAR CONTRASEÑA ALEATORIA
      $password = generateUUID(10);
      // ACTUALIZAR CONTRASEÑA DE USUARIO
      $passwordKey = password_hash($password, PASSWORD_DEFAULT);
      // VALIDAR CUENTA DE USUARIO
      $updated = $connect->query('UPDATE `players` SET `password` = \''. $passwordKey .'\' WHERE '.$WHERE);

      if($updated == true)
      {
        // ENVIAR NUEVA CONTRASEÑA AL USUARIO
        $email = sendEmail( 'newpassword', $_POST['email'], array('name' => $User['username'], 'password' => $password) );
        if($email == true)
        {
          $message = array('Te hemos enviado por email la nueva contrase&ntilde;a por favor verifica tambien en la carpeta spam de tu correo','', 'success');
        }
        else
        {
          $message = array('No hemos podido enviarte un email con la nueva contrase&ntilde;a, inténtalo mas tarde','','error');
        }
      }
      else
      {
        $message = array('No se ha podido actualizar la contrase&ntilde;a','', 'error');
      }
    }
    else
    {
    // SI NO SE HA GENERADO UN HASH
      $message = array('No se ha el usuario o correo electrónico introducido','', 'error');
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="euc-jp">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
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
    </div>
    <div class="login-box-body">
      <p class="login-box-msg">Recuperar Contraseña</p>
      <form action="" method="post">
        <div class="form-group has-feedback <?php if ($error == 1) echo 'has-error';?>">
          <input type="text" name="email" class="form-control" placeholder="Nombre o Correo Electronico" <?php if ($error == 1)echo 'autofocus';?> required>
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-12">
            <button type="submit" name="recover" class="btn btn-primary btn-block btn-flat btn-lg"><i class="fas fa-sign-in-alt"></i>
            &nbsp;Recuperar</button><hr>
          </div>
        </div>
      </form>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <a href="https://my-great-talent.com/">Volver atrás</a>
        </div>
      </div>
      <div class="row" align="center">
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
</div>
</form>
</div>
</div>

<!-- Javascript -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>
</html>

<?php if(isset($_POST['recover'])) setSwal($message); ?>
