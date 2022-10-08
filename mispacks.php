<?php
require("core.php");
head();

if ($rowu['gender'] == 'hombre'){ 

	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
	exit;
}
// BORRAR PACK
if (isset($_GET['trash_id']) and !empty($_GET['trash_id']) ) {
	$idtrash = mysqli_real_escape_string($connect,$_GET['trash_id']);
	$select = mysqli_query($connect,"SELECT id,player_id,imagens,imagen FROM `packsenventa` WHERE id=$idtrash");
	if ($select) {
		if (mysqli_num_rows($select)>0) {
			$row_delete=mysqli_fetch_assoc($select);

    			//SI ES PROPIETARIO DE LA NOTIFICACION
			if ($row_delete['player_id']==$rowu['id'] or $rowu['role']=="Admin") {
				if(!empty($row_delete['imagens']) ){
					$array = json_decode($row_delete['imagens']);
					foreach($array as $key => $Frs){
						unlink($Frs);
					}

				}
				if(!empty($row_delete['imagen']) ){
					$array = json_decode($row_delete['imagen']);
					foreach($array as $key => $Frs){
						unlink($Frs);
					}

				}
    				//BORRAR
				$delete = mysqli_query($connect, "DELETE FROM `packsenventa` WHERE id=$idtrash");

    				//BORRAR NOTIFICACIONES ENVIADAS ANTERIORMENTE
				if ($delete)
				{
					$consult=$connect->query("DELETE FROM `players_notifications` WHERE `action`='$idtrash'");
				}
			}
		}
	}
}
$query = mysqli_query($connect, "SELECT * FROM `packsenventa` WHERE player_id='$player_id' ORDER BY id DESC");
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">



</script>
<div class="content-wrapper">
	<div id="content-container">
		<section class="content-header">
			<h1><i class="fas fa-layer-group"></i> Mis Packs en venta</h1>
		</section>
		<section class="content">
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title"></h3>
						</div>
						<center>
							<div class="alert alert-dismissible alert-secondary">
								<h5><div class="alert alert-dismissible alert-success"><div >
									<center><b>Mis packs en venta</b>
										<br/>(Para retirar el dinero tienes que verificar tu cuenta)</center>
									</div></div>
								</h5>
							</div>
							<div class="center">
								<div class="box">
									<div class="box-body">
										<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>Descripci&oacute;n</th>
													<th>Link del Pack</th>
													<th>Precio</th>
													<th>Ventas Realizadas</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<?php

												while ($rowr = mysqli_fetch_assoc($query)):?>
													<tr>
													<td><?php echo $rowr['descripcion'];?></td>
													<td><?php echo createLink('pack', 'Ir al Pack', array('ID' => $rowr['id'])); ?></td>
													<td><?php echo $rowr['precio'];?></td>
													<td>
														<?php if ($rowr['ventasrealizadas'] > 0): ?>
															<a href="javascript:salesPacksMade('<?php echo $rowr['id']; ?>')"> <?php echo $rowr['ventasrealizadas'];?> </a>
														<?php else: ?>
															<?php echo $rowr['ventasrealizadas']; ?>
														<?php endif ?>
													</td>
													<td>
														<a href="#" onclick="preguntar('<?php echo $sitio["site"].'mispacks.php?trash_id='. $rowr["id"];?>')" class="btn btn-danger">
															<i class="fa fa-trash"></i>
														</a>
													</td>
													</tr>
												<?php endwhile ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</center>
					</div>
					<div class="box" align="center">
						<br>
						<div class="">
							<i class="fa fa-info-circle text-success"> </i>
							<span>Bloquear amigos de mi perfil que no hayan comprado ninguno de mis Packs</span>
							<a class="actionQuest btn btn-success" href="#" data-href="<?php echo createLink('settings' ,null, array('trustFNP' => 'true'),true) ?>" data-quest="Estas seguro que quieres realizar esta accion!?" data-btnaction="Si!" type="submit">Bloquear</a>
						</div>
						<br>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
	<script>
		$(document).ready(function() {
			history.pushState({data:true}, "Titulo", "mispacks.php");
			$('#dt-basic').dataTable( {
				"responsive": true,
				"language": {
					"paginate": {
						"previous": '<i class="fas fa-angle-left"></i>',
						"next": '<i class="fas fa-angle-right"></i>'
					}
				}
			} );

		});
		function preguntar(href) {
			event.preventDefault();
			swal.fire({
				title: "Estás seguro que quieres eliminar este Pack?",
				text: "No podras recuperar este pack!",
				icon: "warning",
				showCancelButton: true,
   			confirmButtonText: `Si, Borralo!`,
			}).then(function(value) {
				var form = $('#miFormulario');
				if (value.isConfirmed) window.location.href = href;
			});
		}
		function salesPacksMade(idPack){
			$.post('ajax.php?getUsersWhoBoughtPack', {'idPack' : idPack} , function(a) {
				console.log(a);
        success:{
				swal.fire({title: "Usuarios que compraron tu Pack",html: a,type: "success"});
				}
			});
		}
	</script>
	<?php
	footer();
	?>
