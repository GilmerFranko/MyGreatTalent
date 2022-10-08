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
	$sqlbuscarbloquebloqueo1 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$iddelamigo'");
	$hayunbloqueo1 = $sqlbuscarbloqueo1 ? mysqli_num_rows($sqlbuscarbloqueo1) : 0;
	
    $sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE fromid='$player_id' AND toid='$iddelamigo'");
	$hayunbloqueo2 = $sqlbuscarbloqueo2 ? mysqli_num_rows($sqlbuscarbloqueo2) : 0;
	
	UpdateUserOnli( $iddelamigo );
	
	if ($hayunbloqueo1 < 1 && $hayunbloqueo2 < 1){

		$amistadsql01 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$iddelamigo'");
		$amistadassoc01 = mysqli_num_rows($amistadsql01);
		
		$amistadsql02 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player2='$player_id' AND player1='$iddelamigo'");
		$amistadassoc02 = mysqli_num_rows($amistadsql02);
		
		//verificando que no existan solicitudes de amistad pendientes
		
		$samistadsql01 = mysqli_query($connect, "SELECT * FROM `solicitudesdeamistad` WHERE fromid='$player_id' AND toid='$iddelamigo'");
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
			$agregandosolicitud     = mysqli_query($connect, "INSERT INTO `solicitudesdeamistad` (fromid, toid) VALUES ('$player_id', '$iddelamigo')");
		}
		
	}
}

if(isset($_GET['eliminar_id'])){
	
	$iddeleliminado = $_GET['eliminar_id'];
	
	$eliminarsql = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$iddeleliminado'");
	$eliminarcount      = mysqli_num_rows($eliminarsql);
	$eliminar      = mysqli_fetch_assoc($eliminarsql);
	if($eliminarcount>0){ $iddelasolicitud = $eliminar['id']; }
	
	$eliminarsql2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player2='$player_id' AND player1='$iddeleliminado'");
	$eliminarcount2      = mysqli_num_rows($eliminarsql2);
		$eliminar2      = mysqli_fetch_assoc($eliminarsql2);
	if($eliminarcount2>0){ $iddelasolicitud = $eliminar2['id']; }
	
	if($eliminarcount > 0 || $eliminarcount2 > 0){
		$querydelete = mysqli_query($connect, "DELETE FROM `friends` WHERE id='$iddelasolicitud'");
	}
	
}

if(isset($_GET['cancelar_id'])){
	//cancelar solicitud de amistad	
	
	$iddelcancelado = $_GET['cancelar_id'];
	
	$cancelarsql = mysqli_query($connect, "SELECT * FROM `solicitudesdeamistad` WHERE fromid='$player_id' AND toid='$iddelcancelado'");
	$cancelarcount = mysqli_num_rows($cancelarsql);
	$cancelar = mysqli_fetch_assoc($cancelarsql);
	
	
	if($cancelarcount > 0){
		$querydelete = mysqli_query($connect, "DELETE FROM `solicitudesdeamistad` WHERE id='$cancelar[id]'");
	}
	
}
	
	//bloquear usuario
if(isset($_GET['bloquear_id'])){
	//ingresando el bloqueo a la bd
	
	$bloquear_id = $_GET['bloquear_id'];

	$sqlbloqueado = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE fromid='$player_id' AND toid='$bloquear_id'");
	$countbloqueado = mysqli_num_rows($sqlbloqueado);
	
	if ($countbloqueado < 1){
		$insertarbloqueo = mysqli_query($connect, "INSERT INTO `bloqueos` (`fromid`, `toid`) VALUES ('$player_id', '$bloquear_id')");
	}
	
	// eliminando la amistad si exste
	$sqldeleteamistad1 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$bloquear_id'");
	$countamistadexistente1 = mysqli_num_rows($sqldeleteamistad1);
	$amistadborrar1 = mysqli_fetch_assoc($sqldeleteamistad1);
	
	if($countamistadexistente1 > 0){		
		$eliminarlaamistad1 = mysqli_query($connect, "DELETE FROM `friends` WHERE id='$amistadborrar1[id]'");		
	}
	
	$sqldeleteamistad2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$bloquear_id' AND player2='$player_id'");
	$countamistadexistente2 = mysqli_num_rows($sqldeleteamistad2);
	$amistadborrar2 = mysqli_fetch_assoc($sqldeleteamistad2);
	
	if($countamistadexistente2 > 0){		
		$eliminarlaamistad2 = mysqli_query($connect, "DELETE FROM `friends` WHERE id='$amistadborrar2[id]'");		
	}	
	
	// eliminando solicitud de amistad si existe
	
	$sqldesolic1 = mysqli_query($connect, "SELECT * FROM `solicitudesdeamistad` WHERE toid='$player_id' AND fromid='$bloquear_id'");
	$countsolic1      = mysqli_num_rows($sqldesolic1);
	$solic1      = mysqli_fetch_assoc($sqldesolic1);
	
	if($countsolic1 > 0){
		
		$eliminarsolic1 = mysqli_query($connect, "DELETE FROM `solicitudesdeamistad` WHERE id='$solic1[id]'");
		
	}
	
	$sqldesolic2 = mysqli_query($connect, "SELECT * FROM `solicitudesdeamistad` WHERE toid='$bloquear_id' AND fromid='$player_id'");
	$countsolic2      = mysqli_num_rows($sqldesolic2);
	$solic2      = mysqli_fetch_assoc($sqldesolic2);
	
	if($countsolic2 > 0){
		
		$eliminarsolic2 = mysqli_query($connect, "DELETE FROM `solicitudesdeamistad` WHERE id='$solic2[id]'");
		
	}
	
	// eliminando mensajes si existen
	
	$sqldesalabloq1 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$bloquear_id' AND player2='$player_id'");
	$countsalabloq1      = mysqli_num_rows($sqldesalabloq1);
	$salabloq1       = mysqli_fetch_assoc($sqldesalabloq1);

	$sqldesalabloq2 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player2='$bloquear_id' AND player1='$player_id'");
	$countsalabloq2      = mysqli_num_rows($sqldesalabloq2);
	$salabloq2       = mysqli_fetch_assoc($sqldesalabloq2);
  
	if($countsalabloq1 > 0 || $countsalabloq2 > 0){
	  
		if($countsalabloq1 > 0){		  
			$idchat_bloq = $salabloq1['id'];		  
		}else{		  
			$idchat_bloq = $salabloq2['id'];		  
		}
	
    $sqldemensajesbloq = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE id_chat='$idchat_bloq'");
	$countmensbloq      = mysqli_num_rows($sqldemensajesbloq);
	
	if($countmensbloq > 0){		
		while ($mensbloq = mysqli_fetch_assoc($sqldemensajesbloq)) {			
			$borrarmensajebloq = mysqli_query($connect, "DELETE FROM `nuevochat_mensajes` WHERE id='$mensbloq[id]'");
		}					
	}
	
	//eliminando la sala de chat si existe
	
	$borrarsalabloqu = mysqli_query($connect, "DELETE FROM `nuevochat_rooms` WHERE id='$idchat_bloq'");
	
	}
	
	$mensajebloqueado = 1;
}


	//desbloquear usuario
if(isset($_GET['desbloquear_id'])){
	
	$desbloquear_id = $_GET['desbloquear_id']; 
	
	$desbloquearuser = mysqli_query($connect, "DELETE FROM `bloqueos` WHERE fromid='$player_id' AND toid='$desbloquear_id'");
	
	$mensajebloqueado = 2;
}


if (isset($_GET['profile_id'])){	
	$id = $_GET['profile_id'];
	$selfuser = false;
}else{
	$id = $player_id;
	$selfuser = true;
}

$timeonline = time() - 60;

$query = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$id'");
$userbuscado = mysqli_fetch_assoc($query);
$iamfrom = ($rowu['registerfrom'] == 'chat' ? 'chat' : "bellasgram");
$author_id = $rowu['id'];
//SI EL USUARIO TIENE EL PERFIL OCULTO Y YO NO SOY UN USUARIO REGISTRADO DESDE EL CHAT PERO QUE NO HAGA REFRESH SI SOY YO EL USUARIO
	if($userbuscado['perfiloculto']!='no' or $userbuscado['hidetochat']=='si'){
		if ($iamfrom!='chat' and $selfuser==false){
		$friend = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$id' AND player2='$author_id'");
			$friend01 = mysqli_num_rows($friend);
			
			$friend2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$author_id' AND player2='$id'");
			$friend02 = mysqli_num_rows($friend2);
			//NO EJECUTAR LO DE ABAJO Y VOLVER AL CICLO
			if($friend02==false && $friend01==false){

				echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'profile.php" />';
				exit;
			}
		}
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

$sqlbono = mysqli_query($connect, "SELECT * FROM `settings` WHERE id=1");
$sbono   = mysqli_fetch_assoc($sqlbono);
$bono    = $sbono['bonoref'];

$sqlcodigo = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$player_id'");
$scodigo   = mysqli_fetch_assoc($sqlcodigo);
$codigo    = $scodigo['refcodigo'];

$queryref = mysqli_query($connect, "SELECT * FROM `players` WHERE referer_id='$player_id'");
$countref = mysqli_num_rows($queryref);

//requests añadir codigo de referer
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<div class="content-wrapper" height="10%">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-address-card"></i> Perfil</h1>
    			  
    			</section>
				<!--Page content-->
				<!--===================================================-->
				<section class="content">
                    
                <div class="row">                  
                
				<div style="width: 100%;">





<?php

if($userbuscado){
	
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
	
	if ($id == $player_id){
?>

<div class="box">
	<div class="box-header">	
		<?php if(!is_null($Pet['frases']) && isset($Frese)): ?>
		<div class="petFrase"><?php Echo $Pet['frases']; ?></div>
		<?php endif; ?>
	</div>
	<div class="box-body">	


<div class="avatar-box">
	<img src="<?php Echo $sitio['site'].$userbuscado['avatar']; ?>">
	<?php if($Pet): ?>
		<div class="pet-avatar">
			<div class="menu-dropdown-arrow"></div>
			<img src="<?php echo porcentaje($Pet['hp'], $UserPet['hp'])<50 ? petImg($Pet['image'])->lifelow : petImg($Pet['image'])->imgNormal;?>">
		</div>
	<?php endif; ?>
	<?php if($userbuscado['role']=="Admin"){ ?>
	<div style="position: absolute; left: 75%;bottom: -5px;">
		<img src="https://bellasgram.com/chat/assets/img/admin.png" alt="administrador">
	</div>
<?php } ?>
</div>

<center>	
	<h2>
		<strong><?php Echo $userbuscado['username']; ?></strong>
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
		
		echo '<hr></center>
		<p><b><em>'. StrToUrl($userbuscado['description']) .'</em></b>
		</p>
		<p>Género: <b>'.$userbuscado['gender'].'</b></p>
		<p>Créditos: <b>'.$userbuscado['creditos'].'</b></p>
		<p>Créditos Especiales: <b>'.$userbuscado['eCreditos'].'</b></p>';
		
		if($rowu['role'] == 'Admin'){
			Echo '<p>IP: <b>'.$userbuscado['ipaddres'].'</b></p>';
		}
		
		if($userbuscado['gender']=='mujer'){
			echo '<p>Perfil Oculto: <b>'.$userbuscado['perfiloculto'].'</b></p>';
		}
		
	}else{
		
		EarchLifePets($id);
		// si hay un bloqueo sacarlo del perfil
		
		$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$userbuscado[id]'");
		$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);
	
		if ($hayunbloqueo > 0){ 
		
			echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
			exit;

		}else{
		
?>
<div class="box">				
	<div class="box-header">	
		<?php if(!is_null($Pet['frases']) && isset($Frese)): ?>
		<div class="petFrase"><?php Echo $Pet['frases']; ?></div>
		<?php endif; ?>
	</div>
	<div class="box-body">	
	
<div class="avatar-box">
	<img src="<?php Echo $sitio['site'].$userbuscado['avatar']; ?>">
	<?php if($Pet): ?>
		<div class="pet-avatar">
			<div class="menu-dropdown-arrow"></div>
			<img src="<?php echo porcentaje($Pet['hp'], $UserPet['hp'])<50 ? petImg($Pet['image'])->lifelow : petImg($Pet['image'])->imgNormal;?>">
		</div>
	<?php endif; ?>
	<?php if($userbuscado['role']=="Admin"){ ?>
	<div style="position: absolute; left: 75%;bottom: -5px;">
		<img src="https://bellasgram.com/chat/assets/img/admin.png" alt="administrador">
	</div>
<?php } ?>
</div>

<center>	
	<h2>
		<strong><?php Echo $userbuscado['username']; ?></strong>
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
	
		echo '<hr></center>
		<p><b><em>'. StrToUrl($userbuscado['description']) .'</em></b>
		</p>
		<p>Género: <b>'. $userbuscado['gender'] .'</b></p>
		<p>Hablo en: <b>'. @$userbuscado['habla'] .'</b></p>
		<p>Escribeme en: <b>'. @$userbuscado['escribeme'] .'</b></p>
		<center>';
		
		if($rowu['role'] == 'Admin'){
			Echo '<p>IP: <b>'.$userbuscado['ipaddres'].'</b></p>';
		}
		if(isset($mensajebloqueado)){
			if ($mensajebloqueado == 1){
				
				echo '<p style="color:green">Bloqueaste a este usuario</p>';
				
			}
			if ($mensajebloqueado == 2){
				echo '<p style="color:green">Desbloqueaste a este usuario</p>';
			}
		}
		//MENU DESPLEGABLE(SOLO PARA ADMIN)
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
		if ($rowu['role'] == 'Admin'){
			if ($userbuscado["baneado"] == 0){
				echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&banear='. $userbuscado["baneado"] .'" class="btn btn-danger dropdown-item width" ><i class="fa fa-times"></i> Banear Usuario</a><br class="br">';
			}else{
				echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&banear='. $userbuscado["baneado"] .'" class="btn btn-danger dropdown-item width" ><i class="fa fa-handshake"></i> Perdonar Usuario</a><br class="br">';
			}
			//BOTON NO-PUBLIC
			$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
			echo '<a href="'.$sitio['site'].'profile.php?page='.$page.'&profile_id=' . $userbuscado['id'] . '&action_nopublic='. $userbuscado["id"] .'" class="btn btn-primary dropdown-item width" ><i class="fa fa-action"></i> Cambiar a No-Public</a><br class="br">';
			//BOTON EDITAR PERFIL
			echo '<a href="'.$sitio['site'].'edituser.php?edit-id=' . $userbuscado['id'].'" class="btn btn-primary dropdown-item width" ><i class="fa fa-action"></i>Editar Perfil</a><br class="br">';
			if ($userbuscado["permission_upload"] == 0){
				echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&permission_upload=1" class="btn btn-success dropdown-item width" ><i class="fa fa-check"></i> Permitir subir fotos</a><br class="br">';
			}else{
				echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&permission_upload=0" class="btn btn-danger dropdown-item width" ><i class="fa fa-lock"></i> Bloquear subir fotos</a><br class="br">';
			}
		}

		if (!isFollow($userbuscado["id"])){
			if($rowu['eCreditos']>=2000){
				echo '<a href="#" data-username="'.$userbuscado['username'].'" data-href="'.$sitio['site'].'profile.php?profile_id='.$userbuscado['id'].'&Subscribe" id="suscribe" class="btn btn-success dropdown-item width" >
					Suscribirme por <br/>2000 créditos especiales 7 días
				</a><br class="br"><br>';
			}else{
				echo '<a class="btn btn-success dropdown-item width" >Suscribirme por <br/> 2000 créditos especiales 7 días</a><br class="br"><br>';
			}
		}else{
			echo '<a class="btn btn-primary dropdown-item" ><i class="fa fa-star"></i> Suscrito por 7 días</a><br class="br"><br>';
		}

		if($userbuscado['gender']=='mujer'){
			echo '<a class="btn btn-primary dropdown-item width" onclick="openDonarCreditos()" ><i class="fa fa-coins"></i> Donar Creditos</a><br class="br"><br>';
		}

		$bloqueosql = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE fromid='$player_id' AND toid='$userbuscado[id]'");
		$countbloqueo      = mysqli_num_rows($bloqueosql);
		
		$amistadsql1 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$userbuscado[id]'");
		$amistadassoc1      = mysqli_num_rows($amistadsql1);
		
		$amistadsql2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player2='$player_id' AND player1='$userbuscado[id]'");
		$amistadassoc2      = mysqli_num_rows($amistadsql2);
		
		$cancelarsql = mysqli_query($connect, "SELECT * FROM `solicitudesdeamistad` WHERE fromid='$player_id' AND toid='$userbuscado[id]'");
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
			
			if ($countbloqueo < 1){
			
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
	
		echo '<form method="POST" action="'. $sitio['site'] .'report.php">
		<input type="hidden" name="userreportado" value="'.$userbuscado['id'].'">
		<button name="iniciarreporte" type="submit" class="btn btn-warning dropdown-item width" >
			<i class="fa fa-exclamation-circle"></i> Reportar Perfil
		</button>
		</form>
		<br class="br"><br>';

		if($userbuscado['gender'] == 'hombre'){
			if ($countbloqueo < 1){
				echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&bloquear_id=' . $userbuscado['id'] . '" name="bloquear" type="submit" class="btn btn-danger dropdown-item width" ><i class="fa fa-times-circle"></i> Bloquear Perfil</a><br class="br"><br>';
			}else{
				echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $userbuscado['id'] . '&desbloquear_id=' . $userbuscado['id'] . '" name="bloquear" type="submit" class="btn btn-danger dropdown-item width" ><i class="fa fa-unlock"></i> Desbloquear Perfil</a><br class="br"><br>';
			}
		}
	
		if ($countbloqueo < 1){	
			echo '
			<a href="'. $sitio['site'].$link .'" name="enviarunmensaje" type="button" class="btn btn-success dropdown-item width" ><i class="fas fa-envelope"></i> Iniciar chat con ' . $userbuscado['username'] . '</a><br><br>';
		}else{	
			echo '<br>
			<button name="botondeshabilitado" id="botondeshabilitado" type="submit" class="btn btn-large btn-success dropdown-item width"  disabled>Iniciar chat con ' . $userbuscado['username'] . '</button>
			<hr></center><br><br>';
		}
		//FIN MENU DESPLEGABLE
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
    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">

        <!--Page content-->
        <!--===================================================-->
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
                                        		if($packs>0){ ?>
<a href="packs.php?id_profile=<?php echo $id; ?>" style="color: #444"> <p style="color:#CC99AD;"><i class="fas fa-images"></i> <?php echo $userbuscado['username'] . "  Posee packs en venta. " ?> <b style="color:#337ab7; "> <br/>Ir a la seccion packs</p></b></a>                                        			
                                        		<?php } ?>
                                        </div>
                                    </div>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
if ($countcp > 0) {
    while ($rowcp = mysqli_fetch_assoc($querycp)) {
        $author_id = $rowcp['player_id'];
        $querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
        $rowcpd    = mysqli_fetch_assoc($querycpd);
		
		$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$author_id'");
		$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);
	
		$sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$author_id' AND fromid='$player_id'");
		$hayunbloqueo2 = mysqli_num_rows($sqlbuscarbloqueo2);
	
	if ($hayunbloqueo < 1 && $hayunbloqueo2 < 1){
		$sub = '';
		if(!isFollow($rowcpd['id']) && $rowcpd['id'] != $player_id){					
			$sub = $rowcp['type'] == 'suscripciones' ? ' noSub': '';
		}
?>
	<?php include "./Row-img-profile.php";

//boton me gusta

$megustasql = mysqli_query($connect, "SELECT * FROM `player_megusta` WHERE player_id='$player_id' AND galeria_id='$rowcp[id]'");
$countmegusta = mysqli_num_rows($megustasql);
	
$totalmegustasql = mysqli_query($connect, "SELECT * FROM `player_megusta` WHERE galeria_id='$rowcp[id]'");
$totalmegustas = mysqli_num_rows($totalmegustasql);

if ($countmegusta < 1){
	$isLike = "";
}else{
	$isLike = "isLike ";
}
//echo '<button type="submit" name="megusta" class="'.$isLike.'btn btn-success float-right" onclick="LikePost(this, '.$rowcp['id'].');"> <i class="fa fa-thumbs-up"></i></button>+'.$totalmegustas.' likes';

?>	

<?php
	}
    }
	
	$profile_id = isset($_GET['profile_id']) ? '&profile_id='.$_GET['profile_id']:'';
	
?>
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
    echo '<div class="alert alert-success"><strong><i class="fa fa-info-circle"></i> Actualmente no hay fotos</strong></div>';
}

?>

                                            

                                        </div>
                                    </div>
                                </tbody>
                            </table>
                            <br>
                    </div>
                </div>

            </div>

    </div>
    <!--===================================================-->
    <!--End page content-->


	</div>
</div>

<div id="DonarCreditos" class="modal fade in" style="display: none;">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Donar Creditos especiales</h4>
			</div>
			<div class="modal-body">
				<center>
					<input type="number" name="Creditos" id="Creditos" value="0" style="
						width: 100%;
						padding: 10px 15px;
						border-radius: 7px;
						border: 1px solid #969696;
						outline: none;
					">
						
					<br><br>
					
						<center>
						   
							<button type="submit" name="comprar" class="btn btn-success" onclick="submitDonarCreditos()">Confirmar</button>
							
						</center>
						
					<br>
					<button type="button" class="btn btn-primary btn-md btn-block" onclick="cancelDonarCreditos()">Cancelar</button>
				</center>
			</div>
		</div>
	</div>
</div>
<?php 
//CONVERTIR IMAGENES EN NO PUBLICAS
if (isset($_GET['action_nopublic']) and !empty($_GET['action_nopublic'] ) ) {
	if ($rowu['role']='Admin') {
		$id = $_GET['action_nopublic'];
		$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` WHERE player_id='{$id}' ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
		$countcp = mysqli_num_rows($querycp);
		//GENERA N NUMERO ALEATORIOS NO REPETIDOS
		$rand = generaterandom(0,$countcp-1);
		if ($countcp > 0) {
			$b=0;
			$a=0;
			//ALMACENA FILAS Y COLUMNAS DE LA CONSULTA EN UN ARRAY
    		while ($rowcp = mysqli_fetch_row($querycp)) {
				for ($i=0; $i < 7; $i++) { 
					$row[$a][$i]=$rowcp[$i];
				}
				$a++;
			}
			/*GENERA LA CONSULTA
			CAMBIARA LA MITAD DE LA PAGE COMO PRETEMINADO(50%)
			*/
			$mitad=ceil($countcp/2);
			$count=0;
			for ($i=0; $i <$mitad; $i++) {
				$idd=$row[$rand[$i]][0];
				//SOLO MODIFICARA IMAGENES SIN LINKS
				if($row[$rand[$i]][5]=="" or $row[$rand[$i]][5]==" " or $row[$rand[$i]][5]==null){
					//SOLO IMAGENES, VIDEOS NO
					if (getSourceType($row[$rand[$i]][2]) != 'film') {
						$querycp = mysqli_query($connect, "UPDATE `fotosenventa` SET `type`=\"suscripciones\" WHERE id=$idd");
						if($querycp){
							$count++;
						}
					}
				}
			}
			//SI NO SE MODIFICARON FILAS
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
</style>
<script>
	var submitDonarCreditos = () => {		
		var formData = new FormData();
		formData.append("id", <?php Echo $id; ?>);
		formData.append("Creditos", $("#Creditos").val());
		
		$.ajax({
			url: "ajax.php?DonarCreditos", 
			type: "POST",
			data: formData, 
			processData: false,
			contentType: false
		}).done(function(response){
			var data = $.parseJSON(response);
			console.log(response);
			if(data.status){
				swal.fire(data.message, "", "success");
			}else{
				swal.fire("Error!", data.message, "error");
			}
			cancelDonarCreditos();
		})
	}	
	var openDonarCreditos = () => {
		$("#DonarCreditos").show();
	}
	var cancelDonarCreditos = () => {
		$("#DonarCreditos").hide();
	}

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
