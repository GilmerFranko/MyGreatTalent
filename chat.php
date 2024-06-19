<?php
require("core.php");
head();

/* Permite subir video si tiene los permisos necesarios */
$video_permission = ($session['permission_send_gift'] == 1) ? ', .mp4' : '';




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
				unlink($row_delete['rutadefotoXXX']);
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
							<div id="actualizar">
								<?php
								$query = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE id_chat='{$chat}' ORDER BY id ASC");
								$marcarcomoleido = mysqli_query($connect, "UPDATE `nuevochat_mensajes` SET leido = IF(author = '$player_id', leido, 'si') WHERE `id_chat`='$chat'");
								while ($mensaje = mysqli_fetch_assoc($query)) {
									if ($mensaje['author'] == $player_id) {
										$amigo = $mensaje['toid'];
										$alinear = 'left';
										$color = 'white';
									} elseif ($mensaje['toid'] == $player_id) {
										$amigo = $mensaje['author'];
										$alinear = 'left';
										$color = 'white';
									}

									$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$mensaje[author]'");
									$rowsuser = mysqli_fetch_assoc($sqluser);

									$class = "";
									if ($rowsuser['id'] == $player_id) {
										$class = " current_user_message";
									}
									?>
									<div id="message<?php echo $mensaje['id']; ?>" class="row_message<?php echo $class; ?>"align="left">
										<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>" translate="no" >
											<img src="<?php echo $sitio['site'].$rowsuser['avatar']; ?>" class="img-circle" width="42">
											<b><?php echo $rowsuser['username']; ?></b>
										</a>
										<!-- BOTON ELIMINAR MENSAJE -->
										<?php if($mensaje['author'] == $player_id){ ?>
											<a class="btn btn-success deleteMsg" href="#" data-href="<?php echo createLink('chat','',array('chat_id' => $mensaje['id_chat'] ,'deleteMsg' => $mensaje['id']),true);?>" translate="no" style="float: right;"><i class="fa fa-trash"></i></a>
										<?php } ?>
										<br>
										<?php if($mensaje['mensaje']!=''){ ?>
											<p class="msg" style="word-break:break-word;"><?php echo $mensaje['mensaje']; ?></p>
										<?php } ?>

										<?php if ($mensaje['foto'] == 'Yes'){ ?>
											<?php if (pathinfo($mensaje['rutadefoto'], PATHINFO_EXTENSION) == 'mp4'){ ?>
												<video width="100%" controls>
													<source src="<?php echo $sitio['site'] . $mensaje['rutadefoto']; ?>" type="video/mp4">
														Tu navegador no soporta el tag de video.
													</video><br><br>
												<?php }else{ ?>
													<img src="<?php echo $sitio['site'] . $mensaje['rutadefoto']; ?>" onclick="openImage('<?php echo $sitio['site'] . $mensaje['rutadefoto']; ?>')" width="100%" /><br><br>
												<?php } ?>
											<?php } ?>
										</div>
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
												<div id="bbbb" onclick="subirfoto();"><input class="float-left" type="file" name="file1" id="file1" accept=".png, .jpeg, .jpg <?php echo $video_permission ?>"></div>
											</span>
											<img id="preview" width="50px" style="max-width:50px;">
											<video id="video-preview" style="max-width: 50px;"></video>
											<input class="btn btn-primary" id="bontondeenviarfoto" style="display:none" type="button" value="Enviar Archivo" onclick="uploadFile()">
										</div>
									</div>
								</center>
								<?php else: ?>
									<?php
									$q = getUser(checkStateChatRoom($chat));
									$uName = $q->fetch_assoc();
									?>
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
					</section>
				</div>
			</div>


			<script type="text/javascript">

				function readImage2(input) {
					if (input.files && input.files[0]) {
						var reader = new FileReader();

						reader.onload = function (e) {
							var mediaType = input.files[0].type.split('/')[0];

							if (mediaType === 'image') {
                		// Si es una imagen, muestra la vista previa de la imagen
                		$('#preview').attr('src', e.target.result).show();
                		$('#video-preview').attr('src', '').hide();
                	} else if (mediaType === 'video') {
                		// Si es un video, crea un Blob y una URL para el contenido del video
                		var blob = new Blob([e.target.result], { type: 'video/mp4' });
                		var videoURL = URL.createObjectURL(blob);

                		// Muestra la vista previa del video
                		$('#preview').attr('src', '').hide();
                		$('#video-preview').attr('src', videoURL).show();
                	} else {
                		// Manejar otros tipos de archivos aquí si es necesario
                		console.error('Tipo de archivo no compatible');
                	}
                }

                if (input.files[0].type.split('/')[0] === 'image') {
                	reader.readAsDataURL(input.files[0]);
                } else if (input.files[0].type.split('/')[0] === 'video') {
            			// Para videos, utiliza el método readAsArrayBuffer
            			reader.readAsArrayBuffer(input.files[0]);
            		}
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
            	var fileInput = _("file1");
            	var file = fileInput.files[0];

            	if (file) {
            		var formData = new FormData();
            		formData.append("file1", file);

            		$.ajax({
            			url: 'upload4.php?urid=<?php echo $player_id; ?>&chatid=<?php echo $chat; ?>&toid=<?php echo $elamigo; ?>',
            			data: formData,
            			method: 'POST',
            			contentType: false,
            			cache: false,
            			processData: false
            		}).done(function (response) {
            			var r = $.parseJSON(response);
            			if(r.status)
            			{
            				showMessage(JSON.stringify(r.msg));
            			}
            			else
            			{
            				swal.fire(r.msg, '', 'error')
            			}
            		});

        				// Resetear el valor y ocultar la vista previa y el botón
        				fileInput.value = "";
        				$('#preview').attr('src', '').hide();
        				$('#video-preview').attr('src', '').hide();
        				$("#bontondeenviarfoto").hide();
        			} else {
        				console.error("No se seleccionó ningún archivo.");
        			}
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
					function showMessage(response) {
						console.log(response)
						try {
							var messages = $.parseJSON(response);

							if (messages.length > 0) {
								for (let i = 0; i < messages.length; i++) {
									appendMessage(messages[i]);
								}
							}
						} catch (error) {
							console.error("Error al parsear la respuesta JSON:", error);
						}
					}

					function appendMessage(message) {
						let isCurrentUser = (message.author == <?php echo $player_id; ?>);
						let Class = isCurrentUser ? 'current_user_message' : '';
						let messageContent = message.mensaje != '' ? "<p class='msg' style='word-break:break-word;'> " + message.mensaje + " </p>" : "";
						let deleteMsg = isCurrentUser ? '<a class="btn btn-success deleteMsg" href="chat.php?chat_id=' + message.id_chat + '&deleteMsg=' + message.id + '" translate="no" style="float: right;"><i class="fa fa-trash"></i></a>' : '';

    				// Verifica si el mensaje es un video y ajusta el contenido en consecuencia
    				let content = "";
    				if (message.foto == "Yes") {
    					if (message.rutadefoto.toLowerCase().endsWith(".mp4")) {
    						content = "<video width='100%' controls><source src='" + message.rutadefoto + "' type='video/mp4'>Tu navegador no soporta el tag de video.</video>";
    					} else {
    						content = "<img src='" + message.rutadefoto + "' onclick='openImage(`" + message.rutadefoto + "`)' width='100%' /> <br><br>";
    					}
    				}

    				$("#actualizar").append("<div id='" + message.id + "' align='left' class='row_message " + Class + "'><a href='profile.php?profile_id=" + message.author + "' translate='no'><img src='" + message.avatar + "' class='img-circle' width='42'> <b>" + message.username + "</b></a>" + deleteMsg + "<br>" + messageContent + " " + content + "</div>");
    			}



    		</script>
    		<?php
    		footer();
    		?>
