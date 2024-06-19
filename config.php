<?php
  //Fill this information
$host     = "localhost"; // Database Host
$user     = "root"; // Database Username
$password = ""; // Database's user Password
$database = "mlywatsm_otrochat"; // Database Name
  //------------------------------------------------------------

$connect = mysqli_connect($host, $user, $password, $database);

  // Checking Connection
if (mysqli_connect_errno()) {
  echo "Failed to connect with MySQL: " . mysqli_connect_error();
}

mysqli_set_charset($connect, 'utf8mb4');

@session_start();

if (isset($_COOKIE['eluser'])) {
  $uname = $_COOKIE['eluser'];
  $suser = mysqli_query($connect, "SELECT id,username,email FROM `players` WHERE username='$uname'");
  if ($suser && mysqli_num_rows($suser) > 0) {
    //Set Online
    $prow    = mysqli_fetch_assoc($suser);
    $timenow = time();
    $update  = mysqli_query($connect, "UPDATE `players` SET timeonline='$timenow' WHERE username='$uname'");
  }
}


  /*=============================================
  =      ESTABLECER CONFIGURACION DEL SITIO     =
  =============================================*/

  $sqlsitio = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
  $sitio = mysqli_fetch_assoc($sqlsitio);

  // ESTABLECER ZONA HORARIA
  date_default_timezone_set('America/Costa_Rica');

  // COMPROBAR SI SE PERMITEN VISITAR PAGINAS SIN ESTAR LOGUEADO
  if ($sitio['limit_unlogged_users'])
  {
    // AGG SITIOS EN LOS QUE SE PUEDE ENTRAR SIN ESTAR LOGUEADO
    $sitesfree = array('profile.php', 'index.php');
  }
  else
  {
    $sitesfree = array('');
  }
  // DATOS PARA USUARIO INVITADO
  $guestUser = array('id' => '0987654321','username' => 'invitado', 'email' => '', 'baneado' => '0','role' => 'Player', 'theme' => 0,'follower' => '[]', 'registerfrom' => 'chat');

  /*===== // ======*/

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
//error_reporting(E_ALL);

  require_once("functions.php");



  if(isset($_COOKIE['session']) and !empty($_COOKIE['session']) ){

    if($session = getSession($_COOKIE['session']))
    {
      $rowu = $session;
      $timenow = time();
      $update  = mysqli_query($connect, "UPDATE `players` SET timeonline='$timenow' WHERE username='$_COOKIE[session]'");
    }


  }

