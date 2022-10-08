<?php
require("core.php");
head();

$where= "";
$id=null;
if (isset($_GET['world_id']) and !empty($_GET['world_id']))
{
	$id=$_GET['world_id'];
	$where="WHERE id='$id'";
}

$farms = mysqli_query($connect, "SELECT * FROM `farms` $where");
$col=0;
$disable=false;
?>
<style type="text/css">
	.item{
	height: ;
    /* border: solid; */
    background-color: #fbfbfb;
    box-shadow: 0px 0px 5px -2px black;
    margin: 32px 15px;
    border-radius: 8px;
    color:black;
	}
	.img{
		height: 110px;
		overflow: hidden;
		width: 113px;
	}
	.img img{
		width: 80%;
	}
	@media (max-width: 770px){
		.item{
			height: unset;
		    border: solid;
		    margin: 5px;
		}
		.img{
			width: 80%;
			height: unset;
			overflow: hidden;
		}

	}

</style>
<div class="content-wrapper">
	<div class="col-md-12" style="">
		<div class="card-body">
			<div class="row">	
				<div class="col-md-12">
					<div class="row" align="center">
						<a class='btn btn-success' href="worlds.php">Mundos</a>
						<a class='btn btn-success' href="items.php">Items</a>
						<a class='btn btn-success' href="">Demas</a>
						<?php if($rowu['role']=="Admin") echo "
						<a class='btn btn-warning' href='agregar_items.php'>Agregar Items</a>
						<a class='btn btn-warning' href='add_farm.php'>Agregar Mundos</a>
						"

						;
						?>
					</div>
					<div class="">

						<?php 
						while ($rowfarms=mysqli_fetch_assoc($farms)) {

							$players_farms = mysqli_query($connect, "SELECT * FROM `players_farms` WHERE farms_id = '$rowfarms[id]'");
							$items = mysqli_query($connect, "SELECT * FROM `farm_items` WHERE farms_id='$rowfarms[id]'");

							?>
							<br><br>
							<div class="farm box" align="center" style="background-color: unset;">
								<h4 style="" class=""><span style=""></span><strong style="color: #ce5785;"><?php echo $rowfarms['name']; ?></strong></h4>
							</div>
							<div class="row" style="background-color: #ecf0f5">

								<?php while($rowitems=mysqli_fetch_assoc($items))
								{ 
									//OBTENER ITEMS COMPRADOS
									$item_bought = mysqli_query($connect, "SELECT * FROM `players_farm_items` WHERE items_id='$rowitems[id]' AND player_id='$rowu[id]'");

									//SI YA ESTA COMPRADO EL ITEM DESACTIVAR EL BOTON
									if (mysqli_num_rows($item_bought)>0)
									{
										$disable=true;
									}
									else
									{
										$disable=false;
									}
									if ($col==12) {
										$col=0;
									}
									?>

									<div class="item col-sm-4" align="center">
										<div class="row">
											<div class="col">
												<div class="img"><img src="<?php echo $rowitems['image']; ?>"></div>
											</div>
										</div>
										<div class="row">
											<div class="col">
												<strong><?php echo $rowitems['name']; ?></strong> 
											</div>
										</div>
										<div class="row">
											<div class="col">
												<span>Produce: </span><?php echo $rowitems['produces']; ?> <span> Puntos cada 24 horas</span>
											</div>
										</div>	
										<div class="row panel-footer">
											<div class="col">
												<span>Precio: </span><?php echo $rowitems['price']; ?> <span> Creditos Normales o Especiales</span>
											</div>
											<div class="col">
												<?php if($disable){ ?>
													<button class="">Comprado</button>
												<?php }else{ ?>
												<button data-cash="<?php echo $rowitems['price'] ?>" data-href="<?php echo $sitio['site']; ?>items.php?buy_item=<?php echo $rowitems['id']; ?>&world_id=<?php echo $id; ?>" class="buy_item btn btn-primary">Comprar</button>
												<?php } ?>
											</div>
										</div>									 
									</div>
								<?php
								$col=$col+4;
								 } 
								?>
							</div>
							
						<?php } ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
footer();
?>
<?php 

if (isset($_GET['buy_item']) AND !empty($_GET['buy_item']) AND is_numeric($_GET['buy_item'])) {
	if (isset($_GET['world_id']) and !empty($_GET['world_id']))
	{
		$id=$_GET['world_id'];
	}
	else
	{
		$id=null;
	}
	buy_item($_GET['buy_item'],$id);
	//echo '<meta http-equiv="refresh" content="1; url='.$sitio['site'].'items.php?world_id=' . $id .' " />';
} else {
	# code...
}


 ?>