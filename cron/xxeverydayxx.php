<?php
include "../config.php";
//borrar todos los mensajes menos 5 de cada chat

$sqlmensajesnuevochat1 = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes`");
while ($rowmensaje = mysqli_fetch_assoc($sqlmensajesnuevochat1)) {	
	$chat_id = $rowmensaje['id_chat'];
	$sqlmensajesnuevochat = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE id_chat='{$chat_id}'");
	$countc = mysqli_num_rows($sqlmensajesnuevochat);
    if ($countc > 20) {
		//si es una foto borrar la foto
		if($rowmensaje['foto'] == 'Yes'){
			$Photo = $rowmensaje['rutadefoto'];
			$uploads = strpos("...{$Photo}", 'uploads/')>0;
			$fotosb = strpos("...{$Photo}", 'images/fotosb/')>0;
			if(!$uploads && !$fotosb){
				$ar = $rowmensaje['rutadefoto'];
				unlink('../'. $ar);
			}
		}
		//borrando el mensaje de la base de datos
		$borrarelmensaje = mysqli_query($connect, "DELETE FROM `nuevochat_mensajes` WHERE id='$rowmensaje[id]'");
    }
}