<?php

require("core.php");

$rowu  = $session;

//SELECCIONAR SECCION
$WHERE="";
if(!isset($_COOKIE['prefer'])) {
	setcookie('prefer', 'hetero', time() + 365 * 24 * 60 * 60);
	$prefer="hetero";
}else{
	$prefer=$_COOKIE['prefer'];
}
if (isset($_GET['select_category']) and !empty($_GET['select_category']) )  {
	if ($_GET['select_category']=='hetero' or $_GET['select_category']=='trans') {

		setcookie('prefer', $_GET['select_category'], time() + 365 * 24 * 60 * 60);
		$prefer=$_GET['select_category'];
	}
}

//marcando como vistas las notificaciones de nuevas galerias
	mysqli_query($connect, "DELETE FROM `notificaciones_fotosnuevas` WHERE player_notificado='$player_id'");

	if(isset($_GET["downloadImage"]) && is_numeric($_GET["downloadImage"])){
		$Id = $_GET["downloadImage"];

		$procces = true;

		$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE id='{$Id}'");
		if(!$querycp && !mysqli_num_rows($querycp)){
			$procces = false;
		}else{
			$foto = mysqli_fetch_assoc($querycp);
		}

		$querycp = mysqli_query($connect, "SELECT * FROM `download` WHERE fotoid='{$Id}' AND uid='{$rowu['id']}'");

		if($querycp && !mysqli_num_rows($querycp) && $procces){
			$procces = false;
			if($rowu[$crt1] >= $priceForDownload || $rowu[$crt2] >= $priceForDownload){
				$Creditos = '';

				if($rowu[$crt1] >= $priceForDownload){
					$Creditos = $crt1;
				}
				elseif($rowu[$crt2] >= $priceForDownload){
					$Creditos = $crt2;
				}

				$newBalance = $rowu[$Creditos] - $priceForDownload;
				$sdrtq = mysqli_query($connect, "UPDATE `players` SET ". $Creditos ."='{$newBalance}' WHERE id='{$rowu['id']}'");
				if($sdrtq){
					$dwlimg = mysqli_query($connect, "INSERT INTO `download` (uid, fotoid) VALUES ('{$rowu['id']}', '{$Id}')");
					if($dwlimg){
						$procces = true;
					}
				}

			}
		}

	// Process download
		$filepath = isset($foto) ? $foto['imagen']:null;

		if(file_exists($filepath) && $procces) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'. basename($filepath) .'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '. filesize($filepath));
		flush(); // Flush system output buffer
		readfile($filepath);
		die();
	} else {
		http_response_code(404);
		die();
	}
	exit();
}
head();
?>
<style type="text/css">
@media (min-width: 400px){
	.upload{
		padding: 8px 8px 13px 13px;
	}
	.img-uploadgalery{
		width: 32px;
		height: 32px;
	}

}
@media (max-width: 400px){
	.upload{
		padding: 4px 4px 8px 8px;
	}
}
.upload{
	border-radius: 50%;position: fixed;bottom: 20px;right: 45px;z-index: 10;
}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">

</script>
<div class="content-wrapper">
	<div id="content-container">
		<div class="row" style="margin:0;">
			<div class="col-sm-12 col-md-6">
			<!-- OCULTO 	<a class="btn btn-success" style="width:100%;" href="friendsgalerias.php">
					<i class="fas fa-camera-retro"></i> Fotos de amigas
				</a>
			</div>

			</div>
		<section class="content">
			<div class="row">
				<div style="width: 100%;">
					<div class="box">

							<div class='btn-group'>
									<style>
									.dropdown-backdrop{
										position: unset;
									}
								</style>
								<button class='btn dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style="background-color: #000000d1;color:white;" align="center"><i class="fa fab-gender"></i><?php echo $prefer=='hetero' ? "MUJERES" : strtoupper($prefer); ?></button>
								<div class='dropdown-menu' aria-labelledby='dropdownMenuButton' style="text-align: center;background-color: #000000d1;">
									<a class="btn" href="galerias.php?select_category=hetero"  style="width: 100%;color:white;"><i class=""></i>MUJERES</a><br>
									<hr style="margin: 0;">
									<a class="btn" href="galerias.php?select_category=trans"  style="width: 100%;color:white;"><i class=""></i>TRANS</a>
								</div>
							</div>
							<br>
							<br>
							<div> -->
								<!-- MOSTRAR BOTON MODO DIVERTIDO SI ESTA ACTIVA LA OPCION -->
<center>
							</div>
						</center>
						<br>
						<center>
							<?php /*if ($prefer=="trans"): ?>
								<span>Esta es la sección <strong>Transgénero</strong> porque ellas también son hermosas y merecen tener su sección para ser admiradas, para volver a la sección de mujeres toca arriba donde dice trans y selecciona mujeres</span>
							<?php endif */?>
						</center>
						<?php
						if($rowu['permission_upload'] == 0): ?>
							<center>
								<button class="btn btn-success upload" data-toggle="modal" data-target="#AlertModal" >
									<img src="assets/img/addphoto.png" class="img-uploadgalery" width="20px" height="20px" style="filter:brightness(15);border-color:#07bf6b;">
								</button>
							</center>
							<br>
						<?php else: ?>
							<center>
								<button class="btn btn-success upload" data-toggle="modal" data-target="#trailerModal">
									<img src="assets/img/addphoto.png" class="img-uploadgalery" width="20px" height="20px" style="filter:brightness(15);border-color:#07bf6b;">
								</button>
							</center>
							<br>
						<?php endif ?>

						<div class="box-body row-list">
							<table>
							<?php
							$timeonline = time() - 60;

							/**
							 * Optiene las imagenes de los perfiles teniendo en cuenta las siguientes caracteristicas

							 * 1. Que las fotos sean de la preferencia sexual del usuario

							 * 2. Si el perfil de la foto tiene habilitada la opcion p.hidden_for_old:
							      Deben mostrarse las fotos de ese perfil solo si el usuario es mas antiguo que ese perfil

							 * 3. Si el perfil tiene la opcion mostrar_en_galeria activada

							 * 4. Las fotos optenidas deben ser solo de personas que tenga
							 		  una amistad con el usuario, no debe mostrar fotos de usuarios
							 		  que no tenga amistad
							**/
							$total_pages = $connect->query("SELECT f.`id` AS id,f.`category`,f.`descripcion`, f.`downloadable`,f.`imagen`, f.`linkdedescarga`, f.`player_id`, f.`thumb`, f.`time`, f.`type`, p.`id`,p.`hidden_for_old`, p.`username` FROM `fotosenventa` AS f INNER JOIN `players` AS `p` ON `p`.id = f.`player_id` AND IF(p.`hidden_for_old` != 0 AND p.`id` != '$rowu[id]', p.`hidden_for_old` <= '$rowu[time_joined]', 1=1) LEFT JOIN `friends` AS b ON ((b.`player1` = f.`player_id` && b.`player2` = '$rowu[id]') || (b.`player2` = f.`player_id` && b.`player1` ='$rowu[id]')) AND p.`mostrar_en_galeria` = 1 WHERE f.`category`='$prefer' AND IF(f.`player_id` != '$rowu[id]',b.`id` IS NOT NULL,1=1) GROUP BY f.`id` ORDER BY f.`id` DESC")->num_rows;

							$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

							$num_results_on_page = 40;
							$calc_page = ($page - 1) * $num_results_on_page;

							// SELECCIONAR FOTOS DE AMIGOS
							$querycp = mysqli_query($connect, "SELECT f.`id` AS id,f.`category`,f.`descripcion`, f.`downloadable`,f.`imagen`, f.`linkdedescarga`, f.`player_id`, f.`thumb`, f.`time`, f.`type`, p.`id` AS pid,p.`hidden_for_old`, p.`username` FROM `fotosenventa` AS f INNER JOIN `players` AS `p` ON `p`.id = f.`player_id` AND IF(p.`hidden_for_old` != 0 AND p.`id` != '$rowu[id]', p.`hidden_for_old` <= '$rowu[time_joined]', 1=1) LEFT JOIN `friends` AS b ON ((b.`player1` = f.`player_id` && b.`player2` = '$rowu[id]') || (b.`player2` = f.`player_id` && b.`player1` ='$rowu[id]')) AND p.`mostrar_en_galeria` = 1 WHERE f.`category`='$prefer' AND IF(f.`player_id` != '$rowu[id]',b.`id` IS NOT NULL,1=1) GROUP BY f.`id` ORDER BY f.`id` DESC LIMIT {$calc_page}, {$num_results_on_page}");

							$countcp = mysqli_num_rows($querycp);
							if ($countcp > 0) {
								while ($rowcp = mysqli_fetch_assoc($querycp)) {
									$author_id = $rowcp['player_id'];
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
										$sub = '';
										if(!isFollow($rowcpd['id']) && $rowcpd['id'] != $player_id){
											$sub = $rowcp['type'] == 'suscripciones' ? ' noSub': '';
										}
										$thumbnail=json_decode($rowcp['thumb']);
										$image=json_decode($rowcp['imagen']);
										include "./Row-img.php";
									}
								}
								?>
								</td>
							</tr>
						</table>
					</div>
					<div class="content" align="center">
						<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
							<ul class="pagination">
								<?php if ($page > 1): ?>
									<li class="prev"><a href="galerias.php?page=<?php echo $page-1 ?>">Anterior</a></li>
								<?php endif; ?>
								<?php if ($page > 3): ?>
									<li class="start"><a href="galerias.php?page=1">1</a></li>
									<li class="dots">...</li>
								<?php endif; ?>
								<?php if ($page-2 > 0): ?><li class="page"><a href="galerias.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
								<?php if ($page-1 > 0): ?><li class="page"><a href="galerias.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>
								<li class="currentpage"><a href="galerias.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>
								<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="galerias.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
								<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="galerias.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>
								<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
									<li class="dots">...</li>
									<li class="end"><a href="galerias.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
								<?php endif; ?>
								<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
									<li class="next"><a href="galerias.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>
						<?php
					} else {
						echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Actualmente no hay fotos, Agrega amistades para ver que publican.</strong></div>';
					}

					?>
				</div>
				<br>

			</div>
		</div>
	</div>

</div>

</div>
<!--===================================================-->
<!--End page content-->


</div>
<!--===================================================-->
<!--END CONTENT CONTAINER-->

<?php
if ($rowu['permission_upload'] == 0){
	?>
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Nueva Foto</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div style="text-align: center;font-weight: 600;color: gray;font-size: 15px;">
						Hola, no puedes subir fotos, primero debes pedir autorización. <br/>
						<a href="monetizacion.php">Ver requisitos</a><br/> Una vez autorizado ya no te saldrá este aviso.
					</div>
				</div>
				<div class="modal-footer">
					<div type="button" class="btn btn-secondary" style="background:#dddddd;" data-dismiss="modal" aria-label="Close">
						Ok
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>
	<div class="modal fade" id="trailerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Nueva Foto <b>No fotos normales que se usan en instagram, no GIF, no fotos ya usadas en BellasGram</b>    </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div>
						<textarea class="form-control" id="descripcion" placeholder="Descripcion (opcional)" style="height:100px!important;margin-bottom:20px;"></textarea>
					</div>
					<div>
						<input class="form-control" type="file" name="fotoFile" id="fotoFile" placeholder="" multiple="">
					</div>
					<div>
						<input type="text" min="0" class="form-control" name="linkext" id="linkext" placeholder="Link al archivo(Dejar en blanco si estas subiendo a este servidor)" >
					</div>
				</div>
				<div class="modal-footer">
					<!--<select class="btn btn-secondary" style="background:#dddddd;" name="type" id="postType">
						<option value="publico">Publico</option>
						<option value="suscripciones">suscripciones</option>
					</select>-->
					<select class="btn btn-secondary" style="background:#dddddd;" name="downloadable" id="downloadable">
						<option value="1">Permitir Descargar</option>
						<option value="0">No Permitir</option>
					</select>
					<div type="button" class="btn btn-secondary" style="background:#dddddd;" data-dismiss="modal" aria-label="Close">
						Cancelar
					</div>
					<div type="button" class="btn btn-success" id="sendForm">
						Enviar
					</div>
				</div>
			</div>
		</div>
	</div>
<div id="DownLoadPhoto" class="modal fade in" style="display: none;">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Descarga por 500 créditos</h4>
				<button type="button" class="close" onclick="cancelDownload()">×</button>
			</div>
			<div class="modal-body">
				<center>
					<span class="badge badge-info"><H4> Confirmar la Descarga de la <br/>foto o video por 500 créditos.<br/><br/> Se te descontarán 500 Créditos.<br/><br/> <a href="infocomprafoto.php"style="color:green">Más informacion tocando aquí</a></H4>
						<H5><br/><br/> Si ya compraste la foto o video, <br/>la siguiente ves que la descargues<br/> es gratis.</H5></span>

						<br><br>

						<center>

							<button type="submit" name="comprar" class="btn btn-success" onclick="DownLoadPhoto()">Confirmar</button>

						</center>

						<br><br>
						<button type="button" class="btn btn-primary btn-md btn-block" onclick="cancelDownload()">Cancelar</button>
					</center>
				</div>
			</div>
		</div>
	</div>

	<script>
		var DownloadId = 0;
		var submitDownload = (id) => {
			DownloadId = id;
			$("#DownLoadPhoto").show();
		}
		var cancelDownload = () => {
			DownloadId = 0;
			$("#DownLoadPhoto").hide();
		}
		var DownLoadPhoto = () => {
			window.location.href = window.location.href + '<?php Echo isset($_GET['page']) ? '&':'?'; ?>downloadImage=' + DownloadId;

			cancelDownload()
		}
		var submidComment = function(ths){
			var dataForm = $(ths);
			var data = dataForm.serialize();
			dataForm.find("textarea[name=comment]").val("");
			var galeria_id = dataForm.find("[name=galeria_id]").val();
			console.log( data );
			$.ajax({
				url: "ajax.php?postComment",
				type: "POST",
				data: data
			}).done(function(response){
				var data = $.parseJSON(response);
				if(data.status){
					$(".box-comment-"+galeria_id).append( data.message );
					console.log(data.message);
					swal.fire("Comentario agregado", "", "success");
				}else{
					swal.fire("Su comentario no pude ser enviado", "", "error");
				}
				console.log(response);
			})
		}

		var LikePost = function (ths, id){
			$.ajax({
				url: "ajax.php?like",
				type: "POST",
				data: {
					gid: id
				}
			}).done(function(response){
				var data = $.parseJSON(response);
				console.log(response);
				$(ths).toggleClass("isLike");
				if(data.status){
					swal.fire(data.message, "", "success");
				}else{
					swal.fire(data.message, "", "error");
				}
			})
		}

		$(document).ready(function() {
			$("#sendForm").on("click", function(e){
				e.preventDefault();
				var file = document.getElementById("fotoFile").files;
				var formData = new FormData();

			//AGREGAR TODOS LOS ARCHIVOS AL ARRAY
			for (const files of file)
			{
				formData.append("fotoFile[]", files);
			}
    		//
    		formData.append("descripcion", $("#descripcion").val());
    		formData.append("linkext", $("#linkext").val());
    		formData.append("postType", 'suscripciones');
    		formData.append("downloadable", $("#downloadable").val());
    		formData.append("gender", '<?php echo $rowu['category']; ?>');
    		$("#descripcion").val("");
    		$("#fotoFile").val("");
    		$("#linkext").val("");
    		$("#gender").val("");
    		$('#trailerModal').modal('hide');

    		$.ajax
    		({
    			url: "ajax.php?addGalery",
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
    				window.location.reload()
    				swal.fire(data.message, "", "success");
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
		});
	</script>
	<?php
	footer();
?>

