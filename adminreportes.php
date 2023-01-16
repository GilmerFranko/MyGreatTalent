<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin' AND $rowu['gender'] != 'mujer' ){

	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
	exit;
}

if(isset($_GET['deleteReport']) AND !empty($_GET['deleteReport']))
{
	$idR = $_GET['deleteReport'];

	if(deleteRow('reportes', $idR))
	{
		setSwal(array('Reporte eliminado!', 'Se ha eliminado el reporte #'.$idR, 'success'));
	}
	else
	{
		//setSwal(array('No se pudo eliminar el Reporte','Ha ocurrido un error','error'));
	}
}

if(isset($_GET['BlockUsersAdminReport']))
{
	BlockUsersAdminReport($rowu['id']);
	clear_url();
}
?>
<div class="content-wrapper" height="10%">
	<div id="content-container">
		<section class="content-header">
			<h1><i class="fas fa-exclamation"></i> Reportes</h1>
		</section>
		<section class="content">
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="box-body">
							<a class="btn btn-success actionQuest" href="#" data-href="adminreportes.php?BlockUsersAdminReport" data-quest="¿Estas seguro que quieres bloquear a todos los usuarios que aparecen en este apartado?" data-btnAction="Bloquear a todos">Bloquear a todos</a>
							<table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
								<tbody>
									<?php
									$timeonline = time() - 60;

									$total_pages = $connect->query('SELECT * FROM reportes')->num_rows;

									$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

									$num_results_on_page = 50;
									$calc_page = ($page - 1) * $num_results_on_page;

									if ($query = $connect->query("SELECT * FROM reportes ORDER BY id LIMIT {$calc_page}, {$num_results_on_page}")) {
										while ($report = mysqli_fetch_assoc($query)){

											$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '". $report['author'] ."'");
											$AuthorReport = mysqli_fetch_assoc($sqluser);

											$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '". $report['userreportado'] ."'");
											$UserReport = mysqli_fetch_assoc($sqluser);

											?>
											<tr>
												<td>
													<div class="col-sm-12 col-md-6 text-left" style="padding:11px;">
														<img src="<?php echo $sitio['site'].$AuthorReport['avatar']; ?>" class="img-circle img-avatar" style="width: 60px;height: 60px;vertical-align: bottom;">
														<div style="display: inline-block;margin-left: 10px;">
															<strong>
																<?php echo createLink('profile',$AuthorReport['username'],array('profile_id' => $AuthorReport['id'])); ?>
															</strong>
															Reporto a
															<strong><?php echo createLink('profile',$UserReport['username'],array('profile_id' => $UserReport['id'])); ?> </strong>
															<br>
															<?php
															if ($AuthorReport['timeonline'] > $timeonline) {
																echo '<span style="color:green">Online</span><br>';
															}
															?>
															<strong>Mensaje: </strong><?php echo $report['mensaje']; ?>
														</div>
													</div>
													<div class="col-sm-12 col-md-4 text-left" style="padding:11px;">
														<img src="<?php echo $sitio['site'].$UserReport['avatar']; ?>" class="img-circle img-avatar" style="width: 60px;height: 60px;">
														<div style="display: inline-block;margin-left: 10px;">
															<strong style="margin-right:10px;"><?php echo createLink('profile',$UserReport['username'],array('profile_id' => $UserReport['id'])); ?> </strong>
															<br>
															<?php
															if ($UserReport['timeonline'] > $timeonline) {
																echo '<p style="color:green">Online</>';
															}
															?>
														</div>
													</div>
													<div class="col-xs-2">
														<a class="btn btn-danger actionQuest" href="#" data-btnaction="Eliminar!" data-quest="¿Desea eliminar este Reporte?" data-href="<?php echo createLink('adminreportes', null, array('deleteReport' => $report['id']),true); ?>"><i class="fa fa-trash"></i></a>
													</div>
												</td>
											</tr>
											<?php
										}
									}else{
										Echo "<tr><td><center>Sin reportes</center></td></tr>";
									}

									?>
								</tbody>
							</table>
							<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
								<ul class="pagination">
									<?php if ($page > 1): ?>
										<li class="prev"><a href="adminreportes.php?page=<?php echo $page-1 ?>">Anterior</a></li>
									<?php endif; ?>

									<?php if ($page > 3): ?>
										<li class="start"><a href="adminreportes.php?page=1">1</a></li>
										<li class="dots">...</li>
									<?php endif; ?>

									<?php if ($page-2 > 0): ?><li class="page"><a href="adminreportes.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
									<?php if ($page-1 > 0): ?><li class="page"><a href="adminreportes.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

									<li class="currentpage"><a href="adminreportes.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

									<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="adminreportes.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
									<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="adminreportes.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

									<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
										<li class="dots">...</li>
										<li class="end"><a href="adminreportes.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
									<?php endif; ?>

									<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
										<li class="next"><a href="adminreportes.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
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



	<?php footer(); ?>
