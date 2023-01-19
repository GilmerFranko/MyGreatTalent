<?php

// INDICACION TEMPORAL
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'com.BelGram.android')
{
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">


	<center> <H4> <font color="green">Esta secci&oacute;n, es solo visible desde nuestra nueva app de BellasGram (Google nos elimino la anterior), para ingresar sigue los pasos descritos abajo y despu&eacute;s <a href="https://play.google.com/store/apps/details?id=com.bellas.gram.app.android">descarga la APP AQUI</a> ya luego borra la app actual e ingresa solo desde la nueva.

	<hr />
	Si ya tienes la nueva APP pero no recuerdas tu contrase&ntilde;a y se te dificultan los pasos para una nueva, entonces aun no elimines la app y <br/> enviale un mensaje a <a href="https://bellasgram.com/chat/newchat.php?id=196558">ANDREA MARTINEZ</a> y pidele una contrase&ntilde;a nueva, ella te ayudara, (las contrase&ntilde;as estan cifradas con seguridad y no te puede decir tu contrase&ntilde;a actual, pero si te puede dar una nueva, no perder&aacute;s nada de lo que tenias).
	<hr />
	La nueva app es mas r&aacute;pida, y salen menos anuncios al ver fotos en BellasGram y el Chat, adem&aacute;s ya no te saldr&aacute;n anuncios de video al tocar la lupa en BellasGram para ver una foto.
	<hr />
	No te olvides darnos 5 estrellas<br/><br/>
	<hr />
	<br/><br/>
	PASOS A SEGUIR:
	<br/><br/>
	<hr />
	Nuestra aplicaci&iacute;n en la PlayStore o Google Play se llama
	<br/><a href="https://play.google.com/store/apps/details?id=com.bellas.gram.app.android">BellasGram </a><br/>(Puedes buscarla en la PlayStore o toca las letras azules para ir a Google Play)
	<hr />

	Instala la APP y la abres e ignora el contenido (a simple vista se ve como si fuera un app de un v&iacute;deojuego, pero lo rico esta oculto)<br/> <br/>
	1: toca la lupa que sale arriba a la derecha
	<br/><br/>
	<img src="https://bellasgram.com/static/images/1.png">


	<hr />
	2: Toca donde donde "buscar"
	<br/><br/>
	<img src="https://bellasgram.com/static/images/2.png">
	<hr />
	Y escribe bellasgram y espera que salga la manzana (al escribir bellasgram saldr&aacute; una manzana) y la tocas para entrar a BellasGram, Ya puedes entrar con tu correo y contrase&ntilde;a, si no recuerdas la contrase&ntilde;a puedes pedir una nueva como se indica arriba.
	<br/><br/>
	<img src="https://bellasgram.com/static/images/3.png">
	<hr />

	';
	exit;
}

/* FIN INDICACION TEMPORAL*/


require("core.php");
head();

if(isset($_GET['Subscribe'])){
	AddListFollow( $_GET['profile_id'] );
	echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'profile.php?profile_id='.$_GET['profile_id'].'" />';
	exit;
}

if(isset($_GET['agregar_id'])){

	$iddelamigo = $_GET['agregar_id'];

	// para poder iniciar una solicitud de amistad no deben haber bloqueos
	$sqlbuscarbloqueo1 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$iddelamigo'");
	$hayunbloqueo1 = $sqlbuscarbloqueo1 ? mysqli_num_rows($sqlbuscarbloqueo1) : 0;

	$sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE fromid='$player_id' AND toid='$iddelamigo'");
	$hayunbloqueo2 = $sqlbuscarbloqueo2 ? mysqli_num_rows($sqlbuscarbloqueo2) : 0;


	if ($hayunbloqueo1 < 1 && $hayunbloqueo2 < 1){

		$amistadsql01 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$iddelamigo'");
		$amistadassoc01 = mysqli_num_rows($amistadsql01);

		$amistadsql02 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player2='$player_id' AND player1='$iddelamigo'");
		$amistadassoc02 = mysqli_num_rows($amistadsql02);

		//verificando que no existan solicitudes de amistad pendientes

		$samistadsql01 = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE fromid='$player_id' AND toid='$iddelamigo' AND not_key='newAmistad'");
		$samistadassoc01 = mysqli_num_rows($samistadsql01);

		//

		if($amistadassoc01>0 || $amistadassoc02>0){
			$sisonamigos = 'si';
		}else{
			$sisonamigos = 'no';
		}

		if($samistadassoc01>0){
			$haysolicitud = 'si';
		}else{
			$haysolicitud = 'no';
		}


		if ($sisonamigos == 'no' && $haysolicitud == 'no'){
			$agregandosolicitud     = mysqli_query($connect, "INSERT INTO `players_notifications` (fromid, toid,not_key,read_time) VALUES ('$player_id', '$iddelamigo','newAmistad','0')");
		}

	}
}
// ELIMINA A UN USUARIO COMO AMIGO
if(isset($_GET['eliminar_id'])){
	deleteFriend($connect->real_escape_string($_GET['eliminar_id']));
}

if(isset($_GET['cancelar_id'])){
	//cancelar solicitud de amistad

	$iddelcancelado = $connect->real_escape_string($_GET['cancelar_id']);

	$cancelarsql = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE fromid='$player_id' AND toid='$iddelcancelado' AND not_key='newAmistad'");
	$cancelarcount = mysqli_num_rows($cancelarsql);

	if($cancelarcount > 0)
	{

		$cancelar = mysqli_fetch_assoc($cancelarsql);

		// Deshabilitar solicitud de amistad
		$querydelete = mysqli_query($connect, "DELETE FROM `players_notifications` WHERE id='$cancelar[id]' AND not_key='newAmistad'");

	}

}

//bloquear usuario
if(isset($_GET['bloquear_id'])){
	//ingresando el bloqueo a la bd
	if(addBlockUser($connect->real_escape_string($_GET['bloquear_id'])))
	{
		redirectTo('bloqueados.php');
	}
}


	//desbloquear usuario
if(isset($_GET['desbloquear_id'])){

	$desbloquear_id = $_GET['desbloquear_id'];

	$desbloquearuser = mysqli_query($connect, "DELETE FROM `bloqueos` WHERE fromid='$player_id' AND toid='$desbloquear_id'");

	$mensajebloqueado = 2;
}

// COMPRUEBA EL ID DEL USUARIO BUSCADO
if (isset($_GET['profile_id']))
{
	// ALMACENALO
	$idUser = $_GET['profile_id'];
	$selfuser = false;
}else
{
	// SI NO ESTOY LOGUEADO
	if(!isLogged())
	{
  // REDIRIGE AL LOGIN
		echo '<meta http-equiv="refresh" content="0;url=index.php">';
		exit;
	}
	// SI NO SELECCIONA MI ID
	$idUser = $player_id;
	$selfuser = true;
}
//-------OBTIENE EL USUARIO------

# Si hay que buscar por ID.
if(is_numeric($idUser))
{
	$sqlUser = getUser($idUser);
}
# Si hay que buscar por Nombre.
elseif(is_string($idUser))
{
	$sqlUser = getUser(str_replace('.',' ',$idUser),true);
}

// SI NO SE ENCONTRO NINGUN USUARIO CON EL NOMBRE/ID
if(!$sqlUser OR !$sqlUser->num_rows > 0)
{
	setSwalFire(array('Parece que el usuario que esta buscando no existe','Verifica que el nombre del usuario esta bien escrito o intente localizarlo <a href=\'search.php\'>aqui</a>','success'), 8000);
	echo '<meta http-equiv="refresh" content="7; url='.$sitio['site'].'profile.php" />';
	exit;
}

$userbuscado = mysqli_fetch_assoc($sqlUser);
$id = $userbuscado['id'];
//-----------------------



if (isLogged())
{

	// VERIFICA SI EL PERFIL ESTA OCULTO PARA PERFILES MAS ANTIGUOS
	$private = $connect->query("SELECT * FROM `players` WHERE id = '$id' AND IF(players.`hidden_for_old` != 0 AND players.`id` != '$rowu[id]', players.`hidden_for_old` <= '$rowu[time_joined]', 1=1)")->num_rows;
	// SI EL PERFIL ES MAS NUEVO QUE EL MIO Y TIENE LA OPCION PARA OCULTAR SU PERFIL A LOS PERFILES MAS ANTIGUOS A ESTE
	if ($private <= 0)
	{
		// REDIRIGE AL LOGIN
		echo '<meta http-equiv="refresh" content="0;url=galerias.php">';
		exit;
	}

	$iamfrom = ($rowu['registerfrom'] == 'chat' ? 'chat' : "bellasgram");
	$author_id = $rowu['id'];
	$sqlbono = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
	$sbono   = mysqli_fetch_assoc($sqlbono);
	$bono    = $sbono['bonoref'];
	$sqlcodigo = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$player_id'");
	$scodigo   = mysqli_fetch_assoc($sqlcodigo);
	$codigo    = $scodigo['refcodigo'];
	$queryref = mysqli_query($connect, "SELECT * FROM `players` WHERE referer_id='$player_id'");
	$countref = mysqli_num_rows($queryref);
}
else
{
	$iamfrom = "bellasgram";
}
$timeonline = time() - 60;
$query = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$id'");
$userbuscado = mysqli_fetch_assoc($query);

#-> SI EL USUARIO TIENE EL PERFIL OCULTO Y NO PUEDO VER SU PERFIL
if((!canSeeYourProfile($id)) AND ($userbuscado['id'] != $rowu['id']))
{
	echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'profile.php" />';
	exit;
}
if ($rowu['role'] == 'Admin' && isset($_GET["permission_upload"]) && $userbuscado){
	$permission_upload = $_GET["permission_upload"];
	$player_update2 = mysqli_query($connect, "UPDATE `players` SET permission_upload={$permission_upload} WHERE id='{$id}'");
	echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'profile.php?profile_id='.$_GET['profile_id'].'" />';
	exit;
}

if ($rowu['role'] == 'Admin' && isset($_GET["banear"]) && $userbuscado){
	$Action = 0;
	$userbuscado["baneado"] = 0;
	if($_GET["banear"] == 0){
		$Action = 1;
		$userbuscado["baneado"] = 1;
	}

	$player_update2 = mysqli_query($connect, "UPDATE `players` SET baneado={$Action} WHERE id='{$id}'");
}

?>

<div class="content-wrapper" height="10%">
	<div id="content-container">
		<div class="row">
			<div style="width: 100%;">
				<?php
				if($userbuscado)
				{
					$UserPet = mysqli_query($connect, "SELECT * FROM `player_pets` WHERE player_id='{$id}' AND live=1 AND profile=1 ORDER BY id ASC LIMIT 1");
					$Pet = false;
					if($UserPet && mysqli_num_rows($UserPet)){
						$UserPet = mysqli_fetch_assoc($UserPet);

						$Pet = mysqli_query($connect, "SELECT * FROM `pets` WHERE id = '{$UserPet["pet_id"]}' LIMIT 1");
						$Pet = mysqli_fetch_assoc($Pet);
						if(!is_null($Pet['frases'])){
							$Frese = json_decode($Pet['frases'], true);
							$Pet['frases'] = $Frese[ rand(0, count($Frese)-1) ];
						}
					}

						//MOTRAR TRADUCTOR AQUI SI NO ESTOY LOGUEADO
					if (!isLogged())
						{ ?>
							<style>.cover-page{top: 100px}</style>
							<div  align="center" style="position: relative;top: -10px;">
								<div id="google_translate_element"></div>
								<script type="text/javascript">
									function googleTranslateElementInit() {
										new google.translate.TranslateElement({pageLanguage: 'es',includedLanguages: 'en,es,fr,it,pt,ar,de,ru,tr', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
									}
								</script>
								<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
								<script type="text/javascript" src="assets/js/translate.js"></script>
							</div>
							<?php
						}
						//

						if ($id == $player_id){
							?>
							<div class="">
								<!--PORTADA-->
								<div style="height: 165px;"></div>
								<div class="cover-page lozad item-zoom" data-toggle="modal" data-target="#cover-page" style="background: url('<?php echo $userbuscado['cover-page']=='' ? '' : $userbuscado['cover-page']; ?>') no-repeat center center ;background-size: cover;" data-src="<?php echo $userbuscado['cover-page']=='' ? '' : $userbuscado['cover-page']; ?>"></div>
								<div class="box-header" style="z-index: 1;">
									<?php if(!is_null($Pet['frases']) && isset($Frese)): ?>
									<div class="petFrase"><?php Echo $Pet['frases']; ?></div>
								<?php endif; ?>
							</div>
							<div class="box-body">
								<!--AVATAR-->
								<div class="avatar-box">
									<img src="<?php Echo $sitio['site'].$userbuscado['avatar']; ?>">
									<?php if($Pet): ?>
										<div class="pet-avatar">
											<div class="menu-dropdown-arrow"></div>
											<img src="<?php echo porcentaje($Pet['hp'], $UserPet['hp'])<50 ? petImg($Pet['image'])->lifelow : petImg($Pet['image'])->imgNormal;?>">
											</div>
										<?php endif;
										get_medallas($id);
										?>
										<?php if($userbuscado['role']=="Admin"){ ?>
											<div style="position: absolute; left: 75%;bottom: -5px;">
												<img src="https://bellasgram.com/chat/assets/img/admin.png" alt="administrador">
											</div>
										<?php } ?>
									</div>

									<center>
										<h2>
											<strong style="z-index: 1;"><?php Echo $userbuscado['username']; ?></strong>
										</h2>
										<?php if($userbuscado['role']=="Admin"){ ?>
											<div>
												<span class="btn btn-primary" style="background-color: #00708e; border-radius: 30px;"><i class="fa fa-key"></i> Admin</span>
											</div>
										<?php } ?>
										<br>
									</center>

									<?php
									if ($userbuscado['timeonline'] > $timeonline) {
										echo '<span class="user-online">Online</span>';
									}else{
										echo '<span class="user-offline">Offline</span>';
									}?>

									<!-- Mostrar cantidad de fotos solo si es mi perfil -->
									<?php if (isLogged()): ?>
										<br><br><span><strong><?php echo $userbuscado['username'] ?></strong> tiene <?php echo CountAllThePhotos($userbuscado['id']);?> fotos y videos</span>
									<?php endif ?>

									<!-- MOSTRAR DESCRIPCIÓN A LOGUEADOS-->
									<?php if (isLogged()): ?>
										<hr></center>
										<p>
											<b><em><?php echo StrToUrl($userbuscado['description']);?></em></b>
										</p>
									<?php endif ?>
									<!---->

									<?php if (isLogged()): ?>
										<?php echo '<p>Género: <b>'.$userbuscado['gender'].'</b></p>'?>
									<?php endif; ?>

									<?php echo '<p>Créditos: <b>'.$userbuscado['eCreditos'].'</b></p>';

									if($rowu['role'] == 'Admin'){
										Echo '<p>IP: <b>'.$userbuscado['ipaddres'].'</b></p>';
									}

									if($userbuscado['gender']=='mujer'):?>
										<p>Perfil Oculto: <b><?php echo $userbuscado['perfiloculto']?></b></p>
									<?php endif; ?>

									<!-- BOTON COPIAR LINK -->
									<button type="button" class="btn btn-success" id="copyUrlProfile" data-clipboard-text="<?php echo 'https://my-great-talent.com'.getUserLink($rowu['id']);?>">
										Copiar enlace al perfil
									</button>


								<?php }else{

									EarchLifePets($id);
										// si hay un bloqueo sacarlo del perfil

									$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$userbuscado[id]'");
									$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);

									if ($hayunbloqueo > 0){

										echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
										exit;

									}else{

										?>
										<div class="">
											<!--PORTADA-->
											<div style="height: 165px;"></div>
											<div class="cover-page lozad item-zoom" data-toggle="modal" data-target="#cover-page" style="background: url('<?php echo $userbuscado['cover-page']=='' ? '' : $userbuscado['cover-page']; ?>') no-repeat center center ;background-size: cover;" data-src="<?php echo $userbuscado['cover-page']=='' ? '' : $userbuscado['cover-page']; ?>"></div>
											<div class="box-header" style="z-index: 1;">
												<?php if(!is_null($Pet['frases']) && isset($Frese)): ?>
												<div class="petFrase"><?php Echo $Pet['frases']; ?></div>
											<?php endif; ?>
										</div>
										<div class="box-body">
											<!--AVATAR-->
											<div class="avatar-box">
												<img src="<?php Echo $sitio['site'].$userbuscado['avatar']; ?>">
												<?php if($Pet): ?>
													<style type="text/css">
														.box-header{
															z-index: 1;
														}
													</style>
													<div class="pet-avatar">
														<div class="menu-dropdown-arrow"></div>
														<img src="<?php echo porcentaje($Pet['hp'], $UserPet['hp'])<50 ? petImg($Pet['image'])->lifelow : petImg($Pet['image'])->imgNormal;?>">
														</div>
													<?php endif;
													get_medallas($id);
													?>
													<?php if($userbuscado['role']=="Admin"){ ?>
														<div style="position: absolute; left: 75%;bottom: -5px;">
															<img src="https://bellasgram.com/chat/assets/img/admin.png" alt="administrador">
														</div>
													<?php } ?>
												</div>

												<center>
													<h2>
														<strong style="z-index: 1;"><?php Echo $userbuscado['username']; ?></strong>
													</h2>
													<?php if($userbuscado['role']=="Admin"){ ?>
														<div>
															<span class="btn btn-primary" style="background-color: #00708e; border-radius: 30px;"><i class="fa fa-key"></i> Admin</span>
														</div>
													<?php } ?>
													<br>
												</center>

												<?php
												if ($userbuscado['timeonline'] > $timeonline) {
													echo '<span class="user-online">Online</span>';
												}else{
													echo '<span class="user-offline">Offline</span>';
												}
												?>


												<!-- Mostrar cantidad de fotos a los no logueados -->
												<?php if (!isLogged()): ?>
													<br><br><span><strong><?php echo $userbuscado['username'] ?></strong> tiene <?php echo CountAllThePhotos($userbuscado['id']);?> fotos y videos</span><br><br>
												<?php endif ?>

												<!-- MOSTRAR DESCRIPCIÓN A LOGUEADOS-->
												<?php if (isLogged()): ?>
													<hr></center>
													<p>
														<b><em><?php echo StrToUrl($userbuscado['description']);?></em></b>
													</p>
												<?php endif ?>
												<!---->

												<?php if (isLogged()): ?>
													<?php echo '<p>Género: <b>'.$userbuscado['gender'].'</b></p>'?>
												<?php endif; ?>

												<?php echo '<p>Créditos: <b>'.$userbuscado['eCreditos'].'</b></p>';

												if($rowu['role'] == 'Admin'): ?>
													<p>IP: <strong><a href="buscaip.php?nombre=<?php echo $userbuscado['ipaddres'] ?>"> <?php echo $userbuscado['ipaddres']?></a></strong></p>
												<?php endif;
												if(isset($mensajebloqueado)){
													if ($mensajebloqueado == 1){

														echo '<p style="color:green">Bloqueaste a este usuario</p>';

													}
													if ($mensajebloqueado == 2){
														echo '<p style="color:green">Desbloqueaste a este usuario</p>';
													}
												}
												#-> MENU DESPLEGABLE(SOLO PARA ADMIN)
												if($rowu['role']=="Admin"){
													echo "<div class='btn-group'>
													<button class='btn btn-secondary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
													Menu
													</button>
													<div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>";
													?>
													<style>
														.width{
															width: 100%;

														}
														.br{
															display: none;
														}
													</style>
													<?php
												}
												if (isLogged()) {
													if ($rowu['role'] == 'Admin'){
														if(!isset($_COOKIE['returnUser'])): ?>
															<a href="javascript:loginAsThisUser('<?php echo $userbuscado['id'] ?>')" class="btn btn-primary dropdown-item width" ><i class="fa fa-times"></i> Entrar como <strong><?php echo getFirstWord($userbuscado['username']) ?></strong></a><br class="br">
														<?php endif;
													// SI NO ESTA BANEADO
														if ($userbuscado["baneado"] == 0)
														{
															echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&banear='. $userbuscado["baneado"] .'" class="btn btn-danger dropdown-item width" ><i class="fa fa-times"></i> Banear Usuario</a><br class="br">';
														}else{
															echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&banear='. $userbuscado["baneado"] .'" class="btn btn-danger dropdown-item width" ><i class="fa fa-handshake"></i> Perdonar Usuario</a><br class="br">';
														}
													#-> BOTON NO-PUBLIC
													/*$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
													echo '<a href="'.$sitio['site'].'profile.php?page='.$page.'&profile_id=' . $userbuscado['id'] . '&action_nopublic='. $userbuscado["id"] .'" class="btn btn-primary dropdown-item width" ><i class="fa fa-action"></i> Cambiar a No-Public</a><br class="br">';*/
													#-> BOTON EDITAR PERFIL
													echo '<a href="'.$sitio['site'].'edituser.php?edit-id=' . $userbuscado['id'].'" class="btn btn-primary dropdown-item width" ><i class="fa fa-action"></i>Editar Perfil</a><br class="br">';
													if ($userbuscado["permission_upload"] == 0){
														echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&permission_upload=1" class="btn btn-success dropdown-item width" ><i class="fa fa-check"></i> Permitir subir fotos</a><br class="br">';
													}else{
														echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&permission_upload=0" class="btn btn-danger dropdown-item width" ><i class="fa fa-lock"></i> Bloquear subir fotos</a><br class="br">';
													}
												}

												/*if (!isFollow($userbuscado["id"])){
													if($rowu['eCreditos']>=2000){
														echo '<a href="#" data-username="'.$userbuscado['username'].'" data-href="'.$sitio['site'].'profile.php?profile_id='.$userbuscado['id'].'&Subscribe" id="suscribe" class="btn btn-success dropdown-item width" >
														Suscribirme por <br/>2000 créditos especiales 7 días
														</a><br class="br"><br>';
													}else{
														echo '<a id="no-credits" class="btn btn-success dropdown-item width" >Suscribirme por <br/> 2000 créditos especiales 7 días</a><br class="br"><br>';
													}
												}else{
													echo '<a class="btn btn-primary dropdown-item" ><i class="fa fa-star"></i> Suscrito por 7 días</a><br class="br"><br>';
												}*/

												?>
												<a class="btn btn-success dropdown-item text-gray" onclick="openDonarCreditos('<?php echo $id ?>, <?php echo $userbuscado['username']?>')" style="width: 30vw;font-size: 16px;font-weight: bold;border-radius: 24px;min-width: 214px;max-width: 400px;color: white !important;padding: 8px;">
													<i class="fas fa-donate"></i>&nbsp;&nbsp;Donar</a><br class="br"><br>
													<?php

													$amistadsql1 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$userbuscado[id]'");
													$amistadassoc1      = mysqli_num_rows($amistadsql1);

													$amistadsql2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player2='$player_id' AND player1='$userbuscado[id]'");
													$amistadassoc2      = mysqli_num_rows($amistadsql2);
													$isBlocking = checkBlocking($player_id,$userbuscado['id']);
													$cancelarsql = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE fromid='$player_id' AND toid='$userbuscado[id]' AND not_key='newAmistad'");
													$cancelarcount      = mysqli_num_rows($cancelarsql);
													if($cancelarcount>0){

														$hayunasolicitud = 'si';

													}else{

														$hayunasolicitud = 'no';
													}

													if($amistadassoc1>0 || $amistadassoc2>0){
														$sonamigos = 'si';
													}else{
														$sonamigos = 'no';
													}

													if ($sonamigos == 'si'){
														echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&eliminar_id=' . $userbuscado['id'] . '" class="btn btn-danger width" ><i class="fa fa-user-times"></i> Eliminar Amistad</a>
														<br class="br"><br>';

													}elseif($sonamigos == 'no' && $hayunasolicitud == 'si'){

														echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&cancelar_id=' . $userbuscado['id'] . '" class="btn btn-danger width" ><i class="fa fa-user-times"></i> Cancelar Solicitud de amistad</a>
														<br class="br"><br>';

													}else{

														if ($isBlocking == false){

															echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&agregar_id=' . $userbuscado['id'] . '" class="btn btn-primary dropdown-item width" ><i class="fa fa-user-plus"></i> Agregar Amistad</a>
															<br class="br"><br>';
														}else{

															echo '<button name="botondeshabilitado" id="botondeshabilitado" type="submit" class="btn btn-large btn-primary dropdown-item width"  disabled><i class="fa fa-user-plus"></i> Agregar Amistad</button><br class="br"><br>';


														}
													}

													$salasql1 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$player_id' AND player2='$userbuscado[id]'");
													$countsala1      = mysqli_num_rows($salasql1);
													$sala1 = mysqli_fetch_assoc($salasql1);

													$salasql2 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$userbuscado[id]' AND player2='$player_id'");
													$countsala2      = mysqli_num_rows($salasql2);
													$sala2 = mysqli_fetch_assoc($salasql2);

													if ($countsala1 > 0){

														$link = 'chat.php?chat_id='. $sala1['id'] .'#chat';

													}elseif($countsala2 > 0){

														$link = 'chat.php?chat_id='. $sala2['id'] .'#chat';
													}else{

														$link = 'newchat.php?id='.$userbuscado['id'];

													}
													?>
													<?php if ($rowu['role']=='Admin'): ?>
														<a class="btn btn-warning dropdown-item width" href="<?php echo createLink('transacciones','',array('profile_id' => $id),true) ?>">Ver Transacciones</a>
													<?php endif ?>
													<?php
													echo '<form method="POST" action="'. $sitio['site'] .'report.php">
													<input type="hidden" name="userreportado" value="'.$userbuscado['id'].'">
													<button name="iniciarreporte" type="submit" class="btn btn-warning dropdown-item width" >
													<i class="fa fa-exclamation-circle"></i> Reportar Perfil
													</button>
													</form>
													<br class="br"><br>';


													if (!$isBlocking){
														echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&bloquear_id=' . $userbuscado['id'] . '" name="bloquear" type="submit" class="btn btn-danger dropdown-item width" ><i class="fa fa-times-circle"></i> Bloquear Perfil</a><br class="br"><br>';
													}else{
														echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&desbloquear_id=' . $userbuscado['id'] . '" name="bloquear" type="submit" class="btn btn-danger dropdown-item width" ><i class="fa fa-unlock"></i> Desbloquear Perfil</a><br class="br"><br>';
													}

													if (!$isBlocking){

													#-> LIMITAR ACCION A USUARIOS QUE NO ESTEN EN LA APP O QUE NO SEAN REGISTRADOS DESDE EL CHAT
														if($sitio['limit_actions']=="no" || its_in()=='in_android' || $rowu['registerfrom']=="chat" || $userbuscado['role']=="Admin")
														{
															echo '
															<a href="'. $sitio['site'].$link .'" name="enviarunmensaje" type="button" class="btn btn-success dropdown-item width" ><i class="fas fa-envelope"></i> Iniciar chat con ' . $userbuscado['username'] . '</a><br><br>';
														}
														else
														{
															echo '
															<a name="enviarunmensaje" type="button" class="btn btn-success dropdown-item width action_limited" ><i class="fas fa-envelope"></i> Iniciar chat con ' . $userbuscado['username'] . '</a><br><br>';
														}
													}else{
														echo '<br>
														<button name="botondeshabilitado" id="botondeshabilitado" type="submit" class="btn btn-large btn-success dropdown-item width"  disabled>Iniciar chat con ' . $userbuscado['username'] . '</button>
														<hr></center><br><br>';
													}
											// SI NO ESTOY LOGUEADO
												}else{ ?>
													<a href="index.php" class="btn btn-primary dropdown-item width" ><i class="fa fa-user-plus"></i> Agregar Amistad</a>
													<br class="br"><br>
													<a id="botondeshabilitado" class="btn btn-large btn-success dropdown-item width" href="index.php">Iniciar chat con <?php echo $userbuscado['username'] ?></a>
												<?php }
											#-> FIN MENU DESPLEGABLE
												if ($rowu['role']=="Admin") {
													echo "</div></div>";
												}
											}

										}



									}
								?> </div>
							</div>

						</div>
					</div>
				</div>

			</div>
			<div class="content-wrapper" style="padding-top: 0;">
				<div id="content-container">
					<div class="content" style=" min-height: 0; padding:0;">
						<div class="row">
							<div class="col-md-12">
								<div class="box">
									<div class="box-body">
										<table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
											<tbody>
												<div class="card">
													<div class="card-body">
														<!--BOTON PACKS-->
														<?php
														$packs = $connect->query("SELECT * FROM `packsenventa` WHERE player_id='{$id}' ORDER BY id DESC")->num_rows;
														if($packs>0){
															$packs = $connect->query("SELECT * FROM `packsenventa` WHERE player_id='{$id}' ORDER BY RAND()");
															$count=0;
															?>
															<a href="packs.php?id_profile=<?php echo $id; ?>" style="color: #444"> <p style="color:#CC99AD;"> <?php echo $userbuscado['username'] . "  Posee packs. " ?> <b style="color:#337ab7; "> <br/>Ir a la seccion packs</p></b></a>

															<?php while ($rowpacks=mysqli_fetch_assoc($packs)) {
																$imgpacks=json_decode($rowpacks['imagens']);
																if ($rowpacks['visible']!="null" and $rowpacks['visible']!="" and $rowpacks['visible']!=null and $rowpacks['visible']!="[]" ) {
																	$json=json_decode($rowpacks['visible']);
																	if (!in_array($rowu['username'], $json) and $rowu['id']!=$rowpacks['player_id'] ) {
																		continue;
																	}
																}
																$author_id = $rowpacks['player_id'];
																$querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
																$rowcpd    = mysqli_fetch_assoc($querycpd);
																#-> SI EL USUARIO TIENE EL PERFIL OCULTO Y YO NO SOY UN USUARIO REGISTRADO DESDE EL CHAT
																if($rowcpd['perfiloculto']!='no' or $rowpacks['hidetochat']=='si' and $iamfrom!='chat')
																{
																#-> SI EL USUARIO ES DIFERENTE AL PROPIETARO DEL PACK
																	if($uname != $rowcpd['username'])
																	{

																		$friend = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$author_id'");
																		$friend01 = mysqli_num_rows($friend);

																		$friend2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$author_id' AND player2='$player_id'");
																		$friend02 = mysqli_num_rows($friend2);
																		if($friend02==false && $friend01==false){
																			continue;
																		}
																	}
																}
																?>
																<a href="packs.php?id_profile=<?php echo $id; ?>">
																	<div class="avatar-box" style="width: 60px;height: 60px;border:unset;display: inline;box-shadow: unset;"><img src="<?php echo $imgpacks[0]; ?>" style="width: 95px;height: 95px;border: 4px solid white;margin: 5px;box-shadow: 0 0 5px -1px black;"></div></a>
																	<?php
																	$count++;
																	if ($count>=3) {
																		break;
																	}
																}
															} ?>
														</div>
													</div>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php if(isLogged()): ?>
							<div class="content">
								<div class="row">
									<div class="box">
										<div class="box-body">
											<table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
												<tbody>
													<div class="card">
														<div class="row-list">

															<?php
															$timeonline = time() - 60;

															$total_pages = $connect->query("SELECT * FROM `fotosenventa` WHERE player_id='{$id}' ORDER BY id DESC")->num_rows;

															$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

															$num_results_on_page = 30;
															$calc_page = ($page - 1) * $num_results_on_page;

															$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE player_id='{$id}' ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
															$countcp = mysqli_num_rows($querycp);
															$recommendation = getRecommendations($id);
															$countWhile = 0;
															if ($countcp > 0) {
																while ($rowcp = mysqli_fetch_assoc($querycp))
																{
																	$countWhile++;
																	$author_id = $rowcp['player_id'];
																	$querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
																	$rowcpd    = mysqli_fetch_assoc($querycpd);

																	$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$author_id'");
																	$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);

																	$sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$author_id' AND fromid='$player_id'");
																	$hayunbloqueo2 = mysqli_num_rows($sqlbuscarbloqueo2);
															// SI NO HAY BLOQUEOS
																	if ($hayunbloqueo < 1 && $hayunbloqueo2 < 1)
																	{
																		$sub = '';

																// SI NO ESTOY SUSCRITO AL PERFIL
																		if(!isFollow($rowcpd['id']) && $rowcpd['id'] != $player_id)
																		{
																	// SI LA IMAGEN ES VIP, COLOCALE UN FILTRO
																			$sub = $rowcp['type'] == 'suscripciones' ? ' noSub': '';
																		}

																// ALMACENA LA MINIATURA
																		$thumbnail=json_decode($rowcp['thumb']);

																// ALMACENA LA FOTO
																		$image=json_decode($rowcp['imagen']);

																// INCLUYE LA FOTO
																		include "./Row-img-profile.php";
																	}
																}
																$profile_id = isset($_GET['profile_id']) ? '&profile_id='.$_GET['profile_id']:'';

																?>
															</div>
														</div>
													</tbody>
												</table>
											</div>
											<div class="content" align="center">
												<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
													<ul class="pagination">
														<?php if ($page > 1): ?>
															<li class="prev"><a href="profile.php?page=<?php echo $page-1 . $profile_id  ?>">Anterior</a></li>
														<?php endif; ?>

														<?php if ($page > 3): ?>
															<li class="start"><a href="profile.php?page=1">1</a></li>
															<li class="dots">...</li>
														<?php endif; ?>

														<?php if ($page-2 > 0): ?><li class="page"><a href="profile.php?page=<?php echo $page-2 . $profile_id ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
														<?php if ($page-1 > 0): ?><li class="page"><a href="profile.php?page=<?php echo $page-1 . $profile_id ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

														<li class="currentpage"><a href="profile.php?page=<?php echo $page . $profile_id ?>"><?php echo $page ?></a></li>

														<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="profile.php?page=<?php echo $page+1 . $profile_id ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
														<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="profile.php?page=<?php echo $page+2 . $profile_id ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

														<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
															<li class="dots">...</li>
															<li class="end"><a href="profile.php?page=<?php echo ceil($total_pages / $num_results_on_page) . $profile_id ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
														<?php endif; ?>

														<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
															<li class="next"><a href="profile.php?page=<?php echo $page+1 . $profile_id ?>">Siguiente</a></li>
														<?php endif; ?>
													</ul>
												<?php endif; ?>
												<?php
											} else {
												echo '<div class="alert alert-success"><strong><i class="fa fa-info-circle"></i> Descubre el talento de ' . $userbuscado['username'] . '</strong></div>';
											}

											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php else: ?>
						<div class="alert alert-success"><i class="fa fa-info-circle"></i> Ingresa y descubre el talento, fotos y videos de <strong><?php echo $userbuscado['username'];?> </strong></div>
					<?php endif; ?>
					<?php
				#-> CONVERTIR IMAGENES EN NO PUBLICAS
					if (isset($_GET['action_nopublic']) and !empty($_GET['action_nopublic'] ) ) {
						if ($rowu['role']='Admin') {
							$id = $_GET['action_nopublic'];
							$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE player_id='{$id}' ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
							$countcp = mysqli_num_rows($querycp);
						#-> GENERA N NUMERO ALEATORIOS NO REPETIDOS
							$rand = generaterandom(0,$countcp-1);
							if ($countcp > 0) {
								$b=0;
								$a=0;
							#-> ALMACENA FILAS Y COLUMNAS DE LA CONSULTA EN UN ARRAY
								while ($rowcp = mysqli_fetch_row($querycp)) {
									for ($i=0; $i < 7; $i++) {
										$row[$a][$i]=$rowcp[$i];
									}
									$a++;
								}
			/**GENERA LA CONSULTA
			*CAMBIARA LA MITAD DE LA PAGE COMO PRETEMINADO(50%)
			**/
			$mitad=ceil($countcp/2);
			$count=0;
			for ($i=0; $i <$mitad; $i++) {
				$idd=$row[$rand[$i]][0];
				#-> SOLO MODIFICARA IMAGENES SIN LINKS
				if($row[$rand[$i]][5]=="" or $row[$rand[$i]][5]==" " or $row[$rand[$i]][5]==null){
					#-> SOLO IMAGENES, VIDEOS NO
					if (getSourceType($row[$rand[$i]][2]) != 'film') {
						$querycp = mysqli_query($connect, "UPDATE `fotosenventa` SET `type`=\"suscripciones\" WHERE id=$idd");
						if($querycp){
							$count++;
						}
					}
				}
			}
			#-> SI NO SE MODIFICARON FILAS
			if ($count==0) {
				?>
				<script>swal.fire('Error. Filas modificadas: <?php echo $count; ?>','Recuerda que esta accion solo modificara a las Fotos sin URL externas','success');</script>
				<?php
				echo '<meta http-equiv="refresh" content="3; url='.$sitio['site'].'profile.php?page='.$page.'&profile_id='.$_GET['profile_id'].'" />';
			}
			else
			{
				?>
				<script>swal.fire('Consulta realizada con exito!','Filas modificadas: <?php echo $count; ?>','success');</script>
				<?php

				echo '<meta http-equiv="refresh" content="1; url='.$sitio['site'].'profile.php?page='.$page.'&profile_id='.$_GET['profile_id'].'" />';
			}

		}
		else
		{
			echo "<script>swal.fire('No hay filas que modificar','ERROR','error');</script>";
			echo '<meta http-equiv="refresh" content="1; url='.$sitio['site'].'profile.php?profile_id='.$_GET['profile_id'].'" />';
		}

	}
}
?>
<style>
	.dropdown-backdrop{
		position: unset;

	}
	.btn{
		/*border-radius: 16px !important;¨*/
	}
</style>
<script src="assets/js/clipboard.min.js?v=1"></script>
<script>

	var LikePost = function (ths, id){
		$.ajax({
			url: "ajax.php?like",
			type: "POST",
			data: {
				gid: id
			}
		}).done(function(response){
			var data = $.parseJSON(response);
			console.log(response);
			$(ths).toggleClass("isLike");
			if(data.status){
				swal.fire(data.message, "", "success");
			}else{
				swal.fire(data.message, "", "error");
			}
		})
	}
	$(document).ready(function(){
		$("[id=suscribe]").click(function(){
			var dHref = $(this).data('href');
			var dusername = $(this).data('username');
			swal.fire({
				title: 'Desea gastar 2000 creditos especiales para suscribirse a "'+dusername+'"?',
				buttons: ["Mejor no", "Si!"],
				showCancelButton: true,
			})
			.then((name) => {
				if(name.isConfirmed){

					window.location.href = dHref;

				}
			});
		})
	})
</script>

<?php
footer();
?>
