<?php
require "config.php";

function handleFileUpload($file, $chatId, $userId, $toId) {
  global $connect, $session;
  $fileName = $file["name"];
  $fileTmpLoc = $file["tmp_name"];
  $fileType = $file["type"];
  $fileSize = $file["size"];

  if (!$fileTmpLoc) {
    http_response_code(400);
    echo json_encode(['error' => 'Por favor, selecciona un archivo antes de enviarlo.']);
    exit();
  }

  $info = pathinfo($fileName);
  $elfilenm = md5(rand() . time()) . "." . $info['extension'];
  $uploadDir = "uploads/src_messages/";
  $uploadPath = $uploadDir . $elfilenm;

  if ((($fileType == 'image/jpeg' || $fileType == 'image/png' || $fileType == 'image/jpg') && $fileSize <= 20000000) || ($fileType == 'video/mp4' && $fileSize <= 20000000 AND $session['permission_send_gift'] == 1)) {
    if (move_uploaded_file($fileTmpLoc, $uploadPath)) {
      $timeroom = time();
      $updateTimeRoom = mysqli_query($connect, "UPDATE `nuevochat_rooms` SET time='{$timeroom}' WHERE id='{$chatId}'");

      $mensaje = '';
      $time = time();
      $foto = 'Yes';
      $rutaDefoto = $uploadPath;

      $postGcMessage = mysqli_query($connect, "INSERT INTO `nuevochat_mensajes` (id_chat, author, toid, mensaje, time, foto, rutadefoto) VALUES ('$chatId', '$userId', '$toId', '$mensaje', '$time', '$foto', '$rutaDefoto')");

      $lastInsertId = mysqli_insert_id($connect);
      $query = $connect->query("SELECT *, nm.`id` AS id FROM `nuevochat_mensajes` AS nm INNER JOIN players AS p ON p.`id` = nm.`author` WHERE nm.`id` = '$lastInsertId'");

      if ($query && $query->num_rows > 0) {
        while ($messages = $query->fetch_assoc()) {
          $msg[] = $messages;
        }
        echo json_encode(['status' => true, 'msg' => $msg]);
      }
    } else {
      //http_response_code(500); // Internal Server Error
      echo json_encode(['status' => false, 'msg' => 'Error al mover el archivo.']);
    }
  } else {
    //http_response_code(400); // Bad Request
    echo json_encode(['status' => false, 'msg' => 'Tipo de archivo no compatible o tamaño excedido.']);
  }
}

// Manejo de la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $file = $_FILES["file1"];
  $urid = $_GET['urid'];
  $chatid = $_GET['chatid'];
  $toid = $_GET['toid'];

  handleFileUpload($file, $chatid, $urid, $toid);
} else {
  http_response_code(405); // Method Not Allowed
  echo json_encode(['error' => 'Método no permitido.']);
}
?>
