<?php
require("core.php");

if ($rowu['role']!='Admin') {
	echo '<meta http-equiv="refresh" content="0;url='.$sitio['site'].'messages.php">';
	exit;
}
head();
$filter_user = (isset($_GET['profile_id']) AND !empty($_GET['profile_id'])) ? $_GET['profile_id'] : '';

/* Filtro por tipo de transacción */
$filter_by_type = (isset($_GET['filter_by_type']) AND !empty($_GET['filter_by_type'])) ? $_GET['filter_by_type'] : '';
/* Filtro por Ingreso/Egreso*/
$filter_by_income = (isset($_GET['filter_by_income']) AND !empty($_GET['filter_by_income'])) ? $_GET['filter_by_income'] : '';
/* Almacenarlos todos en una variable*/
$parameters = array('profile_id' => $filter_user, 'filter_by_type' => $filter_by_type, 'filter_by_income' => $filter_by_income);


/* Aplicar filtros a la consulta */
$WHERE = (isset($_GET['profile_id']) AND !empty($_GET['profile_id'])) ? "WHERE player_id = '$_GET[profile_id]'" : 'WHERE 1' ;

if(!empty($filter_by_type) AND $filter_by_type != 'n') $WHERE .= ' AND `players_movements`.description = ' . $filter_by_type;
if(!empty($filter_by_income) AND $filter_by_income != 'n') $WHERE .= ' AND `in_out` = \''. $filter_by_income .'\'';

$total_pages = $connect->query("SELECT * FROM `players_movements` $WHERE ORDER BY id DESC")->num_rows;
?>

<div class="content-wrapper">
	<div id="content-container">

		<div class="form-group">
			<p>Filtrar por:</p>
			<select id="filter_by_income" class="form-control">
				<option value="n" <?php returnSelected($filter_by_income, 'n')?>>Ninguno</option>
				<option value="%2B" <?php returnSelected($filter_by_income, '+') ?>>Ingresos</option>
				<option value="-" <?php returnSelected($filter_by_income, '-') ?>>Egresos</option>
			</select>

			<p>Filtrar por</p>
			<select id="filter_by_type" class="form-control">
				<option value="n" <?php returnSelected($filter_by_type, 'n')?>>Ninguno</option>
				<option value="1" <?php returnSelected($filter_by_type, '1') ?>>Venta de pack</option>
				<option value="2" <?php returnSelected($filter_by_type, '2') ?>>Suscripción</option>
				<option value="3" <?php returnSelected($filter_by_type, '3') ?>>Créditos Especiales</option>
				<option value="4" <?php returnSelected($filter_by_type, '4') ?>>Compra de sala de chat</option>
				<option value="5" <?php returnSelected($filter_by_type, '5') ?>>Donaciones</option>
				<option value="6" <?php returnSelected($filter_by_type, '6') ?>>Canjeo de Créditos Especiales por normales</option>
				<option value="7" <?php returnSelected($filter_by_type, '7') ?>>Compra de item</option>
				<option value="8" <?php returnSelected($filter_by_type, '8') ?>>Al obtener los items de un mundo</option>
				<option value="9" <?php returnSelected($filter_by_type, '9') ?>>Regalo de mascotas</option>
				<option value="10" <?php returnSelected($filter_by_type, '10') ?>>Créditos Especiales por Likes</option>
				<option value="11" <?php returnSelected($filter_by_type, '11') ?>>Foto con regalo oculto</option>
				<option value="12" <?php returnSelected($filter_by_type, '12') ?>>Créditos de regalo semanal</option>
				<option value="13" <?php returnSelected($filter_by_type, '13') ?>>Al responder preguntas</option>
				<option value="14" <?php returnSelected($filter_by_type, '14') ?>>Regalos de CE usuarios</option>
			</select>
		</div>

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
													<li class="prev"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => $page-1), $parameters), true) ?>">Anterior</a></li>
												<?php endif; ?>

												<?php if ($page > 3): ?>
													<li class="start"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => '1'), $parameters), true) ?>">1</a></li>
													<li class="dots">...</li>
												<?php endif; ?>

												<?php if ($page-2 > 0): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => $page-2), $parameters), true) ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
												<?php if ($page-1 > 0): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => $page-1), $parameters), true) ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

												<li class="currentpage"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => $page), $parameters), true) ?>"><?php echo $page ?></a></li>

												<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => $page+1), $parameters), true) ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
												<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => $page+2), $parameters), true) ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

												<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
													<li class="dots">...</li>
													<li class="end"><a href="<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => ceil($total_pages / $num_results_on_page)), $parameters), true) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
												<?php endif; ?>

												<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
													<li class="next"><a href='<?php echo createLink('transacciones', '', array_merge(array('1' => 1, 'page' => $page+1), $parameters), true) ?>'>Siguiente</a></li>
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

		$(document).ready(function() {
			//Verificar si la URL contiene el símbolo "?"
			if(window.location.href.indexOf('?') === -1){
		  //Agregar el símbolo "?" a la URL
				var newUrl = window.location.href + '?1=1';

		  //Cambiar la URL sin recargar la página
				history.pushState({}, '', newUrl);
			}
		})

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

		$("#filter_by_type").change(function (e) {

			//Obtener el valor del filtro
			var filter_type = $("#filter_by_type").val()

			//Obtener la URL actual
			var currentUrl = window.location.href;

			//Dividir la URL en un arreglo utilizando el carácter "&"
			var urlParts = currentUrl.split("&");

			//Buscar el índice de la variable "filter_by_type"
			var filterIndex = urlParts.findIndex(function(element){
				return element.includes("filter_by_type");
			});

			//Si la variable no existe en la URL, agregarla al final
			if(filterIndex == -1){
				urlParts.push("filter_by_type=" + filter_type);
			}
			//Si la variable existe en la URL, reemplazar su valor
			else{
				urlParts[filterIndex] = "filter_by_type=" + filter_type;
			}

			//Buscar el índice de la variable "page"
			var page = urlParts.findIndex(function(element){
				return element.includes("page");
			});

			//Si la variable no existe en la URL, agregarla al final
			if(page == -1){
				urlParts.push("page=1");
			}
			//Si la variable existe en la URL, reemplazar su valor
			else{
				urlParts[page] = "page=1";
			}

			//Unir el arreglo en una cadena de nuevo utilizando "&" como separador
			var newUrl = urlParts.join("&");

			//Redirigir a la nueva URL
			window.location.href = newUrl;
			e.preventDefault();
		});

		$("#filter_by_income").change(function (e) {

			//Obtener el valor del filtro
			var filter_type = $("#filter_by_income").val()

			//Obtener la URL actual
			var currentUrl = window.location.href;

			//Dividir la URL en un arreglo utilizando el carácter "&"
			var urlParts = currentUrl.split("&");

			//Buscar el índice de la variable "filter_by_income"
			var filterIndex = urlParts.findIndex(function(element){
				return element.includes("filter_by_income");
			});

			//Si la variable no existe en la URL, agregarla al final
			if(filterIndex == -1){
				urlParts.push("filter_by_income=" + filter_type);
			}
			//Si la variable existe en la URL, reemplazar su valor
			else{
				urlParts[filterIndex] = "filter_by_income=" + filter_type;
			}

			//Buscar el índice de la variable "page"
			var page = urlParts.findIndex(function(element){
				return element.includes("page");
			});

			//Si la variable no existe en la URL, agregarla al final
			if(page == -1){
				urlParts.push("page=1");
			}
			//Si la variable existe en la URL, reemplazar su valor
			else{
				urlParts[page] = "page=1" + filter_type;
			}

			//Unir el arreglo en una cadena de nuevo utilizando "&" como separador
			var newUrl = urlParts.join("&");

			//Redirigir a la nueva URL
			window.location.href = newUrl;
			e.preventDefault();
		});


	</script>
	<!--===================================================-->
	<!--END CONTENT CONTAINER-->
	<?php
	footer();

	/**
	 * Compara las dos variables y devuelve 'selected' segun el resultado
	 */
	function returnSelected($var, $string){
		if($var == $string)
		{
			echo 'selected';
		}
	}
	?>
