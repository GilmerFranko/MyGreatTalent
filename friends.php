<?php
require("core.php");
head();

?>

<div class="content-wrapper" height="10%">
	<div id="content-container">
		<section class="content-header">
			<h1><i class="fas fa-users"></i> Amistades</h1>
		</section>
		<section class="content">
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title"></h3>
						</div>
						<div class="box-body">
							<center>
								<?php if ($rowu['permission_send_gift']) : ?>
									<a class="btn btn-warning" href="javascript:openModalGiveGift()">Enviar regalo</a>
								<?php else : ?>
									<a class="btn btn-warning actionInfo" data-info="Aun no puedes enviar regalos" data-secondinfo="Para enviar regalos debes..." href="#">Enviar regalo</a>
								<?php endif ?>
							</center><br>
							<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
								<thead>
									<tr>
									</tr>
								</thead>
								<tbody>

									<?php
									$timeonline = time() - 60;

									$total_pages = $connect->query("SELECT * FROM `friends` WHERE player1='{$player_id}' OR player2='{$player_id}'")->num_rows;
									$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
									$num_results_on_page = 10;
									$calc_page = ($page - 1) * $num_results_on_page;

									if ($query = $connect->query("SELECT * FROM `friends` WHERE player1='{$player_id}' OR player2='{$player_id}' LIMIT {$calc_page}, {$num_results_on_page}"))
									{
										while ($friend = mysqli_fetch_assoc($query))
										{
											if ($friend['player2'] == $player_id)
											{
												$amigo = $friend['player1'];
											}
											elseif ($friend['player1'] == $player_id)
											{
												$amigo = $friend['player2'];
											}

											$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$amigo'");
											$rowsuser = mysqli_fetch_assoc($sqluser);
									?>
											<tr>
												<td>
													<center>
														<a href="<?php echo $sitio['site'] . 'profile.php?profile_id=' . $rowsuser['id']; ?>">
															<img src="<?php echo $sitio['site'] . $rowsuser['avatar'] ?>" class="img-circle img-avatar">
															<br>
															<br>
															<?php echo $rowsuser['username'] . '</a><br>'; ?>

															<?php if ($rowsuser['timeonline'] > $timeonline) : ?>
																<p style="color:green">Online</p><br>
															<?php endif ?>

															<a href="<?php echo $sitio['site'] . 'profile.php?profile_id=' . $rowsuser['id']; ?>">
																<h3 class="text-success">Ir al perfil</h3>
															</a>
															<?php if ($rowu['permission_send_gift']) : ?>
																<a class="btn btn-success" href="javascript:openModalGiveGift('<?php echo $rowsuser['username']; ?>');">Enviar Regalo</a>
															<?php else : ?>
																<a class="btn btn-warning actionInfo" data-info="Aun no puedes enviar regalos" data-secondinfo="Para enviar regalos debes..." href="#">Enviar regalo</a>
															<?php endif ?>
														</a>
													</center>
												</td>
											</tr>

									<?php }
									}
									else
									{
										echo "<tr><td><center>Sin Amigos que mostrar.</center></td></tr>";
									}

									?>
								</tbody>
							</table>
							<?php if (ceil($total_pages / $num_results_on_page) > 0) : ?>
								<ul class="pagination">
									<?php if ($page > 1) : ?>
										<li class="prev"><a href="friends.php?page=<?php echo $page - 1 ?>">Anterior</a></li>
									<?php endif; ?>

									<?php if ($page > 3) : ?>
										<li class="start"><a href="friends.php?page=1">1</a></li>
										<li class="dots">...</li>
									<?php endif; ?>

									<?php if ($page - 2 > 0) : ?><li class="page"><a href="friends.php?page=<?php echo $page - 2 ?>"><?php echo $page - 2 ?></a></li><?php endif; ?>
									<?php if ($page - 1 > 0) : ?><li class="page"><a href="friends.php?page=<?php echo $page - 1 ?>"><?php echo $page - 1 ?></a></li><?php endif; ?>

									<li class="currentpage"><a href="friends.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

									<?php if ($page + 1 < ceil($total_pages / $num_results_on_page) + 1) : ?><li class="page"><a href="friends.php?page=<?php echo $page + 1 ?>"><?php echo $page + 1 ?></a></li><?php endif; ?>
									<?php if ($page + 2 < ceil($total_pages / $num_results_on_page) + 1) : ?><li class="page"><a href="friends.php?page=<?php echo $page + 2 ?>"><?php echo $page + 2 ?></a></li><?php endif; ?>

									<?php if ($page < ceil($total_pages / $num_results_on_page) - 2) : ?>
										<li class="dots">...</li>
										<li class="end"><a href="friends.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
									<?php endif; ?>

									<?php if ($page < ceil($total_pages / $num_results_on_page)) : ?>
										<li class="next"><a href="friends.php?page=<?php echo $page + 1 ?>">Siguiente</a></li>
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

<script>
	$(document).ready(function() {

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

	/* Cargar ultima seleccion de gift_user_list */
	jsonLastUserList = '<?php echo getGiftLastSelection(); ?>'

	jsonLastUserList = JSON.parse(jsonLastUserList);
</script>



<?php
footer();
?>