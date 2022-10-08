<?php
require("core.php");
head();

if(!isset($sitio['costoporchat'])){
	$sitio['costoporchat'] = 1000;
}

$Proc = false;
if($rowu['creditos'] >= $sitio['costoporchat']){
	$Proc = 'creditos';
}elseif($rowu['eCreditos'] >= $sitio['costoporchat']){
	$Proc = 'eCreditos';
}

if (isset($_POST['pagar'])){
	
	// se verifica que el usuario dispone fondos para pagar el chat
	

	if ($Proc && $rowu[ $Proc ] >= $sitio['costoporchat']){
		
		// REGISTRA COMPRA SI SON CRÉDITOS ESPECIALES
		if($Proc == 'eCreditos')
		{
			// ACTUALIZA LOS CREDITOS
			$player_update = updateCredits($rowu['id'],'-',$sitio['costoporchat'],4);
		}
		// SINO SOLO ACTUALIZA LOS CRÉDITOS
		else
		{
			$player_update = $connect->query("UPDATE `players` SET {$Proc}={$Proc}-'{$sitio[costoporchat]}' WHERE id='{$player_id}'");
		}
		


		$elamigo = $_POST['elamigo'];
		
		$creandolasala     = mysqli_query($connect, "INSERT INTO `nuevochat_rooms` (player1, player2) VALUES ('$player_id', '$elamigo')");

		//redireccionando a la sala

		$newsalaslq = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$player_id' AND player2='$elamigo'");
		$countnewsala = mysqli_num_rows($newsalaslq);
		$newsala = mysqli_fetch_assoc($newsalaslq);
		
		if ($countnewsala > 0){		
			echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'chat.php?chat_id='. $newsala['id'] .'#chat" />';
			exit;	
		}
	}else{	
		//si no tiene fondos se redirecciona	
		echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
			exit;
		
	}		
}else{



//vemos si existe una sala con este user, si existe se redirecciona a la sala de chat


if (isset($_GET['id'])){
	
	$elamigo = $_GET['id'];

	$salasql1 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$player_id' AND player2='$elamigo'");
	$countsala1      = mysqli_num_rows($salasql1);
	$sala1 = mysqli_fetch_assoc($salasql1);
	
	$salasql2 = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$elamigo' AND player2='$player_id'");
	$countsala2      = mysqli_num_rows($salasql2);
	$sala2 = mysqli_fetch_assoc($salasql2);
	
	if ($countsala1 > 0){
		
		echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'chat.php?chat_id='. $sala1['id'] .'#chat" />';
        exit;

	
	}elseif($countsala2 > 0){
		
		echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'chat.php?chat_id='. $sala2['id'] .'#chat" />';
	}else{	

	//verificamos que no existan bloqueos entre estos usuarios

    $bloqueosql1 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE fromid='$player_id' AND toid='$elamigo'");
	$countbloqueo1      = mysqli_num_rows($bloqueosql1);
	
	$bloqueosql2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$elamigo' AND fromid='$player_id'");
	$countbloqueo2      = mysqli_num_rows($bloqueosql2);

	if($countbloqueo1 > 0 || $countbloqueo2 > 0){
		
		echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
	
	}else{
	
	//verificamos la cantidad de chats que tiene el usuario, si tiene menos de 5 se crea la sala y se redirecciona, de lo contrario se le pide pagar
	$sqladmin = mysqli_query($connect, "SELECT * FROM `players` WHERE role='Admin'");
	$countadmin = mysqli_num_rows($sqladmin);
	if($countadmin>0){
		$admins = array();
		//ALMACENA LOS ID DE USUARIOS ADMINS
		while ($rowadmin = mysqli_fetch_assoc($sqladmin)) {
			array_push($admins, $rowadmin['id']);
		}
	}
	$chatssql = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$player_id' OR player2='$player_id'");
	$countchats      = mysqli_num_rows($chatssql);
	//SI EL USUARIO TIENE MENOS DE 10 CHATS O SI INICIAR UN CHAT CON UN ADMIN
if ($countchats < 10 or in_array($elamigo, $admins)){
	
	
	$creandolasala     = mysqli_query($connect, "INSERT INTO `nuevochat_rooms` (player1, player2)
VALUES ('$player_id', '$elamigo')");

//redireccionando a la sala


   $newsalaslq = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1='$player_id' AND player2='$elamigo'");
	$countnewsala      = mysqli_num_rows($newsalaslq);
	$newsala = mysqli_fetch_assoc($newsalaslq);
	
	if ($countnewsala > 0){
		
		echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'chat.php?chat_id='. $newsala['id'] .'#chat" />';
        exit;

	
	}
	
}else{

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">



</script>
<div class="content-wrapper">

    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">

        <section class="content-header">
            <h1><i class="fas fa-envelope"></i>Iniciar un Chat</h1>

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

                                    <center>
                                        <p>
                                            Iniciar un nuevo chat tiene un costo de <b><?php echo $sitio['costoporchat']; ?> </b>Créditos Especiales o Normales <br /><br />
                                            En el menu toque en "Créditos Normales Gratis" para ganar créditos de forma gratuita.<br /><br />
                                            Si ya tiene los créditos solo toque en "pagar". (Cuando pagas ganas el derecho de enviarle mensaje, no se asegura que responda.)<br /><br />
                                            Tienes <b><?php echo $rowu['eCreditos']; ?> </b>Créditos Especiales y <b><?php echo $rowu['creditos']; ?> </b> Créditos Normales
										</p>
                                    </center>
                                </tbody>
                            </table>
                        </div>

                        <center>
                            <a name="chat"></a>
                            <form method="POST">
                                <div class="form-group">

                                    <input type="hidden" name="elamigo" value="<?php echo $elamigo; ?>">
<?php
	if($Proc && $rowu[ $Proc ] >= $sitio['costoporchat']){
		echo '<input value="Pagar 50 créditos" type="submit" name="pagar" class="btn btn-primary"/><br><br>';
	}else{	
		echo '<button name="botondeshabilitado" id="botondeshabilitado" class="btn btn-primary" disabled>No tienes suficientes créditos</button><br><br>';
	}
?>

                                </div>
                            </form>
                        </center>
                    </div>
					
                    <br>
                </div>
            </div>
		</section>
    </div>

</div>

<script>
    $(document).ready(function() {

        $('#dt-basic').dataTable({
            "responsive": true,
            "language": {
                "paginate": {
                    "previous": '<i class="fas fa-angle-left"></i>',
                    "next": '<i class="fas fa-angle-right"></i>'
                }
            }
        });
    });
</script>
<?php

	}
	}
	}
}
}

footer();
?>
