<?php
require("core.php");

// SOLO ADMIN Y BOTS
if($rowu['role'] != 'Admin' AND $rowu['role'] != 'BOT') echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'index.php" />';

if (isset($_GET['edit']) && isset($_POST['descripcion']) && isset($_POST['id'])){
	$id = $_POST['id'];
	$descripcion = $_POST['descripcion'];

	if($_POST['dias']>0 || $_POST['horas']>0 || $_POST['minutos']>0){
		$date = time();
		if($_POST['dias']>0){
			$d = $_POST['dias'] * (60*60*24);
			$date = $date + $d;
		}
		if($_POST['horas']>0){
			$d = $_POST['horas'] * (60*60);
			$date = $date + $d;
		}
		if($_POST['minutos']>0){
			$d = $_POST['minutos'] * (60);
			$date = $date + $d;
		}
	}

	$time = isset($date) ? ", time='{$date}'":'';

	$sql = "UPDATE `fotosprogramadas` SET descripcion='{$descripcion}'{$time} WHERE id='{$id}'";

	mysqli_query($connect, $sql);
	var_dump($sql);
	//echo '<meta http-equiv="refresh" content="0; url=fotosprogramadas.php" />';
	exit;
}

head();

if ($_GET['delete']){
	mysqli_query($connect, "DELETE FROM `fotosprogramadas` WHERE id=". $_GET['delete']);
	echo '<meta http-equiv="refresh" content="0; url=fotosprogramadas.php" />';
	exit;
}
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<style>
	.itemsder {
		display: flex;
		position: relative;
		align-items: center;
		padding: 10px;
		border-bottom: 1px solid #31393c;
	}
	.itemsder .csdtcover {
		width: 50px;
		height: 50px;
		border-radius: 7px;
		background-color: gray;
		margin-right: 10px;
		background-size: cover;
		background-position: center;
	}
	.itemsder .time {
		position: absolute;
		right: 15px;
		bottom: 10px;
		font-size: 12px;
	}
	.btn.delete {
		background-color: #ff0047;
		color: white;
		margin-right: 10px;
	}
	.btn.edit {
		background-color: #05963d;
		color: white;
		margin-right: 10px;
	}
</style>
<div class="content-wrapper">
	<!--CONTENT CONTAINER-->
	<!--===================================================-->
	<div id="content-container">

		<!--Page content-->
		<!--===================================================-->
		<section class="content">

			<div class="row">

				<div class="col-md-12">


					<div class="box">
						<div class="box-body">
							<table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">

								<tbody>
									<?php
									if ($rowu['gender'] == 'mujer'){
										?>
										<center>
											<button class="btn btn-success" style="position:relative;">
												Agregar Fotos
												<input type="file" id="InputFiles" name="files" multiple
												style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;">
											</button>
										</center>
										<br>
										<?php
									}
									?>

									<div class="card">
										<div class="card-body">
											<?php
											$timeonline = time() - 60;
											$WHERE = $rowu['role'] == 'Admin' ? '':"WHERE player_id='{$player_id}'";

											$total_pages = $connect->query("SELECT * FROM `fotosprogramadas` {$WHERE} ORDER BY id DESC")->num_rows;

											$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

											$num_results_on_page = 100;
											$calc_page = ($page - 1) * $num_results_on_page;

											$querycp = mysqli_query($connect, "SELECT * FROM `fotosprogramadas` {$WHERE} ORDER BY time ASC LIMIT {$calc_page}, {$num_results_on_page}");
											$countcp = mysqli_num_rows($querycp);
											if ($countcp > 0) {
												while ($rowcp = mysqli_fetch_assoc($querycp)) {
													$author_id = $rowcp['player_id'];
													$querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
													$rowcpd    = mysqli_fetch_assoc($querycpd);
													if (true){
														$Images = json_decode($rowcp['imagen']);
														$sub = '';
														?><tr>
															<div class="col col-md-12">
																<div class="itemsder" data-fotoid="<?php Echo $rowcp['id'];?>">
																	<a class="btn delete" href="?delete=<?php Echo $rowcp['id'];?>">
																		<i class="fa fa-trash"></i>
																	</a>
																	<a class="btn edit" onclick="editPhoto(this)"
																	data-id="<?php Echo $rowcp['id'];?>"
																	data-content="<?php Echo $rowcp['descripcion'];?>"
																	data-time="<?php Echo $rowcp['time'];?>">
																	<i class="fa fa-pencil-alt"></i>
																</a>
																<div class="csdtcover"
																onclick="openImage(`<?php Echo $sitio['site'].$Images[0];?>`)"
																style="background-image: url(<?php Echo $sitio['site'].$Images[0];?>);"></div>
																<div class="details" style="text-align:left;">
																	<?php Echo $rowcp['descripcion'];?>
																	<?php if($rowu['role'] == 'Admin'){
																		Echo '<br><strong style="display:block;">'. $rowcpd['username'] .'</strong>';
																	}?>
																</div>
																<div class="time">
																	<?php echo TimeNext($rowcp['time']) ? 'Se publicara dentro de '.TimeNext($rowcp['time']):'Publicada'; ?>
																</div>
															</div>
														</div>

														<?php
													}
												}
												?>
												<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
													<ul class="pagination">
														<?php if ($page > 1): ?>
															<li class="prev"><a href="fotosprogramadas.php?page=<?php echo $page-1 ?>">Anterior</a></li>
														<?php endif; ?>

														<?php if ($page > 3): ?>
															<li class="start"><a href="fotosprogramadas.php?page=1">1</a></li>
															<li class="dots">...</li>
														<?php endif; ?>

														<?php if ($page-2 > 0): ?><li class="page"><a href="fotosprogramadas.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
														<?php if ($page-1 > 0): ?><li class="page"><a href="fotosprogramadas.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

														<li class="currentpage"><a href="fotosprogramadas.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

														<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="fotosprogramadas.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
														<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="fotosprogramadas.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

														<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
															<li class="dots">...</li>
															<li class="end"><a href="fotosprogramadas.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
														<?php endif; ?>

														<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
															<li class="next"><a href="fotosprogramadas.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
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

</div>
<?php
if ($rowu['gender'] == 'mujer'){
	?>
	<div class="modal fade" id="FotosProgramadasModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Nueva Foto <b>No fotos normales que se usan en instagram, no GIF, no fotos ya usadas en BellasGram</b>    </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="overflow-y: scroll;overflow-x: hidden;max-height: 72vh;">
					<form></form>
				</div>
				<div class="modal-footer">
					<select class="btn btn-secondary" style="background:#dddddd;" name="type" id="postType">
						<option value="publico">Publico</option>
						<option value="suscripciones">suscripciones</option>
					</select>
					<div type="button" class="btn btn-secondary" style="background:#dddddd;" onclick="$('#FotosProgramadasModal').toggleClass('show')">
						Cancelar
					</div>
					<div type="button" class="btn btn-success" id="sendForm">
						Enviar
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="EditFotosProgramadasModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Nueva Foto <b>No fotos normales que se usan en instagram, no GIF, no fotos ya usadas en BellasGram</b>    </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="overflow-y: scroll;overflow-x: hidden;max-height: 72vh;">
					<form method="POST" action="./fotosprogramadas.php?edit">
						<input type="hidden" name="id">
						<div>
							<div><textarea class="form-control" name="descripcion" placeholder="Descripcion (opcional)" style="height:100px!important;margin-bottom:20px;"></textarea></div><br><b>Programa cuando quieres que se publique la foto</b>
							<div style="display: flex;margin-top: 15px;">
								<div style="width:33%;"><input class="form-control fpt-day" type="number" name="dias" placeholder="dias" value="0"></div>
								<div style="width:33%;"><input class="form-control fpt-horas" type="number" name="horas" placeholder="horas" value="0"></div>
								<div style="width:33%;"><input class="form-control fpt-minutos" type="number" name="minutos" placeholder="minutos" value="0"></div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<div type="button" class="btn btn-secondary" style="background:#dddddd;" onclick="$('#EditFotosProgramadasModal').toggleClass('show')">
						Cancelar
					</div>
					<div type="button" class="btn btn-success" onclick="EditSubmit()">
						Enviar
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>

<script>
	const EditSubmit = () => {
		const c = $('#EditFotosProgramadasModal')
		const form = c.find(`form`)

		$(".fpt-day").each(function(){
			const e = $(this)
			if(e.val() == '')
				e.val(0)
		})
		$(".fpt-horas").each(function(){
			const e = $(this)
			if(e.val() == '')
				e.val(0)
		})
		$(".fpt-minutos").each(function(){
			const e = $(this)
			if(e.val() == '')
				e.val(0)
		})

		const id = form.find(`input[name=id]`).val()
		const descripcion = form.find(`textarea[name=descripcion]`).val()
		const dias = form.find(`input[name=dias]`).val()
		const horas = form.find(`input[name=horas]`).val()
		const minutos = form.find(`input[name=minutos]`).val()

		$(`[data-fotoid=${id}]`).find('.details').html(descripcion)

		$.ajax({
			url: `fotosprogramadas.php?edit`,
			type: `POST`,
			data: {
				id, descripcion, dias, horas, minutos
			}
		}).done(function(r) {
			console.log(r)
			c.removeClass('show').addClass('fade')
		})
	}

	const editPhoto = (ths) => {
		const c = $('#EditFotosProgramadasModal')
		const form = c.find(`form`)
		const e = $(ths)
		const time = e.data('time')
		const content = e.data('content')
		const id = e.data('id')
		console.log(content, time, id)
		c.find(`input[name=id]`).val(id)
		c.find(`textarea[name=descripcion]`).val(content)
		c.addClass('show').removeClass('fade')
	}

	const Form = (data, index) => {
		const f = '<div>'
		+'<img src="'+ data.image +'" style="max-width:60%;max-height:350px;display:block;border-radius:10px;margin:10px auto;">'
		+'<input type="hidden" name="file['+ index +']" value="'+ data.image +'">'
		+'<div><textarea class="form-control" name="descripcion['+ index +']" placeholder="Descripcion (opcional)" style="height:100px!important;margin-bottom:20px;"></textarea></div>'
		+'<br><b>Programa cuando quieres que se publique la foto</b>'
		+'<div style="display: flex;margin-top: 15px;">'
		+'<div style="width:33%;"><input class="form-control fpt-day" type="number" name="dias['+ index +']" placeholder="dias"></div>'
		+'<div style="width:33%;"><input class="form-control fpt-horas" type="number" name="horas['+ index +']" placeholder="horas"></div>'
		+'<div style="width:33%;"><input class="form-control fpt-minutos" type="number" name="minutos['+ index +']" placeholder="minutos"></div>'
		+'</div>'
		+'</div>';

		return f
	}

	const InputFiles = $(`#InputFiles`)
	const FormModal = $(`#FotosProgramadasModal`)

	$(document).ready(function() {

		InputFiles.change(function(e) {
			FormModal.addClass('show').removeClass('fade')
			const input = this
			if (input.files && input.files.length) {
				[...input.files].map((File, index) => {
					var reader = new FileReader();

					reader.onload = function (e) {
						const dataForm = Form({
							image: e.target.result
						}, index)
						console.log(index)
						FormModal.find(`form`).append(dataForm)
					}

					reader.readAsDataURL(File)
				})
			}
		})

		$("#sendForm").on("click", function(e){
			e.preventDefault()
			$(".fpt-day").each(function(){
				const e = $(this)
				if(e.val() == '')
					e.val(0)
			})
			$(".fpt-horas").each(function(){
				const e = $(this)
				if(e.val() == '')
					e.val(0)
			})
			$(".fpt-minutos").each(function(){
				const e = $(this)
				if(e.val() == '')
					e.val(0)
			})

			FormModal.find('form').append($(`<input type="hidden" name="postType">`).val($('#postType').val()))

			const formData = FormModal.find('form').serializeArray()

			$.ajax({
				url: "./ajax.php?addprograma",
				type: "POST",
				data: formData
			}).done(function(response){
				console.log('response', response)
				const data = $.parseJSON(response)
				if(data.status)
				{
					alert('fotos programadas con exito')
					window.location.reload()
				}else{
					alert('Error')
				}
			})
		});
	});
</script>
<?php
footer();
?>
