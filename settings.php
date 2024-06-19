<?php
require("core.php");
head();
function resize_image($file, $w, $h, $crop=FALSE) {
	list($width, $height) = getimagesize($file["tmp_name"]);

	if($file["type"] == 'image/png'){
		$src = ImageCreateFromPNG($file["tmp_name"]);
	}else{
		$src = ImageCreateFromJPEG($file["tmp_name"]);
	}

	$dst = imagecreatetruecolor($w, $h);
	imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);

	return $dst;
}

if (isset($_POST['save'])) {
	if (isset($_POST['avatar'])){
		$avatar = $_POST['avatar'];
	}else{
		$avatar = $rowu['avatar'];
	}

	$recommendation = (isset($_POST['searchAjax']) AND !empty($_POST['searchAjax'])) ? $_POST['searchAjax'] : "";

    // COMPRUEBA QUE EL NOMBRE QUE RECOMENDO EXISTE
	$result = getUser($recommendation, true);
		// SI EXISTE EL NOMBRE DEL USUARIO Y SON AMIGOS
	if ($result AND $result->num_rows > 0) {

		$time = time();
		$rowRecommended = $result->fetch_assoc();

			// COMPRUEBA QUE PUEDO VER EL PERFIL DEL USUARIO Y SOY AMIGO
		if(canSeeYourProfile($rowRecommended['id']) AND areFriends($rowRecommended['id'],$rowu['id']))
		{
				// BORRA EL O LOS NOMBRES YA RECOMENDADOS DE MI PERFIL
			$connect->query("DELETE FROM players_recommendations WHERE fromid='$rowu[id]'");

				// AGREGA EL NOMBRE A RECOMENDACIONES
			$connect->query("INSERT INTO players_recommendations (fromid, toid, time) VALUES ('$rowu[id]', '$rowRecommended[id]', '$time')");
		}
	}
		//

	$description = $_POST['description'];

	if($rowu['gender']=='mujer'){
		$perfiloculto = empty($_POST['perfiloculto']) ? 'no' : $_POST['perfiloculto'];
		$mostrar_en_galeria = empty($_POST['mostrar_en_galeria']) ? 0 : $connect->real_escape_string($_POST['mostrar_en_galeria']);
		$hidetochat = empty($_POST['hidetochat']) ? 'no' : $_POST['hidetochat'];
	}else{
		$perfiloculto = 'no';
		$hidetochat = 'no';
	}


	if ($_FILES['avafile']['name'] != '') {
		$target_dir    = "images/avatars/";
		$target_file   = $target_dir . basename($_FILES["avafile"]["name"]);
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
		$filename      = $uname . '.' . $imageFileType;

		$uploadOk = 1;

        // Check if image file is a actual image or fake image
		$check = getimagesize($_FILES["avafile"]["tmp_name"]);
		if ($check !== false) {
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}

        // Check file size
		if ($_FILES["avafile"]["size"] > 1000000) {
			echo "Sorry, your file is too large.";
			$uploadOk = 0;
		}

		$img_original = $_FILES["avafile"]["tmp_name"];
		$img_nueva = $_FILES["avafile"]["tmp_name"];

		$img = resize_image($_FILES["avafile"], 300, 300);

		//Header("Content-Type: image/png");
		if ($uploadOk == 1) {
			$avatar = "images/avatars/" . $filename;
			if($_FILES["avafile"]['type'] == 'image/png'){
				ImagePNG($img, $avatar);
			}else{
				ImageJPEG($img, $avatar);
			}
			$avatar = $avatar . '?' . time();
            //move_uploaded_file($_FILES["avafile"]["tmp_name"], "images/avatars/" . $filename);
		}
	}

	//exit();

	$querysd = $connect->query("UPDATE `players` SET `avatar` = '$avatar', `description` = '$description', `perfiloculto` = '$perfiloculto', `hidetochat` = '$hidetochat', `mostrar_en_galeria` = '$mostrar_en_galeria' WHERE `id` = '$player_id'");

	error_log("UPDATE `players` SET `avatar` = '$avatar', `description` = '$description', `perfiloculto` = '$perfiloculto', `hidetochat` = '$hidetochat', `mostrar_en_galeria` = '$mostrar_en_galeria' WHERE `id` = '$player_id'");

	error_log(var_export($connect->error,1));
	echo '<meta http-equiv="refresh" content="0;url=settings.php">';
}

if (isset($_GET['stopRecommending'])) {
// BORRA EL O LOS NOMBRES YA RECOMENDADOS DE MI PERFIL
	$connect->query("DELETE FROM players_recommendations WHERE fromid='$rowu[id]'");
}
if(isset($_GET['trustFNP']) AND !empty($_GET['trustFNP']))
{
	deleteFriendsNoBuyPacks($rowu['id']);
	//echo '<meta http-equiv="refresh" content="3;url=mispacks.php">';
}

$sqlPR = $connect->query("SELECT *,p.`id` AS p_id FROM players_recommendations AS pr INNER JOIN players AS p ON p.`id`=pr.`toid` WHERE pr.`fromid`='$rowu[id]'");
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<div class="content-wrapper">
	<div id="content-container">
		<div class="content row panel-group" id="accordion">
			<section class="">
				<div class="panel-heading" style="text-align: center; padding: 0;">
					<h3 class="panel-title">
						<a class="btn btn-danger" data-toggle="collapse" data-parent="#accordion" href="#collapse1" style="background-color: rgba(246,178,181,0.2); color: #ee6e73; border: none; width: 100%;"><i class="fas fa-envelope"></i> Perfil</a>
					</h3>
				</div>
				<div id="collapse1" class="panel-collapse collapse in">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title"></h3>
						</div>
						<form enctype="multipart/form-data" id="save" name="save" method="POST" action="">
							<center>
								<div class="form-group">
									<label class="content-input">
										<input id="mostrar_en_galeria" type="checkbox" name="mostrar_en_galeria" value="1" <?php echo $rowu['mostrar_en_galeria'] == '1' ? 'checked=""' :''; ?>>Mostrar mis fotos en la galeria
										<i></i>
									</label>
								</div>
								<?php
								if(true){
									?>
									<div class="form-group">
										<div class="form-group">
											<label class="content-input">
												<input id="" type="checkbox" name="perfiloculto" value="si" <?php echo $rowu['perfiloculto'] == 'si' ? 'checked=""' :''; ?>>Perfil oculto
												<i></i>
											</label>
										</div>
									</div><!-- OCULTO
									<div class="form-group">
										<label class="content-input">
											<input id="typeProgram" type="checkbox" name="hidetochat" value="si" <?php echo $rowu['hidetochat'] == 'si' ? 'checked=""' :''; ?>>Perfil solo visible a quienes se registraron desde el Chat
											<i></i> 
										</label>
									</div>-->
									<br>
									<?php
								}
								?>
								<div class="form-group">
									<i class="fas fa-align-justify"></i>
									<span>Descripción - También puedes poner links hacia fotos o v&iacute;deos</span>
									<input type="text" class="form-control" id="description" name="description" value="<?php Echo $rowu['description']; ?>" style="width: 70%;">
								</div>
								<!-- INPUT RECOMENDACIONES -->
								<div class="form-group" align="center">
									<?php if ($sqlPR AND $sqlPR->num_rows > 0): ?>
										<?php $PR = $sqlPR->fetch_assoc(); ?>

										Est&aacute;s recomendando a <?php echo createLink('profile',$PR['username'], array('profile_id' => $PR['p_id'])); ?> en tu perfil, <a href="settings.php?stopRecommending" style="color:red;">Dejar de recomendar</a>

										<?php else: ?>
											<span class="">
												Recomienda a un usuario en tu Perfil
											</span>
											<input id="searchAjax" type="text" class="form-control" placeholder="Buscar Usuario" name="searchAjax" style="width: 200px" autocomplete="off">
										<?php endif ?>
									</div>
									<!-- /input-group -->
									<div class="form-group">
										Ver a todas las <a class="text-success" href="bloqueados.php">Personas que has bloqueado</a>
									</div>
									<br>
									<div class="form-group">
										<label for="avatar" style="width: 100%;">
											<div class="row" style="margin: 0;border: solid 2px #ecf0f5;">
												<div class="col-sm-6">
													<center>
														<h4>Foto de portada</h4>
													</center>
													<!--PORTADA-->
													<div class="cover-page" style="background: url('<?php echo $rowu['cover-page']=='' ? '' : $rowu['cover-page']; ?>') no-repeat center center;background-size: cover;border-radius: 0;box-shadow: unset;display: flex;flex-direction: column;align-items: center;justify-content: center;position: unset;">
														<a href="#" class="btn btn-primary" onclick="$('#cover-page').modal('show')"><i>Cambiar</i></a>
													</div>
												</div>
												<div class="col-sm-6">
													<center>
														<h4>Foto de perfil</h4>
													</center>
													<div id="preview" class="select-image" style="background-color: unset;background: url('<?php echo $rowu['avatar']=='' ? '' : $rowu['avatar']; ?>') no-repeat center center;background-size: cover;border-radius: 0;box-shadow: unset;display: flex;flex-direction: column;align-items: center;justify-content: center;position: unset;">
														<span style="font-size:25px;">

															<div id="dragAndDrop" class="text-white" style="width: 200px;height: 258px;display: flex;flex-direction: column;justify-content: center;">
																<div style="backdrop-filter: blur(9px); border-radius: 24px;padding: 3px;background-color:#00000033;">
																	<i class="fa fa-upload"  style="font-size:40px;"></i>
																	Arrastrar y soltar
																</div>
															</div>
														</span>
														<div class="btn btn-primary">Seleccionar de mi galeria</div>
														<input type="file" class="custom-file-input" name="avafile" accept="image/*" id="imgInp">
													</div>
												</div>
											</div>
										</label>
									</div>
									<div class="">
										<button type="submit" name="save" class="btn btn-success">Guardar</button>
									</div>
								</center>
							</form>
						</div>

						<div id="cover-page" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title">Subir Foto de Portada</h4>
									</div>
									<center>
										<div class="modal-header">
											<h6 class="modal-title">La imagen no puede exceder el limite se subida. <br></h6>
											<h6 class="modal-title">Limite de subida: <strong>300KB </strong>(Las fotos muy largas es posible que salgan con bordes blancos)</h6>
										</div>
									</center>
									<div>
										<input class="form-control" type="file" name="fotoFile" id="fotoFile" placeholder="" multiple="">
									</div>
									<center>
										<div type="button" class="btn btn-success" id="send_cover-page">
											Subir
										</div>
									</center>
								</div>
							</div>
						</div>
					</div>
				</section>
				<!-- Cuenta -->
				<section class="">
					<div class="panel-heading" style="text-align: center; padding: 0;">
						<h3 class="panel-title">
							<a class="btn btn-danger" data-toggle="collapse" data-parent="#accordion" href="#collapse2" style="background-color: rgba(246,178,181,0.2); color: #ee6e73; border: none; width: 100%;"><i class="fas fa-envelope"></i> Cuenta</a>
						</h3>
					</div>
					<div id="collapse2" class="panel-collapse in collapse text-center">
						<!-- Cambiar contraseña -->
						<div class="box" style="margin: 15px;">
							<a href="cambiarclave.php" class="btn btn-primary">CAMBIAR CONTRASEÑA</a>
						</div>

						<!-- Eliminar cuenta -->
						<div class="box" style="margin: 15px;">
							<button id="deleteAccount" class="btn btn-danger" onclick="DeleteAccountXD(0);"><i class="fa fa-ban"></i> ELIMINAR CUENTA</button>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>


	<style>
		#preview {
			height: 100%;
		}
		.select-image {
			background: #ddd;
			padding: 20px;
			border-radius: 5px;
			position: relative;
		}
		.select-image input[type=file] {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			opacity: 0;
		}
	</style>
	<!--===================================================-->
	<!--End page content-->

	<!--===================================================-->
	<!--END CONTENT CONTAINER-->

	<script>
		function readImage (input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#preview').css('background', 'url("'+e.target.result+'")no-repeat center center'); // Renderizamos la imagen
					$('#preview').css('background-size', 'cover');
					$('#dragAndDrop').css('opacity', '0')
				}
				reader.readAsDataURL(input.files[0]);
			}
		}

		function DeleteAccount(count = 0) {
			if(count == 0) {
				swal.fire({
					title:"¿Estas seguro que quieres eliminar tu cuenta?",
					icon: 'warning',
					html: 'Si eliminas tu cuenta, no podrás recuperar tus datos, aunque podrás volver a registrarte.',
					showDenyButton: true,
					denyButtonText: 'Cancelar!',
					confirmButtonText: 'Si, estoy seguro!',
					showLoaderOnConfirm: true,
				}).then((result) => {
					if (result.isConfirmed) {
						swal.fire({
							title:"Si eliminas tu cuenta, no podrás recuperar tus datos, aunque podrás volver a registrarte.",
							icon: 'warning',
							html: 'Ingresa tu contraseña: <input id="swal-input1" class="swal2-input" type="password">' + '<br><br>Confirma tu contraseña: <input id="swal-input2" class="swal2-input" type="password"><br><br>',
							showDenyButton: true,
							denyButtonText: 'Cancelar!',
							confirmButtonText: 'Eliminalar mi cuenta permanentemente!',
							showLoaderOnConfirm: true,
						}).then(function() {
							if($("#swal-input1").val() != $("#swal-input2").val())
							{
								swal.fire("Las contraseñas no coinciden","","info")
							}
							else
							{
        			// ENVIAR PETICION AL SERVIDOR
        			$.post('ajax.php?DeleteAccount', '&currentPassword=' + encodeURIComponent($("#swal-input2").val()) + '&confirmPassword='+ encodeURIComponent($("#swal-input1").val()) + '&token=<?php echo md5('token' . $rowu['username']);?>', function(a) {
        				success: {
        					if (a.charAt(0) == '1') {
        						swal.fire({title: a.substring(2),icon:'success'}).then(function (){ location.href ="index.php"})
        					} else {
	                  // MOSTRAR MENSAJE
	                  swal.fire({title: a.substring(2),icon:'error'}).then(function (){ location.reload})
	                }
	              }
	            });
        		}
        	})

					}
				})
			}
		}

		$(document).ready(function() {

			$("#imgInp").change(function () {
			// Código a ejecutar cuando se detecta un cambio de archivO
			readImage(this);
		});
			$("#send_cover-page").on("click", function(e){
				e.preventDefault();
				var file = document.getElementById("fotoFile").files[0];
				var formData = new FormData();
				formData.append("fotoFile", file);
				formData.append("id", '<?php echo $rowu["id"] ?>');
				$("#fotoFile").val("");
				$('#cover-page').modal('hide');
				$.ajax
				({
					url: "ajax.php?add_cover-page",
					type: "POST",
					data: formData,
					contentType:false,
					cache: false,
					processData:false
				}).done(function(response)
				{
					var data = $.parseJSON(response);
					console.log(response);
					if(data.status)
					{
						var element = $(".box-body").find("tbody").find("tr:first-child");
						element.before( data.message );

						// Notifica exíto y espera
						swal.fire(data.message, "", "success").then(() => {
							window.location.reload()
						})
					}
					else if(data.message=="link-incorrect")
					{
						swal.fire("Error!", "Parece que la URL que ingresaste no esy correcta", "error");
					}
					else
					{
						swal.fire("Error!", data.message, "error");
					}
					console.log(response);
				})
			});
			$('#searchAjax').typeahead({
				source: function (search, result) {
					var formData = new FormData();
					formData.append("search", search);
					$.ajax({
						url: "ajax.php",
						type: "POST",
						data: formData,
						contentType:false,
						cache: false,
						processData:false,

					}).done(function(response)
					{
						console.log(response);
						var response = $.parseJSON(response);
						result($.map(response, function (item) {
							return item;
						}));

					});
				}
			});
		});


	</script>
	<?php
	footer();
	?>
