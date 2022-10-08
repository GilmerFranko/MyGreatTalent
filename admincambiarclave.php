<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}else{

if (isset($_POST['save'])) {
	
	$password = hash('sha256', $_POST['password']);

    
    
    $query = mysqli_query($connect, "UPDATE `players` SET password='$password' WHERE id='$player_id'");
    
    echo '<meta http-equiv="refresh" content="0;url=admincambiarclave.php">';
    
}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-key"></i> Cambiar Contraseña admin</h1>
    			  <ol class="breadcrumb">
    			     <li class="active">Cambiar Contraseña</li>
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
							<h3 class="box-title"><i class="fas fa-key"></i> Cambiar Contraseña</h3>
						</div>
						<div class="box-body">

											
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Ingresar la nueva contraseña:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="password">
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