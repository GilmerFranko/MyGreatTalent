<?php
require("core.php");
head();

//LIMITAR ACCION A USUARIOS QUE NO ESTEN EN LA APP O QUE NO SEAN REGISTRADOS DESDE EL CHAT
if(its_in()!='in_android')
{
	?>
	<div class="content-wrapper" height="10%">

		<!--CONTENT CONTAINER-->
		<!--===================================================-->
		<div id="content-container">

			<section class="content-header">
				
				<h1><i class="fas fa-info-circle"></i> Los <strong>Créditos Gratis</strong> solo estan disponible desde <a href="instrucciones.php">La App de Bellasgram</a>
			</section>


			<!--Page content-->
			<!--===================================================-->
			<section class="content">

				<div class="row">                  

					<div class="col-md-12">



						<div class="box">
							<div class="box-body">
								Para poder ganar <strong>Créditos Gratis</strong>, debes entrar desde <a href="instrucciones.php">La App de Bellasgram</a>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
	<?php
	
}
else{
function secondsToWords($seconds)
{
    $ret = "";
    
    //Days
    $mdays = intval(intval($seconds) / (3600 * 24));
    if ($mdays > 0) {
        $ret .= "$mdays days ";
    }
    
    //Hours
    $mhours = (intval($seconds) / 3600) % 24;
    if ($mhours > 0) {
        $ret .= "$mhours hours ";
    }
    
    //Minutes
    $mminutes = (intval($seconds) / 60) % 60;
    if ($mminutes > 0) {
        $ret .= "$mminutes minutes ";
    }
    
    /*
    //Seconds
    $seconds = intval($seconds) % 60;
    if ($seconds > 0) {
    $ret .= "$seconds seconds";
    }*/
    
    return $ret;
}

?>
    
<script>

function botonganarcreditos(iduser){
	
	$.ajax({
					
   			        data: 'idparadarcreditos=' + iduser,
					
					url: 'ajax.php',
                    method: 'POST',
  			        
                });
	
	document.getElementById("verotrovideo").style.display="block";
	document.getElementById("botonganar").style.display="none";
	document.getElementById("textowin").style.display="block";
		
}

</script>
<div class="content-wrapper" height="10%">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
				
    			  <h1><i class="fas fa-play-circle"></i> Ganar Créditos Normales</h1>
    			  <br/> Con los Créditos normales puedes: Comprar chats, comprar y cuidar mascotas, comprar fotos y videos, canjearlos por créditos especiales <br/>Para suscribirte a perfiles o  adquirir packs tienes que comprar <a href="comprar.php">créditos especiales</a>
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
<?php
 $timeonline = time() - 60;
 

?>   
 <div class="card border-dark mb-3">
                            <div class="">
                               
                           

<div>
<center>
<p id="textowin" style="display:none;color:green">
				Felicidades! ganaste <?php echo $sitio['montoboton']; ?> créditos
				</p>
				<br>
<?php if ($rowu['botontime'] > 0 && time() >= $rowu['botontime']) {?>
<button name="botonganar" id="botonganar" onclick="botonganarcreditos(<?php echo $player_id; ?>);" type="submit" class="btn btn-large btn-success">Obtener <b><?php echo $sitio['montoboton']; ?> Créditos</button>
<?php } else if ($rowu['botontime'] > 0 && time() < $rowu['botontime']) {
        $timeleft = secondsToWords($rowu['botontime'] - time()); ?>
<button name="botondeshabilitado" id="botondeshabilitado" type="submit" class="btn btn-large btn-success" disabled>Espera el reinicio</button>
<?php }else { ?>
<button name="botonganar" id="botonganar" onclick="botonganarcreditos(<?php echo $player_id; ?>);" type="submit" class="btn btn-large btn-success">Obtener <?php echo $sitio['montoboton']; ?> Créditos</button>
<?php } ?>	
<button name="verotrovideo" id="verotrovideo" type="submit" onclick="javascript:window.location.reload();" class="btn black-background white" style="display:none">Obtener los créditos</button>	
</center>
<br>
<ul>
<li>
Tienes <b><?php echo $rowu['creditos']; ?></b> Créditos
</li>
<hr>
<li>
Hoy damos <b><?php echo $sitio['montoboton']; ?></b> créditos por cada toque al botón
</li>
 <!-- OCULTO<hr>
<li>
Ya has conseguido <b><?php $acumulado = $sitio['montoboton']*$rowu['countboton']; echo $acumulado; ?></b> créditos
</li>OCULTO -->
<hr>
<li>
Puedes tocar el boton <b><?php

if ($rowu['botontime'] > 0 && $rowu['botontime'] > time()){
 echo '0';
}else{
	$aun = 8-$rowu['countboton']; echo $aun; // toques al boton de dar creditos el resto se cambia en ajax
}
 ?>
 </b> veces mas hasta el próximo reinicio
</li>
<hr>
<li>
Próximo reinicio: <b><?php

if ($rowu['botontime'] > 0 && $rowu['botontime'] > time()){
 $timeleft = secondsToWords($rowu['botontime'] - time()); echo $timeleft; 
}else{
	echo 'Obtén todos los creditos disponibles para actualizar la hora de reinicio';
}
 
 
 ?></b>
</li>

		
</ul>	</div>						   
                               
                            </div>
					    </div>

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

			<style type="text/css">

.black-background {background-color:#000000;}
.white {color:#ffffff;}

</style>
			
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
}
footer();
?>