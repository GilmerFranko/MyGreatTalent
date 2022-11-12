<?php
require("core.php");

if ($rowu['role']!='Admin') {
	echo '<meta http-equiv="refresh" content="0;url='.$sitio['site'].'messages.php">';
	exit;
}
head();
$filter_user = (isset($_GET['profile_id']) AND !empty($_GET['profile_id'])) ? $_GET['profile_id'] : '';
$WHERE = (isset($_GET['profile_id']) AND !empty($_GET['profile_id'])) ? "WHERE player_id = '$_GET[profile_id]'" : '' ;
$total_pages = $connect->query("SELECT * FROM `players_movements` $WHERE ORDER BY id DESC")->num_rows;
?>

<div class="content-wrapper">
	<div id="content-container">
		<div class="row" style="margin:0;">
			<section class="content">
				<div class="row">
					<div style="width: 100%;">
						<div class="box">
							<div class="box-body row-list">
								<?php

								$page = (isset($_GET['page']) && is_numeric($_GET['page']))? $_GET['page'] : 1;

								$num_results_on_page = 40;

								$calc_page = ($page - 1) * $num_results_on_page;

								$querycp = mysqli_query($connect, "SELECT *, `players_movements`.id as idMove,players_movements.`description` as descriptionMove FROM `players_movements` INNER JOIN players ON players.id=players_movements.`player_id` $WHERE ORDER BY players_movements.`id` DESC LIMIT {$calc_page}, {$num_results_on_page}");

								$countcp = mysqli_num_rows($querycp);
								?>
								<div id="scroll">
									<table class="table table-striped table-bordered table-hover" id="players">
										<thead>
											<tr>
												<th style="text-align:center;">#</th>
												<th style="text-align:center;">
													<i class="fa fa-user"></i> Nombre
												</th>
												<th style="text-align:center;">
													<i class="fa fa-database"></i> Cr&eacute;ditos Antes
												</th>
												<th style="text-align:center;">
													<i class="fas fa-hand-holding-usd"></i> Transacci&oacute;n
												</th>
												<th style="text-align:center;">
													<i class="fa fa-database"></i> Cr&eacute;ditos Despu&eacute;s
												</th>
												<th style="text-align:center;">
													<i class="fas fa-info"></i> Descripci&oacute;n
												</th>
												<th style="text-align:center;">
													<i class="fas fa-calendar"></i> Fecha
												</th>
											</tr>
										</thead>
										<?php
										if ($countcp > 0) {
											while ($rowcp = mysqli_fetch_assoc($querycp)) {
        								//SI EL USUARIO TIENE EL PERFIL OCULTO Y YO NO SOY UN USUARIO REGISTRADO DESDE EL CHAT
												if ($rowcp['in_out']=='+')
												{
													$description = array(
														0  => '',
														1  => 'Vendió un Pack',
														2  => 'Vendió una Suscripción',
														3  => 'Compró Créditos Especiales',
														4  => '',
														5  => 'Le han donado Créditos Especiales',
														6  => 'Canjeo Créditos Especiales por Créditos Normales',
														7  => '',
														8  => 'Obtuvo todos los Ítems de un Mundo',
														9 => 'Acepto un Regalo de su Mascota',
														10 => 'Canjeo Créditos Especiales por Likes',
														11 => 'Encontró una foto con Créditos de regalo',
														12 => 'Acepto sus Creditos de Regalo Semanal',
														14 => 'Le han enviado un regalo con Créditos Especiales',
													);
												}
												else
												{
													$description = array(
														0  => '',
														1  => 'Compró un Pack',
														2  => 'Compró una Suscripción',
														3  => '',
														4  => 'Compró una sala de Chat',
														5  => 'Donó Créditos Especiales',
														6  => '',
														7  => 'Compró un Ítem',
														8  => '',
														9 => 'Regaló',
														10 => '');
												}

												?>
												<tr>
													<th style="text-align:center;">
														<a href="profile.php?profile_id=<?php echo $rowcp['id']; ?>"> <?php echo $rowcp['idMove']; ?> </a>
													</th>
													<th style="text-align:center;">
														<span>
															<a href="transacciones.php?profile_id=<?php echo $rowcp['id']; ?>"  > <?php echo $rowcp['username']; ?> </a>
														</span>
													</th>
													<th style="text-align:center;">
														<span><?php echo $rowcp['credits_before']; ?></span>
													</th>
													<th style="text-align:center;">
														<span><?php echo $rowcp['in_out'] . ($rowcp['in_out'] == '+' ? $rowcp['credits_after'] - $rowcp['credits_before'] : $rowcp['credits_before'] - $rowcp['credits_after']); ?></span>
													</th>
													<th style="text-align:center;">
														<span><?php echo $rowcp['credits_after']; ?></span>
													</th>
													<th style="text-align:center;">
														<span> <?php echo $description[$rowcp['descriptionMove']]; ?> </span></th>
														<th style="text-align:center;"><span> <?php echo strftime("%d/%m/%Y %H:%M", $rowcp['time']); ?> </span>
														</th>

													</tr>
												<?php } ?>
											</table>
										</div>
										<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
											<ul class="pagination">
												<?php if ($page > 1): ?>
													<li class="prev"><a href="<?php echo createLink('transacciones', '', array('page' => $page-1, 'profile_id' => $filter_user), true) ?>">Anterior</a></li>
												<?php endif; ?>

												<?php if ($page > 3): ?>
													<li class="start"><a href="<?php echo createLink('transacciones', '', array('page' => '1', 'profile_id' => $filter_user), true) ?>">1</a></li>
													<li class="dots">...</li>
												<?php endif; ?>

												<?php if ($page-2 > 0): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array('page' => $page-2, 'profile_id' => $filter_user), true) ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
												<?php if ($page-1 > 0): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array('page' => $page-1, 'profile_id' => $filter_user), true) ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

												<li class="currentpage"><a href="<?php echo createLink('transacciones', '', array('page' => $page, 'profile_id' => $filter_user), true) ?>"><?php echo $page ?></a></li>

												<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array('page' => $page+1, 'profile_id' => $filter_user), true) ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
												<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array('page' => $page+2, 'profile_id' => $filter_user), true) ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

												<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
													<li class="dots">...</li>
													<li class="end"><a href="<?php echo createLink('transacciones', '', array('page' => ceil($total_pages / $num_results_on_page), 'profile_id' => $filter_user), true) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
												<?php endif; ?>

												<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
													<li class="next"><a href='<?php echo createLink('transacciones', '', array('page' => $page+1, 'profile_id' => $filter_user), true) ?>'>Siguiente</a>
													</li>
												<?php endif; ?>
											</ul>
										<?php endif; ?>
										<?php
									} else {
										echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Actualmente no hay Movimientos</strong></div>';
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
	<!-- JavaScript -->
	<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.11.1/build/alertify.min.js"></script>

	<!-- CSS -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.1/build/css/alertify.min.css"/>
	<script>
		$(document).on('submit', 'form', function(e){

			alertify.confirm('Estas seguro que quieres eliminar el historial completo?', 'Mensaje de confirmacion',
				function(){
    //submit
    $.post('transacciones.php', { deleteH : 1234 }, function(resp) {
    	if (resp=="bien") {
    		alertify.success('Historial eliminado con exito');
    		setInterval(function () {
    			location.reload();
    		}, 1000);
    	}
    });
  },
  function(){
  	alertify.error('Cancel')

  });
		});


	</script>
	<!--===================================================-->
	<!--END CONTENT CONTAINER-->
	<?php
	footer();
	?>
