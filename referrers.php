<?php
require("core.php");
head();

$id = $player_id;

$timeonline = time() - 60;

$query = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$id'");
$userbuscado = mysqli_fetch_assoc($query);

if ($rowu['role'] == 'Admin' && isset($_GET["banear"]) && $userbuscado){
	$Action = 0;
	$userbuscado["baneado"] = 0;
	if($_GET["banear"] == 0){
		$Action = 1;
		$userbuscado["baneado"] = 1;
	}

	$player_update2 = mysqli_query($connect, "UPDATE `players` SET baneado={$Action} WHERE id='{$id}'");
}


if (isset($_POST['codigodereferer'])) {

	$sqlbono = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
	$sbono   = mysqli_fetch_assoc($sqlbono);
	$bono    = $sbono['bonoref'];

	$codigodereferer = $_POST['codigodereferer'];
	$sqlreferer     = mysqli_query($connect, "SELECT * FROM `players` WHERE refcodigo='$codigodereferer' LIMIT 1");
    $assocreferer      = mysqli_fetch_assoc($sqlreferer);
    $referer_id = $assocreferer['id'];
	$existsr = mysqli_num_rows($sqlreferer);

    if ($rowu['referer_id'] == 0 && $codigodereferer != $rowu['refcodigo'] && $existsr > 0) {

		$player_update1 = mysqli_query($connect, "UPDATE `players` SET eCreditos=eCreditos+'$bono', referer_id='$referer_id' WHERE id='$player_id'");
		$player_update2 = mysqli_query($connect, "UPDATE `players` SET eCreditos=eCreditos+'$bono' WHERE id='$referer_id'");
		echo '<script type="text/javascript">
            $(document).ready(function() {
                $("#get-salary").modal(\'show\');
            });
        </script>

        <div id="get-salary" class="modal fade">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar un ccódigo de referido</h5>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <center>
                            <h4><span class="badge badge-info">Código agregado</span></h4>
                             <h1><span class="badge badge-info">'.$bono.' Creditos se han añadido a tu cuenta</span></h1><br />

                            <a href="'.$sitio['site'].'profile.php" type="button" class="btn btn-success btn-md btn-block"><i class="fab fa-get-pocket"></i> ok</a>
                        </center>
                    </div>
                </div>
            </div>
        </div>';

	}
}


$sqlbono = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
$sbono = mysqli_fetch_assoc($sqlbono);
$bono = $sbono['bonoref'];

$sqlcodigo = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$player_id'");
$scodigo = mysqli_fetch_assoc($sqlcodigo);
$codigo = $scodigo['refcodigo'];

$queryref = mysqli_query($connect, "SELECT * FROM `players` WHERE referer_id='$player_id'");
$countref = mysqli_num_rows($queryref);

if ($id == $player_id){

?>
<div class="content-wrapper" style="padding: 20px;padding-top: 70px;">
	<div class="card">
		<div class="card-header">
			<h5 class="card-title"><br>
				<i class="fas fa-users"></i> Sistema de Referidos
			</h5>
			<p>Gana créditos con nuestro sistema de referidos, comparte tu codigo de referido y cada vez que un nuevo usuario se registra con tu codigo tu y tu referido se llevarán automaticamente: <?php echo $bono; ?> crédito, y un 5% de lo que tu referido gane, Puedes invitar tantos amigos como quieras.</p>
		</div>
		<div class="card-body">
			<center>
				<h3><i class="fas fa-users"></i> Panel de Referidos</h3>
			</center><br />
			<center>
				<h1>Tienes <?php Echo $countref; ?> referidos</h1>
			</center><br />
			<div class="row">
				<div class="col-sm-12 col-md-6" style="margin-bottom: 20px;">
					<div class="card-header text-white bg-primary" style="padding:20px;border-radius:5px;">
						<center>
							<h1 style="margin:0;"> <?php echo 'Tu codigo de Referido es: <span id="codigo">'.$codigo.'</span>'; ?></h1>
						</center>
					</div>
					<br>
					<center>
						<button type="button" class="btn btn-success" id="copyClip" data-clipboard-target="#codigo">
							Copiar código
						</button>
					</center>
				</div>
				<div class="col-sm-12 col-md-6" style="margin-bottom: 20px;">
					<div class="card-header card-header text-white bg-primary" style="padding:20px;border-radius:5px;word-break: break-all;">
						<center>
							<div><h2 style="margin:0;"> El enlace a tu perfil es:</h2><br> <strong><span id="urlProfile">https://my-great-talent.com<?php echo getUserLink($rowu['id']);?></span></strong></div>
						</center>
					</div>
					<br>
					<center>
						<button type="button" class="btn btn-success" id="copyUrlProfile" data-clipboard-target="#urlProfile">
							Copiar Enlace
						</button>
					</center>
				</div>
			</div>
		</div>
		<br>

		<?php
			if ($rowu['referer_id'] == 0){
		?>
			<br /><br />
			<div class="row float-center">

				<div class="col-md-12">
					<div class="jumbotron">
						<center>
							<h4> Agregar un código</h4>
							<hr />
							<br />

							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-8">
									<form method="post" action="">
										<ul class="list-group">

											<li class="list-group-item">
												<i class=""></i>&nbsp;&nbsp; Ingresa un codigo de referido de otra persona y gana <?php echo $bono; ?> crédito, el dueño del codigo tambien se llevará <?php echo $bono; ?>.
											</li>

										</ul><br>

										<div class="">
											<div class="card bg-light card-body mb-3">
												<div class="form-group">
													<label style="width:100%;">Ingresar El Código</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<div class="input-group-text"><em class="fa fa-fw fa-user"></em></div>
														</div>
														<input name="codigodereferer" type="number" placeholder="Ingresar El Código" class="form-control" required>
													</div>
												</div>
											</div>
										</div>

										<input value="Agregar Codigo" class="btn btn-primary btn-block" name="request" type="submit">

									</form>

								</div>
							</div>
						</center>
					</div>
				</div>
			</div>
		<?php
			}
		?>
		</div>
	</div>
</div>
<script src="assets/js/clipboard.min.js?v=1"></script>
<?php
}

footer();
?>
