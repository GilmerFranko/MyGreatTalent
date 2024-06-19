<?php

require("core.php");

function params (){
	unset($_REQUEST['page']);
	$rsd = '';
	if(count($_REQUEST)){
		$rsd = '&';
	}
	$result = http_build_query($_REQUEST).$rsd;
	return str_replace('search=&','',$result);
}

function Search (){
	global $connect, $player_id, $sitio,$rowu;

	$timeonline = time() - 60;

	$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

	$num_results_on_page = 10;

	$data = [];
	$data['Data'] = '';
	$where = ($rowu['registerfrom'] == 'chat' ? '' : "AND hidetochat != 'si'");

	$hidden_old = "AND IF(players.`hidden_for_old` != 0 AND players.`id` != '$rowu[id]', players.`hidden_for_old` <= '$rowu[time_joined]', 1=1)";

	$iamfrom = ($rowu['registerfrom'] == 'chat' ? 'chat' : "bellasgram");

	if (isset($_GET['nombre']))
	{
		$searchName = $_GET['nombre'];
		if ($searchName != '' AND strlen($searchName) >= 2)
		{
			$buscarpornombre = 'Si';
		}
		else
		{
			exit();
		}

		if (isset($_REQUEST['customCheck1'])) {
			$buscaronlines = 'Si';
		}else{
			$buscaronlines = 'No';
		}
		if (isset($_REQUEST['customCheck2'])) {
			$buscarhombres = 'Si';
		}else{
			$buscarhombres = 'No';
		}
		if (isset($_REQUEST['customCheck3'])) {
			$buscarmujeres = 'Si';
		}else{
			$buscarmujeres = 'No';
		}

		$calc_page = ($page - 1) * $num_results_on_page;

		$query = false;

		if ($buscarpornombre == 'Si' && $buscaronlines == 'Si' && $buscarhombres == 'Si' && $buscarmujeres == 'Si'){
			//aqui buscar usernames parecidos a el especificado que esten online
			$total_pages = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline $hidden_old")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'Si' && $buscaronlines == 'Si' && $buscarhombres == 'Si' && $buscarmujeres == 'No'){
			//aqui buscar usernames parecidos a el especificado que esten online y que el genero sea masculino
			$total_pages = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline AND gender = 'hombre' $hidden_old")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline AND gender = 'hombre' $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'Si' && $buscaronlines == 'Si' && $buscarhombres == 'No' && $buscarmujeres == 'Si'){
			//aqui buscar usernames parecidos a el especificado que esten online y que el genero sea femenino
			$total_pages = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline AND gender = 'mujer' $hidden_old")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline AND gender = 'mujer' $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'Si' && $buscaronlines == 'Si' && $buscarhombres == 'No' && $buscarmujeres == 'No'){
			//aqui buscar usernames parecidos a el especificado que esten online

			$total_pages = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline $hidden_old")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' AND timeonline>$timeonline $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'Si' && $buscaronlines == 'No' && $buscarhombres == 'Si' && $buscarmujeres == 'Si'){
			//aqui buscar usernames parecidos a el especificado

			$total_pages = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' $hidden_old ORDER BY timeonline DESC")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' $hidden_old ORDER BY timeonline DESC LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'Si' && $buscaronlines == 'No' && $buscarhombres == 'No' && $buscarmujeres == 'No'){
			//aqui buscar usernames parecidos a el especificado

			$total_pages = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' $hidden_old ORDER BY timeonline DESC")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE username LIKE '%$_GET[nombre]%' $hidden_old ORDER BY timeonline DESC LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'No' && $buscaronlines == 'No' && $buscarhombres == 'No' && $buscarmujeres == 'No'){
			//buscar a todos los usuarios

			$total_pages = $connect->query("SELECT * FROM `players` WHERE 1=1 $hidden_old ORDER BY timeonline DESC")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE 1=1 $hidden_old ORDER BY timeonline DESC LIMIT {$calc_page}, {$num_results_on_page}");
		}
		/*if ($buscarpornombre == 'No' && $buscaronlines == 'Si' && $buscarhombres == 'No' && $buscarmujeres == 'No'){
			//buscar a todos los usuarios que esten online

			$total_pages = $connect->query("SELECT * FROM `players` WHERE timeonline>$timeonline $hidden_old ")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE timeonline>$timeonline $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}*/
		if ($buscarpornombre == 'No' && $buscaronlines == 'Si' && $buscarhombres == 'Si' && $buscarmujeres == 'Si'){
			//buscar a todos los usuarios que esten online

			$total_pages = $connect->query("SELECT * FROM `players` WHERE timeonline>$timeonline ")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE timeonline>$timeonline $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'No' && $buscaronlines == 'Si' && $buscarhombres == 'Si' && $buscarmujeres == 'No'){
			//buscar a todos los usuarios que esten online y que sean hombres

			$total_pages = $connect->query("SELECT * FROM `players` WHERE gender = 'hombre' AND timeonline>$timeonline ")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE gender = 'hombre' AND timeonline>$timeonline $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'No' && $buscaronlines == 'Si' && $buscarhombres == 'No' && $buscarmujeres == 'Si'){
			//buscar a todos los usuarios que esten online y que sean mujeres

			$total_pages = $connect->query("SELECT * FROM `players` WHERE gender = 'mujer' AND timeonline>$timeonline ")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE gender = 'mujer' AND timeonline>$timeonline $hidden_old LIMIT {$calc_page}, {$num_results_on_page}");
		}
		if ($buscarpornombre == 'No' && $buscaronlines == 'No' && $buscarhombres == 'No' && $buscarmujeres == 'Si'){
			//buscar a todos los usuarios que sean mujeres

			$total_pages = $connect->query("SELECT * FROM `players` WHERE gender = 'mujer' $hidden_old ORDER BY timeonline DESC")->num_rows;
			$query = $connect->query("SELECT * FROM `players` WHERE gender = 'mujer' $hidden_old ORDER BY timeonline DESC LIMIT {$calc_page}, {$num_results_on_page}");
		}

		if ($buscarpornombre == 'No' && $buscaronlines == 'No' && $buscarhombres == 'Si' && $buscarmujeres == 'No'){
			//buscar a todos los usuarios que sean hombres

			$total_pages = $connect->query("SELECT * FROM `players` WHERE gender = 'hombre' $hidden_old ORDER BY timeonline DESC")->num_rows;
			//$query = mysqli_query($connect, "SELECT * FROM `players` WHERE gender = 'hombre' ORDER BY timeonline DESC");
			$query = $connect->query("SELECT * FROM `players` WHERE gender = 'hombre' $hidden_old ORDER BY timeonline DESC LIMIT {$calc_page}, {$num_results_on_page}");

		}
		if ($buscarpornombre == 'No' && $buscaronlines == 'No' && $buscarhombres == 'Si' && $buscarmujeres == 'Si'){
			//buscar a todos los usuarios

			$total_pages = $connect->query("SELECT * FROM `players` WHERE 1=1 $hidden_old ORDER BY timeonline DESC")->num_rows;
			//$query = mysqli_query($connect, "SELECT * FROM `players` ORDER BY timeonline DESC");
			$query = $connect->query("SELECT * FROM `players` WHERE 1=1 $hidden_old ORDER BY timeonline DESC LIMIT {$calc_page}, {$num_results_on_page}");

		}

		if($query){

			while ($userbuscado = mysqli_fetch_assoc($query)) {

			// MOSTRAR SOLO PERFILES PERMITIDOS *NO BLOQUEADOS *NO OCULTOS
			if (!canSeeYourProfile($userbuscado['id'])) {
				$total_pages--;
				continue;
			}

				$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$userbuscado[id]'");
				$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);

				if ($hayunbloqueo < 1){

					$data['Data'] .= '<tr>
						<td><center> <a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '"><img src="'.$sitio['site'].$userbuscado['avatar'] . '" class="img-circle img-avatar"><br><br>
						' . $userbuscado['username'] . '<br>
						<H3>Ir a su perfil</H3></a></center>';

					if ($userbuscado['timeonline'] > $timeonline AND $userbuscado['role'] != 'Admin') {
						$data['Data'] .= '<p style="color:green">Online</p>';
					}
					$data['Data'] .= '</td></tr>';

				}
			}
		}

	}
	$Paginacion = '';

	if ($data['Data']!='' && ceil($total_pages / $num_results_on_page) > 0){
		$Paginacion = '<ul class="pagination">';
		if ($page > 1){
			$Paginacion .= '<li class="prev"><a href="search.php?'. params() .'page='. ($page-1) .'">Anterior</a></li>';
		}

		if ($page > 3){
			$Paginacion .= '<li class="start"><a href="search.php?'. params() .'page=1">1</a></li><li class="dots">...</li>';
		}

		if ($page-2 > 0){
			$Paginacion .= '<li class="page"><a href="search.php?'. params() .'page='. ($page-2) .'">'. ($page-2) .'</a></li>';
		}

		if ($page-1 > 0){
			$Paginacion .= '<li class="page"><a href="search.php?'. params() .'page='. ($page-1) .'">'. ($page-1) .'</a></li>';
		}

		$Paginacion .= '<li class="currentpage"><a href="search.php?'. params() .'page='. ($page) .'">'. ($page) .'</a></li>';

		if ($page+1 < ceil($total_pages / $num_results_on_page)+1){
			$Paginacion .= '<li class="page"><a href="search.php?'. params() .'page='. ($page+1) .'">'. ($page+1) .'</a></li>';
		}

		if ($page+2 < ceil($total_pages / $num_results_on_page)+1){
			$Paginacion .= '<li class="page"><a href="search.php?'. params() .'page='. ($page+2) .'">'. ($page+2) .'</a></li>';
		}

		if ($page < ceil($total_pages / $num_results_on_page)-2){
			$Paginacion .= '<li class="dots">...</li><li class="end"><a href="search.php?'. params() .'page='. ceil($total_pages / $num_results_on_page) .'">'. ceil($total_pages / $num_results_on_page) .'</a></li>';
		}



		if ($page < ceil($total_pages / $num_results_on_page)){
			$Paginacion .= '<li class="next"><a href="search.php?'. params() .'page='. ($page+1) .'">Siguiente</a></li>';
		}
		$Paginacion .= '</ul>';
	}

	$data['link'] = $Paginacion;

	return $data;
}

if(isset($_GET['search'])){
	Echo json_encode(Search());
	exit();
}

head();

?>
<div class="content-wrapper" height="10%">

	<!--CONTENT CONTAINER-->
	<!--===================================================-->
	<div id="content-container">

		<section class="content-header">
		  <h1><i class="fas fa-search"></i> Buscar</h1>

		</section>


		<!--Page content-->
		<!--===================================================-->
		<section class="content">

		<div class="row">

		<div class="col-md-12">

			<div class="box">
				<div class="box-header">
					<h3 class="box-title"></h3>
				</div>
				<div class="box-body">
					<form method="G" action="" id="FormSearch">
						<input name="nombre" class="form-control" autocomplete="off" type="text" placeholder="Ingrese el usuario a buscar" id="InputSearch" value="<?php if(isset($_GET['nombre']) AND !empty($_GET['nombre'])) echo $_GET['nombre'];?>">
						<div class="modal-footer">
							<div class="form-group">
								<div class="custom-control custom-checkbox">
								<!--	<input type="checkbox" class="custom-control-input" id="customCheck1" name="customCheck1">
									<label class="custom-control-label" for="customCheck1">Onlines</label>

									<input type="checkbox" class="custom-control-input" id="customCheck2" name="customCheck2">
									<label class="custom-control-label" for="customCheck2">Hombre</label>

									<input type="checkbox" class="custom-control-input" id="customCheck3" name="customCheck3">
									<label class="custom-control-label" for="customCheck3">Mujer</label>-->
								</div>
								<button type="submit" class="btn btn-success">Buscar</button>

							</div>
						</div>
					</form>

					<table id="dt-basic" class="table" cellspacing="100" width="50%">

						<tbody id="ResultsContent">


					<div width="50%">
							<?php
								$Search = Search();
								Echo $Search['Data'];
							?>
						</tbody>
					</table>
					<div id="paginadorContent">
						<?php
							Echo $Search['link'];
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

<script>
$(document).ready(function() {

	$('#dt-basic').dataTable( {
		"responsive": true,
		"language": {
			"paginate": {
			  "previous": '<i class="fas fa-angle-left"></i>',
			  "next": '<i class="fas fa-angle-right"></i>'
			}
		}
	} );
} );
</script>


<?php
footer();
?>
