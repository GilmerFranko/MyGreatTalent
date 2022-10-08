<?php
/**
 * BORRA TODOS LOS CHATS Y ROOMS DE USUARIOS QUE SE HAN BLOQUEADO (Entre ellos)
 */
include "../config.php";
// SELECCIONA TODOS LOS CHATS Y ROOMS DE USUARIOS QUE ESTEN BLOQUEADOS
$consult = $connect->query("SELECT b.*,nr.`id` AS nr_id, nm.`id_chat` AS nm_id_chat FROM `bloqueos` AS b INNER JOIN `nuevochat_rooms` AS nr ON (nr.`player1` = b.`fromid` AND nr.`player2` = b.`toid`) OR (nr.`player2` = b.`fromid` AND nr.`player1` = b.`toid`) LEFT JOIN `nuevochat_mensajes` AS nm ON nm.`id_chat` = nr.`id`");

if($consult AND $consult->num_rows > 0)
{
	while($chats = mysqli_fetch_assoc($consult)){
		// BORRA LOS ROOMS
 		$connect->query("DELETE FROM `nuevochat_rooms` WHERE `id` = '$chats[nr_id]'");
 		// BORRA LOS MENSAJES
 		$connect->query("DELETE FROM `nuevochat_mensajes` WHERE `id_chat` = '$chats[nm_id_chat]'");
	}

}
?>
