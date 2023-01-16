<?php

function createThumbnail($src, $dest, $targetWidth, $targetHeight = null) {
	$type = strtolower(pathinfo($src, PATHINFO_EXTENSION));
	$src = file_get_contents($src);
	$image = @imagecreatefromstring($src);

	if (!$image) {
		Echo "Error abriendo imagen<br>";
		return null;
	}

	$width = imagesx($image);
	$height = imagesy($image);

	if ($targetHeight == null) {
		$ratio = $width / $height;
		if ($width > $height)
			$targetHeight = floor($targetWidth / $ratio);
		else {
			$targetHeight = $targetWidth;
			$targetWidth = floor($targetWidth * $ratio);
		}
	}

	$thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

	if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
		imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
		if ($type == IMAGETYPE_PNG) {
			imagealphablending($thumbnail, false);
			imagesavealpha($thumbnail, true);
		}
	}

	imagecopyresampled(
		$thumbnail,
		$image,
		0, 0, 0, 0,
		$targetWidth, $targetHeight,
		$width, $height
	);

	imagejpeg($thumbnail, $dest);
}

$priceForDownload = 500;
$crt1 = 'creditos';
$crt2 = 'eCreditos';

function getDomain($url){
	$pieces = parse_url($url);
	$domain = isset($pieces['host']) ? $pieces['host'] : '';
	if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
		return $regs['domain'];
	}
	return FALSE;
}

/**
 * [Convierte una URL en una cadena de caracteres a una etiquera <a>]
 * @param [string] $string [URL]
 */
function StrToUrl($string){
	preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match);
	foreach ($match[0] as $url) {
		$Domain = getDomain($url);
		$string = str_replace($url, '<a href="'.$url.'" class="linkD" target="_blank">'.$Domain.'</a>', $string);
	}
	return $string;
}


function view_Notification_encuesta() {
	global $connect, $rowu;

	if ($rowu['notificacion_encuestas'] > 0){
		mysqli_query($connect, "UPDATE `players` SET notificacion_encuestas=0 WHERE id='{$rowu['id']}'");
	}
}

function view_Notification_pack() {
	global $connect, $rowu;

	if ($rowu['notificacion_pack'] > 0){
		mysqli_query($connect, "UPDATE `players` SET notificacion_pack=0 WHERE id='{$rowu['id']}'");
	}
}

function get_Notification_encuesta($int=false) {
	global $rowu;

	if ($rowu['notificacion_encuestas'] > 0){
		return !$int ? '<span class="count-notify">'.$rowu['notificacion_encuestas'].'</span>':intval($rowu['notificacion_encuestas']);
	}
}

function get_Notification_pack($int=false) {
	global $rowu;

	if ($rowu['notificacion_pack'] > 0){
		return !$int ? '<span class="count-notify">'.$rowu['notificacion_pack'].'</span>':intval($rowu['notificacion_pack'])	;
	}
}

function Notificacion_encuesta() {
	global $connect, $rowu;

	//Enviando notificacion a amigos
	$sqlnotificandoamigos = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='{$rowu['id']}' OR player2='{$rowu['id']}'");
	while ($rowamigo = mysqli_fetch_assoc($sqlnotificandoamigos)) {
		if ($rowamigo['player2'] == $rowu['id']){
			$amigo = $rowamigo['player1'];
		}elseif ($rowamigo['player1'] == $rowu['id']){
			$amigo = $rowamigo['player2'];
		}
		$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE id='{$amigo}'");
		if($suser){
			$userFriend = mysqli_fetch_assoc($suser);

			$notificacion = $userFriend['notificacion_encuestas'] + 1;
			mysqli_query($connect, "UPDATE `players` SET notificacion_encuestas='{$notificacion}' WHERE id='{$amigo}'");
		}
	}
}

function Notificacion_pack($idPack = null, $onlyTo = null) {
	global $connect, $rowu;

	if ($onlyTo == null)
	{
		//Enviando notificacion a amigos
		$sqlnotificandoamigos = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='{$rowu['id']}' OR player2='{$rowu['id']}'");
		while ($rowamigo = mysqli_fetch_assoc($sqlnotificandoamigos)) {

			//EVITAR QUE SE ENVIE SOLICITUD AL MISMO USUARIO
			if ($rowamigo['player2'] == $rowu['id']){
				$amigo = $rowamigo['player1'];
			}elseif ($rowamigo['player1'] == $rowu['id']){
				$amigo = $rowamigo['player2'];
			}

			$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE id='{$amigo}'");

			//SUMARLE +1 NOTIFICACION DE PACK AL AMIGO
			if($suser){
				$userFriend = mysqli_fetch_assoc($suser);

				$notificacion = $userFriend['notificacion_pack'] + 1;
				mysqli_query($connect, "UPDATE `players` SET notificacion_pack='{$notificacion}' WHERE id='{$amigo}'");
			}

			// EVITAR ENVIARME LA NOTIFICACION
			if($rowu['id']!=$userFriend['id'])
			{
				// BORRAR NOTIFICACIONES ANTERIORES DE PACKS
				$deletePack = $connect->query("DELETE FROM `players_notifications` WHERE fromid='$rowu[id]' AND toid='$userFriend[id]'");
				// ENVIAR NOTIFICACION DIRECTA A AMIGOS
				$newPack = mysqli_query($connect, "INSERT INTO `players_notifications` (fromid, toid,not_key,action,read_time) VALUES ('$rowu[id]', '$userFriend[id]', 'newPack' , '$idPack' , '0' )");
			}
		}
	}
	else
	{
		$visibleTo = json_decode($onlyTo);
		foreach($visibleTo AS $nameUser)
		{
			$sql = $connect->query("SELECT `id`,`username` FROM `players` WHERE `username` = '$nameUser'");

			if($sql AND $sql->num_rows >= 0)
			{

				$userNOT = $sql->fetch_assoc();

				// EVITAR ENVIARME LA NOTIFICACION
				if($rowu['id'] != $userNOT['id'])
				{
					// BORRAR NOTIFICACIONES ANTERIORES DE PACKS
					$deletePack = $connect->query("DELETE FROM `players_notifications` WHERE fromid='$rowu[id]' AND toid='$userNOT[id]'");
					// ENVIAR NOTIFICACION DIRECTA A AMIGOS
					$newPack = mysqli_query($connect, "INSERT INTO `players_notifications` (fromid, toid,not_key,action,read_time) VALUES ('$rowu[id]', '$userNOT[id]', 'newPack' , '$idPack' , '0' )");
				}
			}
		}
	}
}


function getSourceType ($filename){
	$ext = pathinfo($filename, PATHINFO_EXTENSION);

	$type = 'camera';
	$videoExt = ['mp4'];

	if(in_array($ext, $videoExt)){
		$type = 'film';
	}

	return $type;
}

function getSource ($filename, $class, $href=false){
	$TempID = md5( $filename . time() );
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	$videoExt = ['mp4'];
	$imageExt = ['jpg', 'png', 'jpeg'];

	if(in_array($ext, $videoExt)){//is video
		return '<div class="VideoContainer" id="VC-'. $TempID .'">
		<video src="'. $filename .'" class="'. $class .' videoContainer" id="'. $TempID .'" style="width: 100%;" type="video/mp4" controls controlslist="nodownload"></video>
		<div class="videoPreview" onclick="">
		<i class="fa fa-play"></i>
		</div>
		<script>
		$(document).ready(() => {
			var elv = $("#VC-'. $TempID .'")
			elv.find(".videoPreview").click(() => {
				if(elv.hasClass("show")){
					elv.removeClass(`show`)
					elv.find(`video`).get(0).pause()
					getThumb("'. $TempID .'")
					}else{
						elv.addClass(`show`)
						elv.find(".videoPreview").attr("hidden","true")
						elv.find(`video`).get(0).play()
					}
					if(elv.find(`video`).get(0).false()){}
					})
					setTimeout(() => {
						getThumb("'. $TempID .'")
						}, 1500)
						})
						</script>
						</div>';
					}
	elseif(in_array($ext, $imageExt)){//is image
		if($class==" noSub"){
			$zoomb=" ";
		}else{
			$zoomb="item-zoom";
		}
		$Zoom = $href ? '':' '.$zoomb;
		$rtn = '';
		if($href){
			$rtn .= $class !== ' noSub' ? '<a href="'. $href .'">' : '';
		}
		$rtn .= '<img data-src="'. $filename .'" class="lozad'. $class . $Zoom .'" style="width: 100%;">';
		if($href){
			$rtn .= $class !== ' noSub' ? '</a>' : '';
		}
		return $rtn;
	}

	return $ext;
}

function strdr ($str){
	return str_replace(' ', '%20', $str);
}

function DelayMessages(){
	global $connect;

	$time    = time();
	$ActualTime = time();
	$querycp = mysqli_query($connect, "SELECT * FROM `respuesta_sala` WHERE time<='{$ActualTime}' AND publicado='no'");

	if($querycp && mysqli_num_rows($querycp)>0){
		while($Mensaje = mysqli_fetch_assoc($querycp)){
			mysqli_query($connect, "UPDATE `respuesta_sala` SET publicado='si' WHERE id='{$Mensaje['id']}'");

			$query = mysqli_query($connect, "SELECT * FROM `respuesta_automatica` WHERE id='{$Mensaje['pregunta_id']}' ORDER BY id DESC");
			$Respuesta = mysqli_fetch_assoc($query);
			$queryc = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE id = '{$Mensaje['chat_room']}'");
			$revision = mysqli_fetch_assoc($queryc);
			if ($revision['player1'] == $Respuesta['uid']){
				$toID = $revision['player2'];
			}else{
				$toID = $revision['player1'];
			}

			mysqli_query($connect, "INSERT INTO `nuevochat_mensajes` (id_chat, toid, author , mensaje, time) VALUES
				('{$Mensaje['chat_room']}', '{$toID}', '{$Respuesta['uid']}', '{$Respuesta["respuesta"]}', '{$time}')");
			}
		}
	}

	DelayMessages();

	function petImg($data) {
		$return = (object) [];
		$img = json_decode($data, true);
		if(!is_null($img)){
			$return->imgNormal = @$img['normal'];
			$return->imgFeed = @$img['feed'];
			$return->imgGame = @$img['game'];
			if(!isset($img['lifelow'])){
				$img['lifelow'] = $img['normal'];
			}
			$return->lifelow = $img['lifelow'];
		}else{
			$return->imgNormal = $data;
			$return->imgFeed = $data;
			$return->imgGame = $data;
			$return->lifelow = $data;
		}
		return $return;
	}

	function EarchLifePets($uid){
		global $connect;
		$pets = $connect->query("SELECT * FROM `player_pets` WHERE player_id='{$uid}' AND live=1");
		if($pets && mysqli_num_rows($pets)>0){
			while($pet = mysqli_fetch_assoc($pets)){
				updateHE($pet);
			}
		}
	}

	function EarchBonusPets($uid){
		global $connect;
		$pets = $connect->query("SELECT * FROM `player_pets` WHERE player_id='{$uid}' AND live=1");
		if($pets && mysqli_num_rows($pets)){
			while($pet = mysqli_fetch_assoc($pets)){
				UpdateBonus($pet);
			}
		}
	}

	function UpdateBonus($pet){
		global $connect;
	$delay = 60*60*24*5; // cada cuanto te regala creditos tu mascota aqui 5 dias

	$pet_updated = time() - $pet['update_bonus'];
	$bonus = rand(2, 5); // cuanto creditos te regala la mascota
	if(is_null($pet['update_bonus']) || $pet_updated >= $delay){
		$pet['update_bonus'] = $pet['update_bonus'] == 0 ? time() - $delay: $pet['update_bonus'];
		$bonus = intval( (time() - $pet['update_bonus'])/ $delay ) * $bonus;
		$connect->query("UPDATE `player_pets` SET bonus=bonus+{$bonus}, update_bonus='". time() ."' WHERE id='{$pet['id']}' AND live=1");
	}
}

function updateHE($pet) {
	global $connect;
	$delay = 60*60*8; // cada cuanto tiempo tu mascota pierde vida aqui cada 2 horas
	$descv = 4; // % a descontar de vida cada mascota

	//$pet = $connect->query("SELECT * FROM `player_pets` WHERE id='{$pet_id}' AND live=1");
	//$pet = mysqli_fetch_assoc($pet);

	$petsp = $connect->query("SELECT * FROM `pets` WHERE id='{$pet["pet_id"]}'");
	$petsp = mysqli_fetch_assoc($petsp);

	$pet_updated = time() - $pet['updated'];
	if(is_null($pet['updated']) || $pet_updated >= $delay || $pet['hp']<=0 || $pet['energy']<=0){
		$perUp = intval( (time() - $pet['updated'])/ $delay );
		$NewHP = $perUp * porcentaje($petsp['hp'], $descv, 2);
		$NewEG = $perUp * porcentaje($petsp['energy'], $descv, 2);
		update_pet_HE($pet["id"], [
			'type' => 0,
			'hp' => $NewHP,
			'last_hp' => $pet['hp'],
			'base_hp' => $petsp['hp'],
			'energy' => $NewEG,
			'last_energy' => $pet['energy'],
			'base_energy' => $petsp['energy']
		]);
	}
}

function update_pet_HE($pet_id, $data) {
	global $connect;

	if($data['type'] == 1){
		$hp = $data['last_hp'] + $data['hp'];
		$energy = $data['last_energy'] + $data['energy'];
	}else{
		$hp = $data['last_hp'] - $data['hp'];
		$energy = $data['last_energy'] - $data['energy'];
	}
	if($hp > $data['base_hp']){
		$hp = $data['base_hp'];
	}
	if($hp <= 0){
		$hp = 0;
	}

	if($energy > $data['base_energy']){
		$energy = $data['base_energy'];
	}
	if($energy <= 0){
		$energy = 0;
	}

	$live = 1;
	if($hp == 0){
		$live = 0;
	}

	$connect->query("UPDATE `player_pets` SET hp={$hp}, energy={$energy}, live={$live}, updated='". time() ."' WHERE id='{$pet_id}' AND live=1");
}
function UpdateUserOnli($uid){
	global $connect;
	$time = time() + (60 * 35);
	$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE id='{$uid}' AND gender='mujer'");
	if($suser && mysqli_num_rows($suser)){
		mysqli_query($connect, "UPDATE `players` SET timeonline={$time} WHERE id='{$uid}'");
	}
}

function porcentaje($total, $int, $mode=1){
	if($mode==1){
		$int = (100 * $int) / $total;
	}else{
		$int = ($total * $int) / 100;
	}

	if($int>100){
		$int = 100;
	}
	$int = (string) $int;
	$val = $int[0].@$int[1].@$int[2];
	if(@$val[2] == '.'){
		$val = $val[0].@$val[1];
	}
	return $val;
}

function isFollow($tid) {
	global $connect, $rowu;
	$User = (object) $rowu;

	$FollowList = [];
	if(!is_null($User->follower)){
		$FollowList = json_decode($User->follower, true);
	}

	if (isset($FollowList[$tid])) {
		return true;
	}else{
		return false;
	}
}

function AddListFollow($tid){
	global $connect, $rowu;

	$User = (object) $rowu;

	// NO SUSCRIBIRME A MI MISMO
	if($User->id && $tid!=$User->id){
		$FollowList = [];
		$FollowersList = [];
		$toUser = $connect->query("SELECT * FROM `players` WHERE id='{$tid}'");
		$toUser = mysqli_fetch_assoc($toUser);
		if(!$toUser){
			return [
				'error' => 'User-Not-Exist'
			];
		}
		$toUser = (object) $toUser;
		if(!is_null($toUser->followers)){
			$FollowersList = json_decode($toUser->followers, true);
		}
		if(!is_null($User->follower)){
			$FollowList = json_decode($User->follower, true);
		}

		$Follow = true;
		if (!isset($FollowersList[$User->id])) {
			$FollowersList[ $User->id ] = time() + ( 60*60*24*7 );
			$FollowList[ $toUser->id ] = time() + ( 60*60*24*7 );
		}
		// ACTUALIZAR CRÉDITOS
		//updateCredits($User->id,'-',2000,2);

		// AGREGAR SUBSCRIPCIÓN
		$connect->query("UPDATE `players` SET follower='". json_encode($FollowList, true) ."' WHERE id='{$User->id}'"); // valor de la suscripcion

		// ACTUALIZAR CREDITOS
		//updateCredits($toUser->id,'+',1200,2);

		// AGREGAR SUBSCRIPCIÓN
		$connect->query("UPDATE `players` SET followers='". json_encode($FollowersList, true) ."' WHERE id='{$toUser->id}'"); // cuanto gana quien te sucribes

		//quitar suscripcionvencida de la lista
		$querydelete = mysqli_query($connect, "UPDATE `notificaciones_suscripcionesvencidas` SET see=1 WHERE usera='$rowu[id]' and userb='$tid'");

		// ENVIAR NOTIFICACION AL USUARIO
		//$newSubscription = mysqli_query($connect, "INSERT INTO `players_notifications` (fromid, toid,not_key,action,read_time) VALUES ('$rowu[id]', '$tid', 'newSubscription' , '' , '0' )");

		$return['FollowCount'] = count($FollowList);
		$return['isFollow'] = $Follow;

		return $return;
	}else{
		return [
			'error' => 'Invalid-Action'
		];
	}

}

function get_client_ip_server() {
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP'])){
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	elseif(isset($_SERVER['HTTP_X_FORWARDED'])){
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	}
	elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])){
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	}
	elseif(isset($_SERVER['HTTP_FORWARDED'])){
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	}
	elseif(isset($_SERVER['REMOTE_ADDR'])){
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	}
	else{
		$ipaddress = 'UNKNOWN';
	}

	return $ipaddress;
}

function TimeAgo($date, $min=false) {
	if(0==$date){
		return 'nunca';
	}

	$strTime = ["segundo", "minuto", "hora", "dia", "mes", "año"];
	$length = ["60","60","24","30","12","10"];

	$currentTime = time();

	if($currentTime >= $date) {
		$diff = time()- $date;
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
			$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		$mores = '';
		if($diff>1){
			$mores = "s";
		}
		if("segundo"==$strTime[$i]){
			if(!$min){
				return 'hace un momento';
			}else {
				return 'Ahora';
			}
		}
		if(!$min){
			$msg = "Hace " . $diff;
		}

		return (!$min) ? $msg . " " . $strTime[$i] . $mores : intval($diff);
	}
}

function TimeNext($date) {
	if(0==$date){
		return null;
	}

	$strTime = ["segundo", "minuto", "hora", "dia", "mes", "año"];
	$length = ["60","60","24","30","12","10"];

	$currentTime = time();

	if($currentTime <= $date) {
		$diff = $date - time();
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
			$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		$mores = '';
		if($diff>1){
			$mores = "s";
		}
		if("segundo"==$strTime[$i]){
			return 'Unos segundos';
		}

		return $diff . " " . $strTime[$i] . $mores;
	} else {
		return null;
	}
}
function TimeNextHours($date) {
	if(0==$date){
		return null;
	}

	$strTime = ["segundo", "minuto", "hora", "dia"];
	$length = ["60","60","24","30"];

	$currentTime = time();

	if($currentTime <= $date) {
		$diff = $date - time();
		for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
			$diff = $diff / $length[$i];
		}

		$diff = round($diff);
		$mores = '';
		if($diff>1){
			$mores = "s";
		}
		if("segundo"==$strTime[$i]){
			return 'Unos segundos';
		}
		if ($strTime[$i]!="hora" and $strTime[$i]!="minuto" and $strTime[$i]!="segundo"){
			return $diff;
		}else{
			return null;
		}
	} else {
		return null;
	}
}
function filtrourl($valor){
	if(trim($valor) == ''){
		echo ' ';
		return " ";
	}else{
		if (!filter_var($valor, FILTER_VALIDATE_URL)) {
			echo ' ';
			return "error";
		}
		if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|](\.)[a-z]{2}/i",$valor)) {
			echo ' ';
			return "error";
		}else{
			echo ' ';
			return $valor;
		}
	}
}
function generaterandom($num1=0, $num2=0){
	$rand = array();

	//Se crea el primer número aleatorio
	$rand1 = mt_rand($num1,$num2);
	//Se inserta
	array_push($rand, $rand1);

	//Se crea variable para iterar
	$x = 0;
	$val=$num2-$num1;
	$val=$val-1;
	while ($x <= $val) {
		$rand2 = mt_rand($num1, $num2);
		if(in_array($rand2, $rand)){
			continue;
		}else{
			array_push($rand, $rand2);
			$x++;
		}
	}
	return $rand;
}
function unlinkJSON($json = null){
	foreach($json as $key => $jsn){
		unlink($jsn);
	}
}
function arrayONarray($consult){
//ALMACENA FILAS Y COLUMNAS DE LA CONSULTA EN UN ARRAY
	$a=0;
	while($arrow = mysqli_fetch_row($consult)) {
		for ($i=0; $i < 6; $i++) {
			$row[$a][$i]=$arrow[$i];
		}
		$a++;
	}
	return $row;
}
/**
* comprueba si una foto tiene creditos para regalar
*
* @param int (id de la foto)
* @param int (tipo de regalo)
* @return int
*/
function givecredits($idfoto = null, $type = 1){
	global $connect, $rowu;

	if($type == 1)
	{
		$consult = mysqli_query($connect, "SELECT * FROM `giftcredits` WHERE used=0");
		if ($consult) {
			if(!empty($consult) AND mysqli_num_rows($consult) >0) {
				$row = mysqli_fetch_assoc($consult);

				//VERIFICA SI LA FOTO TIENE REGALO
				if($row['foto_id'] == $idfoto)
				{
					$json = json_decode($row['given']);

					//VERIFICA SI EL USUARIO NO HA RECIVIDO EL REGALO
					if (!in_array($rowu['id'], $json)) {

						//AGREGA AL USUARIO A LA LISTA
						array_push($json, $rowu['id']);

						$jsoncode = json_encode($json);

						$consult = mysqli_query($connect, "UPDATE `giftcredits` SET `given`='$jsoncode' WHERE id='$row[id]'");

						if ($consult)
						{

							$credits="eCreditos";
							$numrand = rand(0,1);
							$gift=10;
							if ($numrand==0)
							{
								// SI SE REGALARAN CREDITOS ESPECIALES
								$credits="eCreditos";
								$gift=5;

								//TEXTO ALEATORIO
								$txtrand = rand(0,2);
								switch ($txtrand) {
									case 0:
									$swal = array('Felicidades!','Aquí encontraste +5 Créditos Especiales que estaban perdidos y ahora son tuyos. <br> <img src=\'https://bellasgram.com/chat/assets/img/especiales.png\'>','');
									break;
									case 1:
									$swal = array('Increíble!','Encontraste +5 Créditos Especiales y ahora son todos tuyos. <br> <img src=\'https://bellasgram.com/chat/assets/img/especiales.png\'>','');
									break;
									case 2:
									$swal = array('Fantástico!','Has encontrado +5 Créditos Especiales y ahora son tuyos. <br> <img src=\'https://bellasgram.com/chat/assets/img/especiales.png\'>','');
									break;
								}

								// ACTUALIZA CREDITOS Y REGISTRA
								$consult = updateCredits($rowu['id'],'+',$gift,11);

							}
							else
							{
								// SI SE REGALARAN CREDITOS NORMALES
								$credits="creditos";
								$gift=10;

								//TEXTO ALEATORIO
								$txtrand = rand(0,2);
								switch ($txtrand) {
									case 0:
									$swal = array('Genial!','Encontraste +10 Creditos Normales, Quizá tengas mejor suerte mañana y encuentres créditos especiales <br> <img src=\'https://bellasgram.com/chat/assets/img/normales.png\'>','');
									break;
									case 1:
									$swal = array('Felicidades!','Encontraste +10 Creditos Normales que estaban perdidos y ahora son tuyos, Quizá tengas mejor suerte mañana y encuentres créditos especiales <br> <img src=\'https://bellasgram.com/chat/assets/img/normales.png\'>','');
									break;
									case 2:
									$swal = array('Felicidades!','Encontraste +10 Creditos Normales, mañana Quizá encuentres créditos especiales <br> <img src=\'https://bellasgram.com/chat/assets/img/normales.png\'>','');
									break;
								}

								// ACTUALIZA CREDITOS
								$consult = mysqli_query($connect, "UPDATE `players` SET `$credits`=`$credits`+$gift WHERE id='$rowu[id]'");
							}

							if ($consult) {
								setSwalFire($swal);
								return $credits;
							}
						}
					}
				}
			}
		}
	}
	// REGALAR CREDITOS SI LA FOTO ES LA ULTIMA Y FUE SUBIDA HACE MAS DE 30MIN
	elseif($type == 2)
	{
		// SELECCIONA SI HAY UNA FOTO REGISTRADA CON UN REGALO
		$sql = mysqli_query($connect, "SELECT * FROM `photo_gift_credits`");
		// SI EXISTE
		if($sql AND $sql->num_rows >0)
		{
			$row = $sql->fetch_assoc();

			// ALMACENA LOS ID DE TODOS LOS USUARIOS QUE YA SE LE HAN REGALADO CREDITOS DE ESTA IMAGEN
			$json = json_decode($row['given']);

			//VERIFICA SI NO HE RECIVIDO EL REGALO
			if (!in_array($rowu['id'], $json)) {

				// AGREGA MI ID A LA LISTA
				array_push($json, $rowu['id']);

				// CODIFICA EL JSON
				$jsoncode = json_encode($json);

				// ACTUALIZA LOS ID
				$consult = $connect->query("UPDATE `photo_gift_credits` SET `given`='$jsoncode' WHERE id='$row[id]'");

				if($consult)
				{
					$numrand = rand(0,2);
					$gift=5;
					// ACTUALIZA CREDITOS
					$consult = $connect->query("UPDATE `players` SET `creditos`=`creditos`+$gift WHERE id='$rowu[id]'");
					if ($consult)
					{
						switch ($numrand) {
							case 0:
							$swal = 'Que suerte! encontraste +5 Créditos Normales';
							break;
							case 1:
							$swal = 'La suerte esta contigo, encontraste +5 Créditos Normales';
							break;
							case 2:
							$swal = 'Waoo! +5 Créditos Normales para tí!';
							break;
						}
						setSwal(array($swal,'','success'));
					}
				}
			}
		}
	}
}

//DEVUELVE SI LA POSICION DE UN ITEM ES IGUAL
function equalcoordinate($consult, $x,$y,$farm_id)
{
	global $connect, $rowu;


	//RECORRO LOS ITEMS DEL USUARIO
	while($row_items_player=mysqli_fetch_assoc($consult) )
	{

		//BUSTO EN LA BBDD SI EL ITEM ES IGUAL A LA POSICION PASADA
		$consult2 = mysqli_query($connect, "SELECT * FROM `farm_items` WHERE id='$row_items_player[items_id]' AND farms_id='$farm_id' AND position_x='$x' AND position_y='$y'");

		if(mysqli_num_rows($consult2)>0)
		{

			$obj=mysqli_fetch_assoc($consult2);

			//VERIFICO SI EL ITEM YA SE PUEDE COSECHAR
			if ($row_items_player['time']<time() AND $obj['produces']>0)
			{
				//DEVUELVO EL ITEM CON EL BOTON DE COSECHAR
				return "<div class='object'><img class='img-item' src='$obj[image]' style='width:$obj[size]'></img><br><a href='worlds.php?give=$row_items_player[id]&farm_id=$farm_id' class='btn btn-success' style='position:absolute;top:0;left:-80px;'><i class='fa fa-info'></i> RECOJER COSECHA</a></div>";
			}

			//SINO
			else
			{
				//SOLO DEVUELVO ITEM
				return "<div class='object'><img class='img-item' src='$obj[image]' style='width:$obj[size]'></img></div>";
			}
		}
	}
		//Devuelvo al array de la consulta a la posición de partida para volverlo a recorrer
	mysqli_data_seek($consult,0);
}

#-> COMPRA UN ITEM CON CRÉDITOS ESPECIALES
function buy_item($id_item,$id){
	global $connect, $rowu;

	$User = (object) $rowu;
	$players_items = $connect->query("SELECT * FROM `players_farm_items` WHERE player_id='$User->id' AND items_id='$id_item'");
	$items = $connect->query("SELECT * FROM `farm_items` WHERE id='$id_item'");
	$time=time() + 12*60*60;

	if ($items AND mysqli_num_rows($items)>0) {
		$rowitem=mysqli_fetch_assoc($items);
	}
	else
	{
		echo "<script>swal.fire('El item no existe!','','error');</script>";
		exit;
	}
	if ($rowitem['price']==0 or $rowitem['price']==null)
	{
		$time=0;
	}

	$players_farms = $connect->query("SELECT * FROM `players_farms` WHERE player_id='$User->id' AND farms_id='$rowitem[farms_id]'");

	#-> SI EL USUARIO DISPONE DEL MUNDO PARA ALMACENR EL ITEM
	// AND mysqli_num_rows($players_farms)>0
	if ($players_farms)
	{

		#-> SI EL USUARIO NO DISPONE ECREDITOS O CREDITOS SUFIENTES
		if ($User->creditos>=$rowitem['price'] OR $User->eCreditos>=$rowitem['price'])
		{

			#-> SI NO POSEE EL ITEM
			if ($players_items AND !mysqli_num_rows($players_items)>0)
			{

				#-> AGREGAR EL ITEM
				$insertitem = $connect->query("INSERT INTO players_farm_items (items_id, player_id, time) VALUES ('$id_item','$User->id','$time')");

				if ($insertitem)
				{

					#->DECIDIR CON QUE SE PAGARA

					// CON CRÉDITOS NORMALES
					if ($User->creditos>=$rowitem['price'])
					{
						$pay = "creditos";
						$connect->query("UPDATE players SET $pay=$pay-$rowitem[price] WHERE id='$rowu[id]'");
					}

					// CON CRÉDITOS ESPECIALES
					elseif($User->eCreditos>=$rowitem['price'])
					{
						// ACTUALIZAR CREDITOS
						updateCredits($User->id,'-',$rowitem['price'],7);
					}

					#-> COMPRUEBA SI YA ADQUIRI TODOS LOS ITEMS DE UN MUNDO
					$itemscounts = $connect->query("SELECT * FROM `farm_items` WHERE farms_id='$rowitem[farms_id]'")->num_rows;

					$sql_items = $connect->query("SELECT * FROM `farm_items` WHERE farms_id='$rowitem[farms_id]'");

					$all_items_players = $connect->query("SELECT * FROM `players_farm_items` INNER JOIN farm_items ON farm_items.id=players_farm_items.items_id WHERE player_id='$rowu[id]'");

					$counts=0;
					while ($all_items=mysqli_fetch_assoc($all_items_players)) {
						if ($all_items['farms_id'] == $rowitem['farms_id']) {
							$counts++;
						}
					}
					// SI LOS ADQUIRI TODOS
					if ($counts==$itemscounts) {
						// ACTUALIZAR CRÉDITOS
						updateCredits($User->id,'+',100,8);

						echo '<script>
						swal.fire({title: "Felicidades!",text: "Obtuviste todos los items de este Mundo. Ganaste 100 Creditos especiales!",type: "success",showConfirmButton: false}).then((name) => { window.location.href = "items.php?world_id='.$id.'"; });</script>';
					}else
					{
						echo "<script>swal.fire('Item adquirido!','','$rowitem[image]');setTimeout(function(){ window.location.href='items.php?world_id=".$id."'; }, 2000);</script>";
					}
				} else {
					echo "<script>swal.fire('ERROR','','false');</script>";
				}
			}
			else
			{
				echo "<script>swal.fire('Ya Posees este Item','','warning');</script>";
			}

		}
		else
		{
			echo "<script>swal.fire('No dispones de sufiente Cash','','error');</script>";
		}
	}
	else
	{
		echo "<script>swal.fire('No dispones de un Mundo para este Item','','error');</script>";
	}

}
#-> COMPRA UN ITEM CON PUNTOS
function buy_player_item($id_item){
	global $connect, $rowu;

	$User = (object) $rowu;
	$players_items = $connect->query("SELECT * FROM `player_items_bought` WHERE player_id='$User->id' AND item_id='$id_item'");
	$items = $connect->query("SELECT * FROM `player_items` WHERE id='$id_item'");
	$time=time() + 24*60*60;

	if ($items AND mysqli_num_rows($items)>0) {
		$rowitem=mysqli_fetch_assoc($items);
	} else {
		echo "<script>swal.fire('El item no existe!','','error');</script>";
		exit;
	}



	//SI EL USUARIO NO DISPONE DE PUTNOS SUFICIENTES
	if ($User->puntos>=$rowitem['price'])
	{

		//SI NO POSEE EL ITEM
		if ($players_items AND !mysqli_num_rows($players_items)>0)
		{

			//AGREGAR EL ITEM
			$insertitem = $connect->query("INSERT INTO player_items_bought (item_id, player_id, time) VALUES ('$id_item','$User->id','0')");

			if ($insertitem) {

				//PAGAR
				$connect->query("UPDATE players SET `puntos`=`puntos`-$rowitem[price] WHERE id='$rowu[id]'");

				//echo "<script>swal.fire('Item adquirido!','','$rowitem[image]');setTimeout(function(){ window.location.href='tiendapuntos.php'; }, 2000);</script>";

				#-> DETECTA SI EL USUARIO YA OBTUVO TODASS LAS DRAGONBALL
				if($rowitem['type']=="dragonball")
				{

					#-> SELECIONA TODAS LAS ESFERAS QUE HAY //DEFAULT 7
					$itemscounts = $connect->query("SELECT * FROM `player_items` WHERE type='dragonball'")->num_rows;
					$sql_items = $connect->query("SELECT * FROM `player_items` WHERE type='dragonball'");
					$all_items_players = $connect->query("SELECT * FROM `player_items_bought` INNER JOIN player_items ON player_items.id=player_items_bought.item_id WHERE player_id='$rowu[id]'");

					$counts=0;
					while ($all_items=mysqli_fetch_assoc($all_items_players))
					{
						if ($all_items['type'] == $rowitem['type'])
						{
							$counts++;
						}
					}
					if ($counts==$itemscounts)
					{
						//$connect->query("UPDATE players SET eCreditos=eCreditos+100 WHERE id='$rowu[id]'");

						echo "<script>
						var dHref = $(this).data('href');
						var cash = $(this).data('cash');
						swal.fire({
							title: 'Felicidades! obtuviste todas las Esferas del Dragon, Elije cual premio quieres canjear',
							buttons: ['2500 Creditos especiales', '5000 Creditos Normales'],
							icon: 'https://w7.pngwing.com/pngs/658/548/png-transparent-shenron-goku-japanese-dragon-dragon-ball-fighterz-goku-dragon-cartoon-fictional-character-thumbnail.png',
							iconsize: '30x30'

							})
							.then((name) => {
								if(name.isConfirmed){

									window.location.href = 'tiendapuntos.php?canjear=5000';

								}
								else
								{
									window.location.href = 'tiendapuntos.php?canjear=2500';
								}
								});</script>
								<style>
								.swal-button--cancel{
									background-color: #7cd1f9 !important;
									color: #fff !important;
								}
								</style>";
							}
							else
							{
								echo "<script>swal.fire('Item adquirido!','$rowitem[name]','$rowitem[image]');setTimeout(function(){ window.location.href='tiendapuntos.php'; }, 2000);</script>";
							}
						}
						else
						{
							echo "<script>swal.fire('Item adquirido!','$rowitem[name]','$rowitem[image]');setTimeout(function(){ window.location.href='tiendapuntos.php'; }, 2000);</script>";
						}
					}
					else
					{
						echo "<script>swal.fire('ERROR','','false');</script>";
					}
				}
				else
				{
					echo "<script>swal.fire('Ya Posees este Item','','warning');</script>";
				}

			}
			else
			{
				echo "<script>swal.fire('No dispones de sufientes Puntos para comprar este item','','error');</script>";
			}

		}

#-> DEVUELVE LAS MEDALLAS PARA UBICARLAS EN EL PERFIL
		function get_medallas($id=null){
			global $connect, $rowu;

			$items = mysqli_query($connect, "SELECT * FROM `player_items` where type='medalla'");
			while($rowitems=mysqli_fetch_assoc($items))
			{
		#-> OBTENER ITEMS COMPRADOS
				$item_bought = mysqli_query($connect, "SELECT * FROM `player_items_bought` WHERE item_id='$rowitems[id]' AND player_id='$id'");

				if ($item_bought AND mysqli_num_rows($item_bought)>0)
				{
					switch ($rowitems['id']) {
						case '1':
						echo '<div style="position: absolute; left: -12px;bottom: -5px;"><img src="'.$rowitems['image'].'"></div>';
						break;
						case '2':
						echo '<div style="position: absolute; left: 170px;bottom: 100px;"><img src="'.$rowitems['image'].'"></div>';
						break;
						case '3':
						echo '<div style="position: absolute; left: -35px;bottom: 100px;"><img src="'.$rowitems['image'].'"></div>';
						break;
						case '4':
						echo '<div style="position: absolute; left: 71px;bottom: -36px;"><img src="'.$rowitems['image'].'"></div>';
						break;
						case '5':
						echo '<div style="position: absolute;left: 75%;bottom: -5px;"><img src="'.$rowitems['image'].'"></div>';
						break;
						default:
					# code...
						break;
					}
				}
			}
		}
#-> VERIFICA SI YA TIENE TODAS LAS DRAGONBALLS
		function verify_alldragonballs(){
			global $connect, $rowu, $sitio;
			$itemscounts = $connect->query("SELECT * FROM `player_items` WHERE type='dragonball'")->num_rows;
			$sql_items = $connect->query("SELECT * FROM `player_items` WHERE type='dragonball'");
			$all_items_players = $connect->query("SELECT * FROM `player_items_bought` INNER JOIN player_items ON player_items.id=player_items_bought.item_id WHERE player_id='$rowu[id]'");
			$counts=0;

	#-> VERIFICAR SI EL USUARIO TIENE TODAS LAS DRAGONBALL
			while ($all_items=mysqli_fetch_assoc($all_items_players))
			{
				if ($all_items['type'] == "dragonball")
				{
					$counts++;
				}
			}

	#-> SI LAS TIENE TODAS
			if ($counts==$itemscounts)
			{
				echo "<img src='https://bellasgram.com/ciudad/images/esfera4.png' width='30px'><span>Tienes todas las Esferas del Dragon, Canjealas aqui por Creditos, <a href='".$sitio['site']."tiendapuntos.php?canjear=2500'>2500 Creditos Especiales</a>, <a href='".$sitio['site']."tiendapuntos.php?canjear=5000'>5000 Creditos Normales</a><img src='https://bellasgram.com/ciudad/images/esfera4.png' width='30px'>";
			}
		}
#-> DEVUELVE EL DISPOSITIVO CON EL QUE NAVEGA
		function its_in(){

	// SI ESTA EN PC
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] = 'com.bellas.gram.app.android')
			{
				return "in_android";
			}
	// SI ESTA EN ANDROID
			elseif(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android') !== false)
			{
				return 'mobile';
			}
	// SI ESTA EN LA APP DE BELLASGRAM
			else
			{
				return "in_pc";
			}
		}

#-> DEVUELVE LA CANTIDAD DE ITEMS DE UN USUARIO EN UN MUNDO
		function get_num_items_player($consult,$farm_id){
			global $rowu,$connect;

			$item=mysqli_fetch_assoc($consult);
			$num_items = mysqli_query($connect, "SELECT * FROM `players_farm_items` WHERE player_id='$rowu[id]'");

			if ($num_items AND mysqli_num_rows($num_items)>0) {

				$num_items_player = mysqli_query($connect, "SELECT *, players_farm_items.id AS pfi_id FROM `players_farm_items` INNER JOIN farm_items ON farm_items.id=players_farm_items.items_id where players_farm_items.player_id='$rowu[id]' AND farm_items.farms_id='$farm_id'")->num_rows;

				return $num_items_player;

			}
		}

#-> ACTUALIZA LECTURA DE NOTIFCACIONES
		function view_notifications()
		{
			global $connect, $rowu;
			$time=time();
			mysqli_query($connect, "UPDATE `players_notifications` SET read_time='$time' WHERE read_time='0' AND toid='{$rowu['id']}'");
		}


		function islookContentUser($from, $to)
		{
			global $connect;
			if($from == $to){
				return true;
			}
			$Look = $connect->query("SELECT * FROM `bloqueos` WHERE fromid='{$from}' AND toid='{$to}'");
			$Look02 = $connect->query("SELECT * FROM `bloqueos` WHERE fromid='{$to}' AND toid='{$from}'");
			if($Look->num_rows or $Look02->num_rows){
				return false;
			}
			return true;
		}

/**
 * devuelve horas a segundos
 **/
function SecondsToHours($seconds){
	$minutos = floor($seconds / 60);
	$seconds = $seconds - ($minutos * 60);
	$sSeconds =$seconds==0 ? '00' : strval($seconds);
	echo $minutos . ":" . $sSeconds."";

}
/**
 * Crea un Link de etiqueta <a>
 * @params
 * $Linkto //Es al archivo en donde se redigira el link
 * $in_a //Texto dentro de <a>
 * $params //Parametros Get
 **/
function createLink($linkTo = null,$in_a = '',$params = null,$link = false)
{
	switch ($linkTo) {
		case 'profile':
		$url = 'profile.php';
		break;
		case 'nombres':
		$url = 'nombres.php';
		break;
		case 'packs':
		$url = 'packs.php';
		break;
		case 'pack':
		$url = 'pack.php';
		break;
		case 'notifications':
		$url = 'notifications.php';
		break;
		case 'transacciones':
		$url = 'transacciones.php';
		break;
		case 'xxevery10minxx':
		$url = 'cron/xxevery10minxx.php';
		break;
		case 'chat':
		$url = 'chat.php';
		break;
		case 'adminreportes':
		$url = 'adminreportes.php';
		break;
		case 'bloqueados':
		$url = 'bloqueados.php';
		break;
		default:
		$url = 'galerias.php';
		break;
	}
	if(isset($params) && is_array($params))
	{
		$count = 0;
		foreach($params as $key => $val)
		{
			$url .= $count == 0 ? '?' . $key . '=' . $val : '&' . $key . '=' . $val;
			$count++;
		}
	}

	return $link==false ? '<a href="'.$url.'">'.$in_a.'</a>' : $url;
}

// DEVUELVE UN ID DE UNA FOTO DE GALERIA DE FORMA ALEATORIA
function getOneGaleryID()
{
	global $connect,$rowu,$prefer;
	$player_id = $rowu['id'];
	$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE category='$prefer' ORDER BY RAND() LIMIT 0,10");

	while ($rowcp = mysqli_fetch_assoc($querycp))
	{
		$author_id = $rowcp['player_id'];
		$uname = $rowu['username'];
		$querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
		$rowcpd    = mysqli_fetch_assoc($querycpd);
		$iamfrom = ($rowu['registerfrom'] == 'chat' ? 'chat' : "bellasgram");
      //SI EL USUARIO TIENE EL PERFIL OCULTO Y YO NO SOY UN USUARIO REGISTRADO DESDE EL CHAT
		if($rowcpd['perfiloculto']!='no' or $rowcpd['hidetochat']=='si' and $iamfrom!='chat'){
		//SI EL USUARIO ES DIFERENTE AL PROPIETARO DE LA FOTO
			if($uname != $rowcpd['username']){
				$friend = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$author_id'");
				$friend01 = mysqli_num_rows($friend);

				$friend2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$author_id' AND player2='$player_id'");
				$friend02 = mysqli_num_rows($friend2);
			//NO EJECUTAR LO DE ABAJO Y VOLVER AL CICLO
				if($friend02==false && $friend01==false){
					continue;
				}
			}
		}

		$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$author_id'");
		$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);

		$sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$author_id' AND fromid='$player_id'");
		$hayunbloqueo2 = mysqli_num_rows($sqlbuscarbloqueo2);

		if ($hayunbloqueo < 1 && $hayunbloqueo2 < 1){
			//SI TODO VA BIEN, DEVUELVE OBJETO
			return $rowcp['id'];
		}
	}
}
// COMPRUEBA QUE EXISTA UNA SESSION INICIADA
function isLogged()
{
	return (isset($_COOKIE['eluser']) AND !empty($_COOKIE['eluser'])) ? true : false;
}
/**
 *  COMPRUEBA LA PAGINA ACTUAL
 * @return Si es igual la pagina a la actual devuelve True / Clase "active"
 **/

function currentPage($page = null,$echo = 0){

	switch ($echo) {

		// IMPRME CLASE
		case 0:
		echo (basename($_SERVER['SCRIPT_NAME']) == $page) ? 'class="active"' : '';
		break;

		//DEVUELVE TRUE/FALSE
		case 1:
		return (basename($_SERVER['SCRIPT_NAME']) == $page) ? true : false;
		break;

		default:
			// code...
		break;
	}

}

/**
 * ACTUALIZA LOS CREDITOS DEL USUARIO
 * @param
 * $id = ID del usuario
 * $inputOut = tipo de acción, Entrada o Salida
 * $amount = Cantidad de créditos a actualizar
 * $idMovement = ID del tipo de movimiento
 * $toregister = si es true, el movimiento se registrará
 **/
function updateCredits($id = null, $inputOut = '-', $amount = 0, $idMovement = 0, $toregister = true)
{
	global $connect, $rowu;

	$creditsBefore = []; #Almacenara los creditos antes de actualizarlos
	$creditsAfter = []; #Almacenara los creditos despues de actualizarlos
	$time = time();

	$consult = $connect->query("SELECT id, username, eCreditos FROM `players` WHERE `id`='$id'");

	if ($consult AND $consult->num_rows>0)
	{

		$u = mysqli_fetch_assoc($consult);

		// ALMACENA LOS CREDITOS ACUTUALES
		$creditsBefore = $u['eCreditos'];

		// ACTUALIZA LOS CREDITOS
		$consult = $connect->query("UPDATE `players` SET eCreditos=eCreditos $inputOut $amount WHERE id='$id'");

		// ALMACENA LOS CREDITOS ACTUALES
		$creditsAfter = $inputOut == '-' ? ($creditsBefore-$amount) : ($creditsBefore+$amount);

		if ($consult)
		{

			// DEVUELVE LA CANTIDAD DE REGISTROS DEL USUARIO
			$countLogs = $connect->query("SELECT * FROM players_movements WHERE player_id='$id'")->num_rows;

			if ($countLogs AND $countLogs>=100)
			{
				//SI HAY + DE 10 REGISTROS DE ESE USUARIO, ELIMINA EL ULTIMO
				$connect->query("DELETE FROM players_movements WHERE player_id='$id' ORDER BY time ASC LIMIT 1");
			}

			// REGISTRAR EL MOVIMIENTO
			$log = $connect->query("INSERT INTO `players_movements` (player_id, credits_before, in_out, credits_after, description,	time) VALUES ('$id', '$creditsBefore', '$inputOut', '$creditsAfter', '$idMovement', '$time')");

		}
		return $consult;
	}
}

/**
 * RETORNA EL DIA DEL ULTIMO MENSAJE PROGRAMADO
 * @return int
 **/
function getLastDayMessageScheduled()
{
	global $connect, $rowu;

	// SELECCIONA EL ULTIMO MENSAJE PROGRAMADO (EL MENSAJE CON MAS DIAS PROGRAMADO)
	$consult = $connect->query("SELECT * FROM mensajesprogramados WHERE player_id='$rowu[id]' ORDER BY time DESC LIMIT 1");

	// SI EXISTE POR LO MENOS 1 MENSAJE PROGRAMADOS
	if ($consult AND $consult->num_rows>0)
	{

		$msg = mysqli_fetch_assoc($consult);


		$days = intval(TimeNextHours($msg['time'])) + rand(1,4);

		//$days = intval(($msg['time'] - time())/86400);
		// DEVUELVE EL DIA
		return $days;

	}

	// SI NO EXISTEN MENSAJES PROGRAMADOS
	else
	{
		// DEVUELVE NULL
		return NULL;
	}
}

// DEVUELVE TRUE SI HAY BLOQUEOS
function checkBlocking($userA , $userB)
{
	global $connect;

	$consult = $connect->query("SELECT * FROM `bloqueos` AS b WHERE (b.`fromid` = '$userA' && b.`toid` = '$userB') || (b.`toid` = '$userA' && b.`fromid` = '$userB')");
	if($consult AND $consult->num_rows>0)
	{
		return true;
	}
	return false;
}
function checkBlock($userA = null, $userB = null)
{
	global $connect;
    // COMPROBAR QUE LOS USUARIOS SEAN DIFERENTES (NO PUEDO BLOQUEARME A MI MISMO)
	if($userA != $userB)
	{
		$query = $connect->query('SELECT `id` FROM `bloqueos` AS b WHERE (b.`fromid` = \''.$userA.'\' && b.`toid` = \''.$userB.'\') || (b.`fromid` = \''.$userB.'\' && b.`toid` = \''.$userA.'\') LIMIT 1');
        // RETORNAR TRUE SI EXISTE BLOQUEO ENTRE ALGUNO DE ELLOS
			if ($query == true && $query->num_rows > 0)
			{
				return true;
			}
		}
    //
		return false;
	}

// DEVUELVE LOS PERFILES RECOMENDADOS DE UN USUARIO
	function getRecommendations($id, $user = null)
	{
		global $connect;
		if($user == null)
		{
			$consult = $connect->query("SELECT * FROM `players_recommendations` AS r WHERE r.`fromid` = '$id'");
		}
		else
		{
			$consult = $connect->query("SELECT * FROM `players_recommendations` AS r WHERE r.`fromid` = '$id' AND r.`toid` = '$user'");
		}

		return $consult;
	}
// DEVUELVE ARRAY DE UN USUARIO
	function getUser($id = null, $searchName = false)
	{
		global $connect;

	// COMPRUEBA SI HAY QUE BUSCAR POR ID / NOMBRE
		if ($searchName == false)
		{
			$consult = $connect->query('SELECT * FROM `players` WHERE `id` = \''. $connect->real_escape_string($id) .'\'');
		}
		else
		{
			$consult = $connect->query('SELECT * FROM `players` WHERE `username` = \''. $connect->real_escape_string($id). '\'');
		}


		return $consult;
	}
// DEVUELVE ENLACE A UN PERFIL
	function getUserLink($id = null)
	{
		global $connect, $sitio;

	// COMPRUEBA SI HAY QUE BUSCAR POR ID / NOMBRE
		if (is_numeric($id))
		{
			$consult = $connect->query('SELECT `id`,`email`,`username` FROM `players` WHERE `id` = \''. $connect->real_escape_string($id) .'\'');
		}
		else
		{
			$consult = $connect->query('SELECT `id`,`email`,`username` FROM `players` WHERE `username` = \''. $connect->real_escape_string($id). '\'');
		}
		if ($consult AND $consult->num_rows > 0)
		{
			$User = $consult->fetch_assoc();

			$link = str_replace(' ','.',$User['username']);

			return $sitio['site'].'profile.php?profile_id='.$link;
		}
		else
		{
			error_log('No se devolvio el link del usuario deseado. ' .PHP_EOL. 'Linea: '. __LINE__.PHP_EOL.'Variable pasada: '.$id);
			return '';
		}
	}
// DEVUELVE TRUE SI DOS USUARIOS SON AMIGOS
	function areFriends($usera, $userb){ global $connect;

		$consult = $connect->query("SELECT * FROM `friends` AS f WHERE (f.`player1` = '$usera' && f.`player2` = '$userb') || (f.`player2` = '$usera' && f.`player1` = '$userb')");
		if ($consult AND $consult->num_rows > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	* devuelve true si puedo ver el perfil de una persona (SI NO HAY BLOQUEOS Y NO TIENE EL PERFIL OCULTO)
	*
	* @param int (usuario a inspeccionar)
	* @param int (mi usuario(no requerido))
	* @param boolean TRUE requiere que los usuarios sea amigos / FALSE no requiere amistad / PRETEMINADO true
	* @return boolean
*/
	function canSeeYourProfile($User = null , $iSelf = null, $require_friendly = true)
	{
		global $connect, $rowu;

		$iSelf = $iSelf == null ? $rowu['id'] : $iSelf;

		$consult1 = $connect->query("SELECT id, username, hidetochat, perfiloculto FROM players WHERE id = '$User'");

		$consult2 = $connect->query("SELECT id, username, registerfrom FROM players WHERE id = '$iSelf'");

		if($User != $iSelf)
		{
			if($consult1 AND $consult2){

				$U = $consult1->fetch_assoc();
				$I = $consult2->fetch_assoc();
				$iamfrom = ($I['registerfrom'] == 'chat' ? 'chat' : "bellasgram");

			// COMPRUEBA QUE NO HAYA BLOQUEOS
				if(!checkBlock($User,$iSelf))
				{
				// COMPRUEBA QUE EL USUARIO NO TENGA EL PERFIL OCULTO
					if($U['perfiloculto'] == 'no' or areFriends($User, $iSelf))
					{

					// COMPRUEBA QUE NO SOY AMIGO DEL USUARIO
						if (!areFriends($User, $iSelf)){

						// COMPRUEBA SI EL USUARIO TIENE EL PERFIL OCULTO PARA USUARIOS REGISTRADOS FUERA DEL CHAT Y SOY REGISTRADO EN EL CHAT
							if ($U['hidetochat'] == 'si' AND $iamfrom == "chat")
							{
								return true;
							}
							elseif($U['hidetochat'] == 'no')
							{
								return true;
							}
							else
							{
								return false;
							}

						}
						else
						{
							return true;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
		}
	}

// COMPRUEBA EL ESTADO ES UN CHAT ROOM @RETURN (ABIERTO : "open" /CERRADO: ID DE LA PERSONA QUE LO BLOQUEO)

	function checkStateChatRoom($id){
		global $connect;

	//
		$q = $connect->query("SELECT * FROM `nuevochat_rooms` WHERE id = '$id'");

	//
		if ($q AND $q->num_rows > 0)
		{
			$r = $q->fetch_assoc();

			if($r['state']=='open')
			{
				return 'open';
			}
			else
			{
				return $r['state'];
			}
		}
		else
		{
			return false;
		}
	}

	function getColumns($table = null, $input = null, $where = null, $limit = 1, $sentence = false)
	{
		global $connect;
		$columns = is_array($input) ? implode('`,`', $input) : $input;
		$where = is_null($where) ? 'ORDER BY RAND()' : 'WHERE `'.$where[0].'` = \''.$connect->real_escape_string($where[1]).'\'';
		$query = $connect->query('SELECT `'.$columns.'` FROM `'.$table.'` '.$where.' LIMIT '.$limit);
		if ($query == true && $query->num_rows > 0)
		{
			$result = $sentence == true ? $query : $query->fetch_assoc();
            //
			return is_array($input) ? $result : $result[$input];
		}

		return false;
	}
	function setSwal($input = null)
	{
		echo '<script>swal.fire("'. $input[0] .'","'. $input[1] .'","'. $input[2] .'");</script>';
	}
	function setSwalFire($input = null, $timer = null)
	{
		$textTimer = $timer != null ? ',timer: ' . $timer : '';
		echo '<script>swal.fire({title: "'. $input[0] .'",html: "'. $input[1] .'",type: "'. $input[2] .'"'. $textTimer .'}).then(() => {window.location.href = window.location.href});</script>';

	}

	function setSwalQuest($title,$link, $quest = '')
	{

		echo "<script>swal.fire({title: '$title',buttons: ['No', 'Si!'],showCancelButton: true,}).then((name) => {if(name.isConfirmed){window.location.href = '$link';}});</script>";
	}

// DETECTA UN LINK Y LO CONVIERTE EN ENLACE
	function detectLink($text = null)
	{
//cadena origen con los enlaces sin detectar

	//filtro los enlaces normales
		$text = preg_replace("/((http|https|www)[^\s]+)/", '<a href="$1">$0</a>', $text);
	//miro si hay enlaces con solamente www, si es así le añado el https://
		$text= preg_replace("/href=\"www/", 'href="https://www', $text);

	//saco los enlaces de twitter
	//$text = preg_replace("/(@[^\s]+)/", '<a target=\"_blank\"  href="http://twitter.com/intent/user?screen_name=$1">$0</a>', $text);

	//$text = preg_replace("/(#[^\s]+)/", '<strong><a target=\"_blank\"  href="http://twitter.com/search?q=$1">$0</a></strong>', $text);

		return $text;
	}
	function ProgramarMenssage($ImageDir, $index, $lastDay = 0){
	/**
	 * --NOTA--
	 * Tuve un pequeño problema en este algoritmo e implemente el algoritmo de la funcion para programar fotos y funciono bien, luego me di cuenta del error (se estaba multiplicando la hora 60*60 en vez de 60*24) y volvi e implemente este codigo que esta mas optimizado, esta nota es para tener encuenta este algoritmo para implementarlo en la funcion para programar fotos
	 */
	global $player_id, $connect;

	// ALMACENA EL CONTENIDO DEL MENSAJE
	$content = $_POST['content'][ $index ];

	// ALMACENA EL TIPO DEL MENSAJE PROGRAMADO
	$type = (isset($_GET['typeMP']) AND !empty($_GET['typeMP'])) ? $_GET['typeMP'] : 1;

	// FECHA ACTUAL
	$date = time();

	// ALMACENA LOS DIAS INGRESADOS O 0
	$days = (isset($_POST['dias'][ $index ]) AND !empty($_POST['dias'][ $index ])) ? $_POST['dias'][ $index ] : 0;

	// CAMBIA LOS DIAS A UNIX
	$d = $days * (60*60*24);

	// SI NO SE INGRESARON DIAS, SUMA LOS DIAS YA PROGRAMADOS
	$d += ($_POST['dias'][ $index ]==NULL) ? ($lastDay) : 0;
	$date = $date + $d;

	// SI SE INGRESARON HORAS
	if($_POST['horas'][ $index ]>0)
	{
		//SUMALAS
		$d = $_POST['horas'][ $index ] * (60*24);
		$date = $date + $d;
	}
	// SI SE INGRESARON MINUTOS
	if($_POST['minutos'][ $index ]>0)
	{
		// SUMALOS
		$d = $_POST['minutos'][ $index ] * (60);
		$date = $date + $d;
	}
	// GUARDA EL MENSAJE PROGRAMADO
	mysqli_query($connect, "INSERT INTO `mensajesprogramados` (player_id, rutadefoto, message, type, time) VALUES
		('{$player_id}', '{$ImageDir}', '{$content}', '$type', '{$date }')");
}
/**
* Elimina una fila de la base de datos
*
* @param string $table     // NOMBRE DE LA TABLA
* @param string $where     // NOMBRE COLUMNA WHERE
* @param int $id           // ID A ELIMINAR
* @param int $limit        // LIMITE A ELIMINAR
* @return boolean/integer
*/
function deleteRow($table = null, $id = null, $where = 'id', $limit = 1)
{
	global $connect;
// BORRAR FILA
	$query = $connect->query('DELETE FROM `'.$table.'` WHERE `'.$where.'` = \''.$id.'\' LIMIT '.$limit);
//
	if ($query == true && $connect->affected_rows > 0)
	{
		return true;
	}
// RETORNA FALSE SI NO SE HA ELIMINADO NADA
	return false;
}
/**
* Genera un identificador único
*/
function generateUUID($length = 28)
{
	$key = substr(md5(uniqid(true) . microtime()), 0, $length);
  //
	return $key;
}
/**
 * Formatea una fecha legible por humanos (v2)
 *
 * @param int $date
 * @param boolean $format
 * @return string
 */
function getTimeAgo($datetime = '', $full = false)
{
	$now = new DateTime;
	$ago = new DateTime;
	$ago->setTimestamp($datetime);
	$diff = $now->diff($ago);
	$diff->w = floor($diff->d / 7);
	$diff->d-= $diff->w * 7;
	$string = array(
		'y' => 'a&ntilde;o',
		'm' => 'mes',
		'w' => 'semana',
		'd' => 'dia',
		'h' => 'hora',
		'i' => 'minuto',
		's' => 'segundo',
	);
	foreach($string as $k => & $v)
	{
		if ($diff->$k)
		{
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		}
		else
		{
			unset($string[$k]);
		}
	}
	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? ($ago > $now ? 'Dentro de ' : 'Hace ') . implode(', ', $string) : 'Hace unos segundos';
}

/**
 * Desbloquea a un usuario
 * @param  [int] $fromID Usuario Que emitio el bloqueo
 * @param  [int] $toID   Usuario Bloqueado
 * @return [type]
 */
function unBlock($fromID = null, $toID = null, $showAlert = false)
{
	global $connect;
	$delete = $connect->query("DELETE FROM `bloqueos` WHERE `fromid` = '$fromID' AND `toid` = '$toID'");
	// SI SE HA ELIMINADO EL BLOQUEO SATISFACTORIAMENTE
	if($delete AND $connect->affected_rows > 0)
	{
		// OPTENGO EL NAME DEL USUARIO DESBLOQUEADO
		$consult = $connect->query("SELECT username FROM `players` WHERE `id` = '$toID'");
		$userUnBlocked = $consult->fetch_assoc();

		if($showAlert)
		// MUESTRO UN MENSAJE
			setSwal(array("Usuario desbloqueado", "Has desbloqueado a ". $userUnBlocked['username'], "success"));
	}
	else
	{
		//setSwal(array("Ha ocurrido un error", "Porfavor comuníquese con el administrador o intente mas tarde...". $userUnBlocked['username'], "warning"));
	}
}

/**
 * Construye una lista de paginas
 * @param  [string] $base         [Nombre de Pagina]
 * @param  [int] $totalPages      [Total de paginas]
 * @param  [int] $numResultOnPage [Numero de resultado por pagina]
 * @param  [array/string] $input  [Variables url opcionales]
 * @return [html]                 [Devuel el html de la paginacion]
 */
function paginationIndex($base = null ,$totalPages = null, $numResultOnPage = null,$input = ''){

	// PAGINA
	$base = $base . '.php';
	// NUMERO DE PAGINA
	$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
	// VARIABLES DE URL
	$input = (is_array($input)) ? "&" . implode($input, "&") : "&". $input;
	?>
	<?php if ($totalPages > 2): ?>


		<ul class="pagination">
			<?php if ($page > 1): ?>
				<li class="prev"><a href="<?php echo $base ?>?page=<?php echo $page-1 ?><?php echo $input ?>">Anterior</a></li>
			<?php endif; ?>

			<?php if ($page > 3): ?>
				<li class="start"><a href="<?php echo $base ?>?page=1<?php echo $input ?>">1</a></li>
				<li class="dots">...</li>
			<?php endif; ?>

			<?php if ($page-2 > 0): ?><li class="page"><a href="<?php echo $base ?>?page=<?php echo $page-2 ?><?php echo $input ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
			<?php if ($page-1 > 0): ?><li class="page<?php echo $input ?>"><a href="<?php echo $base ?>?page=<?php echo $page-1 ?><?php echo $input ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

			<li class="currentpage"><a href="<?php echo $base ?>?page=<?php echo $page ?><?php echo $input ?>"><?php echo $page ?></a></li>

			<?php if ($page+1 < ceil($totalPages / $numResultOnPage)+1): ?><li class="page"><a href="<?php echo $base ?>?page=<?php echo $page+1 ?><?php echo $input ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
			<?php if ($page+2 < ceil($totalPages / $numResultOnPage)+1): ?><li class="page"><a href="<?php echo $base ?>?page=<?php echo $page+2 ?><?php echo $input ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

			<?php if ($page < ceil($totalPages / $numResultOnPage)-2): ?>
				<li class="dots">...</li>
				<li class="end"><a href="<?php echo $base ?>?page=<?php echo ceil($totalPages / $numResultOnPage) ?><?php echo $input ?>"><?php echo ceil($totalPages / $numResultOnPage) ?></a></li>
			<?php endif; ?>

			<?php if ($page < ceil($totalPages / $numResultOnPage)): ?>
				<li class="next"><a href="<?php echo $base ?>?page=<?php echo $page+1 ?><?php echo $input ?>">Siguiente</a></li>
			<?php endif; ?>
		</ul>
	<?php endif ?>
	<?php
}

function sendEmail( $template = 'normal', $email = NULL, $params = array(), $subject = null, $content = null )
{
	global $sitio;
  // INCLUIR PLANTILLA
	$subject['newpassword'] = 'Recuperar acceso de ' . $sitio['name'];
	$content['newpassword'] = 'Hola ' . $params['name'] .
	', tu nueva contrase&ntilde;a es <strong>' . $params['password']. '</strong> <br /> <br /> Si no la ha solicitado, cambie su contrase&ntilde;a cuanto antes.';
  // CABECERAS
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: '.$sitio['name'].' <contacto@bellasgram.com>' . "\r\n";
  // ENVIAR EMAIL
	$mail = mail($email, $subject[$template], $content[$template], $headers);
	return $mail;
}

/**
 * Devuelve todos los regalos enviados por una persona
 * @param  int $playerID ID del usuario
 * @return Array           Array asociativo
 */
function getAllGiftSendBy($playerID)
{
	global $connect;

	$consult = $connect->query("SELECT * FROM `players_gifts` WHERE `player_id` = \"". $connect->real_escape_string($playerID) ."\"");

	if($consult AND $consult->num_rows > 0)
	{
		$gifts['total'] = $consult->num_rows;
		$gifts['data'] = mysqli_fetch_assoc($consult);
		return $gifts;
	}
	else
	{
		return false;
	}
}

/**
 * Devuelve todos los regalos enviados para una persona
 * @param  int $playerID ID del usuario
 * @return Array           Array asociativo
 */
function getAllGiftSendTo($playerID)
{
	global $connect;

	$consult = $connect->query("SELECT * FROM `players_gift_given` AS pgg INNER JOIN `players_gifts` AS pg ON pg.`id` = pgg.`gift` WHERE pgg.`toid` = \"". $connect->real_escape_string($playerID) ."\"");

	if($consult AND $consult->num_rows > 0)
	{
		$gifts['total'] = $consult->num_rows;
		while ($row = mysqli_fetch_assoc($consult)) {
			$gifts['data'][] = $row;
		}

		return $gifts;
	}
	else
	{
		return false;
	}
}
/**
 * Devuelve todas las "preguntas de usuarios" de la db
 * @return array
 */
function getAllQuestUsers($page = 1,$resultOnPage = 12)
{
	global $connect;
	$calcPage = ($page - 1) * $resultOnPage;
	$total = $connect->query("SELECT sq.*,p.`id` AS pid, p.`username`, p.`avatar` FROM `site_questions` AS sq INNER JOIN `players` AS p ON p.`id` = sq.`player_id` ORDER BY sq.`id` DESC")->num_rows;
	$consult = $connect->query("SELECT sq.*,p.`id` AS pid, p.`username`, p.`avatar` FROM `site_questions` AS sq INNER JOIN `players` AS p ON p.`id` = sq.`player_id` ORDER BY sq.`id` DESC LIMIT ". $connect->real_escape_string($calcPage) .", ". $connect->real_escape_string($resultOnPage) ."");

	if($consult AND $consult->num_rows > 0)
	{
		$quest['total'] = $consult->num_rows;
		$quest['paginator'] = paginationIndex('admin_questions',$total, $resultOnPage);
		while ($row = mysqli_fetch_assoc($consult)) {
			$quest['data'][] = $row;
		}

		return $quest;
	}
	else
	{
		return false;
	}
}


/**
 * Devuelve todas las "preguntas de usuarios" de un usuario en especifico * @return array
 */
function getAllQuestFROM($fromID = null, $page = 1, $resultOnPage = 40)
{

	global $connect;
	$calcPage = ($page - 1) * $resultOnPage;
	$total = $connect->query("SELECT pq.`id` AS `id`,pq.`read_time`, pq.`answer`, sq.`question`, p.`username`, p.`avatar`, p.`id` AS pid  FROM `players_questions` AS pq INNER JOIN `site_questions` AS sq ON sq.`id` = pq.`question` INNER JOIN `players` AS p ON p.`id` = pq.`toid` WHERE `toid` = '$fromID'")->num_rows;
	$consult = $connect->query("SELECT pq.`id` AS `id`,pq.`read_time`, pq.`answer`, sq.`question`, p.`username`, p.`avatar`, p.`id` AS pid  FROM `players_questions` AS pq INNER JOIN `site_questions` AS sq ON sq.`id` = pq.`question` INNER JOIN `players` AS p ON p.`id` = pq.`toid` WHERE `toid` = '$fromID' ORDER BY pq.`sent_time` DESC LIMIT ". $connect->real_escape_string($calcPage) .", ". $connect->real_escape_string($resultOnPage) ."");

	if($consult AND $consult->num_rows > 0)
	{
		$quest['total'] = $consult->num_rows;
		$quest['paginator'] = paginationIndex('preguntas',$total, $resultOnPage);
		while ($row = mysqli_fetch_assoc($consult)) {
			$quest['data'][] = $row;
		}

		return $quest;
	}
	else
	{
		return false;
	}
}
/**
 * Devuelve true si x usuario dió like a x foto
 * @param  int  $idUser
 * @param  int  $idPhoto
 * @return boolean
 */
function isLike($idUser = null, $idPhoto = null)
{
	global $connect;
	$SQL = $connect->query("SELECT * FROM `player_megusta` WHERE `player_id`=\"". $connect->real_escape_string($idUser) ."\" AND `galeria_id`=\"". $connect->real_escape_string($idPhoto) ."\"");
	if($SQL AND $SQL->num_rows > 0)
	{
		return true;
	}
	return false;
}
/**
 * Devuelve url de un perfil
 * @param  int $iDUser
 * @return string
 */
function getProfileURL($iDUser = null)
{
	global $sitio;
	$SQLUSER = getUser($iDUser);

	if($SQLUSER AND $SQLUSER->num_rows > 0)
	{
		$rUser = $SQLUSER->fetch_assoc();
		$Username = str_replace(' ','-',$rUser['username']);
		$Username = str_replace('','/',$Username);
		return $sitio['site'].$Username;
	}
}
/**
 * Devuelve true si hay una solicitud de amistad entre dos usuarios
 * @param  int  $idUser
 * @param  int  $idUser
 * @return boolean
 */
function issetFriendRequest($idUser = null, $idUser2 = null, $active = false)
{
	global $connect;
	$active = ($active == true) ? "AND `action` = '0'" : '';
	$SQL = $connect->query("SELECT action FROM `players_notifications` WHERE ((fromid=\"". $connect->real_escape_string($idUser) ."\" AND toid=\"". $connect->real_escape_string($idUser2) ."\")||(toid=\"". $connect->real_escape_string($idUser) ."\" AND fromid=\"". $connect->real_escape_string($idUser2) ."\")) AND not_key='newAmistad' $active");
		if($SQL AND $SQL->num_rows > 0)
		{
			return true;
		}
		return false;
	}
/**
	* Generar informe
	*/
	function showDebug($display = TRUE)
	{
		if($display == TRUE)
		{
			$sResult = '<span style="color: #3dff00;">' . (number_format(array_sum(explode(' ', microtime())) - START_TIME, 3) . ' segundos</span> ');

			echo $sResult;
			return;
		}
	}

/**
 * Comprueba si es posible introducir Diapositivas chequeando la siguiente lista de reglas:
 * ° La cantidad de imagenes debe ser menor a 1,
 * ° El modo divertivo debe estar desactivado
 * ° se debe estar subscrito al perfil destinario,
 * ° O ignorando las anteriores, el perfil destinario debe ser el mismo al receptor
 * @param  int     $countImages  Cantidad de fotos
 * @param  int     $IDUser       ID del perfil perteneciente a la foto
 * @return boolean
 */
function canSlidesBeIntroduced($countImages = null, $IDUser = null)
{
	global $rowu;
	if($countImages <= 1 AND !$_SESSION['modeFunny'] AND ((isFollow($IDUser)) OR ($rowu['id'] == $IDUser))) return true;
	else return false;
}

/**
 * Envia un mensaje a un usuario
 * @param  int  			$idRoom        	ID del Chat
 * @param  itn  			$from          	Author del mensaje
 * @param  string  		$message       	Contenido del mensaje
 * @param  boolean		$scheduledTime 	Programar para x hora
 * @param  boolean 		$seen        		Definir mensaje como "leido"
 * @param  boolean 		$seen_to       	Definir mensaje como "leido"
 * @param  int  			$time          	Hora de enviado
 * @return boolean
 */
function sendMessage($idRoom = null, $from = null, $message = null, $file = null, $scheduledTime = false, $seen = false, $seen_to = false, $time = null)
{
	global $connect, $rowu;

	// COMPRUEBA SI EL IDROOM PASADO POR PARAMETRO EXISTE
	if($room = getColumns('nuevochat_rooms', array('player1', 'player2', 'state'), array('id',$idRoom)))
	{
		// COMPRUEBA SI EL REMITENTE ES LEGÍTIMO
		if(($room['player1'] == $from) or ($room['player2'] == $from))
		{
			// COMPRUEBA SI EL ROOM ESTA ABIERTO y EL MENSAJE NO ESTA VACIO
			if($room['state'] == 'open' and ($message != '' or $file != null))
			{
				$to = ($room['player1'] == $from) ? $room['player2'] : $room['player1'];
				$message =  checkFilterMessage($message);
				$existPhoto = 'No';
				if(!empty($file))
				{
					$existPhoto = "Yes";
					$message = "";
				}

				// Envia el mensaje
				$send = $connect->query("INSERT INTO `nuevochat_mensajes` (id_chat, author, toid, mensaje, time, foto, rutadefoto) VALUES (\"". $connect->real_escape_string($idRoom) ."\"  , \"". $connect->real_escape_string($from) ."\", \"". $connect->real_escape_string($to) ."\", \"". $connect->real_escape_string($message) ."\", \"". $time ."\" , \"". $existPhoto ."\" , \"". $file ."\")");
				// Actualiza hora de ultimo mensaje enviado a la room
					$connect->query("UPDATE `nuevochat_rooms` SET time = \"". time() ."\"  WHERE `id` = \"". $connect->real_escape_string($idRoom) ."\"");
					if($send)
					{
						return array(true);
					}
				}
				else
				{
					return array(false, 'room-is-closed');
				}
			}
		}
		else
		{
			return array(false, 'room-no-exist');
		}
	}

/**
 * filtra un mensaje chequeando algunas reglas
 * @param  string  $message Mensaje a filtrar
 * @return string  					Devuelve $message
 */
// Por los momentos esta funcion hace solo una verificación, pero se prevee que en el futuro verifique mas de una regla.
function checkFilterMessage($message = '')
{
	$message = detectLink($message);
	return $message;
}

/**
 * Sube una foto para un mensaje de chat
 * @param  array 					$file     array tipo $_FILE del archivo
 * @param  int 						$idRoom ID del room
 * @return boolean/string
 */
function upload_file_in_chat($file = null, $idRoom = null)
{
	$UUID = generateUUID(6);

	$info = pathinfo($file['name']);

	$filename = $idRoom . '-' . $UUID . "." . $info['extension'];
	$directoryUploads = 'uploads/src_messages/';
	// COMPRUEBA SI EL ARCHIVO ES UNA IMAGEN
	if($file['type'] == 'image/jpeg' OR $file['type'] == 'image/png' OR $file['type'] == 'image/jpg')
	{
	  // SI EL ARCHIVO SE MOVIO CORRECTAMENTE
		if(false === is_uploaded_file($file['tmp_name'])){
			return false;
		}
		if(copy($file['tmp_name'], $directoryUploads.$filename))
		{
			return $directoryUploads.$filename;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

/**
 * Elimina a un usuario junto con sus datos
 * @param  int $user_id ID del usuario
 * @return array
 */
function deleteAccount($user_id = null){
	global $connect;

	/*=====  ELIMINACION DE ARCHIVOS SUBIDOS  ======*/

	/* ELIMINAR AVATAR Y FOTO DE PORTADA */
	$query = $connect->query("SELECT `avatar`, `cover-page` FROM `players` WHERE `id` = \"". $user_id ."\" LIMIT 1");
  //
	if ($query == true && $query->num_rows > 0)
	{
    //ELIMINAR AVATAR
		while($post = $query->fetch_assoc() )
		{
			if(basenameProfile($post['avatar']) != 'default-asvatar.jpg')
				deletePostImages(basenameProfile($post['avatar'], true));
			deletePostImages($post['cover-page']);
		}
	}

	/* ELIMINA LAS FOTOS PUBLICADAS */
	$query = $connect->query("SELECT `id`, `imagen`, `thumb` FROM `fotosenventa` WHERE player_id = \"". $user_id ."\"");
  // COMPROBAR SI TIENE POSTS
	if ($query == true && $query->num_rows > 0)
	{
  // ELIMINAR PUBLICACIONES(FOTOS EN VENTAS)
		while($post = $query->fetch_assoc() )
		{
			deletePost($post);
		}
	}

	/* ELIMINA FOTOS DE MENSAJES ENVIADAS O RECIBIDAS */
	$query = $connect->query("SELECT `rutadefoto` FROM `nuevochat_mensajes` WHERE (`author` = \"". $user_id ."\" OR `toid` = \"". $user_id ."\") AND (`foto` = 'Yes')");
  // COMPROBAR SI TIENE MENSAJES CON FOTOS
		if ($query == true && $query->num_rows > 0)
		{
  // ELIMINAR FOTOS
			while($msg = $query->fetch_assoc() )
			{
				deletePostImages($msg['rutadefoto']);
			}
		}

		/* ELIMINA FOTOS DE MENSAJES PROGRAMADOS CREADO POR EL USUARIO */
		$query = $connect->query("SELECT `rutadefoto` FROM `mensajesprogramados` WHERE (`player_id` = \"". $user_id ."\") AND (`rutadefoto` != '')");
  // COMPROBAR SI TIENE MENSAJES CON FOTOS
			if ($query == true && $query->num_rows > 0)
			{
  // ELIMINAR FOTOS
				while($msg = $query->fetch_assoc() )
				{
					deletePostImages($msg['rutadefoto']);
				}
			}

			/* ELIMINA LAS FOTOS PROGRAMADAS */
			$query = $connect->query("SELECT `id`, `imagen` FROM `fotosprogramadas` WHERE `player_id` = \"". $user_id ."\"");
  // COMPROBAR SI TIENE POSTS
			if ($query == true && $query->num_rows > 0)
			{
  //
				while($post = $query->fetch_assoc() )
				{
					deletePostImages($post['imagen']);
				}
			}

			/* ELIMINAR TODOS LOS REGALOS HECHOS POR EL USUARIO */
			$query = $connect->query("SELECT `id`, `player_id`, `files` FROM `players_gifts` WHERE `player_id` = \"". $user_id ."\"");
  // COMPROBAR SI TIENE REGALOS
			if ($query == true && $query->num_rows > 0)
			{
  // ELIMINAR REGALOS
				while($gift = $query->fetch_assoc() )
				{
					deleteGift($gift);
				}
			}

			/* ELIMINAR PACKS */
  /*$query = $connect->query("SELECT `id`, `video`, `imagens` FROM `packsenventa` WHERE `player_id` = \"". $user_id ."\"");
  // COMPROBAR SI EXISTEN
  if ($query == true && $query->num_rows > 0)
  {
  // ELIMINAR PACKS
    while($pack = $query->fetch_assoc() )
    {
      deletePack($pack);
    }
  }*/

  /*=====  ELIMINACIONES RESTANTES  ======*/

  $array = array(
    // COMENTARIOS
  	'DELETE FROM `player_comments` WHERE `author_id` = \''.$user_id.'\'',
    // DESCARGAS
  	'DELETE FROM `download` WHERE `uid` = \''.$user_id.'\'',
    // COMPRAS
  	'DELETE FROM `fotoscompradas` WHERE `comprador_id` = \''.$user_id.'\'',
    // FOTOS PUBLICADAS
  	'DELETE FROM `fotosenventa` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR BLOQUEOS Y BLOQUEADOS
  	'DELETE FROM `bloqueos` WHERE `fromid` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // ELIMINAR REGALO SEMANAL
  	'DELETE FROM `giftcredits_weekly` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR LIKES
  	'DELETE FROM `player_megusta` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR RECOMENDACIONES
  	'DELETE FROM `players_recommendations` WHERE `fromid` = \''.$user_id.'\' || `toid` = \''. $user_id .'\'',
    // NOTIFICACIONES
  	'DELETE FROM `players_notifications` WHERE `toid` = \''.$user_id.'\' || `fromid` = \''.$user_id.'\'',
    // AMISTADES
  	'DELETE FROM `friends` WHERE `player1` = \''.$user_id.'\' || `player2` = \''.$user_id.'\'',
    // PERFIL / CUENTA
    // PREGUNTAS DE USUARIOS
  	'DELETE FROM `site_questions` WHERE `player_id` = \''.$user_id.'\'',
    // RETIROS
  	'UPDATE `retiros` SET `usuario` = "" WHERE `usuario` = \''.$user_id.'\'',
    // RESPUESTAS AUTOMATICAS DE BOTS
  	'DELETE FROM `respuesta_automatica` WHERE `uid` = \''.$user_id.'\'',
    // RESPUESTAS DE BOTS EN ESPERA
  	'DELETE FROM `respuestasbot_enespera` WHERE `bot_id` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // REPORTES
  	'DELETE FROM `reportes` WHERE `author` = \''.$user_id.'\'',
    // ENCUESTAS DE USUARIOS
  	'DELETE FROM `polls` WHERE `uid` = \''.$user_id.'\'',
    // MASCOTAS COMPRADAS
  	'DELETE FROM `player_pets` WHERE `player_id` = \''.$user_id.'\'',
    // ITEMS DE GRANJA COMPRADOS
  	'DELETE FROM `player_items_bought` WHERE `player_id` = \''.$user_id.'\'',
    // COLECCIONES ADQUIRIDAS
  	'DELETE FROM `player_colecciones` WHERE `player_id` = \''.$user_id.'\'',
    // PREGUNTAS REALIZADAS POR EL USUARIO
  	'DELETE FROM `players_questions` WHERE `toid` = \''.$user_id.'\'',
    // ELIMINAR DE LA LISTA DE NOMBRES
  	'DELETE FROM `players_namesactions` WHERE `player_id` = \''.$user_id.'\'',
    // MOVIMIENTOS REALIZADOS
  	'DELETE FROM `players_movements` WHERE `player_id` = \''.$user_id.'\'',
    // REGALOS ENVIADOS Y RECIBIDOS
  	'DELETE FROM `players_gift_given` WHERE `fromid` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // ELIMINAR REGALOS CREADOS
  	'DELETE FROM `players_gifts` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR GRANJAS ADQUIRIDAS
  	'DELETE FROM `players_farms` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR ITEMS COMPRADOS
  	'DELETE FROM `players_farm_items` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR ANTECEDENTES EN COMPRAS DE CREDITOS
  	'DELETE FROM `players_farm_items` WHERE `player_id` = \''.$user_id.'\'',
    // PACKS COMPRADOS
  	'DELETE FROM `packscomprados` WHERE `comprador_id` = \''.$user_id.'\'',
    // SALA DE CHATS
  	'DELETE FROM `nuevochat_rooms` WHERE `player1` = \''.$user_id.'\' || `player2` = \''.$user_id.'\'',
    //
  	'DELETE FROM `notificaciones_suscripcionesvencidas` WHERE `usera` = \''.$user_id.'\' || userb = \''. $user_id .'\'',
    //
  	'DELETE FROM `notificaciones_fotosnuevas` WHERE `player_notificador` = \''.$user_id.'\' || `player_notificado` = \''.$user_id.'\'',
    // NOTAS CREADAS
  	'DELETE FROM `notas` WHERE `uid` = \''.$user_id.'\'',
    // MENSAJES PROGRAMADOS
  	'DELETE FROM `mensajesprogramados` WHERE `player_id` = \''.$user_id.'\'',
    // FOTOS PROGRAMADAS
  	'DELETE FROM `fotosprogramadas` WHERE `player_id` = \''.$user_id.'\'',
    // PACKS EN VENTA
  	'DELETE FROM `packsenventa` WHERE `player_id` = \''.$user_id.'\'',
    // MENSAJES ENVIADOS Y RECIBIDOS
  	'DELETE FROM `nuevochat_mensajes` WHERE `author` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // ELIMINAR COMPROBANTE DE "BIENVENIDA"
  	'DELETE FROM `welcomechat` WHERE `userid` = \''.$user_id.'\'',
    // ELIMINAR *DESCONOCIDO*
  	'DELETE FROM `ventascompradas` WHERE `comprador_id` = \''.$user_id.'\'',
    // ELIMINAR *DESCONOCIDO*
  	'DELETE FROM `ventasenventa` WHERE `player_id` = \''.$user_id.'\'',
  );
  // RECORRER CONSULTAS
foreach($array as $sql)
{
    // EJECUTAR CONSULTA
	if( $connect->query($sql) == false )
	{
		$error[] = '<strong>SQL:</strong> '.$sql.'. <strong>Error:</strong> '.$connect->error;
      break; // DEJAR DE EJECUTAR CONSULTAS
    }
  }
  // SI TODO HA IDO BIEN
  if(empty($error))
  {
  	return true;
  }

  // CONVERTIR ERRORES EN TEXTO
  $msg = 'Problema al eliminar usuario: <br/>';
  $msg .= implode('<br/>', $error);

  // SI ALGO FALLÓ, NOTIFICAR AL ADMIN /*EN REVISIÓN */
  error_log("Error al eliminar Usuario: " . $user_id . PHP_EOL . 'Error: ' . $msg);

  // RETORNAR FALSE
  return false;
}

/**
 * Elimina una publicación (fotoenventa) y sus asociados
 * @param  array $post Array asociativo con datos de un post
 * @return boolean
 */
function deletePost($post = null)
{
	global $connect;
  // ELIMINAR IMAGENES DEL POST
	deletePostImages($post['imagen']);
  // ELIMINAR THUMB DEL POST
	deletePostImages($post['thumb']);
	$array = array(
    // ELIMINAR SHOUT
		'DELETE FROM `fotosenventa` WHERE `id` = \''.$post['id'].'\' LIMIT 1',
    // ELIMINAR COMENTARIOS DE SHOUT
		'DELETE FROM `player_comments` WHERE `galeria_id` = \''.$post['id'].'\'',
    // ELIMINAR LIKES DE SHOUT
		'DELETE FROM `player_megusta` WHERE `galeria_id` = \''.$post['id'].'\'',
    // ELIMINAR DENUNCIAS
    // ELIMINAR NOTIFICACIONES
    // ELIMINAR REGISTRO DE DESCARGAS
		'DELETE FROM `download` WHERE `fotoid` = \''.$post['id'].'\'',
	);
	foreach($array as $sql)
	{
    // EJECUTAR CONSULTA
		if( $connect->query($sql) == false )
		{
			$error[] = '<strong>SQL:</strong> '.$sql.'. <strong>Error:</strong> '.$connect->error;
      break; // DEJAR DE EJECUTAR CONSULTAS
    }
  }
  // SI TODO HA IDO BIEN
  if(empty($error))
  {
  	return true;
  }

  // CONVERTIR ERRORES EN TEXTO
  $msg = 'Problema al eliminar usuario: <br/>';
  $msg .= implode('<br/>', $error);

  // SI ALGO FALLÓ, NOTIFICAR AL ADMIN /*EN REVISIÓN */
  error_log($msg);
  // RETORNAR FALSE
  return false;
}

/**
 * Elimina las fotos de una publicación
 * @param  string/array  $images Ruta a la imagen
 * @return boolean
 */
function deletePostImages($images = null)
{
  // SI ES UN STRING, SE CONVIERTE EN ARRAY
	if(isJson($images))
	{
		$images = json_decode($images);
	}
	elseif( is_string($images) )
	{
		$images = explode(',', $images);
	}
  // BORRA LAS IMAGENES
	foreach ($images as $imgName)
	{
  // BORRA LA IMAGEN
		if( file_exists($imgName) )
		{
			unlink($imgName);
		}
	}
}
function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * Elimina un regalo
 * @param  int/$consult  $gift ID del regalo o $consult de un Regalo
 * @return boolean
 */
function deleteGift($gift)
{
	global $connect;
  // CREA UNA CONSULTA A PARTIR DE LA ID PASADA Y BORRA EL REGALO /* EN REVISION */
	if(is_numeric($gift))
	{

	}
  // SI EL $gift ES UNA CONSULTA DE MYSQL (ARRAY ASOCIATIVO)
	else
	{
    // BORRA LA IMAGEN (DE EXISTIR)
		if( file_exists($gift['files']) )
		{
			unlink($gift['files']);
		}
    // BORRA LA IMAGEN THUMB (DE EXISTIR)
		if( file_exists('uploads/thumb_gift/thumb-'.basename($gift['files'])))
		{
			unlink('uploads/thumb_gift/thumb-'.basename($gift['files']));
		}
	}
	$connect->query("DELETE FROM `players_gift_given` WHERE `gift` = \"". $gift['id'] ."\"");
  // BORRA REGALO
	$deleteSQL = $connect->query("DELETE FROM `players_gifts` WHERE `id` = \"". $connect->real_escape_string($gift['id']) ."\" LIMIT 1");
	if($deleteSQL)
	{
		return true;
	}
	return false;
}

/**
 * Elimina un regalo enviado (no elimina el regalo sino el envío)
 * @param  int 			$giftID 		ID del regalo
 * @return boolean
 */
function deleteGiftSent($giftID)
{
	global $connect;

	if($connect->query("DELETE FROM `players_gift_given` WHERE `id` = \"". $giftID ."\""))
	{
		return true;
	}
	else
	{
		return false;
	}
}
/**
 * Envia una notificación a un usuario
 * @param  int  $to_user   Usuario a quien se le enviara
 * @param  int  $from_user Usuario quien envía
 * @param  int  $key       Tipo de notificación
 * @param  int  $action      ID del objeto que desencadena esta notificación (Ejemplo si es una notificación de un nuevo Pack, entonces se coloca el id de ese Pack)
 * @param boolean $myself Enviarme a mi mismo
 * @return boolean         true
 */
function newNotification($to_user = null, $from_user = null, $key = null, $action = 0, $myself = false)
{
	global $connect;
  // EVITAR ENVIARME A MÍ MISMO
	if($to_user != $from_user || $myself == true)
	{
    // ENVIA NOTIFICACIÓN
		$query = $connect->query('INSERT INTO `players_notifications` (`toid`, `fromid`, `not_key`, `action`, `read_time`) VALUES (\''.$to_user.'\', \''.$from_user.'\', \'' . $key . '\', \''.$action.'\', 0) ');

			if($query == true)
			{
				return true;
			}
		}
  //
		return false;
	}

 /**
  * Devuelve nombre de la foto avatar sin ?xxxx (revisa un avatar de un usuario en la bbdd para entender)
  * @param  string $string Ruta a la imagen
  * @param  boolean $returnPath Evita quitar la ruta
  * @return string
  */
 function basenameProfile($string, $returnPath = false)
 {
 	if($string != '' AND $string != null)
 	{
 		$name = basename($string);
 		$return = '';
 		for ($i=0; $i < strlen($name); $i++)
 		{
 			if($name[$i] == '?') break;
 			$return .= $name[$i];
 		}
 		if($returnPath) return dirname($string) .DIRECTORY_SEPARATOR. $return;
 		return $return;
 	}
 }

 /**
  * Devuelve datos de un usuario de bellasgram
  * @param $user_id id ID de usuario
  * @return object
  */
 function getUserFromBG($user_id = null)
 {

 	$db = getConnectBG();
 	$query = $db->query('SELECT `member_id`, `name`, `email`, `password`, `password_phpost`, `group_id`, `banned`, `session` FROM `members` WHERE `member_id` = \'' . $db->real_escape_string($user_id) . '\' LIMIT 1');
        //
 	if($query == true && $query->num_rows > 0)
 	{
 		return $query->fetch_object();
 	}
  //
 	return false;
 }

 /**
     * Comprueba si un usuario existe en BellasGram
     *
     * @param int $member_id
     * @param string $username
     * @return array
     */
 function isMemberFromBG($member_id = 0, $username = '', $return_id = false)
 {
 	$db = getConnectBG();
  // ESTABLECE LA CONDICIÓN
 	$where = empty($member_id) ? 'LOWER(`name`) = \'' . $db->real_escape_string(strtolower($username)) . '\'' : '`member_id` = \'' . $member_id . '\'';
  // EJEUTA LA CONSULTA
 	$query = $db->query('SELECT `member_id` FROM `members` WHERE ' . $where . ' LIMIT 1');
        //
 	if ($query == true && $query->num_rows > 0)
 	{
 		if ($return_id == true)
 		{
 			$result = $query->fetch_row();
 			return $result[0];
 		}
 		return true;
 	}
 	return false;
 }
 function getConnectBG($sandbox = true)
 {
 	$db = array(
 		'bellasgram' => array(
 			'username' => 'root',
 			'userpass' => 'X96%(?}OJd#c',
 			'database' => 'mlywatsm_bellasgramNew',
 		)
 	);
 	if($sandbox)
 	{
 		$db = array(
 			'bellasgram' => array(
 				'username' => 'root',
 				'userpass' => '',
 				'database' => 'mlywatsm_bellasgramNew',
 			)
 		);
 	}
 	$bellasgram = new MySQLi('p:localhost', $db['bellasgram']['username'], $db['bellasgram']['userpass'], $db['bellasgram']['database']);
	// SI NO SE PUEDE CONECTAR A LA BASE DE DATOS BELLASGRAM
 	if ($bellasgram->connect_errno)
 	{
 		die('Error al conectar a BellasGram: ' . $bellasgram->connect_error);
 	}
 	return $bellasgram;
 }

 /**
  * Elimina un pack
  * @param  array $pack
  * @return boolean
  */
 function deletePack($pack = null)
 {
 	global $connect;
 	// ELIMINA FOTOS Y VIDEO
 	deletePostImages($pack['imagens']);
 	deletePostImages($pack['video']);

 	// ELIMINA LAS COMPRAS DE ESTE PACK
 	$query = $connect->query("DELETE FROM `packscomprados` WHERE `foto_id` = \"". $pack['id'] ."\"");
 }

 /**
  * Envia regalo a usuario
  * @param  string  	$usernames    	Usuarios a quienes se les enviará
  * @param  string  	$msg         		Mensaje que se enviara
  * @param  file  		$file         	Foto a subir
  * @param  int		$optionSelect 			Tipo de envio
  * @param  int  			$amount       	Cantidad a enviar (Solo admin)
  * @return array
  */
 function sendGift($usernames = null, $msg = "", $file = null, $optionSelect = 0)
 {

 	global $connect, $rowu;
	// CODIGO UNICO
 	$token = generateUUID(16);
		// DEFINIR UBICACIÓ DE SUBIDAS
 	$target_dir    = "uploads/gifts/";
		// DEFINIR NOMBRE QUE SE LE OTORGARA A LA IMAGEN
 	$target_file   = $target_dir . basename($file["name"]);
 	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
 	$filename = $token . $rowu['username'] . '.' . 'jpg';
 	$Names = []; // Almacena los nombres de los usuarios a quien se les enviara el regalo
 	$IPS = []; // Almacena las ip de los usuarios que se les envío el regalo
 	$message = array("","","");
 	$countSuccess = 0; // Cuenta las veces que se envio el regalo con éxito

	// SI HAY QUE ENVIAR A TODOS MIS AMIGOS
 	if($optionSelect == 0){
 		$consult = $connect->query("SELECT p.`username` FROM `friends` AS f INNER JOIN `players` AS p ON p.`id` = IF(f.`player1` = \"". $connect->real_escape_string($rowu['id']) ."\", f.`player2`, f.`player1`) WHERE f.`player1` = \"". $connect->real_escape_string($rowu['id']) ."\"  OR f.`player2` = \"". $connect->real_escape_string($rowu['id']) ."\"");
 			if($consult AND $consult->num_rows > 0){
			// ALMACENAR LOS NOMBRES DE TODOS MIS AMIGOS
 				while($names = mysqli_fetch_assoc($consult)) {
 					$Names[] = $names['username'];
 				}
 			}
 			else
 			{
 				$message = array('No puedes enviar regalos si no tienes amigos agregados en tu perfil', 'Intenta denuevo cuando agregues al menos un amigo a tu perfil','error');
 				exit;
 			}
 		}
	// SI HAY QUE ENVIAR REGALO A CIERTAS PERSONAS
 		else if($optionSelect == 1){
			// CONVERTIR EN ARRAY
 			$Names = explode(',',$usernames);
 			$Names = array_unique($Names);
 		}

	// COMPROBAR SI ES UNA IMAGEN Y SI NO EXCEDE EL LIMITE DE TAMAÑO (1MB)
 		if(in_array($imageFileType,array('png','gif','jpg','jpeg')) AND $file['size'] <= 1000000)
 		{

		// MOVER IMAGEN A CARPETA DE SUBIDAS
 			if(move_uploaded_file($file["tmp_name"], $target_dir . $filename))
 			{

			// GENERAR DIRECCIÓN DE THUMNAIL
 				$thumb = 'uploads/thumb_gift/thumb-'. $token . $rowu['username'] .'.jpg';

			//CREAR MINIATURA
 				if (getSourceType($target_file)!="film")
 				{
 					createThumbnail($target_dir . $filename, $thumb, 200,200);
 				}
			// CREAR REGALO
 				$insert = $connect->query("INSERT INTO `players_gifts` (`player_id`, `files`, `comment`, `time`) VALUES (\"". $connect->real_escape_string($rowu['id']) ."\", \"". $connect->real_escape_string($target_dir . $filename) ."\", \"". $connect->real_escape_string($msg) ."\", UNIX_TIMESTAMP())");
 					$IDGift = $connect->insert_id;
 					if($insert)
 					{

				// ENVIAR NOTIFICACIONES Y REGALO
 						foreach($Names as $Name):

 							$consult = $connect->query("SELECT `id`,`username`,`ipaddres` FROM `players` WHERE `username` = \"". $connect->real_escape_string($Name) ."\";");

 							if($consult AND $consult->num_rows > 0)
 							{
 								$User = $consult->fetch_assoc();

						// COMPRUEBA DE QUE SEAN AMIGOS Y NO HAYA BLOQUEOS
 								if(areFriends($rowu['id'], $User['id']) AND !checkBlock($rowu['id'], $User['id'])){


							// COMPRUEBA QUE ESTA IP NO HAYA SIDO REGISTRADA AUN
 									if( !in_array($User['ipaddres'], $IPS) ):

								// ENVIAR NOTIFICACIÓN
 										$not = $connect->query("INSERT INTO `players_notifications` (`fromid`, `toid`, `not_key`, `action`, `read_time`) VALUES (\"". $connect->real_escape_string($rowu['id']) ."\", \"". $connect->real_escape_string($User['id']) ."\", 'newGift' , \"". $IDGift ."\" , '0' )");

								// ENVIAR REGALO
 											$given = $connect->query("REPLACE INTO `players_gift_given` (`fromid`, `toid`, `gift`, `time`) VALUES (\"". $connect->real_escape_string($rowu['id']) ."\", \"". $connect->real_escape_string($User['id']) ."\", \"". $IDGift ."\" , UNIX_TIMESTAMP())");

								// Almacena la ip del usuario
 												if($not AND $given){
 													$IPS[] = $User['ipaddres'];
 												}
 												$message = array("Genial!", "El regalo se han enviado correctamente.","success");
 												$countSuccess++;
 											endif;
 										}
 										else
 										{
 											if($countSuccess == 0) $message = array("Error: No hubo nadie a quien enviarle este regalo.", "", "warning");
 										}
 									}
 									else
 									{
 										$message = array("Ha ocurrido un error, Porfavor intente mas tarde.","","error");
 									}
 								endforeach;
 							}
 							else
 							{
 								$message = array("Ha ocurrido un error, Porfavor intente mas tarde.","","error");
 							}
			// SI NO SE ENVIO NI UNA SOLA VEZ LA FOTO
 							if($countSuccess == 0)
 							{
				//Eliminar archivos subidos
 								unlink($target_dir . $filename);
 								unlink($thumb);
 							}
 						}
 					}
 					else
 					{
 						if($file['size'] >= 1000000)$message = array("El tamaño de la imagen excede el limite permitido","Solo puedes subir imágenes con un tamaño inferior a 1MB.","info");
 						else $message = array("Hasta los momentos solo se permite subir imágenes","Solo puedes subir imagenes tipo jpg/jpeg/png/gif","info");
 					}
 					return json_encode($message);
 				}


 /**
  * Envia un regalo con créditos a todos los usuarios
  * @param  string  $msg          Mensaje
  * @param  integer $amount       Cantidad a regalar
  * @return [type]                [description]
  */
 function sendGiftMoney($msg = "", $amount = 0)
 {
 	global $connect, $rowu;
 	$amount = $connect->real_escape_string($amount);
 	$message = array("","","");
 	$SQLUpdateCredits = "";
 	$SQLInsertGiftGiven = "";
 	$SQLNotification = "";
 	$separator = "";
 	$countSuccess = 0;

	// Crear regalo [Anonimato = true]
 	$insert = $connect->query("INSERT INTO `players_gifts` (`player_id`, `files`, `amount`, `comment`, `anonymous`, `time`) VALUES (\"". $connect->real_escape_string($rowu['id']) ."\", '', \"". $amount ."\", \"". $connect->real_escape_string($msg) ."\", 1, UNIX_TIMESTAMP())");
 		$IDGift = $connect->insert_id;

 	// Comprueba que se haya creado el regalo
 		if($insert)
 		{
 		// Optiene el id de todos los usuarios del chat que no "me" tengan bloqueado
 			$users_id = $connect->query("SELECT p.`id` FROM `players` AS `p` LEFT JOIN `bloqueos` AS `b` ON (b.`toid` = \"". $rowu['id'] ."\" AND b.`fromid` = p.`id`) || (b.`fromid` = \"". $rowu['id'] ."\" AND b.`toid` = p.`id`) WHERE b.`id` IS NULL AND p.`id` != \"". $rowu['id'] ."\";");

 		// Si existe uno o mas
 				if($users_id AND $users_id->num_rows > 0)
 				{
 			// Almacenalos en una lista
 					while ($user = $users_id->fetch_assoc()) {
 						$user_id[] = $user['id'];
 					}

 			// Genera un sql para registrar
 					foreach($user_id AS $userid)
 					{
 						$SQLUpdateCredits .= $separator . "(".$userid.",(SELECT `eCreditos` AS `eC` FROM `players` WHERE `id` = \"". $userid ."\"),'+',((SELECT `eCreditos` AS `eC` FROM `players` WHERE `id` = \"". $userid ."\") + ".$amount."),'14',UNIX_TIMESTAMP())";

 						$SQLInsertGiftGiven .= $separator . "(\"". $connect->real_escape_string($rowu['id']) ."\", \"". $connect->real_escape_string($userid) ."\", \"". $IDGift ."\" , UNIX_TIMESTAMP())";

 						$SQLNotification .= $separator . "(\"". $connect->real_escape_string($rowu['id']) ."\", \"". $connect->real_escape_string($userid) ."\", 'newGiftMoneyAll' , \"". $IDGift ."\" , '0' )";
 						$separator = ",";
 					}
 					/* REGISTRAR MOVIMIENTO - DESACTIVADO */
 			//if($connect->query("INSERT INTO `players_movements` (`player_id`, `credits_before`, `in_out`, `credits_after`, `description`, `time`) VALUES ". $SQLUpdateCredits .""))

 					if($connect->query("UPDATE `players` SET `eCreditos` = (`eCreditos` + $amount) WHERE `id` IN (". implode($user_id, ',') .")")){
 						if($connect->query("REPLACE INTO `players_gift_given` (`fromid`, `toid`, `gift`, `time`) VALUES ". $SQLInsertGiftGiven .""))
 						{
 							if($connect->query("INSERT INTO `players_notifications` (`fromid`, `toid`, `not_key`, `action`, `read_time`) VALUES ". $SQLNotification .""))
 							{
 								$message = array("El regalo se envió con exíto.", "", "success");
 							}
 						}
 					}

 					else
 					{
 						$message = array("Ha ocurrido un error.", "No se ha podido enviar el regalo", "warning");
 						$connect->query("DELETE FROM `players_gifts` WHERE `id` = \"".$IDGift);
 					}
 					echo $connect->error;
 				}
 				else
 				{
 					$message = array("Ha ocurrido un error", "No se ha podido enviar el regalo", "error");
 					$connect->query("DELETE FROM `players_gifts` WHERE `id` = \"".$IDGift);
 				}

		/* ENVIAR SOLO A AMIGOS O ENVIA A TODOS SI ES UN ENVIO CON CREDITOS
 		//if((areFriends($rowu['id'], $User['id'])) OR ($optionSelect == 2)){}
 		// COMPRUEBA QUE NO HAYA BLOQUEOS
 		if(!checkBlock($rowu['id'], $User['id']))
 		{

			// ENVIAR NOTIFICACIÓN
 			$not = $connect->query("INSERT INTO `players_notifications` (`fromid`, `toid`, `not_key`, `action`, `read_time`) VALUES (\"". $connect->real_escape_string($rowu['id']) ."\", \"". $connect->real_escape_string($User['id']) ."\", 'newGift' , \"". $IDGift ."\" , '0' )");

			// ENVIAR REGALO
 			$given = $connect->query("REPLACE INTO `players_gift_given` (`fromid`, `toid`, `gift`, `time`) VALUES (\"". $connect->real_escape_string($rowu['id']) ."\", \"". $connect->real_escape_string($User['id']) ."\", \"". $IDGift ."\" , UNIX_TIMESTAMP())");

			// COMPRUEBA (denuevo) SI SE DEBE ENVIAR CRÉDITOS Y SI ES UN ADMIN QUIEN ENVIA
 			if($optionSelect == 2 AND $rowu['role'] == "Admin")
 			{
 				updateCredits($User['id'],'+', $amount, 14);
 			}

 			$message = array("Genial!", "El regalo se han enviado correctamente.","success");
 		endif;
 		*/
 	}
 	return json_encode($message);
 }

 /**
  * Agrega un bloqueo entre dos usuarios
  * @param int $toID   Usuario a quien se bloqueara
  * @param int $fromID Usuario quien bloquea (default: self)
  */
 function addBlockUser($toID = null, $fromID = null)
 {
 	global $connect, $rowu;

 	// Si el parámetro esta vacío, predeterminar el id de mi usuario
 	$fromID = ($fromID == null) ? $rowu['id'] : $fromID;

 	// Revisar que no haya bloqueos
 	if (!checkBlocking($toID, $fromID))
 	{
 		$insertBlock = $connect->query("INSERT INTO `bloqueos` (`fromid`, `toid`) VALUES (\"". $fromID ."\", \"". $toID ."\")");
 	}

	// Elimina amistad de existir
 	deleteFriend($toID, $fromID);

 	// Selecciona salas de chat pertenecientes a $fromID y $toID
 	$selectRoom = $connect->query("SELECT `id` FROM `nuevochat_rooms` WHERE (`player1` = \"". $fromID ."\" AND `player2` = \"". $toID ."\") OR (`player2` = \"". $fromID ."\" AND `player1` = \"". $toID ."\") LIMIT 2");

 		if($selectRoom AND $selectRoom->num_rows > 0)
 		{
 			while($Room = $selectRoom->fetch_assoc())
 			{
			// Borra sala de chat
 				deleteRoomChat($Room['id']);
 			}
 		}
 		/* ELIIMINAR PACKS COMPRADOS EL UNO AL OTRO */
 		$query = $connect->query("SELECT `id` FROM `packsenventa` WHERE `player_id` = \"". $fromID ."\" OR `player_id` = \"". $toID ."\"");
 		while ($pack = $query->fetch_assoc())
 		{
 			$connect->query("DELETE FROM `packscomprados` WHERE (`foto_id` = \"". $pack['id'] ."\") AND (`comprador_id` = \"". $fromID ."\" OR `comprador_id` = \"". $toID ."\")");
 		}

 		/* ELIIMINAR REGALOS ENVIADOS EL UNO AL OTRO */
 		$query = $connect->query("SELECT `id` FROM `players_gift_given` WHERE (`fromid` = \"". $fromID ."\" AND `toid` = \"". $toID ."\") OR (`toid` = \"". $fromID ."\" AND `fromid` = \"". $toID ."\")");
 			while ($gift = $query->fetch_assoc())
 			{
 				deleteGiftSent($gift['id']);
 			}

 			return $insertBlock;
 		}

 		function deleteFriend($toID = null, $fromID = null)
 		{
 			global $connect, $rowu;

 	// Si el parámetro esta vacío, predeterminar el id de esta sesión
 			$fromID = ($fromID == null) ? $rowu['id'] : $fromID;

 	// VERIFICAR QUE EXISTA UNA AMISTAD
 			if(areFriends($toID, $fromID))
 			{

 				$querydelete = $connect->query("DELETE FROM `friends` WHERE (`player1` = \"". $fromID ."\" AND `player2` = \"". $toID ."\") OR (`player2` = \"". $fromID ."\" AND `player1` = \"". $toID ."\") LIMIT 2");

		#-> ELMINAR NOTIFICACIONES ENVIADAS
 					if ($querydelete)
 					{
 						$selectN = $connect->query("SELECT `id` FROM `players_notifications` WHERE (`fromid` = \"". $fromID ."\" AND `toid` = \"". $toID ."\") OR (`toid` = \"". $fromID ."\" AND `fromid` = \"". $toID ."\")");
 							if($selectN AND $selectN->num_rows > 0)
 							{
 								while($notification = $selectN->fetch_assoc())
 								{
 									deleteNotification($notification['id']);
 								}
 							}
 						}
 					}
 				}

	/**
	 * Elimina una notificación
	 * @param  int $idNotification ID de notificación
	 * @return boolean
	 */
	function deleteNotification($idNotification = null)
	{
		global $connect;

		if($connect->query("DELETE FROM `players_notifications` WHERE `id` = \"". $idNotification ."\" LIMIT 1"))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Elimina una sala de chat y sus dependencias
	 * @param  int $idRoom ID de la sala
	 * @return boolean
	 */
	function deleteRoomChat($idRoom = null)
	{
		global $connect;

		// VERIFICA QUE EXISTA EL ROOM
		if(checkStateChatRoom($idRoom) != false)
		{
			/* ELIMINA FOTOS DE MENSAJES ENVIADAS O RECIBIDAS */
			$query = $connect->query("SELECT `rutadefoto` FROM `nuevochat_mensajes` WHERE `id_chat` = \"". $idRoom ."\"");
  		// COMPROBAR SI TIENE MENSAJES CON FOTOS
			if ($query == true && $query->num_rows > 0)
			{
  			// ELIMINAR FOTOS
				$msg = $query->fetch_assoc();
				deletePostImages($msg['rutadefoto']);
			}

			// ELIMINAR MENSAJES
			$query = $connect->query("DELETE FROM `nuevochat_mensajes` WHERE `id_chat` = \"". $idRoom ."\"");
			// ELIMINAR ROOM
			$query = $connect->query("DELETE FROM `nuevochat_rooms` WHERE `id` = \"". $idRoom ."\"");
		}
	}

	/**
     * Redireccionar a un enlace
     */
	function redirectTo($url)
	{
		$url = urldecode($url);
		if(isset($_POST['ajax']) || isset($_GET['page'])) {
			echo "0: Redirigiendo... <script>window.location.href = '$url'</script>";
		} else {
			echo '<meta http-equiv="refresh" content="0;url='.$url.'">';
		}
		exit;
	}

	/*
	Devuelve la primera palabra de un string
	 */
	function getFirstWord($text = '')
	{
		$t = explode(' ', $text);
		return $t[0];
	}

	/**
	 * Cambia de usuario actual (Logearse en otro usuario)
	 * @param  int 	$userTo Usuario a cambiar
	 * @param  int 	$time   Duracion de session
	 * @return boo
	 */
	function changerUser($userTo, $time = null)
	{
		global $connect;

		$time = ($time == null) ? (time() + (60*60*24*365)) : $time;

	// SELECCIONA USUARIO EN LA BBDD
		$UserTo = getColumns('players', array('id','username'), array('id',$userTo));

		// COMPRUEBA QUE EXISTA
		if($UserTo != false)
		{
			if(!isset($_COOKIE['returnUser']))
			{
			// PREDEFINIR USUARIO DE RETORNO
				setcookie('returnUser', base64_encode($_COOKIE['eluser']), $time);
			}
			// CAMBIAR DE USUARIO
			setcookie('eluser', $UserTo['username'],$time);

			return true;
		}
		return false;
	}

	/**
	 * Agrega un usuario a la lista de nombres (LDN)
	 * @param id $userid [description]
	 */
	function addToLDN($userid = null)
	{
		global $connect;

		// De no estar registrado
		if (!getUserLDN($userid))
		{
			// Registralo
			$addname = $connect->query("INSERT INTO `players_namesactions` (player_id,player_add,time) VALUES ('$userid','$rowu[id]',\"". time() ."\")");
		}
	}

	/**
	 * Devuelve true si un usuario está en la lista de nombres (LDN)
	 * @param  [type] $userid [description]
	 * @return [type]         [description]
	 */
	function getUserLDN($userid = null)
	{
		global $connect;
		$findnames = $connect->query("SELECT * FROM `players_namesactions` WHERE `player_id`='$userid'");

		if ($findnames->num_rows>0) return true;
		return false;
	}

	/**
	 * Devuelve datos de un regalo de creditos
	 * @param  numeric $idGift
	 * @return array
	 */
	function getGiftCredits($idGift = null)
	{
		global $connect;

		$query = $connect->query('SELECT * FROM `players_gifts` WHERE `id` = \''. $idGift .'\'');

		if($query and $query->num_rows > 0)
		{
			return json_encode($query->fetch_assoc());
		}
		else
		{
			return false;
		}
	}

	function url_origin( $s = null, $use_forwarded_host = false )
	{
		global $sitio;
		$s = ($s == null) ? $_SERVER : $s;
		$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
		$sp       = strtolower( $s['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port     = $s['SERVER_PORT'];
		$port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
		$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
		$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
		return $protocol . '://' . $host . $sitio['site'];
	}

	function getFullUrl( $s = null, $use_forwarded_host = false )
	{
		$s = ($s == null) ? $_SERVER : $s;
		return url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
	}

	function getCurrentURL()
	{
		$ssl   = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
		$proto = strtolower($_SERVER['SERVER_PROTOCOL']);
		$proto = substr($proto, 0, strpos($proto, '/')) . ($ssl ? 's' : '' );
		if ($forwarded_host && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
		} else {
			if (isset($_SERVER['HTTP_HOST'])) {
				$host = $_SERVER['HTTP_HOST'];
			} else {
				$port = $_SERVER['SERVER_PORT'];
				$port = ((!$ssl && $port=='80') || ($ssl && $port=='443' )) ? '' : ':' . $port;
				$host = $_SERVER['SERVER_NAME'] . $port;
			}
		}
		$request = $_SERVER['REQUEST_URI'];
		return $proto . '://' . $host . $request;
	}

	/**
	 * Elimina a un usuario de la lista de nombres
	 */
	function removeUserLDN($userid = null)
	{
		global $connect;
		// Busca el id en la lista
		$findnames = $connect->query("SELECT * FROM `players_namesactions` WHERE `player_id`='$userid'");
		// Si está en la bbdd
		if ($findnames->num_rows > 0)
		{
			// Elimina
			$remove = $connect->query("DELETE FROM `players_namesactions` WHERE `player_id` = $userid");
			if($remove)
			{
				return 1;
			}
			return 0;
		}
	}

	function blurImage($image)
	{


		$image = imagecreatefromjpeg($image);
		for ($x=1; $x<=1; $x++)
		{
			imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
		}
		imagepng($image);
		imagedestroy($image);

	}




	/**
	 * Comprueba si existe un chatroom entre dos usuarios
	 * y de existir comprueba si esta abierto
	 * @return [type] [description]
	 */
	function checkChatRoom($usera = null, $userb = null, $checkstate = true)
	{
		global $connect;

		/* Solicita chatroom de existir */
		$existChat = $connect->query("SELECT `id` FROM `nuevochat_rooms` WHERE (player1 = '$usera' AND player2 = '$userb') || (player2 = '$usera' AND player1 = '$userb')");

		/* Comprueba que exista */
		if ($existChat AND $existChat->num_rows > 0)
		{
			$chatroom = $existChat->fetch_assoc()['id'];


			if(!$checkstate)
			{
				return true;
			}

			/* Comprobar estado de chatroom */
			elseif(checkStateChatRoom($chatroom))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function getAllFriends($userid = null)
	{
		global $connect;

		$friend = [];
		$consult = $connect->query("SELECT * FROM `friends` AS f WHERE (f.`player1` = '$userid' OR f.`player2` = '$userid')");

		while ($friends = mysqli_fetch_assoc($consult)) {

			/* Seleccionar amigo */
			if($friends['player1'] == $userid)
			{
				$friend[] = array('id' => $friends['id'], 'friend' => $friends['player2'], 'me' => $friends['player1']);
			}
			else
			{
				$friend[] = array('id' => $friends['id'], 'friend' => $friends['player1'], 'me' => $friends['player2']);
			}
		}
		return $friend;
	}

	/**
	 * Verifica si x usuario está en la lista
	 * @param  int $userid
	 * @return boolean
	 */
	function checkUserInNameSpace($userid)
	{
		global $connect;

		$consult = $connect->query('SELECT `id` FROM `players_namesactions` WHERE `player_id` = \''. $connect->real_escape_string($userid) .'\'');
		/* Comprueba que el usuario esté registrado */
		if($consult and $consult->num_rows > 0)
		{
			return true;
		}
		return false;
	}


	/**
	 * Guarda seleccion del usuario
	 * Para que al recargar la pagina
	 * El usuario aparezca como seleccionado
	 * @param  int $userid
	 * @return [type]
	 */
	function addRememberAction($userid = null)
	{
		global $connect;

		$update = $connect->query('UPDATE `players_namesactions` SET `checked`= 1 WHERE `player_id` = \''. $connect->real_escape_string($userid) .'\'');

		if($update)
		{
			return true;
		}
		return false;
	}

	function addAllRememberAction($data = null)
	{
		global $connect;
		/* Desactiva el checkout de todos los usuarios */
		$connect->query('UPDATE `players_namesactions` SET `checked`= 0');
		/* Guardar seleccion */
		foreach ($data as $nameId)
		{
			addRememberAction($nameId);
		}
	}

	function logs($text)
	{
		echo '<script>console.log(\''. $text .'\')</script>';
	}

	/**
	 * Limpia la url
	 */
	function clear_url()
	{
		?>
		<script type="text/javascript">
			var parametrosUrl = ``;
			var o = new URLSearchParams(parametrosUrl);
			/* o tiene { [ 'ordering', 't1' ], [ 'ordering', 't2' ] } */
			console.log(o.entries());
			var m = new Map(o);
			// m eliminó los duplicados, tiene { 'ordering' => 't2' }
			console.log(m);
			var ns = new URLSearchParams(m);
			/* ns.toString() tiene ordering=t2;*/
			console.log(ns.toString())
			/* cambio la URL:*/
			window.history.replaceState(null,document.title,window.location.origin + window.location.pathname + '?' + ns.toString());
		</script>
		<?php
	}

	function BlockUsersAdminReport($userid = null)
	{
		global $connect;
		/* Seleciona a todas las personas que hicieron reportes */
		$query = $connect->query('SELECT `id`, `author` FROM `reportes`');
		if($query AND $query->num_rows > 0)
		{
			while($UsersReports = $query->fetch_assoc())
			{
				addBlockUser($UsersReports['author'], $userid);
				//$connect->query('DELETE FROM `reportes` WHERE `id` = '. $UsersReports['id']);
			}

		}
	}
