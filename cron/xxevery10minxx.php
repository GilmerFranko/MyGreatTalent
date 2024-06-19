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

	/*
   * Si es una foto, se le debe indicar
   * Al sistema que esta misma foto
   * Se usará para muchos mensajes
   */
	$foto_unica = ($rutadefoto != '' && $rutadefoto) ? 1:0;

	// ALMACENA LA FECHA ACTUAL
	$time    = time();
	$timeroom = time();

	/* Contiene el conjunto de consultas para insert */
	$sqlInsert = '';

	/* Contiene los ids de los chatsroom a actualizar el time */
	$idsChatRoom = [];

	/* Separador */
	$separator = '';

	/* Determina si hay que consultar el
	 * nombre del usuario receptor si existe
	 * en el mensaje la propiedad -user-*/
	$consultUserData = detect_user_String($mensaje);

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
		$exist = $connect->query("SELECT id FROM `nuevochat_mensajes` WHERE id_chat='{$id_chat}' AND author='{$uid}' AND toid='{$toid}' AND mensaje='{$mensaje}' AND foto='{$foto}' AND rutadefoto='{$rutadefoto}' AND `time` >= \"". (time() - (60 * 60)) ."\" ");

		// SELECCIONA EL ULTIMO MENSAJE DEL CHAT
		$lastAnswer = $connect->query("SELECT author FROM `nuevochat_mensajes` WHERE id_chat='$id_chat' ORDER BY id DESC LIMIT 1");

		if ($lastAnswer AND $exist->num_rows <= 1)
		{

			$lA = ($lastAnswer->num_rows > 0) ? $lastAnswer->fetch_assoc() : false;

			// COMPRUEBA QUE EL CHAT ESTE ABIERTO(DESBLOQUEADO)
			if(checkStateChatRoom($id_chat) == 'open')
			{

				// SELECCIONAR TIPO. 1: ENVIAR A TODOS. 2: ENVIAR A USUARIOS CON INACTIVAD DE 5 O MAS DIAS. 3: ENVIAR SI EL ULTIMO MENSAJE ES DEL DESTINARIO.
				if (($data['type'] == 1 OR is_null($data['type'])) OR ($data['type'] == 2 AND $r AND TimeAgo($recipient['timeonline'],true) >= 5) OR ($data['type'] == 3 AND $lA != false AND $lA['author'] != $uid)) {

					/* Transforma '-user-' al nombre del usuario al que se le envía el mensaje */
					$mensaje1 = detectUserString($mensaje, $recipient['username']);

					$sqlInsert .= $separator . "('{$id_chat}', '{$uid}', '{$toid}', '{$mensaje1}', '{$time}', '{$foto}', '{$foto_unica}', '{$rutadefoto}')";

					$idsChatRoom[] = $id_chat;

					$separator = ',';
				}
			}
		}
	}
	return [$sqlInsert, $idsChatRoom];
}

$ActualTime = time();
$insertMensaje = '';

$a = 0;
while($a < 100)
{
  // SELECCIONA UN MENSAJE PROGRAMADO QUE ESTE CADUCADO
	$querycp = mysqli_query($connect, "SELECT * FROM `mensajesprogramados` WHERE time<='{$ActualTime}' LIMIT 1");

	/* Si existe almenos uno; continuar con la ejecucion */
	if($querycp && mysqli_num_rows($querycp)>0)
	{

    /**
     * Borra el mensaje programado
     * Evita que al ejecutarse denuevo el cron
     * Se vuelva a enviar este mensaje
     */
    $Mensaje = mysqli_fetch_assoc($querycp);

    // BORRA EL MENSAJE PROGRAMADO DE LA LISTA
    mysqli_query($connect, "DELETE FROM `mensajesprogramados` WHERE id='". $Mensaje["id"] ."'");

		// ENVIA EL MENSAJE PROGRAMADO (Enviaba, ahora sencillamente genera las consultas sql para enviar los mensajes y las devuelve)
    $data = sendMassMessage($Mensaje);

    $connect->query("INSERT INTO `nuevochat_mensajes` (id_chat, author, toid, mensaje, time, foto, foto_unica, rutadefoto) VALUES $data[0]");

    actualizarRegistros($data[1]);

    $a++;
  }
  else
  {
  	break;
  }
}




/* Esta función se encarga de eliminar todas las compras que han alcanzado su fecha de vencimiento en los packs */
borrarComprasVencidas();
