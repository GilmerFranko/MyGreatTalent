<?php
require("core.php");

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname' LIMIT 1");
$rowu  = mysqli_fetch_assoc($suser);

if(isset($_GET["trash"]) && is_numeric($_GET["trash"])){
	mysqli_query($connect, "DELETE FROM `fotosenventa` WHERE id='".$_GET["trash"]."'");
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
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">



</script>
<div class="content-wrapper">

    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">

		<div class="row" style="margin:0;">
			
			<div class="col-sm-12 col-md-6">
				<a class="btn btn-success" style="width:100%;" href="friendsgalerias.php">
					<i class="fas fa-camera-retro"></i> Fotos de amigas
				</a>
			</div>
	
		</div>

        <!--Page content-->
        <!--===================================================-->
        <section class="content">

            <div class="row">

                <div class="col-md-12">


                    <div class="box">
                       <div class="box-body">
                             <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">

                                <tbody>
									<?php
										if($rowu['permission_upload'] == 0){
											?> 
											<center>
											  	<button class="btn btn-success" data-toggle="modal" data-target="#AlertModal">
													Agregar Foto										
												</button>
											</center>
											<br>
											<?php
										}
										elseif ($rowu['gender'] == 'hombre'){ 
									?> 
                                    <center>
                                      <button class="btn btn-success" data-toggle="modal" data-target="#trailerModal">
											Agregar Foto										
										</button>
                                    </center>
									<br>
									<?php
										}
									?> 
                                    <div class="card">
                                        <div class="card-body">
<?php
$timeonline = time() - 60;
include "./fotoRandom.php";

$total_pages = $connect->query("SELECT * FROM `fotosenventa` ORDER BY id DESC")->num_rows;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

$num_results_on_page = 20;
$calc_page = ($page - 1) * $num_results_on_page;

$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
$countcp = mysqli_num_rows($querycp);
if ($countcp > 0) {
    while ($rowcp = mysqli_fetch_assoc($querycp)) {
        $author_id = $rowcp['player_id'];
        $querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
        $rowcpd    = mysqli_fetch_assoc($querycpd);
		if($rowcpd['perfiloculto']!='no'){
			
			$friend = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$author_id'");
			$friend01 = mysqli_num_rows($friend);
			
			$friend2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$author_id' AND player2='$player_id'");
			$friend02 = mysqli_num_rows($friend2);
			
			if($friend02==false && $friend01==false){
				continue;
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
?><tr>

<td>
	<div class="card text-left" style="position: relative;">
		<div class="card-header bg-secondary mb-3">
			<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowcpd['id']; ?>">
				<img src="<?php echo $sitio['site'].$rowcpd['avatar']; ?>" class="img-circle" style="width:65px;">
				<strong>
					<div style="display:inline-block;vertical-align:middle;">
					<?php 
						echo  $rowcpd['username'] . '</br>'; 
						if ($rowcpd['timeonline'] > $timeonline) {
							echo '<span style="color:green">online</span>';
						} 
					?> 
					</div>		
				</strong>
			</a>

			<div style="position:absolute;top:0;right:0;">
				<a href="<?php Echo $sitio['site'].'foto.php?fotoID='.$rowcp['id'];?>" class="btn btn-primary"><i class="fa fa-link"></i></a>
	<?php
	
		$dwl = mysqli_query($connect, "SELECT * FROM `download` WHERE fotoid='{$rowcp['id']}' AND uid='{$rowu['id']}'");
		
		$price = '';
		if($dwl && !mysqli_num_rows($dwl)){
			$price = $priceForDownload . ' <i class="fas fa-coins"> </i>';
		}
		if($rowcpd['id'] == $player_id || $rowu['role'] == 'Admin'){
			echo '<a href="'. $sitio['site'] .'galerias.php?trash='. $rowcp['id'] .'" class="btn btn-danger" style="margin-right:10px;"><i class="fa fa-trash"></i></a> ';
		}	

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
		
	/* OCULTOboton enviar mensaje	echo '<a href="'. $sitio['site'].$linkc .'" class="btn btn-warning"><i class="fa fa-comments"></i></a>'*/;
	?>
			</div>
		</div><br>
		<div class="card-body comment-emoticons">

			<center>
				<?php Echo getSource($sitio['site'].$rowcp['imagen'], $sub, $sitio['site'].'foto.php?fotoID='.$rowcp['id']); ?>
				<?php 
					if(!$sub == ''){	
				?>
					<div class="noSubMessage">
						<div class="title">
							Esta foto solo se mostrara a los <br/>suscriptores de <?php Echo $rowcpd['username']; ?>
						</div>
						<br>
						<br>
						<br>
						<?php 
						
							if (!isFollow($rowcpd["id"])){
								if($rowu['eCreditos']>=2000){
									echo '<a href="#" data-username="'.$rowcpd['username'].'" data-href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpd['id'].'&Subscribe" id="suscribe" class="btn btn-success">
										suscribirse a '.$rowcpd['username'].' por <br/>2000 créditos especiales 1 mes
									</a><br><br>';
								}else{
									echo '<a class="btn btn-success">Suscribirme por <br/>2000 créditos especiales 1 mes</a><br><br>';
								}
							}else{
								echo '<a class="btn btn-primary"><i class="fa fa-star"></i> Suscrito por 1 mes</a><br><br>';
							}
		
						?>
					</div>
				<?php 
					}
				?>
			</center><br> 
			<?php 
				echo $rowcp['descripcion'];
				$onDownload = false;
				$Disabled = "";
				if($rowu[$crt1] >= $priceForDownload || $rowu[$crt2] >= $priceForDownload){
					$onDownload = true;
				}else{					
					$Disabled = "filter: grayscale(100%);";
				}
				
				if($price == '') {
					$Disabled = "";
					$onDownload = true;
				}
				
				/* OCULTO boton descargar
				$ClickAction = "";
				if($onDownload){
					$ClickAction = 'onclick="submitDownload('. $rowcp['id'] .')"';
				}
				echo '<a class="btn btn-danger" style="margin-right:10px;margin: auto;display: block;max-width: 200px;margin-bottom: 10px;'. $Disabled .'" 
					'. $ClickAction .'>
					<i class="fa fa-download"></i> Descargar '. $price .'
				</a>';*/
			?>
		</div>
		<div class="card-footer">
<?php 

//boton me gusta

$megustasql = mysqli_query($connect, "SELECT * FROM `player_megusta` WHERE player_id='$player_id' AND galeria_id='$rowcp[id]'");
$countmegusta = mysqli_num_rows($megustasql);
	
$totalmegustasql = mysqli_query($connect, "SELECT * FROM `player_megusta` WHERE galeria_id='$rowcp[id]'");
$totalmegustas = mysqli_num_rows($totalmegustasql);

if ($countmegusta < 1){
	$isLike = "";
}else{
	$isLike = "isLike ";
}
echo '<button type="submit" name="megusta" class="'.$isLike.'btn btn-success float-right" onclick="LikePost(this, '.$rowcp['id'].');"> 
	<i class="fa fa-thumbs-up"></i>
</button>+'.$totalmegustas.' likes';

?>		
<?php 
	if($rowcp['type'] == 'suscripciones'){	
?>
	<i class="fa fa-star" style="font-size:25px;color:#f39c12;vertical-align:sub;margin-left:15px;"></i>
<?php 
	}
?>
</div></div><br />

<?php
	}
    }
?>
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
    echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Actualmente no hay fotos</strong></div>';
}

?>

                                            </td>
                                            </tr>

                                        </div>
                                    </div>
                                </tbody>
                            </table>
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
				<h5 class="modal-title" id="exampleModalLabel">Nueva Foto <b>No fotos normales que usas en instagram, no fotos que ya usaste en BellasGram.</b>    </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div style="text-align: center;font-weight: 600;color: gray;font-size: 15px;">
					Hola, no puedes subir fotos, primero debes verificar tu cuenta para confirmar ser quien dices ser. <br/>
					Se responderá por Email<br/>
					<a href="https://bellasgram.com/index.php?app=site&section=contact">Solicitar verificación</a><br/> Esta nueva medida 1-1-2021 permitirá solo subir fotos a mujeres verificadas.
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
<?php
	if ($rowu['gender'] == 'hombre'){ 
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
					<input class="form-control" type="file" name="fotoFile" id="fotoFile" placeholder="">
				</div>
			</div>
			<div class="modal-footer">
				<select class="btn btn-secondary" style="background:#dddddd;" name="type" id="postType">
					<option value="publico">Publico</option>
					<option value="suscripciones">suscripciones</option>
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
<?php
	}
?> 

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
			var file = document.getElementById("fotoFile").files[0];
			var formData = new FormData();
			formData.append("fotoFile", file);
			formData.append("descripcion", $("#descripcion").val());
			formData.append("postType", $("#postType").val());
			$("#descripcion").val("");
			$("#fotoFile").val("");
			$('#trailerModal').modal('hide');
			$.ajax({
				url: "ajax.php?addGalery", 
				type: "POST",
				data: formData, 
				processData: false,
				contentType: false
			}).done(function(response){
				var data = $.parseJSON(response);
				console.log(response);
				if(data.status){
					var element = $(".box-body").find("tbody").find("tr:first-child");
					element.before( data.message );
					swal.fire("Foto Publicada!", "", "success");
				}else{
					swal.fire("Error!", data.message, "error");
				}
			})
        });
    });
</script>
<?php
footer();
?>
