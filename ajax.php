<?php

include 'config.php';

$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

if(!isset($player_id)){
	$player_id = null;
	$uname = $_COOKIE['eluser'];
	if($uname){
		$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname'");
		if($suser){
			$rowu = mysqli_fetch_assoc($suser);
			$player_id = $rowu['id'];
		}
	}
}

if(isset($_GET['changeCode']) && $player_id) {
	$code = $_POST['code'];

	$gifCode = mysqli_query($connect, "SELECT * FROM `gifcodes` WHERE code='$code'");
	if($gifCode && mysqli_num_rows($gifCode)>0 && $code){
		$rowu = mysqli_fetch_assoc($gifCode);
		if($rowu['used'] == 0){
			$creditos = $rowu['creditos'];
			$type = $rowu['type'];
			$time = time();
			$connect->query("UPDATE `players` SET {$type}={$type}+{$creditos} WHERE id='{$player_id}'");
			$connect->query("UPDATE `gifcodes` SET used={$time} WHERE code='{$code}'");

			Echo json_encode([
				'status' => true,
				'code' => $code
			]);
			exit();
		}
	}
	Echo json_encode([
		'status' => false
	]);
	exit();
}

if(isset($_GET['addCodes']) && $player_id) {
	$num = $_POST['num'];
	$value = $_POST['value'];
	$type = $_POST['type'];
	$time = time();
	for ($i=0; $i < $num; $i++) {
		$code = (time()/rand(10,99)) * ($i+1);
		$code = explode('.', $code)[0];
		$connect->query("INSERT INTO `gifcodes` (code, creditos, used, type, created) VALUES ({$code}, {$value}, 0, '{$type}', {$time})");
	}
	Echo json_encode([
		'status' => true
	]);

	exit();
}
if(isset($_GET['updatephoto'])) {
	$idphoto=$_POST['idphoto'];
	$descripcion=$_POST['descripcion'];
	$postType=$_POST['postType'];
	$connect->query("UPDATE `fotosenventa` SET descripcion='$descripcion',type='$postType' WHERE id='{$idphoto}'");
	if($connect){
		Echo json_encode([
			'status' => true,
			'message' => 'Los datos han sido actualizados!'
		]);
	}else{
		Echo json_encode([
			'status' => false,
			'message' => 'Ha ocurrido un error'
		]);
	}

}
if(isset($_GET['DonarCreditos']) && $player_id)
{
	$id = $_POST['id'];
	$Creditos = $_POST['Creditos'];

	// SI TENGO LOS CREDITOS SUFICIENTES PARA DONAR
	if($rowu['eCreditos'] >= $Creditos){
		// SI EL MONTO ES SUPERIOR O IGUAL AL PERMITIDO
		if($Creditos >= $sitio['minToDonate'])
		{

			// RESTAR CREDITOS AL DONADOR
			updateCredits($player_id,'-',$Creditos,5);

			// SUMAR CREDITOS AL DONADO
			updateCredits($id,'+',ceil(($Creditos * 60)/100),5);

			// ENVIAR NOTIFICACION
			$newDonation = mysqli_query($connect, "INSERT INTO `players_notifications` (fromid, toid,not_key,action,read_time) VALUES ('$rowu[id]', '$id', 'newDonation' , '$Creditos' , '0' )");

			// AGREGAR SUBSCRIPCIÓN
			AddListFollow($id);

			// MENSAJE
			Echo json_encode([
				'status' => true,
				'message' => 'Donación enviada!'
			]);
		}
		else
		{
			Echo json_encode([
				'status' => true,
				'message' => 'El mínimo para enviar es de 1.000 Créditos!'
			]);
		}
	}else{
		Echo json_encode([
			'status' => false,
			'message' => 'No tienes suficientes Créditos para donar'
		]);
	}
	exit();
}

function get_comment($where){
	global $connect, $player_id, $sitio;
	$timeonline = time() - 60;

	$querycpp = mysqli_query($connect, "SELECT * FROM `player_comments` {$where}");


	if(!$querycpp && !mysql_num_rows($querycpp)){
		return 'sin resultados';
	}
	$rowcpp = mysqli_fetch_assoc($querycpp);
	$querycpdd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$player_id' LIMIT 1");
	$rowcpdd  = mysqli_fetch_assoc($querycpdd);

	$return = '	<div class="card-comment card text-left">
	<img src="'.$rowcpdd['avatar'].'" style="width:40px;border-radius:30px;">&nbsp;&nbsp;
	<div style="display:inline-block;vertical-align:bottom;">
	<strong>
	<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpdd['id'] . '">'.$rowcpdd['username'].'</a>
	</strong>
	<br>
	'.$rowcpp['comment'].'
	</div>
	<hr>';

	return $return;

}
function get_post($where){
	global $connect, $player_id, $sitio;
	$timeonline = time() - 60;

	$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` {$where}");

	if(!$querycp && !mysql_num_rows($querycp)){
		return 'sin resultados';
	}
	$rowcp = mysqli_fetch_assoc($querycp);
	$author_id = $rowcp['player_id'];
	$querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
	$rowcpd    = mysqli_fetch_assoc($querycpd);

	$return = '<tr>
	<td>
	<div class="card text-left" style="position: relative;">
	<div class="card-header bg-secondary mb-3">
	<img src="'.$sitio['site'].$rowcpd['avatar'].'" class="img-circle" style="width:65px;">
	<strong>
	<div style="display:inline-block;vertical-align:middle;">
	<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpd['id'] . '">' . $rowcpd['username'] . '</a></br>';

	if ($rowcpd['timeonline'] > $timeonline) {
		$return .= '<span style="color:green">online</span>';
	}

	$return .= '</div></strong><br><br>';

	$salasql1 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$player_id' AND player2='$rowcpd[id]'");
	$countsala1      = mysqli_num_rows($salasql1);
	$sala1 = mysqli_fetch_assoc($salasql1);

	$salasql2 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$rowcpd[id]' AND player2='$player_id'");
	$countsala2      = mysqli_num_rows($salasql2);
	$sala2 = mysqli_fetch_assoc($salasql2);

	if ($countsala1 > 0){
		$linkc = 'chat.php?chat_id='. $sala1['id'] .'#chat';
	}elseif($countsala2 > 0){
		$linkc = 'chat.php?chat_id='. $sala2['id'] .'#chat';
	}else{
		$linkc = 'newchat.php?id='.$rowcpd['id'];
	}

	$return .= '<a href="'. $sitio['site'].$linkc .'" class="btn btn-warning" style="position:absolute;top:0;right:0;"><i class="fa fa-comments"></i></a>';

	$return .= '</div><br>
	<div class="card-body comment-emoticons">

	<center>
	<img src="'.$sitio['site'].$rowcp['imagen'].'" width="100%">
	</center><br>
	'.$rowcp['descripcion'].'
	</div>
	<div class="card-footer">';

	$megustasql = mysqli_query($connect, "SELECT * FROM `player_megusta` WHERE player_id='$player_id' AND galeria_id='$rowcp[id]'");
	$countmegusta = mysqli_num_rows($megustasql);

	$totalmegustasql = mysqli_query($connect, "SELECT * FROM `player_megusta` WHERE galeria_id='$rowcp[id]'");
	$totalmegustas = mysqli_num_rows($totalmegustasql);

	if ($countmegusta < 1){
		$isLike = "";
	}else{
		$isLike = "isLike ";
	}
	$return .= '<button type="submit" name="megusta" class="'.$isLike.'btn btn-success float-right" onclick="LikePost(this, '.$rowcp['id'].');">
	<i class="fa fa-thumbs-up"></i>
	</button>+'.$totalmegustas.' likes';

	$return .= '</div>
	</div><br />
	<div class="card">
	<div class="card-body">';
	$querycpp = mysqli_query($connect, "SELECT * FROM `player_comments` WHERE galeria_id='$rowcp[id]' ORDER BY id ASC LIMIT 500");
	$countcpp = mysqli_num_rows($querycpp);
	if ($countcpp > 0) {
		$return .= '<h5 class="card-title"><i class="fa fa-comments"></i> comentarios recientes</h5><hr>';
		while ($rowcpp = mysqli_fetch_assoc($querycpp)) {
			$author = $rowcpp['author_id'];
			$querycpdd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author' LIMIT 1");
			$rowcpdd = mysqli_fetch_assoc($querycpdd);
			$return .= '<div class="card">
			<div class="card-header">
			<img src="'.$rowcpdd['avatar'].'" width="8%">&nbsp;&nbsp;<strong>
			<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpdd['id'] . '">'.$rowcpdd['username'].'</a></a></strong>
			</div>
			</div>
			<hr>';
		}
	} else {
		$return .= '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> sin comentarios</strong></div>';
	}
	$return .= '<form action="" method="post">
	<input type="hidden" name="galeria_id" value="'.$rowcp['id'].'">
	<textarea placeholder="Escribe un comentario" name="comment" class="form-control" required></textarea>
	<br />
	<button type="submit" name="postcomment" class="btn btn-success float-right">
	<i class="fa fa-share"></i> Comentar
	</button>
	</form>
	</div>
	</div></td></tr>';

	return $return;
}

// boton
if (isset($_POST['idparadarcreditos'])){
	$player_id = $_POST['idparadarcreditos'];
	$creditos = $sitio['montoboton'];
	$profittime = time() + (4 * 3600); // cada cuanto tiempo se puede tocar el boton el primero es horas y el segundo segundos

	$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$player_id'");
	$rowu      = mysqli_fetch_assoc($suser);

	if (time() >= $rowu['botontime'] || $rowu['botontime'] == 0) {
		if ($rowu['countboton'] >= 7){ // toques al boton de dar creditos permitidos

			$player_update = mysqli_query($connect, "UPDATE `players` SET creditos=creditos+'$creditos', countboton=0, botontime='$profittime'  WHERE id='$player_id'");

		}else{

			$player_update = mysqli_query($connect, "UPDATE `players` SET creditos=creditos+'$creditos', countboton=countboton+1 WHERE id='$player_id'");

		}
	}

	exit();
}

// ENVIAR MENSAJE

if (isset($_POST['mensaje'])){

	$id_chat = $_POST['idchat'];
	$author  = $_POST['author'];
	$toid    = $_POST['toid'];
	$mensaje = $_POST['mensaje'];
	$mensaje = detectLink($mensaje);
	$time    = time();

	// COMPRUEBA QUE EL CHAT ESTE ABIERTO(DESBLOQUEADO)
	if(checkStateChatRoom($id_chat)=='open')
	{
		//COMPRUEBA QUE EL MENSAJE NO ESTE VACIO
		if($mensaje != '')
		{
			$post_mensaje = mysqli_query($connect, "INSERT INTO `nuevochat_mensajes` (id_chat, author, toid, mensaje, time) VALUES ('$id_chat', '$author', '$toid', '$mensaje', '$time')");

			// OBTIENE EL ULTIMO ID INSERTADO
			$lasidinsert = mysqli_insert_id($connect);

			// CONSULTA EL MENSAJE CON ESE ID
			$query = $connect->query("SELECT *, nm.id AS id FROM `nuevochat_mensajes` AS nm INNER JOIN players AS p ON p.`id` = nm.`author` WHERE nm.`id` = '$lasidinsert'");

			if ($query AND $query->num_rows > 0)
			{
				// GUARDA EL MESAJE EN UN ARRAY
				while ($messages = $query->fetch_assoc())
				{
					$msg[] = $messages;
				}
				// MUESTRA EL MENSAJE
				echo json_encode($msg);
			}

			$update_time_room = mysqli_query($connect, "UPDATE `nuevochat_rooms` SET time='{$time}' WHERE id='{$id_chat}'");

			$queryc = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE id = '{$id_chat}'");
			$revision = mysqli_fetch_assoc($queryc);
			if ($revision['player1'] == $player_id){
				$elamigo = $revision['player2'];
			}else{
				$elamigo = $revision['player1'];
			}

			$query = mysqli_query($connect, "SELECT * FROM `respuesta_automatica` WHERE uid='{$player_id}' ORDER BY id DESC");

			if($query){
				while ($RSPDa = mysqli_fetch_assoc($query)) {
					mysqli_query($connect, "DELETE FROM `respuesta_sala` WHERE chat_room='{$id_chat}' AND pregunta_id='{$RSPDa["id"]}' AND publicado='no'");
				}
			}

			$query = mysqli_query($connect, "SELECT * FROM `respuesta_automatica` WHERE uid='{$elamigo}' ORDER BY id DESC");

			if($query){
				while ($RSPDa = mysqli_fetch_assoc($query)) {
					$Rdrs = explode(',', trim($RSPDa["pregunta"]));
					$pross = true;
					$mensaje = "##::".$mensaje;
					foreach($Rdrs as $itm){
						if(!strpos($mensaje, $itm)){
							$pross = false;
						}
					}
					$DelayTime = time() + (60*10);
					$ssda = mysqli_query($connect, "SELECT * FROM `respuesta_sala` WHERE chat_room='{$id_chat}' AND pregunta_id='{$RSPDa["id"]}'");
					$SDRT = mysqli_fetch_assoc($ssda);
					if($pross && !$SDRT){

						mysqli_query($connect, "INSERT INTO `respuesta_sala` (chat_room, pregunta_id, publicado, time) VALUES
							('{$id_chat}', '{$RSPDa["id"]}', 'no', '{$DelayTime}')");
						break;
					}
				}
			}
		}
	}

	// revisando si el mensaje va hacia un bot
	$sqlrevisiondebot = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$toid'");
	$rowubot = mysqli_fetch_assoc($sqlrevisiondebot);

	if ($rowubot['role'] == 'BOT'){
		//vemos que el bot no ha respondido y que no se ha insertado una solicitud de respuesta

		$sqlrevisiondemensjbot = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE id='$id_chat'");

		$revisiondemensjbot = mysqli_fetch_assoc($sqlrevisiondemensjbot);

		if ($revisiondemensjbot['mensaje_chatbot'] == 0){


			$sqlrevisiondesolic = mysqli_query($connect, "SELECT * FROM `respuestasbot_enespera` WHERE chat_id='$id_chat'");

			$countrevisiondesoclic = mysqli_num_rows($sqlrevisiondesolic);

			if ($countrevisiondesoclic < 1){

				$mensajedebot = $rowubot['respuesta_automatica'];
				$respuestatime = time() + ($rowubot['tiempoderespuesta'] * 60);

				$sqlfotodebot = mysqli_query($connect, "SELECT * FROM `fotosbot` WHERE lista_id='$rowubot[id_listadefotos]' ORDER BY RAND() LIMIT 1");
				$rowufotodebot = mysqli_fetch_assoc($sqlfotodebot);

				$fotodebot = $rowufotodebot['rutadefoto'];
				// insertar la solicitud de respuesta del bot
				$post_solicitudbot = mysqli_query($connect, "INSERT INTO `respuestasbot_enespera` (bot_id, chat_id, toid, mensaje, foto, respuesta_time) VALUES ('$toid', '$id_chat', '$author', '$mensajedebot', '$fotodebot', '$respuestatime')");

			}

		}

	}

	$sqlrevisiondecantidaddemensajes = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE chat_id='$id_chat'");

	if($sqlrevisiondecantidaddemensajes){

		$countmensajes = mysqli_num_rows($sqlrevisiondecantidaddemensajes);

		if($countmensajes > 20){

			$mensajeaborrar = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE chat_id='$id_chat' ORDER BY id ASC LIMIT 1");
			$msjdelete  = mysqli_fetch_assoc($mensajeaborrar);

			$borrarmensaje = mysqli_query($connect, "DELETE FROM `nuevochat_mensajes` WHERE id='$msjdelete[id]'");

		}

	}

	exit();
}

//comentarios

if (isset($_GET['postComment'])) {
	$comment = $_POST['comment'];
	$galeria_id = $_POST['galeria_id'];
	$author = $player_id;
	$date = date('d F Y');
	$time = date('H:i');

	$querycpc = mysqli_query($connect, "SELECT * FROM `player_comments` WHERE author_id='$player_id' AND galeria_id='$galeria_id' AND comment='$comment' AND date='$date' LIMIT 1");
	$countcpc = mysqli_num_rows($querycpc);
	if ($countcpc == 0) {

		if(mysqli_query($connect, "INSERT INTO `player_comments` (galeria_id, author_id, comment, date, time) VALUES ('$galeria_id', '$player_id', '$comment', '$date', '$time')")){

			Echo json_encode([
				'status' => true,
				'message' => get_comment("WHERE comment='{$comment}' AND date='{$date}' AND time='{$time}'")
			]);
		}

		$querycpbrr1 = mysqli_query($connect, "SELECT * FROM `player_comments` WHERE galeria_id='$galeria_id'");
		if($querycpbrr1){
			$countcpbrr = mysqli_num_rows($querycpbrr1);
			if ($countcpbrr > 500) {
				$querycpbrr2 = mysqli_query($connect, "SELECT * FROM `player_comments` WHERE galeria_id='$galeria_id' ORDER BY id ASC LIMIT 1");
				$rowbrr = mysqli_fetch_assoc($querycpbrr2);
				$brrcmnt = $rowbrr['id'];
				$brr = mysqli_query($connect, "DELETE FROM `player_comments` WHERE id='$brrcmnt'");
			}
		}
		exit();
	}

	Echo json_encode([
		'status' => false,
		'message' => "Error enviando comentario."
	]);

	Exit();
}

function admCreditos($gid, $value="+"){
	global $connect, $player_id;
	$post = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE id='{$gid}'");
	if($post && mysqli_num_rows($post)){
		$post = mysqli_fetch_assoc($post);
		$connect->query("UPDATE `players` SET Likesdados=Likesdados{$value}1 WHERE id='{$player_id}'");
		$connect->query("UPDATE `players` SET LikesRecibidos=LikesRecibidos{$value}1 WHERE id='{$post['player_id']}'");
	}
}

//me gusta

if (isset($_GET['like'])) {
	$gid = $_POST['gid'];

	$querycpc = mysqli_query($connect, "SELECT * FROM `player_megusta` WHERE player_id='$player_id' AND galeria_id='$gid' LIMIT 1");
	if ($querycpc && !mysqli_num_rows($querycpc)) {
		if(mysqli_query($connect, "INSERT INTO `player_megusta` (player_id, galeria_id) VALUES ('$player_id', '$gid')")){
			Echo json_encode([
				'status' => true,
				'message' => "Megusta Añadido!!"
			]);

			admCreditos($gid, '+');
		}
		Exit();
	}else{
		if(mysqli_query($connect, "DELETE FROM player_megusta WHERE player_id='$player_id' AND galeria_id='$gid'")){
			Echo json_encode([
				'status' => false,
				'message' => "Megusta Removido!!"
			]);

			admCreditos($gid, '-');
		}
		Exit();
	}
	Echo json_encode([
		'status' => false,
		'message' => "Error"
	]);
	Exit();
}

///


if(isset($_GET['addProgramMessagesMass'])) {

	// RECORRE TODOS LOS MENSAJES
	foreach ($_POST['image'] as $index => $File) {
		$FileDirName = null;
		if($File && $File!='none') {
			$image = str_replace("data:image/png;base64,", '', $File);
			$image = str_replace("data:image/jpg;base64,", '', $image);
			$image = str_replace("data:image/jpeg;base64,", '', $image);
			$image = str_replace("data:image/gif;base64,", '', $image);
			$image = base64_decode($image);

			$FileName = $player_id .'-'. time() .'-'. rand(100, 999) .'.jpg';
			$target_dir = "uploads/";
			$FileDirName = $target_dir . $FileName;

			file_put_contents($FileDirName, $image);
		}
		// ALMACENA EL DIA DEL ULTIMO MENSAJE PROGRAMADO
		$lastDay = getLastDayMessageScheduled();

		//CAMBIAR DIA A UNIX//
		if($lastDay != NULL) $lastDay = ($lastDay * (60*60*24)); else $lastDay = 0;

		// PROGRAMA EL MENSAJE
		ProgramarMenssage($FileDirName, $index, $lastDay);
	}

	Echo json_encode(['status' => true]);
	exit();
}

function ProgramarFoto($ImageDir, $index){
	global $player_id, $connect,$rowu;

	$descripcion = $_POST['descripcion'][ $index ];
	$postType = $_POST['postType'];
	$date = time();
	$numsave=0;
	if($_POST['dias'][ $index ]==0 && $_POST['horas'][ $index ]==0 && $_POST['minutos'][ $index ]==0){
		/*---EVITAR DIAS REPETIDOS---*/
		$sql = mysqli_query($connect, "SELECT * FROM fotosprogramadas WHERE player_id=$player_id");
		if(mysqli_num_rows($sql)>0){
			$b=0;
			$a=0;
			//ALMACENA FILAS Y COLUMNAS DE LA CONSULTA EN UN ARRAY
			while($arrow = mysqli_fetch_row($sql)) {
				for ($i=0; $i < 6; $i++) {
					$row[$a][$i]=$arrow[$i];
				}
				$a++;
			}
			$numsave=0;
			//BUSCAR CUALES PROGRAMACIONES ESTAN Y CUALES FALTAN
			for($a=0; $a<mysqli_num_rows($sql); $a++){
				if (isset($row[$a]) and !empty($row[$a])) {
					for($b=0; $b<mysqli_num_rows($sql); $b++){
						if (isset($row[$b]) and !empty($row[$b]))
						{
							if (TimeNextHours($row[$b][5])==TimeNextHours($row[$a][5])){
								//echo TimeNextHours($row[$b][5])." AND ".TimeNextHours($row[$a][5])."<br>";
								$save[$numsave]=$row[$b][5];
								$saveday[$numsave]=TimeNextHours($row[$b][5]);
								$numsave++;
							}
						}
					}
				}
			}
			$a=0;
			//SELECCIONAR LOS NUMEROS(DIAS) QUE NO ESTAN EN UN RANGO DE X A X NUMEROS
			$range=range(0,max($saveday));
			$missingValues = array_diff($range,$saveday);
			foreach ($missingValues as $key => $value) {
				//SACAR AL 0 PARA EVITAR COLOCAR "0 DIAS"
				if($key!=0){
					$missing[$a]=$key;
					$a++;
				}
			}
		//SI NO HAY DIAS REPETIDOS EN LAS FOTOS PROGRAMADAS
			if(!isset($missing) or empty($missing)){
				$sql = mysqli_query($connect, "SELECT * FROM fotosprogramadas WHERE player_id=$player_id ORDER BY time DESC");
					//SELECCIONA EL ULTIMO DIA PROGRAMADO
				$arrow1=mysqli_fetch_assoc($sql);
				$missing[0]=TimeNextHours($arrow1['time'])+1;
			}
		//SI NO EN FOTOS PRAGAMADAS
		}else{
			//QUE LA PRIMERA PROGRAMACION SEA DE UN DIA
			$missing[0]=1;
		}
		$d = $missing[0] * (60*60*24);
		$date = $date + $d;

		$d = rand(0, 24) * (60*24);
		$date = $date + $d;

		$d = rand(0, 60) * (60);
		$date = $date + $d;
	}else{
		if($_POST['dias'][ $index ]>0){
			$d = $_POST['dias'][ $index ] * (60*60*24);
			$date = $date + $d;
		}
		if($_POST['horas'][ $index ]>0){
			$d = $_POST['horas'][ $index ] * (60*60);
			$date = $date + $d;
		}
		if($_POST['minutos'][ $index ]>0){
			$d = $_POST['minutos'][ $index ] * (60);
			$date = $date + $d;
		}
	}
	//CONVERTIR EN JSON
	$ImageDir = '["' . $ImageDir . '"]';

	mysqli_query($connect, "INSERT INTO `fotosprogramadas` (player_id, imagen, descripcion, type, time,category) VALUES
		('{$player_id}', '{$ImageDir}', '{$descripcion}', '{$postType}', '". $date ."','$rowu[category]')");
}

if(isset($_GET['addprograma'])) {

	foreach ($_POST['file'] as $index => $File) {
		$image = str_replace("data:image/png;base64,", '', $File);
		$image = str_replace("data:image/jpg;base64,", '', $image);
		$image = str_replace("data:image/jpeg;base64,", '', $image);
		$image = str_replace("data:image/gif;base64,", '', $image);
		$image = base64_decode($image);

		$FileName = $player_id .'-'. time() .'-'. rand(100, 999) .'.jpg';
		$target_dir    = "shout/galeria/";

		file_put_contents($target_dir . $FileName, $image);

		ProgramarFoto($target_dir . $FileName, $index);
	}

	Echo json_encode(['status' => true]);
	exit();
}

if(isset($_GET['addGalery'])) {

	$time = time();
	$dia = date('d', time());
	$SelectPhonoDay = mysqli_query($connect, "SELECT *, DATE_FORMAT(FROM_UNIXTIME( time),'%d') AS day FROM fotosenventa WHERE player_id = '$player_id' AND DATE_FORMAT(FROM_UNIXTIME( time),'%d') = '$dia'");
	if($SelectPhonoDay->num_rows >= 15){
		Echo json_encode([
			'status' => false,
			'message' => "Llegaste al limite de imagenes por dia."
		]);
		exit();
	}
	$gender = $_POST['gender'];
	$descripcion = $_POST['descripcion'];
	$downloadable = $_POST['downloadable'];
	$postType = $_POST['postType'];
	$error=0;
	//SI EL USUARIO INGRESO UN LINK EXTERNO
	if(isset($_POST['linkext']) and !empty($_POST['linkext'])) {
		//VERIFICA QUE SEA UN LINK
		if(filtrourl($_POST['linkext'])=="error"){
			//SI NO ES, NOTIFICAR AL USUARIO
			Echo json_encode([
				'status' => false,
				'message' => "link-incorrect"
			]);
			exit;
		}else{
			$linkext = $_POST['linkext'];
		}
		//SI NO EXITE ALGUNA IMAGEN, SUBIR A LA BBDD SIN IMAGENES
		if(!isset($_FILES["fotoFile"]) or empty($FILE_["fotoFile"])) {
			if(!$insertarcompra = mysqli_query($connect, "INSERT INTO `fotosenventa` (player_id, descripcion,linkdedescarga, type,downloadable, time,category) VALUES
				('$player_id', '$descripcion', '$linkext' , '{$postType}','$downloadable', '". $time ."','$gender')")){
				$error=2;
				Echo json_encode([
					'status' => false,
					'message' => "error guardando la publicacion"
				]);
				exit;
			}else{
				Echo json_encode([
					'status' => true,
					'message' => "Imagen subida con exito."
				]);
				exit;
			}
		}
	//SI NO HAY URL, COLOCARLA EN BLANCO
	}else{
		$linkext=" ";
	}
	if(!isset($player_id)){
		Echo json_encode([
			'status' => false,
			'message' => "debes iniciar session para continuar."
		]);
	}

	/// imagen
	$uname = $_COOKIE['eluser'];
	$Images = [];
	$thumbjson = [];
	$countvideos=0;
	$countimages=0;
	$countfiles=count($_FILES["fotoFile"]["name"]);
		foreach($_FILES["fotoFile"]["name"] as $key => $FILE)
		{
			$token = rand(111,999);
			$target_dir    = "shout/galeria/";
			$target_file   = $target_dir . basename($_FILES["fotoFile"]["name"][$key]);
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			//No permitir subir + de 1 videos ni videos con imagenes

				if (getSourceType($target_file)=="film")
				{
					$countvideos++;
				}else{
					$countimages++;
				}

			if (getSourceType($target_file)=="film" and $countimages>0) {
				continue;
			}

			$filename      = $token . ( $token*time() ) . $uname . '.' . $imageFileType;
			$imagen        = "shout/galeria/" . $filename;
			if(move_uploaded_file($_FILES["fotoFile"]["tmp_name"][$key], "shout/galeria/" . $filename))
			{

				$thumb = 'thumb/'. $token. ( $token*time() ) . $uname .'.jpg';

			//ALMACENAR EN ARRAY
				$Images[] = $imagen;
				$thumb1[]= $thumb;


			//CREAR MINIATURA
				if (getSourceType($target_file)!="film")
				{
					createThumbnail($imagen, $thumb, 300);
				}

			}
			else
			{
				$error=1;
			}
			if($countvideos>0){
				break;
			}
		}
	$jsonImages = json_encode($Images);
	$thumbjson  = json_encode($thumb1);
	if(!$error){
		if(!$insertarcompra = mysqli_query($connect, "INSERT INTO `fotosenventa` (player_id, imagen, thumb, descripcion,linkdedescarga, type,downloadable, time,category) VALUES
			('$player_id', '$jsonImages', '$thumbjson', '$descripcion', '$linkext' , '{$postType}','$downloadable', '". $time ."','$gender')")){
			$error=2;
		}else{
			if ($countfiles>1 and $countvideos>0)
			{
				$message="Video subido con exito! Recuerda que solo puedes subir un video por publicacion.";
			}
			elseif ($countfiles=1 and $countvideos>0)
			{
				$message="Video subido con exito! Recuerda que solo puedes subir un video por publicacion.";
			}
			elseif ($countimages>0 and $countvideos>0)
			{
				$message="Publicacion relizada con exito! Recuerda que solo puedes subir videos sin imagenes.";
			}
			elseif ($countimages>0 and $countvideos<=0)
			{
				$message="Imagen subida con exito!";
			}

			Echo json_encode([
			'status' => true,
			'message' => $message
			]);

		}
	}else{
		unlinkJSON($Images);
		unlinkJSON($thumb1);
		Echo json_encode([
			'status' => false,
			'message' => "error guardando la foto."
		]);
		exit();
	}

	if($error == 2){
		unlinkJSON($Images);
		unlinkJSON($thumb1);
		Echo json_encode([
			'status' => false,
			'message' => "error guardando la publicacion."
		]);
		exit();
	}

	//enviando notificacion a amigos

	$sqlnotificandoamigos = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1 = '$player_id' OR player2 = '$player_id'");
	while ($rowamigo = mysqli_fetch_assoc($sqlnotificandoamigos)) {

		if ($rowamigo['player2'] == $player_id){
			$amigo = $rowamigo['player1'];
		}elseif ($rowamigo['player1'] == $player_id){
			$amigo = $rowamigo['player2'];
		}
		$insertarcompra = mysqli_query($connect, "INSERT INTO `notificaciones_fotosnuevas` (player_notificador, player_notificado) VALUES ('$player_id', '$amigo')");
	}
	// BORRA LA <<FOTO DE REGALO>>
	$connect->query('TRUNCATE `photo_gift_credits`');

	exit();
}
//get user
if (isset($_POST['get_user'])){
	$username=$_POST['get_user'];
	$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$username'");
	//Guardamos los datos en un array
	if(mysqli_num_rows($suser)>0){
		$user=mysqli_fetch_assoc($suser);
		$datos = array(
			'state' => 'ok',
			'id' => $user['id'],
			'username' => $user['username'],
			'email' => $user['email'],
			'avatar' => $user['avatar']
		);
	//Devolvemos el array pasado a JSON como objeto
		echo json_encode($datos, JSON_FORCE_OBJECT);
	}else{
		$datos = array(
			'state' => 'error'
		);
		echo json_encode($datos, JSON_FORCE_OBJECT);
	}
}

//Notificaciones
if (isset($_POST['notify']) or isset($_GET['notify'])){

	$uname = $_COOKIE['eluser'];
	$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname'");
	if(mysqli_num_rows($suser)>0){
		$rowu = mysqli_fetch_assoc($suser);
		$player_id = $rowu['id'];

		$query = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1 = '$player_id' OR player2 = '$player_id'");
		$rooms = [];
		if($query){
			while ($room = mysqli_fetch_assoc($query)) {
				$rooms[] = $room['id'];
			}
		}

		$Mensajes = 0;

		// OPTIENE LOS MENSAJES NO LEIDOS DE "$player_id"
		$Mensajes = $connect->query("SELECT * FROM `nuevochat_mensajes` AS nm INNER JOIN nuevochat_rooms AS nr ON (nr.`player1` = '$player_id' || nr.`player2` = '$player_id') AND nr.`id` = nm.`id_chat` WHERE leido = IF(author = '$player_id','no devolver mensajes','no')")->num_rows;

		$sqlnovistos = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE toid='{$player_id}' AND read_time='0'");
		$Notificaciones   = mysqli_num_rows($sqlnovistos);

		$sqlfotonovista = mysqli_query($connect, "SELECT * FROM `notificaciones_fotosnuevas` WHERE player_notificado='{$player_id}' AND visto='no'");
		$Fotos = mysqli_num_rows($sqlfotonovista);

		$Uquestion = $connect->query("SELECT id FROM `players_questions` WHERE `toid` = '$rowu[id]' AND `read_time` = 0")->num_rows;
		Echo json_encode([
			"Msg" => $Mensajes,
			"notify" => $Notificaciones,
			"Fotos" => $Fotos,
			"Packs" => get_Notification_pack(true),
			"Encuesta" => get_Notification_encuesta(true),
			"Questions" => $Uquestion
		]);
	}
	exit();
}
//CAMBIA FOTO DE PORTADA
if (isset($_GET['add_cover-page'])) {

	//SOLO SI ES EL DUEÑO DEL PERFIL
	if ($_POST['id']==$rowu['id']) {

		//SI LA IMAGEN SUPERA EL LIMITE.
		# DEFAULT 300KB
		if ($_FILES["fotoFile"]["size"]>300100)
		{
			Echo json_encode([
				'status' => false,
				'message' => 'El tamaño de la imagen excede el límite permitido!'
			]);
			exit;
		}

		//DECLARACION DE DIRECTORIOS
		$id=$_POST['id'];
		$token = rand(111,999);
		$target_dir    = "shout/cover-pages/";
		$target_file   = $target_dir . basename($_FILES["fotoFile"]["name"]);
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
		$error=0;
		$filename      = "cover-page-" . $token . ( $token*time() ) . $uname . '.' . $imageFileType;
		$imagen        = "shout/cover-pages/" . $filename;

		//DE NO SER UNA IMAGEN
		if (getSourceType($target_file)!="camera")
		{
			Echo json_encode([
				'status' => false,
				'message' => 'Selecciona solo Imagenes!'
			]);
			exit;
		}

		//SI LA IMAGEN SE ALMACENA CORRECTAMENTE
		if(move_uploaded_file($_FILES["fotoFile"]["tmp_name"], "shout/cover-pages/" . $filename))
		{

			$thumb = 'thumb/cover-page-'. $token. ( $token*time() ) . $uname .'.jpg';
		}
		else
		{
			$error=1;
		}

		//SUBE LA DIRECCION DE LA IMAGEN A LA BBDD
		if(!$error){

			/*/CREAR MINIATURA
			createThumbnail($imagen, $thumb, 1500);	*/

			//BORAR FOTO PORTADA ANTERIOR
			$coverpage = mysqli_query($connect, "SELECT `cover-page` FROM `players` WHERE id=$id");
			if ($coverpage) {
				$coverpage = mysqli_fetch_assoc($coverpage);
				unlink($coverpage['cover-page']);
			}

			//ACTUALIZAR PORTADA
			if(!$insertarcompra = mysqli_query($connect, "UPDATE `players` SET `cover-page`='$imagen' WHERE id=$id ")){
				$error=2;
			}else{
				Echo json_encode([
				'status' => true,
				'message' => 'Imagen subida con exito!'
				]);

			}

		}else{
			unlink($imagen);
			Echo json_encode([
				'status' => false,
				'message' => "Ha ocurrido un error al subir la foto. Intentalo de nuevo"
			]);
			exit();
		}
	}
}

//AGREGA UN NUEVO ITEM PARA X MUNDO
if(isset($_GET['additem'])) {

	//TIENEN QUE ESTAR TODOS LOS PARAMETROS
	if (isset($_POST['name']) AND !empty($_POST['name']) AND isset($_FILES['files']) AND !empty($_FILES['files']) AND isset($_POST['price']) AND !empty($_POST['price']) AND isset($_POST['pos_x']) AND !empty($_POST['pos_x']) AND isset($_POST['pos_y']) AND !empty($_POST['pos_y']) AND isset($_POST['farm']) AND !empty($_POST['farm']) AND isset($_POST['size']) AND !empty($_POST['size']) )
	{

		//SE INICIALIZAN LAS VARIABLES
		$name = $_POST['name'];
		$files = $_FILES['files'];
		$price = $_POST['price'];
		$pos_x = $_POST['pos_x'];
		$pos_y = $_POST['pos_y'];
		$farm = $_POST['farm'];
		$size = $_POST['size'];
		$time = time();
		if (isset($_POST['produces']) AND !empty($_POST['produces'])) {
			$produces = $_POST['produces'];
		} else {
			$produces=0;
		}


		//OBTENGO EL NOMBRE DEL MUNDO PARA VERIFICAR QUE EXISTE
		$farms = mysqli_query($connect, "SELECT id FROM `farms` WHERE name='$farm'");
		if ($farms and mysqli_num_rows($farms)>0)
		{

			$rowfarms=mysqli_fetch_assoc($farms);

			//MOVER IMAGEN AL DIRECTORIO
			$token = rand(111,999);
			$target_dir    = "shout/cover-pages/";
			$target_file   = $target_dir . basename($_FILES["files"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$error=0;
			$filename      = $farm . "-" . $token . ( $token*time() ) . '.' . $imageFileType;
			$imagen        = "assets/img/items/" . $filename;

			//DE NO SER UNA IMAGEN
			if (getSourceType($target_file)!="camera")
			{
				Echo json_encode([
					'status' => false,
					'message' => 'Selecciona solo Imagenes!'
				]);
				exit;
			}

			//SI LA IMAGEN SE ALMACENA CORRECTAMENTE
			if(move_uploaded_file($_FILES["files"]["tmp_name"], $imagen))
			{

			}
			else
			{
				Echo json_encode([
					'status' => false,
					'message' => 'La imagen no pudo ser almacenada'
				]);
				exit;
			}

			//SE INSERTA EL ITEM
			if($connect->query("INSERT INTO `farm_items` (farms_id, name, image,size, position_x, position_y,price,produces) VALUES ($rowfarms[id], '$name', '$imagen', '$size' , '$pos_x', '$pos_y','$price','$produces')")){
				Echo json_encode([
					'status' => true,
					'message' => 'Item agregado!'
				]);
				exit;
			}
			else
			{
				Echo json_encode([
					'status' => false,
					'message' => 'No se pudo agregar el item'
				]);
				exit;
			}

		}
		else
		{
			Echo json_encode([
				'status' => false,
				'message' => "No existe ese Mundo!"
			]);
			exit();
		}
	}
	else
	{
		Echo json_encode([
			'status' => false,
			'message' => "Ingrese todos los datos correctamente"
		]);
		exit();
	}

}
//AGREGA MUNDO NUEVO
if(isset($_GET['addfarm'])) {

	//TIENEN QUE ESTAR TODOS LOS PARAMETROS
	if (isset($_POST['name']) AND !empty($_POST['name']) AND isset($_FILES['files']) AND !empty($_FILES['files']))
	{

		//SE INICIALIZAN LAS VARIABLES
		$name = $_POST['name'];
		$files = $_FILES['files'];
		if (isset($_POST['price']) AND !empty($_GET['POST'])) {
			$price = $_POST['price'];
		} else {
			$price=0;
		}

		//OBTENGO EL NOMBRE DEL MUNDO PARA VERIFICAR QUE NO EXISTE OTRA CON ESE NOMBRE
		$farms = mysqli_query($connect, "SELECT id FROM `farms` WHERE name='$name'");
		if ($farms and !mysqli_num_rows($farms)>0)
		{

			//MOVER IMAGEN AL DIRECTORIO
			$token = rand(111,999);
			$target_dir    = "assets/img/farms";
			$target_file   = $target_dir . basename($_FILES["files"]["name"]);
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
			$error=0;
			$filename      = $name . "-" . $token . ( $token*time() ) . '.' . $imageFileType;
			$imagen        = "assets/img/farms/" . $filename;

			//DE NO SER UNA IMAGEN
			if (getSourceType($target_file)!="camera")
			{
				Echo json_encode([
					'status' => false,
					'message' => 'Selecciona solo Imagenes!'
				]);
				exit;
			}

			//SI LA IMAGEN SE ALMACENA CORRECTAMENTE
			if(move_uploaded_file($_FILES["files"]["tmp_name"], $imagen))
			{
				//CREAR MINIATURA
				$thumb = 'assets/img/farms/thumb-'.$name ."-". $token. ( $token*time() ) .'.jpg';
				createThumbnail($imagen, $thumb, 300);
			}
			else
			{
				Echo json_encode([
					'status' => false,
					'message' => 'La imagen no pudo ser almacenada'
				]);
				exit;
			}

			//SE INSERTA EL ITEM
			if($connect->query("INSERT INTO `farms` (name, image,thumbnail, price) VALUES ('$name', '$imagen','$thumb','$price')")){
				Echo json_encode([
					'status' => true,
					'message' => 'Mundo agregado!'
				]);
				exit;
			}
			else
			{
				Echo json_encode([
					'status' => false,
					'message' => 'No se pudo agregar el Mundo'
				]);
				exit;
			}

		}
		else
		{
			Echo json_encode([
				'status' => false,
				'message' => "Ya existe un Mundo con ese nombre!"
			]);
			exit();
		}
	}
	else
	{
		Echo json_encode([
			'status' => false,
			'message' => "Ingrese todos los datos correctamente"
		]);
		exit();
	}

}


// ENVIAR NOTIFICACIONES A USUARIOS DE LA LISTA NOMBRES
if (isset($_POST['sendNotifications']) AND isset($_POST['idUser']))
{
	$countsend = 0;
	$idUser = $_POST['idUser'];
	$sqlNames = $connect->query('SELECT p.`id` AS id,p.`username` AS username FROM `players_namesactions` AS pa INNER JOIN players AS p ON p.`id`= pa.`player_id`');
	if ($sqlNames AND $sqlNames->num_rows>0) {
		while ($Names = mysqli_fetch_assoc($sqlNames))
		{
			$nameId = $Names['id'];
			// NO ENVIARME NOFICACION A MI MISMO
			if ($nameId != $idUser) {
				//COMPRUEBA QUE NO HAYA BLOQUEO
				$block = $connect->query("SELECT * FROM `bloqueos` AS f WHERE (f.`fromid` = '$idUser' && f.`toid` = '$nameId') || (f.`toid` = '$idUser' && f.`fromid` = '$nameId')");
				if ($block AND $block->num_rows <= 0)
				{
					// BORRAR NOTIFICACIONES ANTERIORES DE PACKS
					$deletePack = $connect->query("DELETE FROM `players_notifications` WHERE fromid='$idUser' AND toid='$nameId'");

					//ENVIAR NOTIFICACION
					$addrequest = $connect->query("INSERT INTO `players_notifications` (fromid, toid,not_key,read_time) VALUES ('$idUser', '$nameId','newPack','0')");
					if ($addrequest) {
						$countsend++;
					}
				}
			}
		}
		Echo json_encode([
				'state' => true ,
				'message' => 'Notificaciones enviadas con éxito',
				'countsend' => intval($countsend)
			]);
	}
	else
	{
		if (!$sqlNames)
		{
			Echo json_encode([
				'state' => false ,
				'message' => 'Ha ocurrido un error en la consulta',
				'countsend' => intval($countsend)
			]);
		}
		else
		{
			Echo json_encode([
				'state' => false ,
				'message' => 'No existen nombres en la lista',
				'countsend' => intval($countsend)
			]);
		}
	}
}
// REALIZA BUSQUEDAS INSTANTANEAS
if(isset($_POST['search']) AND !empty($_POST['search'])){

	$type = (isset($_POST['type']) AND !empty($_POST['type'])) ? $_POST['type'] : 'profile'; // TIPO DE BUSQUEDA

	$key = strval($_POST['search']);

	$search = "{$key}%";

	$consult = $connect->prepare("SELECT p.`id`,p.`username`,p.`perfiloculto` FROM players AS p LEFT JOIN friends AS f ON (f.`player1` = ? && f.`player2` = p.`id` ) || (f.`player2` = ? && f.`player1` = p.`id`) LEFT JOIN `bloqueos` AS b ON (b.`fromid` = ? && b.`toid` = p.`id`) || (b.`fromid` = p.`id` && b.`toid` = ?) WHERE p.`username` LIKE ? AND p.`perfiloculto` = ? AND  f.`id` IS NOT NULL AND  b.`id` IS NULL LIMIT 0 , 5");

	$hide = "no";

	$consult->bind_param("iiiiss", $rowu['id'], $rowu['id'], $rowu['id'], $rowu['id'], $search ,$hide);
	$consult->execute();
	$result = $consult->get_result();

	if ($result->num_rows > 0) {
		while($rows = $result->fetch_assoc()) {
			$username[] = $rows["username"];
		}
		echo json_encode($username);
	}
	else
	{
		echo '[""]';
	}
	$consult->close();
}

// DEVUELVE TODOS LOS MENSAJES NO LEIDOS
if (isset($_GET['getMessagesNoSee']))
{

	$idChat = isset($_POST['idChat']) ? $_POST['idChat'] : 1 ; // ALMACENA EL ID DEL CHAT

	// OBTIENE LOS MENSAJES NO LEIDOS DE "$player_id"
	$query = $connect->query("SELECT * FROM `nuevochat_mensajes` AS nm INNER JOIN `players` AS p ON p.`id` = nm.`author` INNER JOIN nuevochat_rooms AS nr ON (nr.`player1` = '$player_id' || nr.`player2` = '$player_id') AND nr.`id` = $idChat WHERE leido = IF(author = '$player_id','no devolver mensajes','no') AND nm.`id_chat` = '$idChat'");

	if ($query AND $query->num_rows > 0)
	{
		// GUARDA LOS MESAJES EN UN ARRAY
		while ($messages = $query->fetch_assoc())
		{
			$msg[] = $messages;
		}
		// ACTUALIZA LOS MENSAJES A VISTO
		$connect->query("UPDATE `nuevochat_mensajes` SET leido = IF(author = '$player_id', leido, 'si') WHERE `id_chat`='$idChat'");

		echo json_encode($msg);
	}
	else
	{
		echo '[]';
	}
}

// DEVUELVE UNA LISTA DE USUARIOS QUE COMPRARON CIERTOS PACKS (para mispacks.php)
if(isset($_GET['getUsersWhoBoughtPack']))
{
	// OBTENGO EL ID DEL PACK
	$idPack = $_POST['idPack'];

	// SELECCIONO LA VENTA
	$SalesMade = $connect->query("SELECT *, p.`id` AS pid FROM `packscomprados` AS pc INNER JOIN `players` AS p ON p.`id` = pc.`comprador_id` WHERE pc.`foto_id` = '$idPack'");

	if ($SalesMade AND $SalesMade->num_rows > 0)
	{?>
		<div style="display: flex;flex-direction: column;flex-wrap: wrap;align-items: flex-start;align-content: center;">
		<?php while($sale = mysqli_fetch_assoc($SalesMade)): ?>
			<div>
				<img class="img-avatar img-circle" src="<?php echo $sale['avatar']; ?>" style="width: 30px; height: 30px">
				<?php echo createLink('profile', $sale['username'], array('profile_id' => $sale['pid'])); ?>
			</div>
			<br>
		<?php endwhile;?>
		</div>
	<?php }
}

/*
	RECOLECTA LOS giftCreditsWeekly Y ACREDITA LOS CREDITOS
 */
if(isset($_GET['acceptGiftCreditsW']))
{
	$consult = $connect->query("SELECT * FROM giftcredits_weekly WHERE `player_id` = '$rowu[id]' ");

	// COMPRUEBA QUE EL USUARIO TENGA CRÉDITOS DE REGALO POR ACEPTAR
	if ($consult AND $consult->num_rows > 0)
	{
		$giftCredit = $consult->fetch_assoc();

		// BORRAR NOTIFICACION
		$deleteNotification = $connect->query("DELETE FROM `players_notifications` WHERE toid='$rowu[id]' AND not_key='giftWeekly'");

		// BORRAR DE LA LISTA
		$deleteGifCredits = $connect->query("DELETE FROM `giftcredits_weekly` WHERE `player_id` = '$rowu[id]'");

		if($deleteGifCredits)
		{
			// ACREDITAR CRÉDITOS
			updateCredits($rowu['id'], '+', $giftCredit['credits'], 12);
			Echo json_encode([
				'state' => true ,
				'message' => 'En hora buena! Has obtenido '. $giftCredit['credits'] .' Créditos Especiales'
			]);
			exit;
		}
		else
		{
			Echo json_encode([
				'state' => false ,
				'message' => 'Ha ocurrido un error. Porfavor intenta mas tarde...'
			]);
			exit;
		}
	}
	else
	{
		Echo json_encode([
			'state' => false ,
			'message' => 'Ya no tienes Creditos de Regalo para recolectar.'
		]);
		exit;
	}
}

/*
	Envia un regalo
 */
	if(isset($_GET['sentGift']) AND isset($_FILES['gift']) AND !empty($_FILES['gift']))
	{
		# ALMACENAR DATOS DE REGALO
		// TIPO DE OPCIÓN SELÉCCIONADA
		$optionSelect = (isset($_POST['optionSelect']) AND !empty($_POST['optionSelect'])) ? $_POST['optionSelect'] : '0';
		// FOTO
		$file = $_FILES['gift'];
		// MENSAJE
		$comment = (isset($_POST['comment']) AND !empty($_POST['comment'])) ? $_POST['comment'] : '';
		// USUARIOS A QUIENES SE LES ENVIARA
		$usernames = $_POST['usernames'];
		// ENVIAR REGALO
		echo sendGift($usernames, $comment, $file, $optionSelect);
	}

	/*
	Envia un regalo con creditos
 */
	if(isset($_GET['sentGiftMoney']) AND isset($_POST['amount']) AND !empty($_POST['amount']))
	{
		// MENSAJE
		$comment = (isset($_POST['comment']) AND !empty($_POST['comment'])) ? $_POST['comment'] : '';
		// ENVIAR REGALO
		echo sendGiftMoney($comment, $_POST['amount']);
	}

	// DEVUELVE DATOS DE UN REGALO DE USUARIO (JSON)
	if(isset($_GET['getGiftFromUser']) AND isset($_POST['idGift'])){

		$SQLGift = $connect->query("SELECT pg.*, p.`username` FROM `players_gifts` AS pg INNER JOIN `players` AS p ON p.`id` = pg.`player_id` WHERE pg.`id` = \"". $connect->real_escape_string($_POST['idGift']) ."\"");
		if($SQLGift AND $SQLGift->num_rows > 0)
		{

			// COMPROBAR QUE EL REGALO SI SE OTORGO (evita mostrar regalos que no han sido otorgados)
			$consult = $connect->query("SELECT * FROM `players_gift_given` AS pgg WHERE pgg.`toid` = \"". $connect->real_escape_string($rowu['id']) ."\" AND pgg.`gift` = \"". $connect->real_escape_string($_POST['idGift']) ."\"");
			if($consult AND $consult->num_rows > 0)
			{
				while($Gift = mysqli_fetch_assoc($SQLGift))
				{
					$rGift = $Gift;
				}
				// Humanizar fecha
				$rGift['time'] = date('d/m/Y  h:i',$rGift['time']);
				// Nombre de la foto en el directorio
				$rGift['filename'] = basename($rGift['files']);
				//
				$rGift['comment'] = html_entity_decode($rGift['comment'],ENT_QUOTES);

				$giftCredits = ($rGift['amount'] != '' and $rGift['amount'] != null) ? $rGift['amount'] : 0;
				$return = array('state' => '1', 'msg' => '', 'giftCredits' => $giftCredits, 'gift' => $rGift);
			}
			else
			{
				$return = array('state' => '0', 'msg' => 'Este regalo no te ha sido otorgado');
			}
		}
		else
		{
			$return = array('state' => '0', 'msg' => 'El regalo que estas intentando abrir no existe o no se encuentra disponible porque el author lo elimino.');
		}
		echo json_encode($return);
	}
// AÑADIR UNA NUEVA PREGUNTA DE USUARIO A LA LISTA
if(isset($_GET['addQuestUser']))
{
	$Quest = (isset($_POST['Quest']) AND !empty($_POST['Quest'])) ? $_POST['Quest'] : '';

	// AÑADIR A LA LISTA
	$consult = $connect->query("INSERT INTO `site_questions` (player_id, question, description, time) VALUES (\"". $connect->real_escape_string($rowu['id']) ."\",\"". $connect->real_escape_string($Quest) ."\",\"". $connect->real_escape_string('') ."\", UNIX_TIMESTAMP())");

	// COMPRUEBA QUE EL USUARIO TENGA CRÉDITOS DE REGALO POR ACEPTAR
	if ($consult)
	{
		$message = array('state' => true, 'questID' => $connect->insert_id);
	}
	else
	{
		$message = array('state' => false);
	}
	echo json_encode($message);
}
// BORRA UNA PREGUNTA DE USUARIO DE LA DB
if(isset($_GET['deleteQuestUser']))
{
	$Quest = (isset($_POST['idQuest']) AND !empty($_POST['idQuest'])) ? $_POST['idQuest'] : '';


	// BORRAR PREGUNTA ENVIADAS
	$consult2 = $connect->query("DELETE FROM `players_questions` WHERE `question` = \"". $connect->real_escape_string($Quest) ."\"");
	// BORRAR PREGUNTA PRINCIPAL DE LA LISTA
	$consult = $connect->query("DELETE FROM `site_questions` WHERE `id` = \"". $connect->real_escape_string($Quest) ."\"");

	// COMPRUEBA QUE EL USUARIO TENGA CRÉDITOS DE REGALO POR ACEPTAR
	if ($consult AND $connect->affected_rows > 0)
	{
		$message = array('state' => true);
	}
	else
	{
		$message = array('state' => false);
	}
	echo json_encode($message);
}

// RESPONDE UNA PREGUNTA DE USUARIO EN LA DB
if(isset($_GET['sentAnswer']) AND (isset($_POST['idQuest']) AND !empty($_POST['idQuest']) AND (isset($_POST['answer']) AND !empty($_POST['answer']))))
{

	$Quest =  $_POST['idQuest'];
	$answer =  $_POST['answer'];

	$consult = $connect->query("UPDATE `players_questions` SET `answer` = \"". $connect->real_escape_string($answer) ."\", `read_time` = \"". time() ."\" WHERE `id` = \"". $connect->real_escape_string($Quest) ."\"");

	// COMPRUEBA QUE EL USUARIO TENGA CRÉDITOS DE REGALO POR ACEPTAR
	if ($consult AND $connect->affected_rows > 0)
	{
		$message = array('state' => true);
	}
	else
	{
		$message = array('state' => false);
	}
	echo json_encode($message);
}

if(isset($_GET['controllerTouchInFoto']) AND isset($_GET['controllerTouchInFoto']))
{
	$m = [];
	$Photos = [];
	if(isset($_POST['idPhoto']) AND !empty($_POST['idPhoto']))
	{
		$SQLPhoto = $connect->query("SELECT * FROM `fotosenventa` WHERE `id` = \"". $connect->real_escape_string($_POST['idPhoto']) ."\"");
		// SI EXISTE LA FOTO
		if($SQLPhoto AND $SQLPhoto->num_rows > 0)
		{
			$Photo = $SQLPhoto->fetch_assoc();

			// COMPROBAR SI EXISTEN MAS DE UNA FOTO DEL MISMO USUARIO
			if($_POST['selfBuG'] == 1){
				$SQLPhotos = $connect->query("SELECT * FROM `fotosenventa` WHERE `id` = \"". $connect->real_escape_string($_POST['idPhoto']) ."\"");
			}
			else
			{
				$noID = $connect->real_escape_string(implode($_POST['noID'], ','));
				$SQLPhotos = $connect->query("SELECT * FROM `fotosenventa` WHERE `player_id` = \"". $connect->real_escape_string($Photo['player_id']) ."\"  AND `id` != \"". $connect->real_escape_string($_POST['idPhoto']) ."\"");
			}
			if($SQLPhotos AND $SQLPhotos->num_rows > 0)
			{
				while ($rPhotos = mysqli_fetch_assoc($SQLPhotos)) {
					// Si es un video
					if((isset(json_decode($rPhotos['imagen'])[0]) ? getSourceType(json_decode($rPhotos['imagen'])[0]) : 'film')=='film')					// Cantidad de imagenes
						$rPhotos['isVideo'] = true;
					else
						$rPhotos['isVideo'] = false;
					// Cantidad de imagenes
					$rPhotos['countImages'] = is_countable(json_decode($rPhotos['imagen'])) ? count(json_decode($rPhotos['imagen'])) : 0;
					// Cantidad de Likes
					$rPhotos['totalLikes'] = $connect->query("SELECT * FROM `player_megusta` WHERE `galeria_id`='$rPhotos[id]'")->num_rows;
					// Colocar Filtro (Si es necesario)
					$rPhotos['noSub'] = (!isFollow($rPhotos['player_id']) AND $rPhotos['player_id'] != $player_id) ? ' noSub': '';

					$rPhotos['isLike'] = isLike($rowu['id'], $rPhotos['id']) ? 'isLike' : '';

					if(is_countable(json_decode($rPhotos['imagen'])) AND count(json_decode($rPhotos['imagen'])) <= 1) $Photos[] = $rPhotos;


				}
				if(count($Photos) > 0)$m = array('status' => true, 'data' => $Photos);
				else $m = array('status' => 'noPhotos', 'msg' => 'No hay mas fotos');

			}
			else
			{
				$m = array('status' => 'noPhotos', 'msg' => 'No hay mas fotos');
			}

		}
		else
		{
			$m = array('status' => false, 'msg' => 'Hemos tenido un problema interno, Por favor inténtalo mas tarde');
		}
	}
	echo json_encode($m);
}

// PROCESA UN PAGO DE PAYPAL
if(isset($_GET['ProcessPayment']) AND isset($_POST['data']) AND !empty($_POST['data']))
{
	$Items = [
		101 => [
			'Creditos' => 2000,
			'price' => 20
		],
		250 => [
			'Creditos' => 5000,
			'price' => 40
		],
		301 => [
			'Creditos' => 10000,
			'price' => 60
		],
		404 => [
			'Creditos' => 30000,
			'price' => 120
		],
		548 => [
			'Creditos' => 50000,
			'price' => 150
		]
	];
	// Decodifica datos pasados por PayPal
	$data = json_decode(html_entity_decode($_POST['data']));

  // Si se efectuo la compra
  if($data->status == "COMPLETED")
  {
  	if(updateCredits($rowu['id'],'+',$Items[$data->purchase_units[0]->reference_id]['Creditos'],3))
  	{
  		// Agregar usuario a lista de nombres
  		addToLDN($rowu['id']);

  		// Devolver datos
  		echo json_encode(array('status' => true,'coinsTotal' => ($Items[$data->purchase_units[0]->reference_id]['Creditos']) + $rowu['eCreditos']));
  	}
  	else echo json_encode(array('status' => false, 'error' => 1));
	}
	else
	{
		echo json_encode(array('status' => false, 'error' => 2));
	}
}
// REGISTRA CUANDO ALGUIEN DA CLICK A UN BOTON PAYPAL
if(isset($_GET['registerClickButtomPaypal']) AND isset($_POST['buttomPrice']) AND !empty($_POST['buttomPrice']))
{
	if($connect->query("INSERT INTO `payment_list` (`userid`, `paid`, `date`) VALUES (\"". $rowu['id'] ."\" , \"". $connect->real_escape_string($_POST['buttomPrice']) ."\", \"". time() ."\" )")) echo true;

}

// BORRAR CUENTA DE USUARIO
if( isset($_GET['DeleteAccount']) AND isset($_POST['token']) AND !empty($_POST['token']) AND isset($_POST['currentPassword']) AND !empty($_POST['currentPassword']) AND isset($_POST['confirmPassword']) AND !empty($_POST['confirmPassword']) )
{
	$token = $_POST['token'];
	$currentPassword = $_POST['currentPassword'];
	$confirmPassword = $_POST['confirmPassword'];
	$passwordUser = $rowu['password'];
	// VERIFICA QUE EL USUARIO HAYA INTRODUCIDO CORRECTAMENTE LA CONTRASEÑA DE CONFIRMACION
	if($currentPassword === $confirmPassword)
	{
		// COMPRUEBA SI EL USUARIO ES UN USUARIO REGISTRADO EN BELLASGRAM (que este en la bbdd de BG)
		if(isMemberFromBG($rowu['id']))
		{
			$UserBG = getUserFromBG($rowu['id']);
			$passwordUser = $UserBG->password;
		}
		// VERIFICA QUE SEA EL USUARIO QUIEN ESTE EJECUTANDO ESTA ACCION (EVITA FRAUDES)
		if(md5('token' . $rowu['username']) == $token)
		{
			// VALIDA QUE LA CLAVE INTRODUCIDA SEA CORRECTA
			if(password_verify($currentPassword, $passwordUser) === true)
			{
				if(deleteAccount($rowu['id']))
				{
					$message[] = array(
					'Cuenta eliminada con &eacute;xito',
					1
					);
				}
				else
				{
					$message[] = array(
					'Error al eliminar la cuenta. Por favor contacte con nuestro equipo escribiéndonos un <a href="https://bellasgram.com/chat/newchat.php?id=196558">Mensaje</a>',
					0
				);
				}
			}
			else
			{
				$message[] = array(
					'Contraseña Incorrecta',
					0
				);
			}
		}
		else
		{
			$message[] = array(
					'Ha ocurrido un error',
					0
			);
		}
	}
	else
	{
		$message[] = array(
					'Las contraseñas no coinciden',
					0
				);
	}
	die($message[0][1].':'.$message[0][0]);
}

// LOGUEARSE COMO X USUARIO DE MANERA INSTANTANEA
if(isset($_GET['loginAsThisUser']) AND isset($_POST['userTo']) AND !empty($_POST['userTo']))
{
	if($rowu['role'] == 'Admin' AND !isset($_COOKIE['returnUser']))
	{
		if(changerUser($_POST['userTo']))
		{
			$msg = array('status' => true);
		}
		else
		{
			$msg = array('status' => false);
		}
	}
	else
	{
		setcookie('returnUser', '', time() - 1);
		$msg = array('status' => false);
	}
	echo json_encode($msg);
}

// SALIR DE UN PERFIL (SALE DE UN PERFIL DEL QUE ESTÁ MIRANDO EL ADMIN)
if(isset($_GET['logoutProfileGuest']))
{
	// COMPRUEBA QUE SE PUEDA RETORNAR USUARIO
	if(isset($_COOKIE['returnUser']) AND !empty($_COOKIE['returnUser']))
	{
		//
		$returnUser = getColumns('players', array('id'), array('username', base64_decode($_COOKIE['returnUser'])), 1, true);
		if($returnUser AND $returnUser->num_rows > 0)
		{

			setcookie('returnUser', '', time() - 1);
			// CAMBIAR DE USUARIO
			setcookie('eluser', base64_decode($_COOKIE['returnUser']), time() + (60*60*24*90));

			$msg = array('status' => true);
		}
		else
		{
			$msg = array('status' => false);
		}
	}
	else
	{
		$msg = array('status' => false);
	}
	echo json_encode($msg);
}

// Devuelve datos de un regalo
if(isset($_GET['getGiftCredits']) and isset($_POST['idGift']) and is_numeric($_POST['idGift']))
{
	echo getGiftCredits($_POST['idGift']);
}
?>

