<?php
include "../config.php";

/*
 * Elimina todos los mensajes de un chatroom y deja solo los 20 recientes
 */

// Obtener IDs de rooms con más de 20 mensajes
$roomsQuery = $connect->query("SELECT id FROM `nuevochat_rooms` LIMIT 40000, 99999999");

while ($rowRoom = $roomsQuery->fetch_assoc()) {
  $chatId = $rowRoom['id'];

    // Obtener IDs de mensajes que no están entre los 20 más recientes
  $messagesQuery = $connect->query("SELECT id, foto, rutadefoto FROM `nuevochat_mensajes` WHERE id_chat='$chatId' ORDER BY id DESC LIMIT 20,99999999");

  // Obtener todos los IDs de mensajes a eliminar
  $messagesToDelete = array();
  while ($rowMessage = $messagesQuery->fetch_assoc()) {
        // Si es una foto, borra la foto
    if ($rowMessage['foto'] == 'Yes' && !strpos($rowMessage['rutadefoto'], 'uploads/') && !strpos($rowMessage['rutadefoto'], 'images/fotosb/')) {
      @unlink('../' . $rowMessage['rutadefoto']);
    }

    $messagesToDelete[] = $rowMessage['id'];
  }

  // Eliminar todos los mensajes no deseados con una sola consulta
  if (!empty($messagesToDelete)) {
    $messageIds = implode(',', $messagesToDelete);
    $connect->query("DELETE FROM `nuevochat_mensajes` WHERE id IN ($messageIds)");
  }
}










//borrar todos los mensajes menos 5 de cada chat

// $sqlmensajesnuevochat1 = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes`");
// while ($rowmensaje = mysqli_fetch_assoc($sqlmensajesnuevochat1)) {
// 	$chat_id = $rowmensaje['id_chat'];
// 	$sqlmensajesnuevochat = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE id_chat='{$chat_id}'");
// 	$countc = mysqli_num_rows($sqlmensajesnuevochat);
//     if ($countc > 20) {
// 		//si es una foto borrar la foto
// 		if($rowmensaje['foto'] == 'Yes'){
// 			$Photo = $rowmensaje['rutadefoto'];
// 			$uploads = strpos("...{$Photo}", 'uploads/')>0;
// 			$fotosb = strpos("...{$Photo}", 'images/fotosb/')>0;
// 			if(!$uploads && !$fotosb){
// 				$ar = $rowmensaje['rutadefoto'];
// 				unlink('../'. $ar);
// 			}
// 		}
// 		//borrando el mensaje de la base de datos
// 		$borrarelmensaje = mysqli_query($connect, "DELETE FROM `nuevochat_mensajes` WHERE id='$rowmensaje[id]'");
//     }
// }
