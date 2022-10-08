<?php

require "config.php";

$eltoken = rand(11111,99999);
$urid = $_GET['urid'];
$chatid = $_GET['chatid'];
$toid= $_GET['toid'];
$fileName = $_FILES["file1"]["name"]; // The file name
$fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["file1"]["type"]; // The type of file it is
$fileSize = $_FILES["file1"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true

if (!$fileTmpLoc) { // if file not chosen
  echo "ERROR: por favor primero haga click en seleccionar archivo antes de hacer click en enviar foto.";
  exit();
}
$info = pathinfo($_FILES['file1']['name']);

$elfilenm = md5(rand().time()).".".$info['extension'];

// COMPRUEBA SI EL ARCHIVO ES UNA IMAGEN
if($fileType == 'image/jpeg' OR $fileType == 'image/png' OR $fileType == 'image/jpg')
{
  // SI EL ARCHIVO SE MOVIO CORRECTAMENTE
  if(move_uploaded_file($fileTmpLoc, "uploads/src_messages/$elfilenm"))
  {
    $mensaje = '';
    $time    = time();
    $foto = 'Yes';
    $rutadefoto = 'uploads/src_messages/'.$elfilenm;
    $timeroom = time();

    $update_time_room = mysqli_query($connect, "UPDATE `nuevochat_rooms` SET time='{$timeroom}' WHERE id='{$chatid}'");

    $post_gcmessage = mysqli_query($connect, "INSERT INTO `nuevochat_mensajes` (id_chat, author, toid, mensaje, time, foto, rutadefoto) VALUES ('$chatid', '$urid', '$toid', '$mensaje', '$time', '$foto', '$rutadefoto')");

    $lasidinsert = mysqli_insert_id($connect);
    $query = $connect->query("SELECT *, nm.`id` AS id FROM `nuevochat_mensajes` AS nm INNER JOIN players AS p ON p.`id` = nm.`author` WHERE nm.`id` = '$lasidinsert'");

    if ($query AND $query->num_rows > 0)
    {
        // GUARDA EL MESAJE EN UN ARRAY
      while ($messages = $query->fetch_assoc())
      {
        $msg[] = $messages;
      }
      echo json_encode($msg);
    }
  } else {
    echo "error 980";
  }
}else{
  echo "nadabien";
}
?>
