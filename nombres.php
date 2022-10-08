<?php

require("core.php");
head();
//if($rowu['role'] != 'Admin')  die('<meta http-equiv="refresh" content="0; url='.$sitio['site'].'index.php" />') ;

$search = (isset($_GET['search']) AND !empty($_GET['search'])) ? $_GET['search'] : '';

/*DECIDIR QUE MOSTRAR*/

// NOMBRES GUARDADOS EN LA LISTA
if ($search == '')
{
	$sqlNames = $connect->query("SELECT pa.`time`,p.`id` AS id,p.`username` AS username,f.`id` AS f_id, email, ipaddres, timeonline, p.`ipaddres` AS `ip` FROM `players_namesactions` AS pa INNER JOIN players AS p ON p.`id`= pa.`player_id` LEFT JOIN friends AS f ON (f.`player1`=p.`id` AND f.`player2`='$rowu[id]') || (f.`player1`='$rowu[id]' AND f.`player2`=p.`id`) GROUP BY p.`id` ORDER BY pa.`time` DESC");
}
	// NOMBRES BUSCADOS
else
{
	$sqlNames = $connect->query("SELECT p.`id` AS id,p.`username` AS username, f.`id` AS f_id, email , timeonline, p.`ipaddres` AS `ip` FROM `players` AS p LEFT JOIN friends AS f ON (f.`player1`=p.`id` AND f.`player2`='$rowu[id]') || (f.`player1`='$rowu[id]' AND f.`player2`=p.`id`) WHERE p.`username` LIKE '%$search%' GROUP BY p.`id`");
}
$count = 0;
?>
<style type="text/css">
	.view_email{
		display: none;
	}
</style>
<div class="content-wrapper">
	<div id="content-container">
		<div class="">
			<form method="GET">
				<div class="row">
					<div class="col-sm-6">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
							</span>
							<input type="text" class="form-control" placeholder="Buscar Usuario" name="search">
						</div><!-- /input-group -->
					</div><!-- /.col-lg-6 -->
				</div>
			</form>
			<br>
			<form id="formActions" method="POST" enctype="multipart/form-data">
				<!-- BOTONES HEADER -->
				<div class="row" align="center">
					<!-- BOTON SELECCIONAR TODO -->
					<a href="#" id="selectall" class="btn btn-primary" name=""><i class="fa fa-check"></i> Seleccionar todo</a>
					<!-- BOTON AGREGAR NOMBRES -->
					<button class="btn btn-primary" name="addnames"><i class="fa fa-plus"></i> Agregar nombres a la lista</button>
					<!-- BOTON MOSTRAR EMAILS -->
					<a id="showEmails" href="#" class="btn btn-primary"><i class="fa fa-eye"></i> Mostrar Emails</a>
				</div>
				<!--/-->
				<br>
				<div class="" align="center">
					<select class="btn" style="background:white;" name="option" id="postType">
						<option value="newAmistad">Enviar solicitud de amistad</option>
						<option value="newChat">Iniciar Chat</option>
						<option value="sendMessages">Enviar mensajes masivos</option>
						<option value="removeUser">Eliminar de la lista</option>
					</select>
					<div id="modalMessage" style="display: none;align-items: center;flex-direction: row;justify-content: center; margin: 20px 0px;">
						<textarea id="inputMessage" placeholder="Escribe tu mensaje" class="form-control" name="inputMessage" style="width: 263px;"></textarea>
						&nbsp;
						<div align="center">
							<div class="item" id="item-0" style="display: inline-block;"><span id="deselectFile" style="display: none;position: absolute; color: red;"><i class="fa fa-window-close"></i></span><img class="preview" src="assets/img/foto.png" style="width: 32px;height: 32px;"></div>
							<span class="btn btn-primary btn-file float-left sdt-btn">
								<i class="fas fa-camera"></i>
								<div>
									<input id="inputFile" onchange="readImage(this, $('.preview'));" class="float-left" type="file" name="inputFile" accept="image/png,image/jpeg">
								</div>
							</span>
						</div>
					</div>
					<input class="btn btn-success submitForm" type="submit" name="send">
				</div>
				<br>
				<div id="scroll" class="box" style="border-top:0px;">
					<table id="datatable" class="table table-bordered table-hover no-margin" style="min-width: 700px;">
						<thead>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th colspan="3">
									<?php if ($search==''): ?>
										<h5><strong>Nombres agregados</strong></h5>
										<?php else: ?>
											<h5></h5>
										<?php endif ?>
									</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php while($names =  mysqli_fetch_assoc($sqlNames)){ ?>
										<td>
											<span>
												<input class="case" type="checkbox" name="namesId[]" value="<?php echo $names['id']; ?>">
												<label>
													<?php echo createLink('profile',$names['username'],array('profile_id' => $names['id'])); ?>
												</label>
												<div class="view_email"> <small><strong>Email:</strong><br><?php echo $names['email']; ?></small></div>

												<div class="view_email"> <small><strong>Dirección Ip:</strong> <br><?php echo $names['ip']; ?></small></div>

												<div class="view_email"> <small><strong>Ultima conexión:</strong> <br><?php echo TimeAgo($names['timeonline']); ?></small></div>
												<!-- DE ESTAR AGREGADO EL NOMBRE EN LA LISTA -->
												<?php if (isset($names['time'])): ?>
													<div class="view_email"><small><strong>Fecha de agregado: </strong><br><?php echo date('d/m/Y  h:i',$names['time']); ?></small></div>
												<?php endif ?>
											</span>
											<?php if ($names['f_id']!=null): ?>
												&nbsp;
												<i class="fa fa-square" style="color:green;"></i>
												<?php else: ?>
													<i class="fa fa-square" style="color:red;"></i>
												<?php endif ?>
											</td>
											<?php
											$count++;
											if ($count == 7): ?>
											</tr><tr>
												<?php
												$count = 0;
											endif;
										}

								/**
								* COMPLETA LAS FILAS VACIAS
								* IMPORTANTE PARA QUE DATATABLE FUNCIONE
								**/
								if ($count != 0) {
									for ($count; $count < 7 ; $count++) {
										echo '<td>_</td>';
									}

								}
								?>
							</tr>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	var turn = true;

	/* Al enviar formulario */
	$(".submitForm").click(function()
	{
		/* Si se desea eliminar un usuario*/
		if($("#postType option:selected").val() == 'removeUser'){

			/* Mensaje de confirmación */
			if(confirm("¿Desea eliminar los usuarios seleccionados?"))
			{
				/* Enviar formulario */
				$("#formActions").submit()
			}

		}
		return false
	})

	//- SELECCIONA TODOS LOS CHECKBOX
	$("#selectall").on("click", function() {
		$(".case").prop("checked", turn);
		turn = turn==false ? true : false;
	});
	// MUETRA LOS EMAIL
	$("#showEmails").click(function (){
		$(".view_email").toggle();
	});

	$("#postType").change(function(){
		// AL SELECCIONAR Enviar Mensajes
		if($("#postType option:selected").val() == 'sendMessages'){
			// Mostrar opciones
			$("#modalMessage").css('display','flex')
		}
		// SI NO SE SELECCIONO
		else{
			// Vacias Selecciones
			$("#modalMessage").hide()
			$("#inputMessage").val(null)
			$("#inputFile").val(null)
			$("#inputFile").val(null)
			$(".preview").prop("src", "assets/img/foto.png")
			$("#deselectFile").hide()
			$("#inputMessage").removeAttr('disabled')
		}
	})
	// SI SE SELECCIONO ALGUNA IMAGEN
	$('#inputFile').change(function(){
		// Bloquear el formulario de mensaje (No se puede enviar una foto y un mensaje al mismo tiempo)
		$("#inputMessage").prop('disabled', 'true')
		// Vaciar formulario de mensaje
		$("#inputMessage").val('')
		// Mostrar deseleccionador de imagen
		$("#deselectFile").show()
	})
	// Al deseleccionar una imagen
	$("#deselectFile").click(function(){
		// Vaciar input
		$("#inputFile").val(null)
		// Cambiar preview
		$(".preview").prop("src", "assets/img/foto.png")
		// Habilitar formulario de mensaje
		$("#inputMessage").removeAttr('disabled')
		// Ocultar deseleccionador de imagen
		$("#deselectFile").hide()
	})
</script>



<?php
if (isset($_POST['namesId']) AND isset($_POST['option']))
{
	$NamesId = $_POST['namesId'];
	$option = $_POST['option'];
	$countaddrequest = 0; # Cuenta las solicitudes enviadas
	$selffriend = 0; # Cuenta las veces que se le envia solicitud a usuario que ya es amigo
	$countchats = 0; # Cuenta los chats iniciados
	$countaddnames = 0; # Cuenta los nombres agregados a la bbdd
	$time = time(); #Hora actual

// SI HAY QUE AÑADIR LOS NOMBRES A LA BBDD
	if(isset($_POST['addnames']))
	{
		foreach ($NamesId as $key => $nameId)
		{
		// BUSCAR EL ID EN LA LISTA
			$findnames = $connect->query("SELECT * FROM `players_namesactions` WHERE `player_id`='$nameId'");
		// SI NO ESTA EN LA BBDD
			if ($findnames->num_rows<=0)
			{
			//AGREGA EL ID
				$addname = $connect->query("INSERT INTO `players_namesactions` (player_id,player_add,time) VALUES ('$nameId','$rowu[id]','$time')");
			// SI SE AGREGO CON EXITO
				if ($addname)
				{
					$countaddnames++;
				}
			}
		}
		echo "<script>swal.fire({ title:'Excelente',text:'Perfiles agregados: ".$countaddnames."',});</script>";
		exit;

	}
// ENVIAR SOLICITUDES DE AMISTAD
	if ($option == 'newAmistad')
	{
		foreach ($NamesId as $key => $nameId) {
			if ($nameId != $rowu['id']) {

			// COMPRUEBA QUE NO HAYA BLOQUEOS
				if (!checkBlocking($rowu['id'] , $nameId))
				{
				//COMPRUEBA QUE NO EXISTAN SOLICITUDES DE AMISTAD
					$friendrequest = $connect->query("SELECT * FROM `players_notifications` AS nt WHERE (nt.`fromid` = '$rowu[id]' && nt.`toid` = '$nameId' && `not_key`='newAmistad') || (nt.`fromid` = '$nameId' && nt.`toid` = '$rowu[id]' && `not_key`='newAmistad')");
					if ($friendrequest AND $friendrequest->num_rows <= 0)
					{

					//COMPRUEBA QUE NO HAYA AMISTAD
						$friend = $connect->query("SELECT * FROM `friends` AS f WHERE (f.`player1` = '$rowu[id]' && f.`player2` = '$nameId') || (f.`player2` = '$rowu[id]' && f.`player1` = '$nameId')");
						if ($friend AND $friend->num_rows <= 0)
						{

						//AGREGA SOLICITUD
							$addrequest = $connect->query("INSERT INTO `players_notifications` (fromid, toid,not_key,read_time) VALUES ('$rowu[id]', '$nameId','newAmistad','0')");

							if ($addrequest)
							{
								$countaddrequest++;
							}

						}
					}
				}
				else
				{
					$selffriend++;
				}
			}
		}
		echo "<script>swal.fire({ title:'Excelente',text:'Solicitudes enviadas : ".$countaddrequest."',});</script>";
	}
// AGREGAR CHAT A TODOS LOS USUARIOS
	elseif($option == 'newChat')
	{
		foreach ($NamesId as $key => $nameId)
		{
		//SI EL ID ES DIFERENTE AL MIO
			if($nameId != $rowu['id'])
			{
				if (!checkBlocking($rowu['id'] , $nameId))
				{
			// COMPRUEBA SI YA EXISTE UN CHAT
					$existChat = $connect->query("SELECT * FROM `nuevochat_rooms` WHERE (player1='$rowu[id]' AND player2='$nameId') || (player1='$nameId' AND player2='$rowu[id]')");
			// SI NO EXISTE NINGUN CHAT INICIADO
					if ($existChat AND $existChat->num_rows<=0)
					{
				// INICIA UN CHAT
						$createroom = $connect->query("INSERT INTO `nuevochat_rooms` (player1, player2) VALUES ('$nameId', '$rowu[id]')");

						if ($createroom)
						{
							$countchats++;
						}
					}
				}
			}
			echo "<script>swal.fire({ title:'Excelente',text:'Chats iniciados : ".$countchats."',});</script>";
		}
	}
	// ENVIA MENSAJES MASIVOS
	elseif($option == 'sendMessages')
	{
		$success = 0;
		$error_room_no_exist = 0;
		$error_room_is_closed = 0;
		$errors = array('file-no-uploaded' => 0, 'room-is-closed' => 0, 'room-no-exist' => 0);
		if((isset($_FILES['inputFile']) AND !empty($_FILES['inputFile']) OR (isset($_POST['inputMessage']) AND !empty($_POST['inputMessage']))))
		{
			$message = (isset($_POST['inputMessage']) and !empty($_POST['inputMessage'])) ? $_POST['inputMessage'] : null;
			$file = (isset($_FILES['inputFile']) and !empty($_FILES['inputFile'])) ? $_FILES['inputFile'] : null;
			$response = array();
			// COMBPRUEBA SI SE DEBE SUBIR UNA FOTO
			$filename = '';
			if(!empty($file) and !empty($file['tmp_name']))
			{
				if(!$filename = upload_file_in_chat($file, 21)) return array(false, 'file-no-uploaded');
			}
			foreach ($NamesId as $key => $nameId)
			{
				// EVITAR ENVIAR A ESTE ID
				if($nameId != $rowu['id'])
				{
					$SQLroom = $connect->query("SELECT `id` FROM `nuevochat_rooms` WHERE (`player1` = $rowu[id] and `player2` = $nameId) or (`player2` = $rowu[id] and `player1` = $nameId)");
					// COMPRUEBA QUE EXISTA UN CHATROOM ENTRE LOS DOS USUARIOS
					if($SQLroom and $SQLroom->num_rows > 0)
					{
						$room = $SQLroom->fetch_assoc();
						// Envia mensaje a usuario
						$response = sendMessage($room['id'], $rowu['id'], $message, $filename);

						// SI OCURRIO ALGUN ERROR
						if (!$response[0]) {
							// Guardar error
							$errors[$response[1]]++;
						}
						else
						{
							$success++;
						}
					}
					else
					{
						// Guardar error
						$errors['room-no-exist']++;
					}
				}
			}
		}
		else
		{
			setSwal(array('No se ha realizado ninguna acci&oacute;n porque los formularios est&aacute;n vac&iacute;os','', 'warning'));
			return false;
		}
		$string_error = '';
		// DE HABER ERRORES, CREA UN PARRAFO INDICANDOLOS
		if($errors['file-no-uploaded'] > 0 or $errors['room-no-exist'] > 0 or $errors['room-is-closed'] > 0) $string_error = '<br><h3>Mensajes no enviados por las siguientes causas:</h3><br>'.$errors['file-no-uploaded'].': Fotos no se subieron al servidor.<br>'.$errors['room-is-closed'].': Room cerrada.<br>'.$errors['room-no-exist'].': No existió una room_chat al cual enviar el mensaje.';
		// Mostrar mensaje de resultados
		setSwal(array('Operación realizada con éxito', '<h3>Mensajes enviados con exito: '.$success.'</h3>'.$string_error,''));
	}
	// Si hay que enviar eliminar a usuario(s) de la lista
	elseif ($option == 'removeUser')
	{
		$countRemoves = 0;
		foreach ($NamesId as $key => $nameId)
		{
			/* Evitar enviar a este id */
			if($nameId != $rowu['id'])
			{
				$countRemoves = $countRemoves + removeUserLDN($connect->real_escape_string($nameId));
			}
		}
		setSwal(array('Se han eliminado ' . $countRemoves . ' Usuarios', '', 'success'));
	}
}
?>

<?php footer(); ?>
