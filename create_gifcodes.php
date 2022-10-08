<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
    exit;
}else{
	if(isset($_GET['DeleteCodes'])){
		mysqli_query($connect, "DELETE FROM `gifcodes` WHERE used!=0");
		echo '<meta http-equiv="refresh" content="0; url=create_gifcodes.php" />';
		exit;
	}
?>
<div class="content-wrapper">

	<div id="content-container">

	<section class="content-header">
		<h1><i class="fa fa-gif"></i> Codigos de Regalo</h1>
	</section>
	
	<section class="content">

	<div class="row">
		
	<div class="col-md-12">
		<div class="box">
			<div class="box-body">
				<button class="btn btn-success" data-toggle="modal" data-target="#trailerModal">
					Agregar Codigos							
				</button>
				<a class="btn btn-danger" href="create_gifcodes.php?DeleteCodes">
					Eliminar codigos usados						
				</a>
					
				<table id="dt-basic" class="table table-bordered table-hover no-margin">
					<thead>
						<tr>
							<th><i class="fas fa-list-ul"></i> ID</th>
							<th><i class="fas fa-list-ul"></i> Code</th>
							<th><i class="fas fa-user"></i> Valor Creditos</th>
							<th><i class="fas fa-user"></i> type</th>
							<th><i class="fas fa-star"></i> usado</th>
							<th><i class="fas fa-dollar-sign"></i> creado</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sqlua = mysqli_query($connect, "SELECT * FROM `gifcodes` ORDER BY id DESC");
							while ($rowua = mysqli_fetch_assoc($sqlua)) {
								echo '<tr>
									<td>#' . $rowua['id'] . '</td>
									<td>' . $rowua['code'] . '</td>
									<td>' . $rowua['creditos'] . '</td>
									<td>' . $rowua['type'] . '</td>
									<td>' . ($rowua['used'] == 0 ? 'FALSE':TimeAgo($rowua['used'])) . '</td>
									<td>' . TimeAgo($rowua['created']) . '</td>
								</tr>';
							}
						?>				  
					</tbody>
				</table>							 
			</div>
		</div>
    </div>
</div>
                    
				</div>
				
			</div>

<div class="modal fade" id="trailerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Agregar codigos de regalo</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="FormAddGifCodes">
					<input class="form-control" type="number" name="value" id="value" placeholder="Valor del codigo">
					<br>
					<input class="form-control" type="number" name="num" id="num" placeholder="Cantidad de codigos">
					<br>
					<select name="type" id="type" class="form-control">
						<option value="creditos">creditos normales</option>
						<option value="eCreditos">creditos Especiales</option>
					</select>
				</form>
			</div>
			<div class="modal-footer">
				<div type="button" class="btn btn-secondary" style="background:#dddddd;" data-dismiss="modal" aria-label="Close">
					Cancelar
				</div>
				<div type="button" class="btn btn-success" id="sendForm">
					Enviar
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$("#sendForm").click(function(){
		var form = $("#FormAddGifCodes")
		console.log(form.serialize())
		
		$.ajax({
			url: "ajax.php?addCodes", 
			type: "POST",
			data: form.serialize()
		}).done(function(response){
			var data = $.parseJSON(response)
			console.log(response)
			if(data.status){
				swal.fire("Codigos agregados", "", "success")
				window.location.reload()
			}else{
				swal.fire("No se pudo agregar los codigos", "", "error")
			}
		})
	})

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
}
?>
