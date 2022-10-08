<?php
require("core.php");
head();

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname' LIMIT 1");
$rowu  = mysqli_fetch_assoc($suser);

if(isset($_GET["trash"]) && is_numeric($_GET["trash"])){
	mysqli_query($connect, "DELETE FROM `fotosenventa` WHERE id='".$_GET["trash"]."'");
}

//marcando como vistas las notificaciones de nuevas galerias
mysqli_query($connect, "DELETE FROM `notificaciones_fotosnuevas` WHERE player_notificado='$player_id'");

$Friends = $connect->query("SELECT * FROM `friends` WHERE player1 = '$player_id' OR player2 = '$player_id'");
if($Friends){
	$FriendsList = [];
	while ($Friend = mysqli_fetch_assoc($Friends)) {
		if($Friend['player1'] == $rowu['id']){
			$FriendsList[] = $Friend['player2'];
		}else{
			$FriendsList[] = $Friend['player1'];
		}
	}
	
	$FriendsList = implode(',', $FriendsList);
	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<div class="content-wrapper">

    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">

		<div class="row" style="margin:0;">
			<div class="col-sm-12 col-md-6">
				<a class="btn btn-success" style="width:100%;" href="galerias.php">
					<i class="fas fa-camera-retro"></i> Todas las Fotos
				</a>
			</div>
		</div>

        <!--Page content-->
        <!--===================================================-->
        <div class="content">

            <div class="row">

                <div class="col-md-12">


                    <div class="box">
					
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">

                                <tbody> 
								<!-- OCULTO	<?php
										if ($rowu['gender'] == 'mujer'){ 
									?> 
                                    <center>
                                        <button class="btn btn-success" data-toggle="modal" data-target="#trailerModal">
											Agregar Foto										
										</button>
                                    </center>
									<br>
									<?php
										}
									?> OCULTO -->

                                    <div class="card">
                                        <div class="card-body">
<?php
$timeonline = time() - 60;

$total_pages = @$connect->query("SELECT * FROM `fotosenventa` WHERE player_id IN ({$FriendsList}) ORDER BY id DESC")->num_rows;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

$num_results_on_page = 20;
$calc_page = ($page - 1) * $num_results_on_page;

$querycp = $connect->query("SELECT * FROM `fotosenventa` WHERE player_id IN ({$FriendsList}) ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
if (@$querycp->num_rows > 0) {
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
		
		echo '<a href="'. $sitio['site'].$linkc .'" class="btn btn-warning"><i class="fa fa-comments"></i></a>';
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
										suscribirse a '.$rowcpd['username'].' <br/>2000 por créditos especiales 1 mes
									</a><br><br>';
								}else{
									echo '<a class="btn btn-danger">Suscribirme <br/>2000 por créditos especiales 1 mes</a><br><br>';
								}
							}else{
								echo '<a class="btn btn-success"><i class="fa fa-star"></i> Suscrito por 1 mes</a><br><br>';
							}
		
						?>
					</div>
				<?php 
					}
				?>
			</center><br> 
			<?php echo $rowcp['descripcion'];?>
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
	<li class="prev"><a href="friendsgalerias.php?page=<?php echo $page-1 ?>">Anterior</a></li>
	<?php endif; ?>

	<?php if ($page > 3): ?>
	<li class="start"><a href="friendsgalerias.php?page=1">1</a></li>
	<li class="dots">...</li>
	<?php endif; ?>

	<?php if ($page-2 > 0): ?><li class="page"><a href="friendsgalerias.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
	<?php if ($page-1 > 0): ?><li class="page"><a href="friendsgalerias.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

	<li class="currentpage"><a href="friendsgalerias.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

	<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="friendsgalerias.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
	<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="friendsgalerias.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

	<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
	<li class="dots">...</li>
	<li class="end"><a href="friendsgalerias.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
	<?php endif; ?>

	<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
	<li class="next"><a href="friendsgalerias.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
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
</div>
	
	<?php
}else{
	Echo ('sin amigos');
}

?>


<script>
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
	if ($rowu['gender'] == 'mujer'){ 
?> 
<div class="modal fade" id="trailerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Nueva Galeria</h5>
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
	
footer();
?>
