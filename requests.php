<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){	
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
    exit;
}

if (isset($_GET['rechazar'])) {
    $id = (int) $_GET["rechazar"];
    $query  = mysqli_query($connect, "UPDATE `retiros` SET status='rechazado' WHERE id='$id'");
}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-users"></i> Retiros Pendientes</h1>
    			  <ol class="breadcrumb">
   			         <li><a href="dashboard.php"><i class="fas fa-home"></i> Admin Panel</a></li>
    			     <li class="active">Retiros Pendientes</li>
    			  </ol>
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">
                    
                <div class="row">                  
                
				<div class="col-md-12">
<?php
if (isset($_GET['aprobar'])) {
    $id     = (int) $_GET["aprobar"];

	$query  = mysqli_query($connect, "UPDATE `retiros` SET status='pagado' WHERE id='$id'");

}
?>




				    <div class="box">
						<div class="box-header">
							<h3 class="box-title">Retiros Pendientes</h3>
						</div>
						<div class="box-body">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											
											<th>Usuario</th>
                                            <th>Metodo</th>
										    <th>Información de Pago</th>					
											<th>Monto</th>
									        <th>Fecha</th>
											<th>Status</th>
											<th>Tipo</th>
											<th>Accion</th>
										</tr>
									</thead>
									<tbody>
<?php

$query = mysqli_query($connect, "SELECT * FROM `retiros` WHERE status = 'pendiente' ORDER BY id DESC");
while ($requests = mysqli_fetch_assoc($query)) {
	$rid = $requests['usuario'];
	$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$rid'");
    $rowsuser = mysqli_fetch_assoc($sqluser);
	
    echo '<tr>		
		<td>' . $rowsuser['username'] . '</td>
		<td>' . $requests['metodo'] . '</td>
		<td>' . $requests['identificacion'] . '</td>
		<td>' . $requests['monto'] . '</td>
		<td>' . $requests['date'] . '</td>
		<td>' . $requests['status'] . '</td>
		<td>' . $requests['type'] . '</td>
		<td>
		<a href="?aprobar=' . $requests['id'] . '" class="btn btn-flat btn-primary"><i class="fas fa-edit"></i> Pagado</a>
		<a href="?rechazar=' . $requests['id'] . '" class="btn btn-flat btn-danger"><i class="fas fa-trash"></i> Rechazar</a>
		<a href="checkuser.php?userid=' . $rowsuser['id'] . '" class="btn btn-flat btn-danger"><i class=""></i> Chequear</a>
	   
		</td>
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
				<!--===================================================-->
				<!--End page content-->


			</div>
			<!--===================================================-->
			<!--END CONTENT CONTAINER-->


<?php
footer();
?>