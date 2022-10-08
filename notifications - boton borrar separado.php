<?php
require("core.php");
head();

// ACEPTAR TODAS LAS SOLICITUDES DE AMISTAD
if (isset($_POST['aceptartodas'])){

	// SELECCIONA TODAS LAS SOLICITUDES DE AMISTAD PENDIENTES
	$queryall = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE toid = '$player_id' AND not_key='newAmistad'");

	while ($solicitud = mysqli_fetch_assoc($queryall)) {

		// GUARDAR ID DE QUIEN ENVIO LA SOLICITUD DE AMISTAD
		$amigoaceptadoall = $solicitud['fromid'];
		
		// AGREGAR AMISTAD
		$agregandoamigos     = mysqli_query($connect, "INSERT INTO `friends` (player1, player2) VALUES ('$player_id', '$amigoaceptadoall')");
		
		// BORRAR LA SOLICITUD DE AMISTAS
		$borrarsolicitud    = mysqli_query($connect, "DELETE FROM `players_notifications` WHERE id='$solicitud[id]'");		
	}
}

// ACEPTAR SOLICITUD DE AMISTAD
if (isset($_GET['aceptar_id'])){

	$Aid = $_GET['aceptar_id'];
	$queryjsa = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE id='{$Aid}' AND not_key='newAmistad'");
	$rowjsa = mysqli_fetch_assoc($queryjsa);
		
	$amigoaceptado = $rowjsa['fromid'];
	
	$queryjsc = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$amigoaceptado' AND player2='$player_id'");
    $countjsc = mysqli_num_rows($queryjsc);
	
	$queryjsd = mysqli_query($connect, "SELECT * FROM `friends` WHERE player2='$amigoaceptado' AND player1='$player_id'");
    $countjsd = mysqli_num_rows($queryjsd);
    
	if ($countjsc < 1 && $countjsd < 1){		
		$agregandoamigo = mysqli_query($connect, "INSERT INTO `friends` (player1, player2) VALUES ('$player_id', '$amigoaceptado')");	
		$borrarsolicitud = mysqli_query($connect, "DELETE FROM `players_notifications` WHERE id='{$Aid}' AND not_key='newAmistad'");		
	}
}

if (isset($_GET['trash'])){
	$borrarsolicitud    = mysqli_query($connect, "DELETE FROM `players_notifications` WHERE id='$_GET[trash]' AND `toid`='$rowu[id]'");
}

// COLOCAR EN VISTO TODAS LAS NOTIFICACIONES
view_notifications();
?>
<style type="text/css">
	.ntification{
		padding: 11px;
    display: flex;
    flex-direction: row;
    align-content: center;
    justify-content: flex-start;
    align-items: baseline;
    flex-wrap: nowrap;
	}
	.margin-top{
		margin-top: 10px;
	}
</style>
<div class="content-wrapper" height="10%">
	<div id="content-container">
		<section class="content-header">
			<h1><i class="fas fa-exclamation"></i> Notificaciones</h1>
		</section>
		<section class="content">
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title"></h3>
						</div>
						<div class="box-body">
							<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<div>
										<form method="POST" action="">
											<center>
												<button name="aceptartodas" type="submit" class="btn btn-success">Aceptar Todas</button>
												</center>
												<br>
											</form>
										</div>
										<?php
										$timeonline = time() - 60;
										$total_pages = $connect->query("SELECT * FROM players_notifications WHERE toid = '$player_id'")->num_rows;
										$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
										$num_results_on_page = 10;
										$calc_page = ($page - 1) * $num_results_on_page;
										$query = $connect->query("SELECT * FROM players_notifications WHERE toid = {$player_id} ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
										if ($query AND $query->num_rows>0) {
											while ($notification = mysqli_fetch_assoc($query)){
												$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '". $notification['fromid'] ."'");
												$rowsuser = mysqli_fetch_assoc($sqluser);
												?>
												<tr>
													<!-- SI ES UNA SOLICITUD DE AMISTAD -->
													<?php if ($notification['not_key']=='newAmistad'){ ?>
														<td class="notification">
															<div class="col-sm-12 col-md-6 text-left ">
																<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>">
																	<img src="<?php echo $sitio['site'].$rowsuser['avatar']; ?>" class="img-circle img-avatar" style="width: 60px;height: 60px;">
																	<strong style="margin:0 10px;"><?php echo $rowsuser['username']; ?></strong>
																</a>
																<br>
																te envió una solicitud de amistad
															</div>
															<div class="col-sm-5 col-md-5 text-center">
																<div class="display:inline-block;">
																	<a href="<?php echo $sitio['site']; ?>notifications.php?aceptar_id=<?php echo $notification['id']; ?>" class="btn btn-success margin-top">Aceptar</a> &nbsp;
																	<a href="<?php echo $sitio['site']; ?>notifications.php?trash=<?php echo $notification['id']; ?>" class="btn btn-warning margin-top">Rechazar</a>
																</div>
															</div>
															<div class="col-sm-1 col-md-1 text-center">
																<a class="btn btn-danger margin-top" href="<?php echo createLink('notifications', '', array('trash' => $notification['id']), true); ?>"><i class="fa fa-trash"></i></a>
															</div>
														</td>
													<!-- SI ES UN PACK -->
														<?php }elseif ($notification['not_key']=='newPack'){ ?>
														<td class="notification">
															<div class="col-sm-12 col-md-6 text-left">
																<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>">
																	<img src="<?php echo $sitio['site'].$rowsuser['avatar']; ?>" class="img-circle img-avatar" style="width: 60px;height: 60px;">
																	<strong style="margin:0 10px;"><?php echo $rowsuser['username']; ?> </strong>
																</a>
																<br>
																Subio un nuevo Pack <br>
															</div>
															<div class="col-sm-5 col-md-5 text-center">
																<div class="display:inline-block;">
																	<a href="<?php echo $sitio['site']; ?>packs.php?id_profile=<?php echo $rowsuser['id']; ?>" class="btn btn-primary margin-top">Ver el Pack</a>
																	&nbsp
																</div>
															</div>
																<div class="col-sm-1 col-md-1 text-center">
																	<a class="btn btn-danger margin-top" href="<?php echo createLink('notifications', '', array('trash' => $notification['id']), true); ?>"><i class="fa fa-trash"></i></a>
																</div>
														</td>
														<!-- SI SE SUBSCRIBIERON MI PERFIL -->
													<?php }elseif ($notification['not_key']=='newSubscription'){ ?>
														<td class="notification">
															<div class="col-sm-12 col-md-12 text-left">
																<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>">
																	<img src="<?php echo $sitio['site'].$rowsuser['avatar']; ?>" class="img-circle img-avatar" style="width: 60px;height: 60px;">
																	<strong style="margin:0 11px;"><?php echo $rowsuser['username']; ?> </strong>
																</a>
																<br>
																<span>Se suscribi&oacute; a tu perfil</span>
															</div>
															<div class="col-sm-5 col-md-5 text-center"></div>
															<div class="col-sm-1 col-md-1 text-center">
																<div class="display:inline-block;">
																	<a class="margin-top btn btn-danger" href="<?php echo createLink('notifications', '', array('trash' => $notification['id']), true); ?>"><i class="fa fa-trash"></i></a>
																</div>
															</div>
															<div class="row">
																<div class="col-xs-2">
																	<?php //if ($rowsuser['timeonline'] > $timeonline) echo '<p style="color:green">Online</>';?>
																</div>
															</div>
														</td>
													<?php }elseif ($notification['not_key']=='newPurchasePack'){ ?>
														<td class="notification">
															<div class="col-sm-12 col-md-6 text-left">
																<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>">
																	<img src="<?php echo $sitio['site'].$rowsuser['avatar']; ?>" class="img-circle img-avatar" style="width: 60px;height: 60px;">
																	<strong style="margin:0 10px;"><?php echo $rowsuser['username']; ?> </strong>
																</a>
																<br>
																<span>Compr&oacute; tu <?php echo createLink('pack','Pack',array('ID' =>  $notification['action'])); ?></span>
															</div>
															<div class="col-sm-5 col-md-5 text-center"></div>
															<div class="col-sm-1 col-md-1 text-center">
																<div class="display:inline-block;">
																	<a class="margin-top btn btn-danger" href="<?php echo createLink('notifications', '', array('trash' => $notification['id']), true); ?>"><i class="fa fa-trash"></i></a>
																</div>
															</div>
															<br>
															<div class="row">
																<div class="col-xs-2">
																	<?php //if ($rowsuser['timeonline'] > $timeonline) echo '<p style="color:green">Online</>';?>
																</div>
															</div>
														</td>
													<?php }elseif ($notification['not_key']=='newDonation'){ ?>
														<td class="notification">
															<div class="col-sm-12 col-md-6 text-left">
																<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>">
																	<img src="<?php echo $sitio['site'].$rowsuser['avatar']; ?>" class="img-circle img-avatar" style="width: 60px;height: 60px;">
																	<strong style="margin:0 10px;"><?php echo $rowsuser['username']; ?> </strong>
																</a>
																<br>
																<span>Te ha donado <?php echo $notification['action']; ?> Cr&eacute;ditos Especiales</span>
															</div>
															<div class="col-sm-5 col-md-5 text-center">
															</div>
															<div class="col-sm-1 col-md-1 text-center">
																<div class="display:inline-block;">
																	<a class="margin-top btn btn-danger" href="<?php echo createLink('notifications', '', array('trash' => $notification['id']), true); ?>"><i class="fa fa-trash"></i></a>
																</div>
															</div>
															<br>
															<div class="row">
																<div class="col-xs-2">
																	<?php //if ($rowsuser['timeonline'] > $timeonline) echo '<p style="color:green">Online</>';?>
																</div>
															</div>
														</td>
													<?php } ?>
													</tr>
												<?php
											}
										}else{
											Echo "<tr><td><center>Sin notificaciones</center></td></tr>";
}

?>														</tbody>
							<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
							<ul class="pagination">
								<?php if ($page > 1): ?>
								<li class="prev"><a href="notifications.php?page=<?php echo $page-1 ?>">Anterior</a></li>
								<?php endif; ?>

								<?php if ($page > 3): ?>
								<li class="start"><a href="notifications.php?page=1">1</a></li>
								<li class="dots">...</li>
								<?php endif; ?>

								<?php if ($page-2 > 0): ?><li class="page"><a href="notifications.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
								<?php if ($page-1 > 0): ?><li class="page"><a href="notifications.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

								<li class="currentpage"><a href="notifications.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

								<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="notifications.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
								<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="notifications.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

								<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
								<li class="dots">...</li>
								<li class="end"><a href="notifications.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
								<?php endif; ?>

								<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
								<li class="next"><a href="notifications.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
								<?php endif; ?>
							</ul>
							<?php endif; ?>
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
footer();
?>
