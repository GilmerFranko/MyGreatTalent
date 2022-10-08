<?php

include "config.php";

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

if (isset($_POST['signin']) AND isset($_POST['ajax']))
{
  $username = mysqli_real_escape_string($connect, $_POST['username']);

  // SI SE INGRESO UN EMAIL
  if(filter_var($username, FILTER_VALIDATE_EMAIL))
  {
    $WHERE = 'email = \''. $username .'\'';
  }
  // SINO, SE INGRESO UN USERNAME
  else
  {
    $WHERE = 'username = \''. $username .'\'';
  }

  $check = mysqli_query($connect, "SELECT username, password FROM `players` WHERE $WHERE");

  if (mysqli_num_rows($check) > 0)
  {
    $User = $check->fetch_assoc();
    if (password_verify($_POST['password'],$User['password'])) {

        //
      $connect->query("UPDATE `players` SET ipaddres='".get_client_ip_server()."' WHERE username='{$username}'");
        //
      setcookie("eluser", $User['username'], time() + 365 * 24 * 60 * 60);
        //
      $User = $check->fetch_assoc();

      //$return=welcomechat($User['id'],$email);
        //
      $message= array('Has sido logeado con éxito','',true);

    }
    else
    {
      $message= array('La contraseña que ingreso es invalida','',false);
    }
  }
  else
  {
    $message = array('El usuario o correo electronico que ingreso no esta registrado','',false);
  }
  echo json_encode($message);
}


?>
<?php if(!isset($_POST['ajax'])): ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="euc-jp">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
  <title>My Great Talent</title>

  <!-- CSS -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
  <link rel="stylesheet" href="assets/css/admin.min.css">

  <!-- Favicon -->
  <link rel="shortcut icon" href="../assets/img/favicon.png">
</head>

<body class="hold-transition login-page">


  <div class="login-box">
    <div class="login-logo">
      <a href="index.php"> <strong><?php echo $sitio['name']; ?></strong></a><br>
      Tu talento te premia
    </div>
    <!--TRANSLATE TRADUCTOR--> <center>
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
    <?php } ?> <hr /></center>
    <!--/TRANSLATE-->
    <div class="login-box-body">
      <p class="login-box-msg">Acceder</p>
      <?php

      ?>
      <form id="formLogin" action="" method="post" onsubmit="return Login()">
        <div class="form-group has-feedback <?php
        if ($error == 1) {
          echo 'has-error';
        }
      ?>">
      <input id="logEmail" type="username" name="username" class="form-control" placeholder="Nombre de usuario" <?php
      if ($error == 1) {
        echo 'autofocus';
      }
    ?> required>
    <span class="glyphicon glyphicon-user form-control-feedback fa fa-user"></span>
  </div>
  <div class="form-group has-feedback">
    <input id="logPassword" type="password" name="password" class="form-control" placeholder="Contraseña" required>
    <span class="glyphicon glyphicon-lock form-control-feedback fa fa-lock"></span>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <span class="">¿Has olvidado tu contraseña? <a href="recover.php">Recuperala aqui</a></span>                </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <button type="submit" name="signin" class="btn btn-primary btn-block btn-flat btn-lg"><i class="fas fa-sign-in-alt"></i>
        &nbsp;Acceder</button><hr>

        <a href="<?php echo $sitio['site']; ?>register.php" class="btn btn-primary btn-block btn-flat btn-lg">Registrarse</a>

      </div>

    </div>
  </form>
</div>
<center><br/>
  <img src="https://bellasgram.com/chat/assets/img/foto.png"> Apoya a tus autores favoritos <img src="https://bellasgram.com/chat/assets/img/video.png">
  <br/><br/>
  <img src="https://bellasgram.com/chat/assets/img/bolsa-de-dinero.png"> Gana premios y regalos <img src="https://bellasgram.com/chat/assets/img/bolsa.png">
  <br/><br/>
  <img src="https://bellasgram.com/chat/assets/img/amista1.png"> Haz nuevas amistades <img src="https://bellasgram.com/chat/assets/img/amistad2.png">


  <hr />


  <a href="https://bellasgram.com/index.php?app=site&section=pages&name=dmca">DMCA</a> &nbsp; &nbsp;
  <a href="https://bellasgram.com/index.php?app=site&section=pages&name=privacy">Privacidad</a> &nbsp; &nbsp;
  <a href="https://bellasgram.com/index.php?app=site&section=pages&name=terms">T&eacute;rminos de Uso</a> &nbsp;&nbsp;
  <a href="https://bellasgram.com/index.php?app=site&section=pages&name=protocol">Protocolo</a>  &nbsp;&nbsp;
  <a href="https://bellasgram.com/index.php?app=site&section=contact">Contacto</a>



</center>
</div>

<script type="text/javascript">

    function Login(){
      var formData = new FormData();
      formData.append("username", $("#logEmail").val());
      formData.append("password", $("#logPassword").val());
      formData.append("ajax", "true");
      formData.append("signin", "true");
      $.ajax({
        url: "index.php",
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
          location.reload()
        }
        else
        {
          swal.fire(a[0],a[1],'error');
        }
      });
      return false;
    };
</script>
<!-- Javascript -->
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/sweetalert.min.js"></script>
<style type="text/css">
  .swal2-popup {
    font-size: 1.5rem !important;
  }
</style>
</body>
</html>
<?php endif; ?>
<?php 
function welcomechat($id,$email){
  include "core.php";
  $wlcomecount=mysqli_query($connect,"SELECT * FROM welcomechat WHERE userid=$id")->num_rows;
  if ($wlcomecount>0) {
    echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
    exit;
  }else{
    $to = $email;
    $subject = "Welcome to My Great Talent";
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: BellasGram <contacto@bellasgram.com>';
    $message = "Hi <strong>".$rowu['username']."</strong><br>

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
