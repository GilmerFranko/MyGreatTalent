<?php
require("core.php");
if(isset($_GET["downloadImage"])){
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
	$filepath1 = isset($foto) ? json_decode($foto['imagen']):null;
	$filepath = $filepath1[0];
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

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname' LIMIT 1");
$rowu  = mysqli_fetch_assoc($suser);

if(isset($_GET["trash"]) && is_numeric($_GET["trash"])){
	mysqli_query($connect, "DELETE FROM `fotosenventa` WHERE id='".$_GET["trash"]."'");
}

//marcando como vistas las notificaciones de nuevas galerias
mysqli_query($connect, "DELETE FROM `notificaciones_fotosnuevas` WHERE player_notificado='$player_id'");


?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<!-- OCULTO CODIGO QUE HACE MOVERSE LA FOTO
<style>
#player {
  text-align: center;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  padding-bottom: 20px;
}

#player  .player-img {
    width: 100%;
    // overflow:hidden;
    background: transparent;
    position: relative;
    transform: scale3d(1, 1, 1);
    transform-origin: bottom center;
    animation-name: player;
    animation-iteration-count: infinite;
    animation-duration: 0.7s;
    animation-timing-function: ease-in-out;
    object-fit: contain;
}


#player .player-img img:before {
      content: "";
      width: 100px;
      left: 50%;
      transform: translateX(-50%);
      height: 20px;
      bottom: -5px;
      position: absolute;
      z-index: -1;
      display: block;
      background-color: rgba(0, 0, 0, 0.3);
    }

@keyframes bounce {
  0% {
    transform: scale3d(1.2, 1.2, 1.2);
  }
  50% {
    transform: scale3d(1.1, 1.1, 1.1);
  }
  200% {
    transform: scale3d(1.2, 1.2, 1.2);
  }
}

@keyframes player {
  0% {
    transform: scale3d(1, 1, 1);
  }
  30% {
    transform: scale3d(1, 1.03, 1);
  }
  60% {
    transform: scale3d(1.03, 1, 1);
  }
  200% {
    transform: scale3d(1, 1, 1);
  }
}
</style>OCULTO -->

<div class="content-wrapper">

    <div id="content-container">
	
        <section class="content">

            <div class="row">

				<div style="width: 100%;">

                    <div class="box">
					
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">

                                <tbody>								

                                    <div class="card">
                                        <div class="card-body">
<?php
$fotoID = $_GET['fotoID'];

$timeonline = time() - 60;

$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE id='{$fotoID}'");
$countcp = mysqli_num_rows($querycp);
if ($countcp > 0) {
    while ($rowcp = mysqli_fetch_assoc($querycp)) {
        $author_id = $rowcp['player_id'];
        $querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
        $rowcpd    = mysqli_fetch_assoc($querycpd);
		
		$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$author_id'");
		$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);
	
		$sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$author_id' AND fromid='$player_id'");
		$hayunbloqueo2 = mysqli_num_rows($sqlbuscarbloqueo2);
	
	if ($hayunbloqueo < 1 && $hayunbloqueo2 < 1){
		$sub = '';
		if(!isFollow($rowcpd['id']) && $rowcpd['id'] != $player_id){					
			$sub = $rowcp['type'] == 'suscripciones' ? ' noSub': '';
		}
	$Images = json_decode($rowcp['imagen']);
	$thumb = json_decode($rowcp['thumb']);
?><tr>

<td>
	<div class="card text-left" style="position: relative;">
		<div class="card-header bg-secondary mb-3">
			<a href="<?php echo $sitio['site']; ?>profile.php?profile_id=<?php echo $rowcpd['id']; ?>">
				<img src="<?php echo $sitio['site'].$rowcpd['avatar']; ?>" class="img-circle" style="width:65px;">
			</a>
			<strong>
				<div style="display:inline-block;vertical-align:middle;">
				<?php 
					echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpd['id'] . '">' . $rowcpd['username'] . '</a></br>'; 
					if ($rowcpd['timeonline'] > $timeonline) {
						echo '<span style="color:green">online</span>';
					} 
				?> 
				</div>		
			</strong>

			<div style="position:absolute;top:0;right:0;">
	<?php
	
		if($rowcpd['id'] == $player_id || $rowu['role'] == 'Admin'){
			echo '<a href="'. $sitio['site'] .'galerias.php?trash='. $rowcp['id'] .'" class="btn btn-danger" style=""><i class="fa fa-trash"></i></a> ';
			echo'<button class="btn btn-success" data-toggle="modal" data-target="#trailerModal"><i class="fa fa-edit"></i></button>';
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
		
		echo '<a href="'. $sitio['site'].$linkc .'" class="btn btn-warning"><i class="fa fa-comments"></i></a>';
	?>
			</div>
		</div><br>
		<div class="card-body comment-emoticons">

			<center>
				
				<!-- Aqui -->
				<div id="player" style="overflow: hidden;">
					<div class="player-img">				
						<?php
			foreach ($Images as $key => $images) {
							$image = $sitio['site'].$images;
							if ($rowcp['imagen']!=null){
							Echo getSource($image, $sub);
							echo $rowcp['linkdedescarga'] != " " ? "<a href='".$rowcp['linkdedescarga']."'> Ir al link </a>":'';
							}else{ ?>
							<div class="row-image <?php Echo getSourceType($rowcp['imagen']) == 'film' ? 'videoPreview':'';?>"        
							onclick="window.location.href=`<?php Echo $rowcp["linkdedescarga"];?>`"
							style="background-image: url(<?php Echo $sitio['site'] . ($thumb[$key] ? strdr($thumb[$key]):strdr($image)); ?>);">
							<!--TAMBIEN COLOCAR UN BOTON DE LINK PARA PODER REDIRIGIRSE A LA PUBLICACION PARA OTRAS OPCIONES, COMO ELIMINARLA-->
							<div style="position: absolute;top: -86px;right: 96px;">
							<a class="btn-primary" href="<?php Echo $sitio['site'] .'foto.php?fotoID='. $rowcp['id'];?>">
							<button class="btn btn-primary" data-toggle="modal" style="">
							<i class="fa fa-link"></i>                            
							</button>
							</a>
							</div>
							<!--LINK DE DESCARGA-->
							 <?php if($rowcp["linkdedescarga"]!=" "){  ?>
		        				<div style="position: absolute;top: 30vw;width: 100%;">
		            				<a href="<?php Echo $rowcp["linkdedescarga"];?>">
		            					<button class="btn btn-success btn-info-content2" data-toggle="modal" style="">
		               					VER CONTENIDO                            
		            					</button>
		            				</a>
		            				<br><br>
            						<div class="alert alert-success btn-info-content" style="padding: 2px; position: absolute;width: 100%;"><strong><i class="fa fa-info-circle"></i> Este contenio se encuentra fuera de Bellasgram</strong></div>
		        				</div>
		        				<br>
		        			<?php } 
		        		

		        	?>
						</div>
						<br>
						<style>
    @media (max-width: 460px){
        .btn-info-content{
            transform: scale(0.5);
            top: -14vw;
            font-size: 12px;
        }
    }
    @media (max-width: 295px) {
        .btn-info-content{
            font-size: 10px;
            top: -7vw;
        }
    	.btn-info-content2{
            font-size: 8px;
            top: 28px;
        }

    }
</style>
					<?php } ?>
						<?php 
						//BOTON DESCARGAR FOTO
						if(count($Images)<=1 and $sub=='' and $rowcp['downloadable']==1){
							$dwl = mysqli_query($connect, "SELECT * FROM `download` WHERE fotoid='{$rowcp['id']}' AND uid='{$rowu['id']}'");
	
							$price = '';
							if($dwl && !mysqli_num_rows($dwl)){
								$price = $priceForDownload . ' <i class="fas fa-coins"> </i>';
							}

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
							
							$ClickAction = "";
							if($onDownload){
								$ClickAction = 'onclick="submitDownload('. $rowcp['id'] .')"';
							}
							echo '<a class="btn btn-danger" style="margin: auto;display: block;max-width: 200px;margin-top:10px;'. $Disabled .'" 
								'. $ClickAction .'>
								<i class="fa fa-download"></i> Descargar '. $price .'
							</a>';
							}
						
						
						?>
					</div>
				</div>
				
				<?php 
					if(!$sub == '' and $key==0){	
				?>
					<div class="noSubMessage" style="border-radius: 8px;top:40%;">
						<br>
						<div style="color: white;margin:0;">
							Esta foto sólo se muestra a los suscriptores de <strong style="color:#1FD2BB;"><?php Echo $rowcpd['username']; ?></strong>
							<?php if($rowu['eCreditos']<=2000){ ?>
								<br>
								(El costo es 2000 créditos especiales, Tienes <?php echo $rowu['eCreditos']; ?>)
							<?php } ?>
						</div>
						<br>
						<?php 
						
							if (!isFollow($rowcpd["id"])){
								if($rowu['eCreditos']>=2000){
									echo '<a href="#" data-username="'.$rowcpd['username'].'" data-href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpd['id'].'&Subscribe" id="suscribe" class="btn btn-danger">
										Suscribirme 7 dias por <br/>2000 créditos especiales</a><br><br>';
								}else{
									echo '<a href="comprar.php" class="btn btn-danger" style="color:white;background-color:#000000db;border:none;">Comprar créditos especiales</a><br><br>';
									echo '<a href="#" class="btn btn-success" style="color:white;">Suscribirme</a><br><br>';
								}
							}else{
								echo '<a class="btn btn-primary"><i class="fa fa-star"></i> Suscrito por 7 dias</a><br><br>';
							}
		
						?>
					</div>
				<?php 
					}

				//FIN FOREACH
				}
				?>
			</center><br>
			<div class="descripcion">
			<?php echo $rowcp['descripcion'];?>
			</div>
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
	if(!$sub == ''){	
?>
	<i class="fa fa-star" style="font-size:25px;color:#f39c12;vertical-align:sub;margin-left:15px;"></i>
<?php 
	}
?>
</div></div>
<div class="card-body">
<!--BOTON PACKS-->
<hr /><?php
	$packs = $connect->query("SELECT * FROM `packsenventa` WHERE player_id='$author_id' ORDER BY id DESC")->num_rows;
	if($packs>0){ ?>
		<a href="packs.php?id_profile=<?php echo $author_id; ?>" style="color: #444"> <p style="color:#CC99AD;"><i class="fas fa-images"></i> <?php echo $rowcpd['username'] . "  Posee packs en venta. " ?> <b style="color:#337ab7; "> <br/>Ir a la seccion packs</p></b></a>

		
	<?php } ?>
</div>

<hr> 
<?php
if($rowu['id'] == $author_id || $rowu['role'] == 'Admin'){
	$querycpp = mysqli_query($connect, "SELECT * FROM `player_comments` WHERE galeria_id='$rowcp[id]' ORDER BY id ASC LIMIT 500");
	$countcpp = mysqli_num_rows($querycpp);
	if ($countcpp > 0) { ?>
	<div class="comment-body box-comment-<?php Echo $rowcp['id']; ?>">
	<?php
		while ($rowcpp = mysqli_fetch_assoc($querycpp)) {
			$author = $rowcpp['author_id'];
			$querycpdd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author' LIMIT 1");
			$rowcpdd    = mysqli_fetch_assoc($querycpdd);
	?>
		<div class="card-comment card text-left">
			<a href="<?php echo $sitio['site']; ?>profile.php?profile_id=<?php echo $rowcpdd['id']; ?>">
				<img src="<?php echo $rowcpdd['avatar'];?>" style="width:40px;border-radius:30px;">&nbsp;&nbsp;
			</a>
			<div style="display:inline-block;vertical-align:bottom;">
				<strong>
					<?php echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpdd['id'] . '">'.$rowcpdd['username'].'</a>';?>
				</strong>
				<br>
				<?php Echo $rowcpp['comment']; ?>
			</div>		
		</div>
		<hr>
	<?php
		}
	} else {
		echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> sin comentarios</strong></div>';
	}
}else{
	echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Para proteger tu privacidad los comentarios son solo visibles para la dueña de la foto, así las demás personas no verán que le escribes.</strong></div>';
}
?>
</div>
<form onsubmit="submidComment(this);return false;">
	<input type="hidden" name="galeria_id" value="<?php echo $rowcp['id']; ?>">
	<textarea placeholder="Escribe un comentario" name="comment" class="form-control" required></textarea>
	<br />
	<button type="submit" name="postcomment" class="btn btn-success float-right">
		<i class="fa fa-share"></i> Comentar
	</button>
</form>
<?php
	}
}

?>

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


<div class="box-body row-list">
<?php
	$ToID = $author_id;
	include "./fotoRandom.php";
	$RandomID = $rowcp['id'];
	include "./fotoRandom.php";
	$RandomID = $rowcp['id'];
	include "./fotoRandom.php";
	$RandomID = $rowcp['id'];
	include "./fotoRandom.php";
?>
</div>

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
	if ($rowu['gender'] == 'mujer'){ 
		if($rowcpd['id'] == $player_id || $rowu['role'] == 'Admin'){
?> 
<div class="modal fade" id="trailerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Editar Foto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div>
					<textarea class="form-control" id="descripcion" placeholder="Descripcion" style="height:100px!important;margin-bottom:20px;"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<input id="idphoto" type="text" name="idphoto" hidden="" value="<?php echo $_GET['fotoID'] ?>">
				<select class="btn btn-secondary" style="background:#dddddd;" name="type" id="postType">
					<option value="publico">Publico</option>
					<option value="suscripciones">suscripciones</option>
				</select>
				<div type="button" class="btn btn-secondary" style="background:#dddddd;" data-dismiss="modal" aria-label="Close">
					Cancelar
				</div>
				<div type="button" class="btn btn-primary" id="sendForm">
					Enviar
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	}
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
		window.location.href = window.location.href + '&downloadImage=' + DownloadId;
		
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
			var formData = new FormData();
			formData.append("descripcion", $("#descripcion").val());
			formData.append("postType", $("#postType").val());
			formData.append("idphoto", $("#idphoto").val());
			$('#trailerModal').modal('hide');
			$.ajax({
				url: "ajax.php?updatephoto", 
				type: "POST",
				data: formData, 
				processData: false,
				contentType: false
			}).done(function(response){
				var data = $.parseJSON(response);
				console.log(response);
				if(data.status){
					$(".descripcion").html($("#descripcion").val());
					$("#descripcion").val("");
					swal.fire(data.message, "", "success");
				}else{
					swal.fire("Error!", data.message, "error");
					$("#descripcion").val("");
				}
			})
        });
    });
</script>
<?php
footer();
?>
