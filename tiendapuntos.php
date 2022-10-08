<?php
require("core.php");
head();

$where= "";
$id=null;

$items = mysqli_query($connect, "SELECT * FROM `player_items`");
$col=0;
$disable=false;
?>
<style type="text/css">
	.item{
	height: 260px;
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
		width: 100%;
	}
	@media (max-width: 770px){
		.item{
			height: unset;
		    margin: 8;
		}
		.img{
			width: 80%;
			height: unset;
			overflow: hidden;
		}
		.img img{
			width: unset;
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
					<div class="" align="center">
						<br>	
						<i class="fa fa-info-circle"> </i> <span style="font-size: 14px">Tienes <strong><?php echo $rowu['puntos']; ?></strong> puntos recolectados</span>
						<br>	

						<?php verify_alldragonballs(); ?>

						<br>
						<div class="row" style="background-color: #ecf0f5">

							<?php while($rowitems=mysqli_fetch_assoc($items))
							{ 
								//OBTENER ITEMS COMPRADOS
								$item_bought = mysqli_query($connect, "SELECT * FROM `player_items_bought` WHERE item_id='$rowitems[id]' AND player_id='$rowu[id]'");

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
											</span><?php echo $rowitems['description']; ?>
										</div>
									</div>	
									<div class="row panel-footer">
										<div class="col">
											<span>Precio: </span><?php echo $rowitems['price']; ?> <span> Puntos</span>
										</div>
										<div class="col">
											<?php if($disable){ ?>
												<button class="">Comprado</button>
											<?php }else{ ?>
											<button data-cash="<?php echo $rowitems['price'] ?>" data-href="<?php echo $sitio['site']; ?>tiendapuntos.php?buy_item=<?php echo $rowitems['id']; ?>" class="buy_player_item btn btn-primary">Comprar</button>
											<?php } ?>
										</div>
									</div>									 
								</div>
							<?php
							$col=$col+4;
							 } 
							?>
						</div>
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
	buy_player_item($_GET['buy_item']);
	//echo '<meta http-equiv="refresh" content="1; url='.$sitio['site'].'items.php?world_id=' . $id .' " />';
} else {
	# code...
}
if(isset($_GET['canjear']) AND !empty($_GET['canjear']) AND is_numeric($_GET['canjear'])){

	//SELECIONA TODAS LAS ESFERAS QUE HAY //DEFAULT 7
	$itemscounts = $connect->query("SELECT * FROM `player_items` WHERE type='dragonball'")->num_rows;
	$sql_items = $connect->query("SELECT * FROM `player_items` WHERE type='dragonball'");
	$all_items_players = $connect->query("SELECT * FROM `player_items_bought` INNER JOIN player_items ON player_items.id=player_items_bought.item_id WHERE player_id='$rowu[id]'");
	$counts=0;

	//VERIFICAR SI EL USUARIO TIENE TODAS LAS DRAGONBALL
	while ($all_items=mysqli_fetch_assoc($all_items_players))
	{
		if ($all_items['type'] == "dragonball")
		{
			$counts++;
		}
	}

	//SI LAS TIENE TODAS
	if ($counts==$itemscounts)
	{
		/*VERIFICAR QUE SE CANJEARA*/

		//CREDITOS ESPECIALES
		if ($_GET['canjear']==2500)
		{
			$canjear="eCreditos";
			$totalcanjear=2500;
		}

		//CREDITOS NORMALES
		elseif($_GET['canjear']==5000)
		{
			$canjear="creditos";
			$totalcanjear=5000;
		}

		//CANJEAR LOS CREDITOS
		$consult=$connect->query("UPDATE players SET `$canjear`=`$canjear`+$totalcanjear WHERE id='$rowu[id]'");

		//SI SE EJECUTO LA CONSULTA CORRECTAMENTE
		if ($consult) {

			//BORRAR TODAS LAS ESFERAS DE LA LISTA DE COMPRAS DEL USUARIO
			$delete = $connect->query("SELECT *, player_items_bought.id AS bought_id FROM `player_items_bought` INNER JOIN player_items ON player_items.id=player_items_bought.item_id WHERE player_id='$rowu[id]'");
			while ($rowdelete=mysqli_fetch_assoc($delete))
			{
				if ($rowdelete['type'] == "dragonball")
				{
					$deleteall = $connect->query("DELETE FROM `player_items_bought` WHERE id='$rowdelete[bought_id]'");
				}
			}
			//REDIRIGIR
			echo "<script>swal.fire('Los creditos se canjearon correctamente!','','success');</script>";
			echo '<meta http-equiv="refresh" content="1; url='.$sitio['site'].'tiendapuntos.php " />';
		}
	}
	else{
		echo "<script>swal.fire('ERROR CRITICO','','error');</script>";
		echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'galerias.php " />';
	}
}


 ?>
