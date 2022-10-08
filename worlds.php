<?php
require("core.php");
head();

//OBTENER EL ID DEL MUNDO A MOSTRAR
if (isset($_GET['farm_id']) AND !empty($_GET['farm_id']) AND is_numeric($_GET['farm_id'])) {
	$farm_id=$_GET['farm_id'];
} else {
	$farms = mysqli_query($connect, "SELECT * FROM `farms`");
	$ff=mysqli_fetch_assoc($farms);
	$farm_id=$ff['id'];
}

$items_player =mysqli_query($connect, "SELECT * FROM `players_farm_items` WHERE player_id='$rowu[id]'");
$farms = mysqli_query($connect, "SELECT * FROM `farms`");
$currentfarm= mysqli_query($connect, "SELECT * FROM `farms` WHERE id='$farm_id'");

if ($currentfarm and mysqli_num_rows($currentfarm)>0)
{
	$cntfarm=mysqli_fetch_assoc($currentfarm);
} 
$num_items_player=get_num_items_player($items_player,$farm_id);
?>
<style type="text/css">
	.farm{
		background: url('<?php echo $cntfarm["image"]; ?>');
		height: 474px;
		width: 664px;
		overflow: hidden;
		background-size: 100% 100%;
	}
	.box{
		min-width: 554px;
	}
	.table{
		height: 200%;
		width: 200%;
		color:black;
		font-weight: 600;
		max-width: unset;
	}
	.object{
		position: relative;
	}
	.img-item{
		margin-top: -50%;
		margin-left: -50%;
		width: 80px;
		filter: opacity(1);
	}
	.visible{
		display: none;
	}
	.prevfarm{
		box-sizing: border-box;
		background-color: #1b7171; 
		padding:0px;
		text-decoration: none;
		font-size: 12px;
		font-weight: bold;
		color: #616872;
		border-radius: 50% !important;
		min-width: 40px;
		height: 70px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 15px;
		width: 70px;
		overflow: hidden;

	}
	.prevfarm a{
		color:white;
	}
	.prevfarm img{
		margin:0;
		height: 70px;
	}
	.scroll {
		overflow-x:scroll;
		height:100%;
		width:100%;
	}
	table{
		table-layout: fixed;
	}

	th, td {
		word-wrap: break-word;
		height: 10px;
	}
	.td{
		display: inline-flex;
		width: 40px;
	}
</style>
<div class="content-wrapper">
	<div class="col-md-12" style="float:inherit;">
		<div class="card-body">
			<div class="row">	
				<div class="col-md-12">
					<div class="row" align="center">
						<?php if($rowu['role']=="Admin") echo "
						<a class='btn btn-warning' href='agregar_items.php'>Agregar Items</a>
						<a class='btn btn-warning' href='add_farm.php'>Agregar Mundo</a> 
						<span id='viewgrill' class='btn btn-warning'><i class='fa fa-eye'></i></span>
						"

						;
						?>
					</div>
					
					<div class="row" align="center" style="padding: 8px;">
						<?php if ($farms and mysqli_num_rows($farms)>0) { 
							while ($rowfarm = mysqli_fetch_assoc($farms)) {
						?>
						<div class="item col-xs-2" align="center">
							<div class="prevfarm">
								<img src="<?php echo $rowfarm['thumbnail']; ?>" onclick="window.location.href='worlds.php?farm_id=<?php echo $rowfarm["id"] ?>'">
							</div>
						</div>
					<?php } } ?>
					</div>
					<div class="row" align="center">
						<div class="scroll">
							<div class="farm">
								<table id="dt-basic" class="table" cellspacing="0" width="100%">
									<?php 
										for ($pos_y=0; $pos_y < 20; $pos_y++) { ?>

											<tr>
												<?php

												$pos_x=0;

												for ($a=$pos_y; $a < 30; $a++) { 
													$pos_x++;

													//COMPRUEBA QUE EL USUARIO TENGA ITEMS EN ESTE MUNDO
													if ($num_items_player==0 AND $rowu['role']!="Admin") {
														echo "<a href='$sitio[site]items.php?world_id=$cntfarm[id]' class='btn btn-success' style='position: relative;top: 45%;width: 655px;'>Ir a la tienda de $cntfarm[name] </a>";
														break 2;
													}
													//DEVUELVE UN ITEM SI ESTA ES SU POSICION DE UN ITEM ES IGUAL
													else
													{
														$returned=equalcoordinate($items_player,$pos_x,$pos_y,$farm_id);
													?>
														<td class="td" style="border-top:0;">
															<?php

															//IMPRIMIR SOLO LA PRIMERA FILA Y LA PRIMERA COLUMNA
															if ($pos_y==0 or $pos_x==1) {
																echo "<span class='visible'>". $a ."</span>";
															} else {
																echo "<span class='visible'>0</span>";
															}
														
															echo $returned;
														
															?>
															
															</td>
												<?php
												 //echo "HOLA";
												}
												}
												 ?>						
											</tr>
										<?php } ?>
								</table>

							</div>
						</div>
						<div class="box card center" align="center" style="background:#ecf0f5;box-shadow: 1px 1px 1px 0px chocolate;min-width: unset;">
							<i class="fa fa-info-circle"> </i> <span style="font-size: 14px">Tienes <strong ><?php echo $rowu['puntos']; ?></strong> puntos recolectados</span> <a href="tiendapuntos.php">Ir a la tienda y comprar con puntos</a>
						</div>
						<a href="items.php?world_id=<?php echo $cntfarm['id']; ?>" class="btn btn-warning">Comprar items para <span style="color:black"><?php echo $cntfarm['name']; ?></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#viewgrill").click(function(){
			$(".visible").show();
		})
	});
</script>
<?php 
footer();

if (isset($_GET['give']) AND !empty($_GET['give']) AND is_numeric($_GET['give'])) {
	$give=$_GET['give'];
	$farm_id=$_GET['farm_id'];
	$time=time() + (60*60*12); // DEFAULT 12HORAS
	$items_player =mysqli_query($connect, "SELECT * FROM `players_farm_items` WHERE id='$give'");
	$currentfarm= mysqli_query($connect, "SELECT * FROM `farms` WHERE id='$farm_id'");

	// CONFIRMA SI YA SE PUEDE RECOJER LA COSECHA
	if ($items_player AND mysqli_num_rows($items_player)>0)
	{
			//
			$row_items_player=mysqli_fetch_assoc($items_player);
			// SELECCIONA EL ITEM
			$consult2 = mysqli_query($connect, "SELECT * FROM `farm_items` WHERE id='$row_items_player[items_id]'");
			//
			if ($consult2 AND mysqli_num_rows($consult2)>0)
			{
				//
				$row_items_farm=mysqli_fetch_assoc($consult2);
				// SI YA SE PUEDE RECOJER LA COSECHA
				if ($row_items_player['time']<time() AND $row_items_player['time']>0)
				{

					$puntos=$row_items_farm['produces'];
					// ACTUALIZA LOS PUNTOS
					$consult3=$connect->query("UPDATE players SET puntos=puntos + '$puntos' WHERE id='$rowu[id]'");
					// AGREGA LA SIGUIENTE CONSECHA PARA DENTRO DE X SEGUNDOS
					$consult4=$connect->query("UPDATE players_farm_items SET time='$time' WHERE id='$give'");

					if ($consult3 AND $consult4)
					{
						echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'worlds.php?farm_id='.$farm_id.' " />';
					}

				}
			}
			else
			{
				echo "no exite el item";
			}
		}
		else
		{
			echo "1";
		}
	}

 ?>
