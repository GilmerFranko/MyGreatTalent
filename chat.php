<?php
require("core.php");
head();


if (isset($_GET['chat_id'])){
	
	$chat = $_GET['chat_id'];
	
	$queryc = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE id = '$chat'");
	$revision = mysqli_fetch_assoc($queryc);
	
	if ($revision['player1'] == $player_id || $revision['player2'] == $player_id){
		
		if ($revision['player1'] = $player_id){
			$elamigo = $revision['player2'];
			$see = 'leido';
		}else{
			$elamigo = $revision['player1'];
			$see = 'leido_to';
		}

	}else{
		echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
		exit;
	}	
	
}else{
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
	exit;
}


// DESBLOQUEA UN CHAT
if (isset($_GET['unlock_chat']) AND !empty($_GET['unlock_chat']) AND is_numeric($_GET['unlock_chat'])){

	$idUnlock = $_GET['unlock_chat'];

	// COMPRUEBA QUE EL CHAT ESTE BLOQUEADO
	if (checkStateChatRoom($idUnlock) != 'open')
	{
		// OPTIENE EL ID DEL USUARIO QUE BLOQUEO EL CHAT
		$idUser = getColumns('nuevochat_rooms', array('state','id'), array('id',$idUnlock));

		// COMPRUEBA QUE YO SEA LA MISMA PERSONA QUE BLOQUEO EL CHAT
		if ($idUser['state'] == $rowu['id']) {

			// DESBLOQUEA EL CHAT
			$connect->query("UPDATE nuevochat_rooms SET state='open' WHERE id= '$idUnlock'");

		}
		else
		{
			setSwal(array('Error','No tienes suficientes permisos para desbloquear este chat','error'));
		}
	}
	else
	{

	}
}
// BLOQUEA UN CHAT
if (isset($_GET['lock_chat']) AND !empty($_GET['lock_chat']) AND is_numeric($_GET['lock_chat'])){

	$idLock = $_GET['lock_chat'];

	// COMPRUEBA QUE EL CHAT NO ESTE BLOQUEADO
	if (checkStateChatRoom($idLock) == 'open'){

		// BLOQUEA EL CHAT
		$connect->query("UPDATE nuevochat_rooms SET state='$rowu[id]' WHERE id= '$idLock'");
	}

}

// ELIMINA UN MENSAJE
if (isset($_GET['deleteMsg']) and !empty($_GET['deleteMsg']) )
{

	$idtrash = mysqli_real_escape_string($connect,$_GET['deleteMsg']);

	$select = mysqli_query($connect,"SELECT id,author,foto,rutadefoto FROM `nuevochat_mensajes` WHERE id= '$idtrash'");

	if ($select AND $select->num_rows > 0)
	{

		$row_delete=$select->fetch_assoc();

		//SI ES PROPIETARIO DEL MENSAJE O ES ADMIN
		if ($row_delete['author']==$rowu['id'] or $rowu['role']=="Admin")
		{
			//BORRAR
			$connect->query("DELETE FROM `nuevochat_mensajes` WHERE id = '$idtrash'");

			// SI ES UNA IMAGEN
			if ($row_delete['foto']=='Yes')
			{
				unlink($row_delete['rutadefoto']);
			}
		}
	}
}
?>

<!--END CONTENT CONTAINER-->
<style type="text/css">
	.msg a{
		color: #72eaff;
	}
</style>
<div class="content-wrapper">
	<div id="content-container">
		<section class="content-header">
			<h1><i class="fas fa-envelope"></i> Chat</h1>
		</section>
		<section class="content" width="100%">
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title"></h3>
						</div>
						<div class="box-body">
							<div id="actualizar" >
								<?php

								$query = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE id_chat='{$chat}' ORDER BY id ASC");
								$marcarcomoleido = mysqli_query($connect, "UPDATE `nuevochat_mensajes` SET leido = IF(author = '$player_id', leido, 'si') WHERE `id_chat`='$chat'");
								while ($mensaje = mysqli_fetch_assoc($query)) {

									if ($mensaje['author'] == $player_id){
										$amigo = $mensaje['toid'];
										$alinear = 'left';
										$color = 'white';
									}elseif ($mensaje['toid'] == $player_id){
										$amigo = $mensaje['author'];
										$alinear = 'left';
										$color = 'white';
									}

									$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$mensaje[author]'");
									$rowsuser = mysqli_fetch_assoc($sqluser);

									$class = "";
									if($rowsuser['id'] == $player_id){
										$class = " current_user_message";
									}
									?>
									<tr>
									<td>
									<div id="message <?php echo $mensaje['id']; ?>" align="left" class="row_message<?php echo $class; ?>">
									<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>" translate="no">
									<img src="<?php echo $sitio['site'].$rowsuser['avatar']; ?>" class="img-circle" width="42">
									<b><?php echo $rowsuser['username']; ?></b></a>
									<!-- BOTON ELIMINAR MENSAJE -->
									<?php if($mensaje['author'] == $player_id): ?>
									<a class="btn btn-success deleteMsg" href="#" data-href="<?php echo createLink('chat','',array('chat_id' => $mensaje['id_chat'] ,'deleteMsg' => $mensaje['id']),true);?>" translate="no" style="float: right;"><i class="fa fa-trash"></i></a>
									<?php endif ?>
									<br>
									<?php if($mensaje['mensaje']!=''): ?>
										<p class="msg" style="word-break:break-word;"><?php echo $mensaje['mensaje']; ?></p>
									<?php endif ?>

									<?php if($mensaje['foto'] == 'Yes'): ?>

										<img src="<?php echo $sitio['site'].$mensaje['rutadefoto'];?>" onclick="openImage(`<?php echo $sitio['site'].$mensaje['rutadefoto']; ?>`)" width="100%"/><br><br>
									<?php endif ?>

									</div>
									</td>
									</tr>
									<?php } ?>

							</div>
						</div>
						<!-- SI EL CHAT ESTA ABIERTO -->
						<?php if (checkStateChatRoom($chat) == 'open'): ?>
						<center>
							<a name="chat"></a>
							<input type="hidden" name="salaid" id="idch" value="<?php echo $chat; ?>">
							<input type="hidden" name="author" id="auth" value="<?php echo $player_id; ?>">
							<input type="hidden" name="toid" id="amig" value="<?php echo $elamigo; ?>">
							<div class="row" style="margin: 0;">
								<div class="col-md-10">
									<textarea placeholder="Escribir" class="form-control" name="mensaje" id="mens" rows="3" spellcheck="false"></textarea>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6"></div>
								<div class="col-md-6" style="padding-top: 5px;text-align: right;">
									<!-- MENU DESPLEGABLE -->
									<div class='btn-group' style="">
										<button class='btn btn-success dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
											<i class="fas fa-bars text-white" ></i>
										</button>
										<div class='dropdown-menu' aria-labelledby='dropdownMenuButton' style="margin: -90px -81px 0px;">
											<a href="<?php echo createLink('chat', '', array('chat_id' => $chat , 'lock_chat' => $chat), true) ?>" class="btn btn-danger dropdown-item width"> Bloquear Chat</a>
										</div>
									</div>
									<input value="Enviar" type="button" id="postmensaje" onclick="hola();" name="post_chatmessage" class="btn btn-primary btn-md float-right" />

									<span class="btn btn-primary btn-file float-left">
										<i class="fas fa-camera"></i>
										<div id="bbbb" onclick="subirfoto();"><input class="float-left" type="file" name="file1" id="file1" accept=".png, .jpeg, .jpg"></div>
									</span>
									<img id="preview" width="50px" style="max-width:50px;">
									<input class="btn btn-primary" id="bontondeenviarfoto" style="display:none" type="button" value="Enviar Foto" onclick="uploadFile()">
								</div>
							</div>
						</center>
						<?php else: ?>
						<?php
						$q = getUser(checkStateChatRoom($chat));
						$uName = $q->fetch_assoc();
						?>
						</div>
							<div class="box-footer">
								<div align="center">
									<?php if ($uName['id'] == $rowu['id']): ?>
									Has <span class="text-red">Bloqueado</span> este Chat <a href="chat.php?chat_id=<?php echo $chat; ?>&unlock_chat=<?php echo $chat; ?>"> ¿Deseas desbloquearlo?</a>
									<?php else: ?>
									Este Chat ha sido <strong><span class="text-red">Bloqueado</span></strong> por <?php echo createLink('profile', $uName['username'], array('profile_id' => $uName['id'])) ?></span>
								<?php endif ?>
								</div>
							</div>

						<?php endif ?>
						</div>
					</div>
				</div>

			</div>

		</div>

		<script type="text/javascript">

			function readImage2 (input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();
					reader.onload = function (e) {
				$('#preview').attr('src', e.target.result).show(); // Renderizamos la imagen
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	function mostrarprogreso() {
		$("#proystat").show();
		$("#bontondeenviarfoto").hide();
	}

	function _(el) {
		return document.getElementById(el);
	}

	function uploadFile() {
		var file = _("file1").files[0];
		var formdata = new FormData();
		formdata.append("file1", file);
		$.ajax({
			url: 'upload4.php?urid=<?php echo $player_id; ?>&chatid=<?php echo $chat; ?>&toid=<?php echo $elamigo; ?>',
			data: formdata,
			method: 'POST',
			contentType:false,
    	cache: false,
    	processData:false
		}).done(function(response){
			showMessage(response);
		});

		$("#file1").val("");
		$('#preview').attr('src', '').hide();
		$("#bontondeenviarfoto").hide();
	}

	function progressHandler(event) {
		_("loaded_n_total").innerHTML = "Cargado " + event.loaded + " bytes de " + event.total;
		var percent = (event.loaded / event.total) * 100;
		_("progressBar").value = Math.round(percent);
		_("status").innerHTML = Math.round(percent) + "% Cargando... por favor espere";

		if (percent >= 100) {
			document.getElementById("proystat").hide();
		}
	}

	function completeHandler(event) {
		_("status").innerHTML = event.target.responseText;
		_("progressBar").value = 0;
		$("#bontondeenviarfoto").hide();
	}

	function errorHandler(event) {
		_("status").innerHTML = "Upload Failed";
	}

	function abortHandler(event) {
		_("status").innerHTML = "Upload Aborted";
	}

	function hola() {

		var idchat = document.getElementById('idch').value;
		var author = document.getElementById('auth').value;
		var toid = document.getElementById('amig').value;
		var mensaje = document.getElementById('mens').value;
		
		console.log( 'idchat=' + idchat + '&author=' + author + '&toid=' + toid + '&mensaje=' + mensaje );

		$.ajax({
			data: {"idchat" :  idchat, "author" : author, "toid" : toid, "mensaje" : mensaje},
			url: 'ajax.php',
			method: 'POST',
		}).done(function(response){
			showMessage(response);
		});

		document.getElementById("mens").value = "";
	}

	$(document).ready(function() {

		setInterval(function() { searchMessages()}, 3000);
		$("#file1").change(function () {
			// Código a ejecutar cuando se detecta un cambio de archivO
			readImage2(this);
			$("#bontondeenviarfoto").show();
		});

		$('#dt-basic').dataTable({
			"responsive": true,
			"language": {
				"paginate": {
					"previous": '<i class="fas fa-angle-left"></i>',
					"next": '<i class="fas fa-angle-right"></i>'
				}
			}
		});
	});
	function searchMessages(){
		//$(".row_message:last").append('hol').load('chat.php?chat_id=<?php echo $chat;?> .row_message:last');

		$.ajax({
			url: "ajax.php?getMessagesNoSee",
			type: "POST",
			data: {"idChat": "<?php echo $chat;?>", "see": "<?php echo $see; ?>"}
		}).done(function(response) {
			showMessage(response);
		});
	}
	function showMessage(response){
		var r = $.parseJSON(response);
		console.log(response);
			if (r.length > 0) {
				for(let i = 0; i < r.length; i++) {

					let Class = (r[i].author == <?php echo $player_id; ?>) ? 'current_user_message' : '';
					let foto = r[i].foto == "Yes" ? "<img src='"+ r[i].rutadefoto +"' onclick='openImage(`"+r[i].rutadefoto +"`)' width='100%' /> <br><br>" : "";
					let message = r[i].mensaje != '' ? "<p class='msg' style='word-break:break-word;'> "+ r[i].mensaje +" </p>" : "";
					let deleteMsg = (r[i].author == <?php echo $player_id;?>) ? '<a class="btn btn-success deleteMsg" href="chat.php?chat_id='+ r[i].id_chat +'&deleteMsg=' +r[i].id + '" translate="no" style="float: right;"><i class="fa fa-trash"></i></a>' : '';

					$("#actualizar").append("<div id='"+r[i].id+"' align='left' class='row_message "+ Class +"'><a href='profile.php?profile_id="+ r[i].author +"' translate='no'><img src='"+ r[i].avatar +"' class='img-circle' width='42'> <b>"+ r[i].username + "</b></a>"+ deleteMsg +"<br>" + message +" "+ foto +"</div>")
				}
			}
	}
</script>
<?php
footer();
?>
