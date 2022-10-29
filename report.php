<?php
require("core.php");
head();

if (isset($_POST['userreportado'])){

	if(isset($_POST['reportar'])){

		if($_POST['mensaje'] != ''){



	//ingresando el reporte

			$userreportado = $_POST['userreportado'];
			$mensaje = $_POST['mensaje'];
			$author  = $_POST['author'];
			$date    = date('d F Y');
			$time    = date('H:i');




			$addreporte = mysqli_query($connect, "INSERT INTO `reportes` (author, userreportado, mensaje, date, time) VALUES ('$author', '$userreportado', '$mensaje', '$date', '$time')");





			$enviado = 1;

		}else{

			$enviado = 2;
		}
	}

	?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">



	</script>
	<div class="content-wrapper">

		<!--CONTENT CONTAINER-->
		<!--===================================================-->
		<div id="content-container">

			<section class="content-header">
				<h1> Reportar a esta persona</h1>

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
								<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">


									<tbody>
										<center><p>
											Escribe aqui la razon por la cual quieres reportar a esta persona
										</center></p>

									</tbody>
								</table>
							</div>
							<?php

							if ($enviado == 1){

								echo '<center style="color:green">Reporte Enviado</center>';

							}
							if ($enviado == 2){

								echo '<center style="color:red">No puedes enviar un reporte vacío</center>';

							}

							?>
							<center>

								<form action="" method="POST">
									<div class="form-group">
										<label for="exampleTextarea"></label>
										<textarea placeholder="Escribe tu mensaje" class="form-control" name="mensaje" id="mens" rows="3" spellcheck="false"></textarea>
										<div class="modal-footer">
											<input type="hidden" name="author" id="auth" value="<?php echo $player_id; ?>">
											<input type="hidden" name="userreportado" value="<?php echo $_POST['userreportado']; ?>">
											<input value="Enviar" type="submit" id="postmensaje" name="reportar" class="btn btn-primary"/>

										</div>
									</div>

								</form>

							</div>



							<br>
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
	}else{

		echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
		exit;
	}
	?>
