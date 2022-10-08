<?php
require("core.php");
head();

if ($rowu['gender'] == 'hombre'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">
 

 
 </script>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-layer-group"></i> Mis fotos en venta</h1>

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
					
<center>

	
	<div class="alert alert-dismissible alert-secondary">
	
<h5><div class="alert alert-dismissible alert-warning"><div >
<center><b>Mis fotos en venta</b></center>
                                </div></div>
								</h5>
								
  

</div>


<!--          -->



<div class="center">
	
	  <div class="box">
						<div class="box-header">
							<h3 class="box-title">Mis fotos en venta</h3>
						</div>
						<div class="box-body">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											
											<th>Descripcion</th>
										    <th>Link de descarga</th>
											<th>Precio</th>
											<th>Ventas Realizadas</th>
											



											
										</tr>
									</thead>
									<tbody>
<?php
$query = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE player_id='$player_id' ORDER BY id DESC");
while ($rowr = mysqli_fetch_assoc($query)) {
    echo '
										<tr>
											
                                            <td>' . $rowr['descripcion'] . '</td>
											<td>' . $rowr['linkdedescarga'] . '</td>
											<td>' . $rowr['precio'] . '</td>
											<td>' . $rowr['ventasrealizadas'] . '</td>
											
											

											
										</tr>
';
}
?>   
								</tbody>
								</table>
                        </div>
						
                     </div>
	
  </center>

 

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