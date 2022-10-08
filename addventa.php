<?php
require("core.php");
head();

if ($rowu['gender'] == 'hombre'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname' LIMIT 1");
$rowu  = mysqli_fetch_assoc($suser);

if (isset($_POST['save'])){
		
	
	$precio = $_POST['precio'];
	
	if ($precio >= 0){
		
	$descripcion = $_POST['descripcion'];
	$link = $_POST['linkdedescarga'];

 
 /// imagen
        $token = rand(111,999);
        $target_dir    = "images/mercado/";
        $target_file   = $target_dir . basename($_FILES["avafile"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $filename      = $token . $uname . '.' . $imageFileType;
        $imagen        = "images/mercado/" . $filename;
 move_uploaded_file($_FILES["avafile"]["tmp_name"], "images/mercado/" . $filename);
///

		
	 $insertarcompra = mysqli_query($connect, "INSERT INTO `ventasenventa` (player_id, imagen, precio, descripcion, linkdedescarga) VALUES ('$rowu[id]', '$imagen', '$precio', '$descripcion', '$link')");

		//mostramos mensaje de exito y link de descarga / cambie precio en linea 20 y linea 199
		
		echo '
        <script type="text/javascript">
            $(document).ready(function() {
                $("#sell-vehicle").modal(\'show\');
            });
        </script>

        <div id="sell-vehicle" class="modal fade">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Publicacion</h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <center>
                           
                            <h4><span class="badge badge-info">Venta Publicada!</span></h5>
                            	<a href="ventas.php"> <button class="btn btn-success">Ver mi Venta</button></a>
                                <br/><br/>

                            <button type="button" class="btn btn-success btn-md btn-block" data-dismiss="modal" aria-hidden="true"><i class="fab fa-get-pocket"></i> Ok</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>';
	}else{
		
		
		///mensaje de minimo 100 creditos
		
		echo '
        <script type="text/javascript">
            $(document).ready(function() {
                $("#sell-vehicle").modal(\'show\');
            });
        </script>

        <div id="sell-vehicle" class="modal fade">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Publicacion</h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <center>
                            <h4><span class="badge badge-info">Venta no Publicada!, el precio minimo es de 10 credito</span></h5>
                                
                            
								
                          
                            <button type="button" class="btn btn-success btn-md btn-block" data-dismiss="modal" aria-hidden="true"><i class="fab fa-get-pocket"></i> OK</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>';
	

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
    			  <h1><i class="fas fa-layer-group"></i> Vender en el mercado</h1>

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
Agrega foto, descripción mencionado el producto que vendes y el precio.
</center></p>

								</tbody>
								</table>
                        </div>
    <div class="card">
                        <div class="card-body">

				           <form action="" method="post" class="form-horizontal" enctype="multipart/form-data">
						   
						   
						   <div class="form-group">
				

				
												<label class="col-md-3 control-label" for="inputDefault">Seleccionar archivo:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    
													<input name="avafile" type="file" required accept="image/*"/>
                                                   <br> 
													</div>
												</div>
											</div>
						   
						   
						   <div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Descripción:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"></span>
													<input autocomplete="off" type="text" class="form-control" name="descripcion" placeholder="Descripcion" required>

                                                    </div>
												</div>
											</div>
						  
						  
						  <div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Tiempo de entrega :</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"></span>
													<input autocomplete="off" type="number" min="0" class="form-control" name="linkdedescarga" placeholder="Tiempo de entrega" required>

                                                    </div>
												</div>
											</div>
						  
						  <div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Precio mínimo :</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"></span>
													<input type="number" min="0" class="form-control" name="precio" placeholder="Precio" required>

                                                    </div>
												</div>
											</div>
						   
						   <div class="panel-footer text-left">
							<input class="btn btn-flat btn-success" name="save" type="submit" value="Enviar">
				        </div>
						   
						   </form>
						   
						   
                            
                        </div>
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
?>