<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}else{

if (isset($_POST['save'])) {
    
    $botusername        = addslashes(strip_tags($_POST['botusername']));
    $fotodeperfilbot  = addslashes(strip_tags($_POST['fotodeperfilbot']));
    $tiempoderespuesta     = addslashes(strip_tags($_POST['tiempoderespuesta']));
    $description  = addslashes(strip_tags($_POST['description']));
    $respuestaautomatica   = addslashes(strip_tags($_POST['respuestaautomatica']));
    $listasfotosbot = addslashes(strip_tags($_POST['listasfotosbot']));
	
	$botpassword = $botusername.'-sinpassword';
	$botemail = $botusername.'@demomail.com';
    
    
	            $addbot = mysqli_query($connect, "INSERT INTO `players` (username, password, email, avatar, role, gender, description, tiempoderespuesta, respuesta_automatica, id_listadefotos) VALUES ('$botusername', '$botpassword', '$botemail', '$fotodeperfilbot', 'BOT', 'mujer', '$description', '$tiempoderespuesta', '$respuestaautomatica', '$listasfotosbot')");

    
    echo '<meta http-equiv="refresh" content="0;url=admincrearbot.php">';
    
}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-robot"></i> Crear nuevo bot</h1>
    			  <ol class="breadcrumb">
    			     <li class="active">Crear nuevo bot</li>
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
							<h3 class="box-title"><i class="fas fa-robot"></i> Crear bot</h3>
						</div>
						<div class="box-body">

											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Username del bot:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="botusername" placeholder="username del bot" required>
                                                    </div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Ubicación de la foto de perfil del bot:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="fotodeperfilbot" placeholder="images/fotob/imagen.jpg">
                                                    </div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Tiempo de respuesta en minutos:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="number" class="form-control" name="tiempoderespuesta" placeholder="30" min="1" required>
                                                    </div>
												</div>
											</div>
										
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Descripción o bio del bot:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="description" placeholder="buen dia a todos">
                                                    </div>
												</div>
											</div>
											
											
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Respuesta automatica:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="respuestaautomatica" placeholder="Hola, como estas me llamo ana y aqui te envío mi foto">
                                                    </div>
												</div>
											</div>
											
											
											<div class="form-group">
 			       <label for="listasfotosbot" style="width: 100%;"> Lista de fotos</span>
                    <select name="listasfotosbot">
					<?php 

$query = mysqli_query($connect, "SELECT * FROM `listasfotosbot`");
while ($option = mysqli_fetch_assoc($query)) {
	
	echo '<option value="'.$option['id'].'">'.$option['nombre'].'</option>';
	
	
}

					?>
					

                    </select>
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
							<h3 class="box-title">Bots Creados</h3>
						</div>
						<div class="box-body">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th> Username</th>
											<th> Descripción</th>
                                            <th> Tiempo de respuesta</th>
											<th> Respuesta automatica</th>

										</tr>
									</thead>
									<tbody>
<?php
$query = mysqli_query($connect, "SELECT * FROM `players` WHERE role='BOT'");
while ($row = mysqli_fetch_assoc($query)) {

  
  
    echo '
										<tr>
											<td>' . $row['username'] . '</td>
                                            <td>' . $row['description'] . '</td>
                                            <td>' . $row['tiempoderespuesta'] . '</td>
											<td>' . $row['respuesta_automatica'] . '</td>
										
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