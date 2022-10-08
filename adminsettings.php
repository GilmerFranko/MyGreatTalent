<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}else{

if (isset($_POST['save'])) {
    
    $title        = addslashes(strip_tags($_POST['title']));
    $site  = addslashes(strip_tags($_POST['site']));
    $montoboton     = addslashes(strip_tags($_POST['montoboton']));
    $costoporchat  = addslashes(strip_tags($_POST['costoporchat']));
    $name   = addslashes(strip_tags($_POST['name']));
    $limit_unlogged_users = (isset($_POST['limit_unlogged_users']) AND !empty($_POST['limit_unlogged_users'])) ? $_POST['limit_unlogged_users'] : 'No';

		$description = addslashes(strip_tags($_POST['description']));
    $mostrarprimermensaje = addslashes(strip_tags($_POST['mostrarprimermensaje']));
    $limit_actions = addslashes(strip_tags($_POST['limit_actions']));
    
    
    $query = mysqli_query($connect, "UPDATE `settings` SET title='$title', site='$site', name='$name', description='$description', 	montoboton='$montoboton', costoporchat='$costoporchat', mostrarprimermensaje='$mostrarprimermensaje', limit_actions='$limit_actions', limit_unlogged_users='$limit_unlogged_users' WHERE id=1");
    
    echo '<meta http-equiv="refresh" content="0;url=adminsettings.php">';
    
}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-cogs"></i> Admin Settings</h1>
    			  <ol class="breadcrumb">
    			     <li class="active">Admin Settings</li>
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
							<h3 class="box-title"><i class="fas fa-cog"></i> Options Settings</h3>
						</div>
						<div class="box-body">

											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Titulo del sitio:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="title" value="<?php
echo $sitio['title'];
?>" required>
                                                    </div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Carpeta donde se ubica el sitio (si es en un dominio colocar una barra "/" sin comillas, si es un subdominio "/carpeta/" sin comillas):</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="site" value="<?php
echo $sitio['site'];
?>">
                                                    </div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Créditos a ganar por cada clic en el boton:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="number" class="form-control" name="montoboton" value="<?php
echo $sitio['montoboton'];
?>" min="0" required>
                                                    </div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Costo en créditos para iniciar un nuevo chat:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="number" class="form-control" name="costoporchat" value="<?php
echo $sitio['costoporchat'];
?>" min="0" required>
                                                    </div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Nombre del sitio:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="name" value="<?php
echo $sitio['name'];
?>">
                                                    </div>
												</div>
											</div>
											
											
											<div class="form-group">
												<label class="col-md-3 control-label" for="inputDefault">Descripción del sitio:</label>
												<div class="col-md-8">
                                                    <div class="input-group">
                                                    <span class="input-group-addon"><i class="fas fa-inbox"></i></span>
													<input type="text" class="form-control" name="description" value="<?php
echo $sitio['description'];
?>">
                                                    </div>
												</div>
											</div>
											
											
											<div class="form-group">
												<label for="mostrarprimermensaje" style="width: 100%;"><i class="fas fa-eye"></i><span> Mostrar previsualización de mensajes</span>
													<select name="mostrarprimermensaje">
														<?php 
														if ($sitio['mostrarprimermensaje'] == 'no'){

															$opcion1 = 'no';
															$opcion2 = 'si';

														}else{

															$opcion1 = 'si';
															$opcion2 = 'no';

														}

														?>
														<option value="<?php echo $opcion1; ?>"><?php echo $opcion1; ?></option>
														<option value="<?php echo $opcion2; ?>"><?php echo $opcion2; ?></option>
													</select>
												</label>

												</div>  
												<div class="form-group">
												<label for="limit_actions" style="width: 100%;"><i class="fa fa-cog"></i><span> Limitar acciones a usuarios no registrados desde El Chat</span>
													<select name="limit_actions">
														<?php 
														if ($sitio['limit_actions']=="no"){

															$limit_opcion1 = 'no';
															$limit_opcion2 = 'si';

														}else{

															$limit_opcion1 = 'si';
															$limit_opcion2 = 'no';

														}

														?>
														<option value="<?php echo $limit_opcion1; ?>"><?php echo $limit_opcion1; ?></option>
														<option value="<?php echo $limit_opcion2; ?>"><?php echo $limit_opcion2; ?></option>
													</select>
												</label>

												</div>
												<div class="form-group">
												<label for="limit_unlogged_users" style="width: 100%;"><i class="fa fa-users"></i><span> Permitir la entrada de Usuarios no Logueados </span>
													<select name="limit_unlogged_users">
														<?php
														if (!$sitio['limit_unlogged_users']){
															$limit_unlogged1 = 0;
															$limit_unlogged2 = 1;
														}else{
															$limit_unlogged1 = 1;
															$limit_unlogged2 = 0;
														}
														?>
														<option value="<?php echo $limit_unlogged1; ?>"><?php echo $limit_unlogged1 == 0 ? 'No' : 'Si'; ?></option>
														<option value="<?php echo $limit_unlogged2; ?>"><?php echo $limit_unlogged2 == 1 ? 'Si' : 'No'; ?></option>
													</select>
												</label>
												</div>



												<br />
												<a class="btn-warning btn" href="https://diaches-game.com/ComprarCreditos.php?BG_U=<?php echo base64_encode($rowu['username']); ?>&FROM_SITE=<?php echo base64_encode('MY-GREAT-TALENT'); ?>">Comprar</a>

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
