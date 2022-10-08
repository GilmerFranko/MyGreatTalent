<?php

include "../config.php";

//cron de cada 10 min
//mensajes automaticos de bots

$sqlmensajesbot = mysqli_query($connect, "SELECT * FROM `respuestasbot_enespera`");
if(mysqli_num_rows($sqlmensajesbot)>0){
	while ($rowmensaje = mysqli_fetch_assoc($sqlmensajesbot)) {

	    if (time() >= $rowmensaje['respuesta_time']) {			
			$id_chat = $rowmensaje['chat_id'];
			$author  = $rowmensaje['bot_id'];
			$toid    = $rowmensaje['toid'];
			$mensaje = $rowmensaje['mensaje'];
			$time    = time();
			$foto    = 'Yes';
			$rutadefoto = $rowmensaje['foto'];
			$timeroom = time();

			// COMPRUEBA QUE EL CHAT ESTE ABIERTO(DESBLOQUEADO)
			if(checkStateChatRoom($id_chat)=='open')
			{
		    $responder = mysqli_query($connect, "INSERT INTO `nuevochat_mensajes` (id_chat, author, toid, mensaje, time, foto, rutadefoto) VALUES ('$id_chat', '$author', '$toid', '$mensaje', '$time', '$foto', '$rutadefoto')");
				//borrar la respuesta en espera
				$borrarrespuestaenespera = mysqli_query($connect, "DELETE FROM `respuestasbot_enespera` WHERE id='$rowmensaje[id]'");
				// indicandole a la sala que ya se le respondió al bot_id
				$updateroom = mysqli_query($connect, "UPDATE `nuevochat_rooms` SET mensaje_chatbot='1', time='$timeroom' WHERE id='$id_chat'");
			}
		}
	}
}

function sendMassMessage($data) {
	global $connect;

	// ALMACENA EL ID DE QUIEN ENVIARA EL MENSAJE
	$uid = $data['player_id'];

	// ALMACENA EL CONTENIDO DEL MENSAJE
	$mensaje = detectLink($data['message']);

	// SI ES UNA FOTO, ALMACENA LA RUTA
	$rutadefoto = $data['rutadefoto'];
	$foto = ($rutadefoto != '' && $rutadefoto) ? 'Yes':'No';

	// ALMACENA LA FECHA ACTUAL
	$time    = time();
	$timeroom = time();

	// SELECCIOA TODOS LOS CHATS INICIADOS POR $uid
	$sqlsala = $connect->query("SELECT * FROM `nuevochat_rooms` WHERE player1='{$uid}' OR player2='{$uid}'");
	//
	while ($sala = mysqli_fetch_assoc($sqlsala)) {

		// SELECCIONAR EL ID CONTRARIO A $uid
		if ($sala['player1'] == $uid){
			$toid = $sala['player2'];
		}else{
			$toid = $sala['player1'];
		}
		
		// OPTIENE EL USUARIO DESTINARIO
		$r = $connect->query("SELECT id, username, timeonline FROM `players` WHERE id = '$toid'");

		$recipient = ($r AND $r->num_rows > 0) ? $r->fetch_assoc() : false;

		$id_chat = $sala['id'];

		// COMPRUEBA QUE NO EXISTA OTRO MENSAJE IDENTICO (evita enviar mesajes repetidos)
		$exist = $connect->query("SELECT * FROM `nuevochat_mensajes` WHERE id_chat='{$id_chat}' AND author='{$uid}' AND toid='{$toid}' AND mensaje='{$mensaje}' AND foto='{$foto}' AND rutadefoto='{$rutadefoto}'");

		// SELECCIONA EL ULTIMO MENSAJE DEL CHAT
		$lastAnswer = $connect->query("SELECT * FROM `nuevochat_mensajes` WHERE id_chat='$id_chat' ORDER BY id DESC LIMIT 1");

		if ($lastAnswer)
		{

			$lA = ($lastAnswer->num_rows > 0) ? $lastAnswer->fetch_assoc() : false;

			// COMPRUEBA QUE EL CHAT ESTE ABIERTO(DESBLOQUEADO)
			if(checkStateChatRoom($id_chat) == 'open')
			{
				//
				if(true)
				{

					// SELECCIONAR TIPO. 1: ENVIAR A TODOS. 2: ENVIAR A USUARIOS CON INACTIVAD DE 5 O MAS DIAS. 3: ENVIAR SI EL ULTIMO MENSAJE ES DEL DESTINARIO.
					if (($data['type'] == 1 OR is_null($data['type'])) OR ($data['type'] == 2 AND $r AND TimeAgo($recipient['timeonline'],true) >= 5) OR ($data['type'] == 3 AND $lA != false AND $lA['author'] != $uid)) {

						// ENVIAR MENSAJE PROGRAMADO
						$addmensaje = mysqli_query($connect, "INSERT INTO `nuevochat_mensajes` (id_chat, author, toid, mensaje, time, foto, rutadefoto) VALUES
						('{$id_chat}', '{$uid}', '{$toid}', '{$mensaje}', '{$time}', '{$foto}', '{$rutadefoto}')");

						// ACUTALIZAR FECHA DEL CHAT
						$update_time_room = mysqli_query($connect, "UPDATE `nuevochat_rooms` SET time='$timeroom' WHERE id='$id_chat'");
					}
				}
			}
		}
	}
}

$ActualTime = time();

// SELECCIONA TODOS LOS MENSAJES PROGRAMADOS QUE ESTEN CADUCADOS
$querycp = mysqli_query($connect, "SELECT * FROM `mensajesprogramados` WHERE time<='{$ActualTime}'");

if($querycp && mysqli_num_rows($querycp)>0){
	//
	while($Mensaje = mysqli_fetch_assoc($querycp)){

		// ENVIA EL MENSAJE PROGRAMADO
		sendMassMessage($Mensaje);
		
		// BORRA EL MENSAJE PROGRAMADO DE LA LISTA
		mysqli_query($connect, "DELETE FROM `mensajesprogramados` WHERE id='". $Mensaje["id"] ."'");
	}
}
