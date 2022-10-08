<?php
	//ACEPTA TODAS LAS SOLICITUDES DE AMISTAD PENDIENTES
	include "../config.php";
	$queryall = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE not_key='newAmistad'");
	while ($solicitud = mysqli_fetch_assoc($queryall)) {
		$amigoaceptadoall = $solicitud['fromid'];
		$player_id = $solicitud['toid'];
		$agregandoamigos     = mysqli_query($connect, "INSERT INTO `friends` (player1, player2) VALUES ('$player_id', '$amigoaceptadoall')");
		
		$borrarsolicitud    = mysqli_query($connect, "DELETE FROM `players_notifications` WHERE id='$solicitud[id]' AND not_key='newAmistad'");
		if ($amigoaceptadoall==true and $agregandoamigos==true) {
				//echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'profile.php" />';
			}	
	}
?>