<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}else{

if (isset($_GET['delete-id'])) {
    $id    = (int) $_GET["delete-id"];
    $query = mysqli_query($connect, "DELETE FROM `colecciones` WHERE id='$id'");
	$querye2 = mysqli_query($connect, "DELETE FROM `player_colecciones` WHERE coleccion_id='$id'");
}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-list-ul"></i> Colecciones</h1>
    			  <ol class="breadcrumb">
   			         <li><a href="dashboard.php"><i class="fas fa-home"></i> Admin Panel</a></li>
    			     <li class="active">Colecciones</li>
    			  </ol>
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">

<?php
if (isset($_POST['add'])) {
	$titulo          = $_POST['titulo'];
    $imagen          = $_POST['imagen'];
    $codigo          = $_POST['codigo'];
    

        
        $query = mysqli_query($connect, "INSERT INTO `colecciones` (titulo, imagen, codigo) VALUES('$titulo', '$imagen', '$codigo')");
    
}
?>
                    
                <div class="row">
				<div class="col-md-9">
				

				
				    <div class="box">
						<div class="box-header">
							<h3 class="box-title">Colecciones</h3>
						</div>
						<div class="box-body">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Titulo</th>
											<th>Imagen</th>
											<th>Codigo</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
<?php
$query = mysqli_query($connect, "SELECT * FROM `colecciones`");
while ($row = mysqli_fetch_assoc($query)) {
    echo '
										<tr>
											<td>' . $row['titulo'] . '</td>
											<td><center><img src="../chat/' . $row['imagen'] . '" width="50px" height="50px"></center></td>
											<td>' . $row['codigo'] . '</td>
											<td>
                                            <a href="?delete-id=' . $row['id'] . '" class="btn btn-flat btn-flat btn-danger"><i class="fas fa-trash"></i> Eliminar</a>
											</td>
										</tr>
';
}
?>
									</tbody>
								</table>
                        </div>
                     </div>
                </div>
                    
				<div class="col-md-3">
<form class="form-horizontal" action="" method="post">
				     <div class="box">
						<div class="box-header">
							<h3 class="box-title">Agregar Colecciones</h3>
						</div>
				        <div class="box-body">
								<div class="form-group">
											<label class="col-sm-4 control-label">Titulo: </label>
											<div class="col-sm-8">
												<input type="text" name="titulo" class="form-control" required>
											</div>
							    </div>
								
								<div class="form-group">
											<label class="col-sm-4 control-label">Imagen: </label>
											<div class="col-sm-8">
												<input type="text" name="imagen" class="form-control" placeholder="images/colecciones/"  required>
											</div>
								</div>
								<div class="form-group">
											<label class="col-sm-4 control-label">Codigo: </label>
											<div class="col-sm-8">
												<input type="text" name="codigo" class="form-control" required>
											</div>
								</div>
                        </div>
                        <div class="panel-footer">
							<button class="btn btn-flat btn-primary" name="add" type="submit">Guardar</button>
							
				        </div>
				     </div>
</form>

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
}
?>