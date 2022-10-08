<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}else{

if (isset($_POST['save'])) {
    
    $nombredelista        = addslashes(strip_tags($_POST['nombredelista']));

    
    
	            $addlista = mysqli_query($connect, "INSERT INTO `listasfotosbot` (nombre) VALUES ('$nombredelista')");

    
    echo '<meta http-equiv="refresh" content="0;url=adminlistasfotosbot.php">';
    
}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-images"></i> Listas de fotos</h1>
    		
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">

                <div class="row">
                    
				<div class="col-md-12">
<form class="form-horizontal" method="post">
				    <div class="box">
						<div class="box-header">
							<h3 class="box-title"><i class="fas fa-images"></i> Crear nueva lista</h3>
						</div>
						<div class="box-body">

											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Nombre de la lista:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="nombredelista" placeholder="lista1" required>
                                                    </div>
												</div>
											</div>
											
											
											
											<br />
											
                        </div>
                        <div class="panel-footer text-left">
							<button class="btn btn-flat btn-primary" name="save" type="submit">Guardar</button>
				            
				        </div>
                     </div>
</form>




<div class="box">
						<div class="box-header">
							<h3 class="box-title">Listas Creadas</h3>
						</div>
						<div class="box-body">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th> Nombre de la lista</th>
											<th> Acción</th>

										</tr>
									</thead>
									<tbody>
<?php
$query = mysqli_query($connect, "SELECT * FROM `listasfotosbot`");
while ($row = mysqli_fetch_assoc($query)) {

  
  
    echo '
										<tr>
											<td>' . $row['nombre'] . '</td>
                                            <td>
                                            <a href="'. $sitio['site'].'adminaddfotos.php?id=' . $row['id'] . '" class="btn btn-flat btn-primary"><i class="fas fa-edit"></i> Agregar fotos</a>
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
				</div>
                    
				</div>
				<!--===================================================-->
				<!--End page content-->


			</div>
			<!--===================================================-->
			<!--END CONTENT CONTAINER-->
<?php
footer();

}
?>