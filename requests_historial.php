<?php
require("core.php");
head();

?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-users"></i> Historial Retiros</h1>
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">
                    
                <div class="row">                  
                
				<div class="col-md-12">


				    <div class="box">
						<div class="box-header">
							<h3 class="box-title">Historial Retiros</h3>
						</div>
						<div class="box-body" id="scroll">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" align="center">
									<thead>
										<tr>													
											<th style="text-align: center;">Fecha</th>
									        <th style="text-align: center;">Cambiaste</th>
											<th style="text-align: center;">Recibiste</th>
											<th style="text-align: center;">Estado</th>
										</tr>
									</thead>
									<tbody>
<?php

$query = mysqli_query($connect, "SELECT * FROM `retiros` WHERE usuario='{$player_id}' ORDER BY id DESC");
while ($requests = mysqli_fetch_assoc($query)) {
	$rid = $requests['usuario'];
	$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$rid'");
    $rowsuser = mysqli_fetch_assoc($sqluser);
	
    echo '<tr>		
		<td>' . $requests['date'] . '</td>
		<td>' . $requests['type'] . '</td>
		<td>' . $requests['monto'] . '</td>
		<td>' . $requests['status'] . '</td>
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

<style>
#scroll {
     overflow-x:scroll;
     height:100%;
     width:100%;
}
#scroll table {
    width:100%;
    height: 100%;
}
</style>
<?php
footer();
?>