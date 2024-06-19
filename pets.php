<?php
require "core.php";


$player_id = $rowu['id'];

$adoptepPet = false;

if(isset($_GET['aceptarRegalo'])){
	$pet = $connect->query("SELECT * FROM `player_pets` WHERE id='{$_GET['aceptarRegalo']}' AND bonus>0 AND live=1");
	if($pet && mysqli_num_rows($pet)){
		$pet = mysqli_fetch_assoc($pet);

		$connect->query("UPDATE `player_pets` SET bonus=0 WHERE id='{$_GET['aceptarRegalo']}' AND live=1");
		updateCredits($pet['player_id'],'+',$pet['bonus'],9);
	}
	echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'pets.php" />';
	exit;
}

if(isset($_POST['BuyFeedItem'])){
	$pet_id = $_POST['pet_id'];
	$item_id = $_POST['item_id'];
	$item = $connect->query("SELECT * FROM `items` WHERE id='{$item_id}'");
	$item = mysqli_fetch_assoc($item);

	$Proc = false;
	if($item['price'] <= $rowu['creditos']){
		$Proc = 'creditos';
	}elseif($item['price'] <= $rowu['eCreditos']){
		$Proc = 'eCreditos';
	}

	if($Proc!=false){
		$pet = $connect->query("SELECT * FROM `player_pets` WHERE id='{$pet_id}' AND live=1");
		$pet = mysqli_fetch_assoc($pet);

		$petS = $connect->query("SELECT * FROM `pets` WHERE id='{$pet["pet_id"]}'");
		$petS = mysqli_fetch_assoc($petS);

		if($pet[ $item['type'] ] >= $petS[ $item['type'] ]){
			$name = str_replace('energy', 'estado de ánimo', $item['type']);
			$name = str_replace('hp', 'amor', $name);
			Echo json_encode([
				'status' => false,
				'message' => 'por ahora no se puede restaurar mas '. $name .' a su mascota'
			]);
			exit();
		}

		$newValue = $pet[ $item['type'] ] + $item['value'];
		if($newValue > $petS[ $item['type'] ]){
			$newValue = $petS[ $item['type'] ];
		}

		$Updated = $item['type'] .'='. $newValue;
		$connect->query("UPDATE `player_pets` SET {$Updated} WHERE id='{$pet_id}' AND live=1");
		$money = $rowu[ $Proc ] - $item['price'];
        $connect->query("UPDATE `players` SET {$Proc}='{$money}' WHERE id='{$player_id}'");
		Echo json_encode([
			'status' => true,
			'message' => 'success',
			'icon' => $item['image'],
			'value' => $Updated,
			'Val'.$item['type'] => $newValue.'/'.$petS[ $item['type'] ],
			'Por'.$item['type'] => porcentaje($petS[ $item['type'] ], $newValue)
		]);
	}else{
		Echo json_encode([
			'status' => false,
			'message' => 'no tienes creditos suficientes'
		]);
	}
	exit();
}

if(isset($_POST['ShowProfile'])){
	$pet_id = $_POST['pet_id'];
	$connect->query("UPDATE `player_pets` SET profile=0 WHERE live=1 AND player_id='{$player_id}'");
	$connect->query("UPDATE `player_pets` SET profile=1 WHERE id='{$pet_id}' AND live=1");
}

if(isset($_GET['AdoptarMascora'])) {
    $PetID = (int) $_POST['id'];
    $name = $_POST['name'];
    $queryps = $connect->query("SELECT * FROM `pets` WHERE id = '{$PetID}' LIMIT 1");
    $pet_exist = $connect->query("SELECT * FROM `player_pets` WHERE pet_id='{$PetID}' AND player_id='{$player_id}' AND live=1");
    $countps = mysqli_num_rows($queryps);
    if ($countps > 0 && !mysqli_num_rows($pet_exist)) {
		$rowps = mysqli_fetch_assoc($queryps);
		if($name==''){
			$name = $rowps['name'];
		}

		$hp = $rowps['hp'];
		$energy = $rowps['energy'];

		if($rowps['creditos'] <= $rowu['creditos']){
			$Proc = 'creditos';
		}elseif($rowps['creditos'] <= $rowu['eCreditos']){
			$Proc = 'eCreditos';
		}

        $money = $rowu[ $Proc ] - $rowps['creditos'];

		$adoptepPet = [
			'status' => false
		];

        if ($Proc!=false) {

            $pet_pay = $connect->query("UPDATE `players` SET {$Proc}='{$money}' WHERE id='{$player_id}'");

            $pet_adopt = $connect->query("INSERT INTO `player_pets` (player_id, pet_id, name, hp, energy, updated)
				VALUES ('{$player_id}', '{$PetID}', '{$name}', '{$hp}', '{$energy}', '". time() ."')");

			$adoptepPet = [
				'status' => true,
				'name' => $name,
				'price' => $rowps['creditos'],
				'image' => petImg($rowps['image'])->imgNormal,
				'total_money' => $money
			];

        }

		Echo json_encode($adoptepPet);

    }
	exit();
}

if (isset($_GET['returnId'])) {
    $petfr_id = (int) $_GET["returnId"];

    $queryspp = mysqli_query($connect, "SELECT * FROM `player_pets` WHERE pet_id='$petfr_id' and player_id='$player_id' LIMIT 1");
    $countspp = mysqli_num_rows($queryspp);

    if ($countspp > 0) {

        $querypfrc = mysqli_query($connect, "SELECT * FROM `pets` WHERE id='$petfr_id' LIMIT 1");
        $rowpfrc   = mysqli_fetch_assoc($querypfrc);

        $money_back   = $rowpfrc['money'] / 2;
        $gold_back    = $rowpfrc['gold'] / 2;
        $respect_back = $rowpfrc['respect'];
        $bonustypeg   = $rowpfrc['bonustype'];
        $bonusvalueg  = $rowpfrc['bonusvalue'];

        $return_pet = mysqli_query($connect, "DELETE FROM `player_pets` WHERE pet_id='$petfr_id' AND player_id='$player_id'");

        if ($bonustypeg == 'power') {
            $values_back = mysqli_query($connect, "UPDATE `players` SET money=money+'$money_back', gold=gold+'$gold_back', respect=respect-'$respect_back', power=power-'$bonusvalueg' WHERE id='$player_id'");
        }
        if ($bonustypeg == 'agility') {
            $values_back = mysqli_query($connect, "UPDATE `players` SET money=money+'$money_back', gold=gold+'$gold_back', respect=respect-'$respect_back', agility=agility-'$bonusvalueg' WHERE id='$player_id'");
        }
        if ($bonustypeg == 'endurance') {
            $values_back = mysqli_query($connect, "UPDATE `players` SET money=money+'$money_back', gold=gold+'$gold_back', respect=respect-'$respect_back', endurance=endurance-'$bonusvalueg' WHERE id='$player_id'");
        }
        if ($bonustypeg == 'intelligence') {
            $values_back = mysqli_query($connect, "UPDATE `players` SET money=money+'$money_back', gold=gold+'$gold_back', respect=respect-'$respect_back', intelligence=intelligence-'$bonusvalueg' WHERE id='$player_id'");
        }

    }
}

head();

$Action = @$_GET["Action"];

$pet_adopt = $connect->query("SELECT * FROM `player_pets` WHERE player_id='{$player_id}' AND live=1");
if($Action == ""){
	if(!mysqli_num_rows($pet_adopt)){
		$Action = "Shop";
	}else{
		$Action = "MyPets";
	}
}

?>

<div class="content-wrapper">
<div class="row" style="margin:0;">
	<div class="col-sm-12 col-md-6">
		<a class="btn btn-primary" style="width:100%;" href="pets.php?Action=MyPets">
			<i class="fa fa-paw"></i> Tus mascotas
		</a>
	</div>
	<div class="col-sm-12 col-md-6">
		<a class="btn btn-primary" style="width:100%;" href="pets.php?Action=Shop">
			<i class="fa fa-paw"></i> Abrir la Tienda
		</a>
	</div>
</div>

<?php
if($Action == "MyPets"):
?>
<style>
.animation01 {
  width: 100px;
  height: 100px;
  position: relative;
  left:5%;
  -webkit-animation: myfirst 5s linear 2s infinite alternate; /* Safari 4.0 - 8.0 */
  animation: myfirst 5s linear 2s infinite alternate;
}

/* Safari 4.0 - 8.0 */
@-webkit-keyframes myfirst {
  0%   {left:5%; top:0px;transform: scale(1);}
  25%  {left:80%; top:0px;transform: scale(1.2);}
  50%  {left:80%; top:100%;transform: scale(1);}
  75%  {left:5%; top:100%;transform: scale(1.2);}
  100% {left:5%; top:0px;transform: scale(1);}
}

/* Standard syntax */
@keyframes myfirst {
  0%   {left:5%; top:0px;transform: scale(1);}
  25%  {left:80%; top:0px;transform: scale(1.2);}
  50%  {left:80%; top:100%;transform: scale(1);}
  75%  {left:5%; top:100%;transform: scale(1.2);}
  100% {left:5%; top:0px;transform: scale(1);}
}

.animation02 {
  width: 100px;
  height: 100px;
  position: relative;
  -webkit-animation: ansecond 10s linear 2s infinite alternate; /* Safari 4.0 - 8.0 */
  animation: ansecond 10s linear 2s infinite alternate;
}

/* Safari 4.0 - 8.0 */
@-webkit-keyframes ansecond {
  0%   {top:0px;transform: scale(1);}
  25%  {top:100%;transform: scale(1.3);}
  50%  {top:0px;transform: scale(1);}
  75%  {top:100%;transform: scale(1.3);}
  100% {top:0px;transform: scale(1);}
}

/* Standard syntax */
@keyframes ansecond {
  0%   {top:0px;transform: scale(1);}
  25%  {top:100%;transform: scale(1.3);}
  50%  {top:0px;transform: scale(1);}
  75%  {top:100%;transform: scale(1.3);}
  100% {top:0px;transform: scale(1);}
}
#juguete {
    width: 50px;
    height: 50px;
    left: 50%;
    transform: translateX(-50%);
    background-size: cover;
    position: absolute;
    display: none;
    top: 0;
}
</style>
    <center><h3><i class="fa fa-paw"></i>Tus mascotas</h3><p style="color:red;">Pronto actualizaremos las mascotas, esperamos nos envien 3 fotos solicitadas a las mujeres.</p></center><br />
	<div class="row" style="margin: 0;">

<?php
while ($rowpp = mysqli_fetch_assoc($pet_adopt)):

	$querypp = mysqli_query($connect, "SELECT * FROM `pets` WHERE id='{$rowpp["pet_id"]}'");
	$pet = mysqli_fetch_assoc($querypp);
?>

	<div class="col-sm-12 col-md-6" id="pet-<?php echo $rowpp['id']; ?>">
		<center>
			<ul class="breadcrumb">
				<li class="active" style="width:49%;"><h5><?php echo $rowpp['name']; ?></h5></li>
				<?php if($rowpp["profile"] != 1): ?>
					<li class="active text-right" style="width:49%;">
						<form action="" method="POST">
							<input type="hidden" name="ShowProfile" value="true">
							<input type="hidden" name="pet_id" value="<?php echo $rowpp['id']; ?>">
							<button type="submit" class="btn btn-success">Mostrar en mi perfil</button>
						</form>
					</li>
				<?php endif; ?>
			</ul>
		</center>

		<div class="row">
			<div class="col-md-7">
				<center><img src="<?php echo porcentaje($pet['hp'], $rowpp['hp'])<50 ? petImg($pet['image'])->lifelow : petImg($pet['image'])->imgNormal;?>" style="max-height: 370px;"></center>
			</div>
			<div class="col-md-5">
				<div id="stats2">
					<div class="row">
						<div class="col-lg-12">
							<h6><i class="fa fa-heart"></i> Amor</h6>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-animated bg-success Porhp hp<?php Echo porcentaje($pet['hp'], $rowpp['hp']) <= 50 ? ' low':''; ?>" style="width:<?php Echo porcentaje($pet['hp'], $rowpp['hp']); ?>%;">
									<span class="Valhp"><?php Echo $rowpp['hp'].'/'.$pet['hp']; ?></span>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<h6><i class="fa fa-bolt"></i> Estado de ánimo</h6>
							<div class="progress mb-3">
								<div class="progress-bar progress-bar-striped progress-animated Porenergia energy<?php Echo porcentaje($pet['energy'], $rowpp['energy']) <= 50 ? ' low':''; ?>" style="width:<?php Echo porcentaje($pet['energy'], $rowpp['energy']); ?>%;">
									<span class="Valenergia"><?php Echo $rowpp['energy'].'/'.$pet['energy']; ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<span data-toggle="modal" data-target="#ModalFeed-<?php echo $rowpp['id']; ?>" class="btn btn-success btn-md btn-block"><i class="fa fa-paw"></i> Darle Amor</span>
				<span data-toggle="modal" data-target="#ModayGame-<?php echo $rowpp['id']; ?>" class="btn btn-success btn-md btn-block"><i class="fa fa-paw"></i> Jugar con ella</span>
			</div>
		</div>
		<hr />
	</div>
	<div class="modal fade" id="ModayGame-<?php echo $rowpp['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 style="margin:0 15px;"><?php echo $rowpp['name']; ?></h3>
				</div>
				<div class="modal-body">
					<div class="row">

						<div class="col-md-7">

							<div id="juguete" class="animation02"></div>

							<center><img src="<?php echo petImg($pet['image'])->imgGame;?>" style="max-height: 350px;"></center>

						</div>
						<div class="col-md-5">

							<div id="stats2">
								<div class="row">
									<div class="col-lg-12">
										<h6><i class="fa fa-heart"></i> Amor</h6>
										<div class="progress">
											<div class="progress-bar progress-bar-striped progress-animated bg-success Porhp hp" style="width:<?php Echo porcentaje($pet['hp'], $rowpp['hp']); ?>%;">
												<span class="Valhp"><?php Echo $rowpp['hp'].'/'.$pet['hp']; ?></span>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<h6><i class="fa fa-bolt"></i> Estado de ánimo</h6>
										<div class="progress mb-3">
											<div class="progress-bar progress-bar-striped progress-animated Porenergia energy" style="width:<?php Echo porcentaje($pet['energy'], $rowpp['energy']); ?>%;">
												<span class="Valenergia"><?php Echo $rowpp['energy'].'/'.$pet['energy']; ?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>


					</div>

					<br>
						<center><h2>Juega con tu mascota</h2></center>
					<br>
					<div class="row">
				<?php

					$items = mysqli_query($connect, "SELECT * FROM `items` WHERE type='energy'");
					while ($item = mysqli_fetch_assoc($items)):
				?>
						<div class="col-sm-4 col-md-3">
							<div class="item-shop">
								<img src="<?php Echo $item['image']; ?>" width="100%" style="max-width:60px;margin:auto;display:block;margin-bottom:10px;">
								<div class="item-footer text-center">

									<strong><?php Echo $item['name']; ?></strong>

									<div style="border:1px solid #ddd;padding:10px 0;border-radius:3px;margin:5px 0;">
										<i class="far fa-money-bill-alt"></i>
										<span class="badge badge-success">
											<?php Echo $item['price']; ?>
										</span>
									</div>
									<div style="border:1px solid #ddd;padding:10px 0;border-radius:3px;margin:5px 0;">
										<i class="fa fa-plus"></i>
										<span class="badge badge-success">
											<?php Echo $item['value']; ?>
										</span>
									</div>
								<?php
								if ($rowu['creditos'] < $item['price'] && $rowu['eCreditos'] < $item['price']) {
								?>
									<a class="btn btn-warning btn-md btn-block" href="comprar.php">Comprar</a>
								<?php
								} else {
								?>
									<form action="" method="POST" data-action="jugar">
										<input type="hidden" name="BuyFeedItem" value="true">
										<input type="hidden" name="pet_id" value="<?php echo $rowpp['id']; ?>">
										<input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
										<button type="submit" class="btn btn-success btn-md btn-block">Comprar</button>
									</form>
								<?php
								}
								?>

								</div>
							</div>
						</div>
				<?php
					endwhile;
				?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="ModalFeed-<?php echo $rowpp['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 style="margin:0 15px;"><?php echo $rowpp['name']; ?></h3>
				</div>
				<div class="modal-body">
					<div class="row">

						<div class="col-md-7">

							<center><img src="<?php echo petImg($pet['image'])->imgFeed;?>" style="max-height: 350px;"></center>

						</div>
						<div class="col-md-5">

							<div id="stats2">
								<div class="row">
									<div class="col-lg-12">
										<h6><i class="fa fa-heart"></i> Amor</h6>
										<div class="progress">
											<div class="progress-bar progress-bar-striped progress-animated bg-success Porhp hp" style="width:<?php Echo porcentaje($pet['hp'], $rowpp['hp']); ?>%;">
												<span class="Valhp"><?php Echo $rowpp['hp'].'/'.$pet['hp']; ?></span>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<h6><i class="fa fa-bolt"></i> Energia</h6>
										<div class="progress mb-3">
											<div class="progress-bar progress-bar-striped progress-animated Porenergia energy" style="width:<?php Echo porcentaje($pet['energy'], $rowpp['energy']); ?>%;">
												<span class="Valenergia"><?php Echo $rowpp['energy'].'/'.$pet['energy']; ?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>


					</div>

					<br>
						<center><h2>Dale Amor a tu Mascota</h2></center>
					<br>
					<div class="row">
				<?php

					$items = mysqli_query($connect, "SELECT * FROM `items` WHERE type='hp'");
					while ($item = mysqli_fetch_assoc($items)):
				?>
						<div class="col-sm-4 col-md-3">
							<div class="item-shop">
								<img src="<?php Echo $item['image']; ?>" width="100%" style="max-width:60px;margin:auto;display:block;margin-bottom:10px;">
								<div class="item-footer text-center">

									<strong><?php Echo $item['name']; ?></strong>

									<div style="border:1px solid #ddd;padding:10px 0;border-radius:3px;margin:5px 0;">
										<i class="far fa-money-bill-alt"></i>
										<span class="badge badge-success">
											<?php Echo $item['price']; ?>
										</span>
									</div>
									<div style="border:1px solid #ddd;padding:10px 0;border-radius:3px;margin:5px 0;">
										<i class="fa fa-plus"></i>
										<span class="badge badge-success">
											<?php Echo $item['value']; ?>
										</span>
									</div>
								<?php
								if ($rowu['creditos'] < $item['price'] && $rowu['eCreditos'] < $item['price']) {
								?>
									<a class="btn btn-warning btn-md btn-block" href="comprar.php">Comprar</a>
								<?php
								} else {
								?>
									<form action="" method="POST" data-action="alimentar">
										<input type="hidden" name="BuyFeedItem" value="true">
										<input type="hidden" name="pet_id" value="<?php echo $rowpp['id']; ?>">
										<input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
										<button type="submit" class="btn btn-success btn-md btn-block">Comprar</button>
									</form>
								<?php
								}
								?>

								</div>
							</div>
						</div>
				<?php
					endwhile;
				?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

<?php
endwhile;
?>
</div>
<script>
var isActive = false;
$(document).ready(function(){
	$("[data-action=jugar]").submit(function(e){
		e.preventDefault();
		var data = $(this).serialize();
		isActive = true;

		$.ajax({
			url:'pets.php',
			type:'POST',
			data: data
		}).done(function(response){
			var response = $.parseJSON(response);
			isActive = false;
			if(response.status){
				var rand = Math.floor(Math.random() * (2 - 1 + 1)) + 1;
				$(".modal-open .modal").scrollTop(0);
				console.log( response.icon );
				$("[id=juguete]").css({
					'background-image':'url('+response.icon+')'
				}).show();
				setTimeout(function(){
					$("[id=juguete]").hide();
					$(".Valenergia").html( response.Valenergy );
					$(".Porenergia").css( {"width": response.Porenergy + "%"} );
					swal.fire({
						title: 'Jugaste con ella y está feliz',
						icon: 'success',
						button: true
					})
				}, 6500);
			}else{
				swal.fire({
					title: response.message,
					icon: 'warning',
					button: true
				})
			}
		})
	})

	$("[data-action=alimentar]").submit(function(e){
		e.preventDefault();
		var data = $(this).serialize();
		isActive = true;

		$.ajax({
			url:'pets.php',
			type:'POST',
			data: data
		}).done(function(response){
			var response = $.parseJSON(response);
			isActive = false;
			if(response.status){
				console.log(response);
				$(".Valhp").html( response.Valhp );
				$(".Porhp").css( {"width": response.Porhp + "%"} );
				swal.fire({
					title: 'Tu Mascota te Ama',
					icon: 'success',
					button: true
				})
			}else{
				swal.fire({
					title: response.message,
					icon: 'warning',
					button: true
				})
			}
		})
	})
})
</script>
<?php
endif;

if($Action == "PetsDeathsss"):
?>
    <center><h3><i class="fa fa-paw"></i>Tus mascotas muertas</h3></center><br />
	<div class="row" style="margin: 0;">

<?php

$PetsDeath = $connect->query("SELECT * FROM `player_pets` WHERE player_id='{$player_id}' AND live=0");
while ($rowpp = mysqli_fetch_assoc($PetsDeath)):

	$querypp = mysqli_query($connect, "SELECT * FROM `pets` WHERE id='{$rowpp["pet_id"]}'");
	$pet = mysqli_fetch_assoc($querypp);
?>

	<div class="col-sm-12 col-md-6">
		<center>
			<ul class="breadcrumb"><li class="active"><h5><?php echo $rowpp['name']; ?></h5></li></ul>
		</center>
		<div class="row">
			<div class="col-md-7">
				<center><img src="<?php echo petImg($pet['image'])->imgNormal;?>" style="max-height: 370px;"></center>
			</div>
			<div class="col-md-5">
				<div id="stats2">
					<div class="row">
						<div class="col-lg-12">
							<h6><i class="fa fa-heart"></i> Amor</h6>
							<div class="progress">
								<div class="progress-bar progress-bar-striped progress-animated bg-success" style="width:<?php Echo porcentaje($pet['hp'], $rowpp['hp']); ?>%;">
									<span><?php Echo $rowpp['hp'].'/'.$pet['hp']; ?></span>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<h6><i class="fa fa-bolt"></i> Estado de ánimo</h6>
							<div class="progress mb-3">
								<div class="progress-bar progress-bar-striped progress-animated" style="width:<?php Echo porcentaje($pet['energy'], $rowpp['energy']); ?>%;">
									<span><?php Echo $rowpp['energy'].'/'.$pet['energy']; ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		<hr />
	</div>

<?php
endwhile;

Echo '</div>';

endif;

if($Action == "Shop"):
?>
    <center><h3><i class="fa fa-paw"></i>Tienda de mascotas</h3>
    <p style="color:red;">Pronto actualizaremos las mascotas, esperamos nos envien 3 fotos solicitadas a las mujeres.</p></center><br />
	Presentamos la tienda de mascotas, donde podras adoptar auna de las mujeres para cuidarla. (Sabías que las mascotas te dan créditos al cuidarlas?)<br />
	<div class="row" style="margin: 0;">

<?php
$querypp = mysqli_query($connect, "SELECT * FROM `pets` ORDER BY creditos ASC");
$countpp = mysqli_num_rows($querypp);
while ($rowpp = mysqli_fetch_assoc($querypp)) {

    $pet_id   = $rowpp['id'];
    $queryppc = mysqli_query($connect, "SELECT * FROM `player_pets` WHERE pet_id='$pet_id' AND player_id='$player_id' AND live=1 LIMIT 1");
    $rowppc   = mysqli_fetch_assoc($queryppc);
    $countppc = mysqli_num_rows($queryppc);

?>
	<div class="col-sm-12 col-md-6">
		<center>
			<ul class="breadcrumb"><li class="active"><h5><?php echo $rowpp['name']; ?></h5></li></ul>
		</center>
		<div class="row">
			<div class="col-md-7">
				<center><img src="<?php echo petImg($rowpp['image'])->imgNormal;?>" style="max-height: 370px;"></center>
			</div>
			<div class="col-md-5">
				<ul class="list-group">
					<li class="list-group-item active">
						<center>Detalles de mascota</center>
					</li>
					<li class="list-group-item">
						<i class="far fa-money-bill-alt"></i> Creditos
						<span class="badge badge-success float-right">
							<?php
								echo $rowpp['creditos'];
							?>
						</span>
					</li>

				</ul><br />
<?php
if ($countppc > 0) {
	echo '<a href="?returnId=' . $rowpp['id'] . '" class="btn btn-danger btn-md btn-block"><i class="fa fa-reply"></i> Ya no la quiero</a>';
} else if ($rowu['creditos'] < $rowpp['creditos'] && $rowu['eCreditos'] < $rowpp['creditos']) {
	echo '<button class="btn btn-warning btn-md btn-block" disabled><em class="fa fa-fw fa-paw"></em>Adoptar</button>';
} else {
	echo '<span data-action="adoptar" data-id="' . $rowpp['id'] . '" class="btn btn-success btn-md btn-block"><i class="fa fa-paw"></i> Adoptar</span>';
}
?>
			</div>
		</div>
		<hr />
	</div>
<?php
}
?>
	</div>

<script>
$(document).ready(function(){
	$("[data-action=adoptar]").click(function(){
		var PetID = $(this).data("id");

		swal.fire({
			title: 'Dale un nombre a tu mascota',
			content: "input",
			buttons: ["Mejor no", "Adoptar!"],
			showCancelButton: true,
		})
		.then((name) => {
			if(name.isConfirmed){

				$.ajax({
					url:'pets.php?AdoptarMascora',
					type:'POST',
					data: {
						id: PetID,
						name: name
					}
				}).done(function(response){
					var response = $.parseJSON(response);
					if(response.status){
						console.log(response);
						swal.fire({
							title: 'Adoptaste a "'+ response.name +'" por un precio de '+ response.price +' creditos, te quedan '+ response.total_money,
							icon: response.image,
							button: true,
						})
					}
				})

			}
		});

	})
})
</script>
<?php
endif;
?>
</div>
<?php

footer();
?>
