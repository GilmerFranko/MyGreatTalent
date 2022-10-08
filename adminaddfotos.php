<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}else{
	
	
	$lista_id = $_GET['id'];

if (isset($_POST['save'])) {
    
    $rutadefoto        = addslashes(strip_tags($_POST['rutadefoto']));

    
	            $addfoto = mysqli_query($connect, "INSERT INTO `fotosbot` (rutadefoto, lista_id) VALUES ('$rutadefoto', '$lista_id')");

    
    echo '<meta http-equiv="refresh" content="0;url=adminaddfotos.php?id='.$lista_id.'">';
    
}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-images"></i> Agregar fotos a una lista</h1>
    			  <ol class="breadcrumb">
    			     <li class="active">Agregar fotos a una lista</li>
    			  </ol>
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">

                <div class="row">
                    
				<div class="col-md-12">
<form class="form-horizontal" method="post">
				    <div class="box">
						<div class="box-header">
							<h3 class="box-title"><i class="fas fa-images"></i> Agregar foto</h3>
						</div>
						<div class="box-body">

											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Ruta de la foto:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="rutadefoto" placeholder="images/fotosb/imagen.jpg" required>
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
							<h3 class="box-title">Fotos de esta lista</h3>
						</div>
						<div class="box-body">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th> id</th>
											<th> Ruta de la foto</th>

										</tr>
									</thead>
									<tbody>
<?php
$query = mysqli_query($connect, "SELECT * FROM `fotosbot` WHERE lista_id='$lista_id'");
while ($row = mysqli_fetch_assoc($query)) {

  
  
    echo '
										<tr>
											<td>' . $row['id'] . '</td>
                                            <td>' . $row['rutadefoto'] . '</td>
										
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