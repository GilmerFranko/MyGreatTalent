<?php
include "config.php";

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['SCRIPT_FILENAME'] != 'ajax.php'){
	$ActualTime = time();

	$querycp = mysqli_query($connect, "SELECT * FROM `fotosprogramadas` WHERE time<'{$ActualTime}'");
	$countcp = mysqli_num_rows($querycp);
	$SD = [];
	if ($countcp > 0){
		while($foto = mysqli_fetch_assoc($querycp)){
      $Images = json_decode($foto['imagen']);
      $FotoID = $foto['id'];
      $dia = time();
      $name = (explode('/', $Images[0])[2]);
      $thumb = 'thumb/'. $name;

      $Image = file_get_contents($Images[0]);
			//createThumbnail($foto['imagen'], $thumb, 200);
      createThumbnail($Images[0],"./". $thumb, 300);
      $thumb = '["' . $thumb . '"]';
			//file_put_contents($thumb, $Image);
      $SD[] = [
        'dia' => $dia,
        'FotoID' => $FotoID,
        'thumb' => $thumb,
        'Image' => $Images[0]
      ];
      mysqli_query($connect, "INSERT INTO `fotosenventa` (player_id, imagen, thumb, descripcion, type, time,category) VALUES
       ('". $foto['player_id'] ."',
       '". $foto['imagen'] ."',
       '". $thumb ."',
       '". $foto['descripcion'] ."',
       '". $foto['type'] ."',
       '". $dia ."','$foto[category]')");
      mysqli_query($connect, "DELETE FROM `fotosprogramadas` WHERE id='{$FotoID}'");
      // BORRA LA <<FOTO DE REGALO>>
      $connect->query('TRUNCATE `photo_gift_credits`');
    }
  }
}
// COMPRUEBA QUE EXISTA UNA SESSION INICIADA
if (isset($_COOKIE['eluser']))
{
    // CONSULTA LOS DATOS DEL USUARIO
  $uname = $_COOKIE['eluser'];
  $suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname'");
    // GUARDALOS EN UNA VARIABLE GLOBAL
  $rowu = mysqli_fetch_assoc($suser);
  $player_id = $rowu['id'];
    // SI NO SE ENCUENTRA EL USUARIO EN LA BBDD
  if ($suser AND $suser->num_rows <= 0)
  {
    echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'index.php" />';
    exit;
  }
}
// SI NO EXISTE NINGUNA SESSION INICIADA
// COMPRUEBA QUE EL SITIO QUE ESTE VISITANDO EL USUARIO ESTE PERMITIRDO PARA NO LOGUEADOS
elseif (in_array(basename($_SERVER['SCRIPT_NAME']), $sitesfree))
{
  $rowu = $guestUser;
  $player_id = $rowu['id'];
}
// SI NO DEVUELVE AL LOGIN
else
{
  echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'index.php" />';
  exit;
}

if (!empty(isLogged()))
{


  if(isset($_GET['ChangeTheme']))
  {
  	$theme = 0;
  	if($rowu["theme"] == 0){
  		$theme = 1;
  	}
  	mysqli_query($connect, "UPDATE `players` SET theme='{$theme}' WHERE id='{$rowu['id']}'");
  	exit();
  }

  if($rowu["baneado"]){
  	header("location: banned.php");
  }

  EarchLifePets($player_id);
  EarchBonusPets($player_id);

  //AddListFollow(2);
}

function head()
{
	global $rowu,$player_id;
  include 'config.php';

?>
<!DOCTYPE html>
<html>
<head>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="../assets/img/favicon.png">
  <title>My Great Talent</title>
  <!--STYLESHEET-->
  <!--=================================================-->

  <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
  <!--Bootstrap Stylesheet-->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/red-skin.min.css">
  <!--Font Awesome-->
  <link href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" rel="stylesheet">
  <!--Stylesheet-->
  <link href="assets/css/admin.min.css" rel="stylesheet">
  <!-- CustomStyle -->
  <link href="assets/css/custom.css" rel="stylesheet">
  <!--DataTables-->
  <link href="assets/css/datatables.1.10.css" rel="stylesheet">
  <!--DatePicker-->
  <link href="assets/plugins/datepicker/datepicker.min.css" rel="stylesheet">
  <!--Swiper-->
  <link rel="stylesheet" href="assets/css/swiper.min.css"/>
  <!--SCRIPT-->
  <!--=================================================-->
  <!--jQuery-->
  <script src="assets/js/jquery-3.3.1.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="assets/js/sweetalert.min.js"></script>
  <script src="assets/js/function.js"></script>

  <script type="text/javascript" src="assets/js/typeahead.js"></script>

	<script>
    $('video').bind('contextmenu',function() { return false; });
    function getThumb(videoId){
      var video = document.getElementById( videoId );
      var w = video.videoWidth; //video.videoWidth * scaleFactor;
      var h = video.videoHeight; //video.videoHeight * scaleFactor;
      var canvas = document.createElement('canvas');
      canvas.width = w;
      canvas.height = h;
      var ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, w, h);
      var data = canvas.toDataURL("image/jpg");
      $("#VC-"+ videoId).find('.videoPreview').css({'background-image':`url(${data})`})
      //video.remove()
    }
  </script>
  <!--BLOQUEADOR COPIAR PEGAR, AGREGAR LUGARES DONDE QUIERA QUE SI SE PUEDA COPIAR Y PEGAR-->
  <?php
  if (basename($_SERVER['SCRIPT_NAME']) != 'messages.php' and basename($_SERVER['SCRIPT_NAME']) != 'search.php' and basename($_SERVER['SCRIPT_NAME']) != 'collections.php' and basename($_SERVER['SCRIPT_NAME']) != 'nombres.php' and basename($_SERVER['SCRIPT_NAME']) != 'mass.php' and basename($_SERVER['SCRIPT_NAME']) != 'notas.php' and basename($_SERVER['SCRIPT_NAME']) != 'compraspackadmin.php' and basename($_SERVER['SCRIPT_NAME']) != 'suscriptores.php' and basename($_SERVER['SCRIPT_NAME']) != 'settings.php' and basename($_SERVER['SCRIPT_NAME']) != 'mensajesauto.php' and basename($_SERVER['SCRIPT_NAME']) != 'regalo1.php' and basename($_SERVER['SCRIPT_NAME']) != 'foto2.php' and basename($_SERVER['SCRIPT_NAME']) != 'pack2.php' and basename($_SERVER['SCRIPT_NAME']) != 'edituser.php' and basename($_SERVER['SCRIPT_NAME']) != 'whopaid.php' and basename($_SERVER['SCRIPT_NAME']) != 'fotosprogramadas.php' and basename($_SERVER['SCRIPT_NAME']) != 'comprar.php' and basename($_SERVER['SCRIPT_NAME']) != 'referrers.php' AND $rowu['role']!='Admin') {
    ?>
    <script type="text/javascript">
      $(document).ready(function () {
              //Disable full page
              $("body").on("contextmenu",function(e){
                return false;
              });


              //Disable part of page
              $("#id").on("contextmenu",function(e){
                return false;
              });
              onkeydown = e => {
                let tecla = e.which || e.keyCode;

                  // Evaluar si se ha presionado la tecla Ctrl:
                  if ( e.ctrlKey ) {
                  // Evitar el comportamiento por defecto del nevagador:
                  // Mostrar el resultado de la combinación de las teclas:
                  //BLOQUEA TECLA CTRL + S Y CTRL + U
                  if ( tecla === 85 || tecla === 83 ){
                    e.preventDefault();
                    e.stopPropagation();
                  }
                }
                  //BLOQUEA TECLA F12
                  if (e.which === 123) {
                    return false;
                  }
                }
              });
      </script>
      <!--BLOQUEAR DRAG AND DROP usar asi 'cut copy paste'linea 227-->
      <BODY ondragstart="return false;" ondrop="return false;">
        <!--TAMBIEN HAY QUE MANTENER SIEMPRE LA PORTPAPELES LIMIPIA-->
        <style>
        @media print {
          body { visibility: hidden; }
        }
      </style>
      <script type="text/javascript">
        $(document).ready(function ()
        {
          //Disable full page
          <?php if(basename($_SERVER['SCRIPT_NAME']) != 'profile.php'): ?>
          $('body').bind('cut copy', function (e) {
            e.preventDefault();
          });
          <?php endif; ?>
          //Disable part of page
          $('#id').bind('cut copy paste', function (e) {
            e.preventDefault();
          });

        });
      //BLOQUEAR TECLA IMPR PANT
      </script>
    <?php
  }
  ?>
  <script>
    function openImage(image,nosub = " "){
    	console.log('img', image)
        //SI LA IMAGEN REQUIERE FILTRO; AGREGARLO
    	$('.img-mdl-cnt').remove()
    	const close = $(`<div>`).addClass('btn-close').html($(`<i>`).addClass('fa fa-times'))
    	const expand = $(`<div>`).addClass('btn-expand').html($(`<i>`).addClass('fa fa-expand'))
    	const bg = $(`<div>`).addClass('bg')
    	const img = $(`<img>`).addClass('image-content max-size '+ nosub).attr({'src':image})
    	const c = $(`<div>`).addClass('img-mdl-cnt')
    		.append(bg)
    		.append(close)
    		.append(expand)
    		.append(img)
    	expand.click(function(){
    		console.log("expand ptmadree")
    		img.toggleClass('max-size')
    	})
    	close.click(function(){
    		c.remove()
    	})
    	bg.click(function(){
    		c.remove()
    	})

    	$(`body`).append(c)
    }

    $(document).ready(function(){
    	$('.item-zoom').click(function(){
    		const img = $(this).data('src')
    		openImage(img)
    	});
        //CON FILTRO
        $('.item-zoom-2').click(function(){
            const img = $(this).data('src')
            openImage(img,"noSub");
        });
    })
  </script>
</head>
<body class="hold-transition skin-red sidebar-mini<?php
	if($rowu['theme'] == 1){
		Echo ' --dark';
	}
?>">
<div class="wrapper">
  <header class="main-header">
    <!-- BARRA NAVBAR -->
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <i class="fas fa-bars"></i>
        <span class="sr-only">Toggle navigation</span>
      </a>
      <ul class="sidebar-menu header-menu" style="width: 70%">
        <!-- SI ESTOY LOGUEADO -->
        <?php if(isLogged()): ?>
          <li <?php currentPage('messages.php'); ?> id="msgContent" align="center">
            <a href="messages.php">
              <i class="fas fa-envelope"></i>
              <span>
                <?php


                $noleidos = 0;

                // OPTIENE LOS MENSAJES NO LEIDOS DE "$player_id"
                $noleidos = $connect->query("SELECT * FROM `nuevochat_mensajes` AS nm INNER JOIN nuevochat_rooms AS nr ON (nr.`player1` = '$player_id' || nr.`player2` = '$player_id') AND nr.`id` = nm.`id_chat` WHERE leido = IF(author = '$player_id','no devolver mensajes','no')")->num_rows;

                if ($noleidos > 0){
                  echo '<span class="count-notify">'.$noleidos.'</span>';
                }



                $sqlfotonovista = mysqli_query($connect, "SELECT * FROM `notificaciones_fotosnuevas` WHERE player_notificado='{$player_id}' AND visto='no'");
                $Fotos = mysqli_num_rows($sqlfotonovista);
                ?>
              </span>
            </a>
          </li>

          <li <?php currentPage('galerias.php'); ?> id="fotosContent" align="center">
            <a href="galerias.php">
              <i class="fas fa-camera-retro"></i>
              <span>
                <?php
                if ($Fotos > 0){
                  echo '<span class="count-notify">'.$Fotos.'</span>';
                }
                ?>
              </span>
            </a>
          </li>

          <li <?php currentPage('search.php'); ?> align="center">
            <a href="search.php">
              <i class="fas fa-search"></i>
            </a>
          </li>

          <li <?php currentPage('notifications.php'); ?> id="notifyContent" align="center">
            <a href="notifications.php">
              <div>
                <i class="fas fa-bell"></i>
                <span>
                  <?php
//ver si hay notificaciones no vistas

                  $sqlnovistos = mysqli_query($connect, "SELECT * FROM `players_notifications` WHERE toid='$player_id' AND read_time='0'");
                  $novistos   = mysqli_num_rows($sqlnovistos);

                  if ($novistos > 0){
                    echo '<span class="count-notify">'.$novistos.'</span>';
                  }
                  ?>
                </span>
              </div>
            </a>
          </li>


          <li align="center">
            <div>
              <a class="dark-theme" style="position: relative;
              left: 20px;">
              <i class="fa fa-lightbulb"></i>
            </a>
          </div>
        </li>
      <?php else: ?>
        <li align="center">
          <a href="index.php">
            <i class="fa fa-sign-in-alt"></i>
          </a>
        </li>
        <li align="center">
          <a href="register.php">
            <i class="fa fa-user-plus"></i>
          </a>
        </li>
      <?php endif ?>
      </ul>
      <!-- /BARRA NAVBAR -->

      <!-- MENU PERFIL -->
      <?php if (isLogged()): ?>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding: 0px!important;">
                <img src="<?php echo $sitio['site'].$rowu['avatar'];?>" class="user-image" style="width: 40px;height: 40px;margin: 5px;">
                <span class="hidden-xs" style="padding: 15px;display: inline-block;"><?php echo $_COOKIE['eluser']; ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <img src="<?php echo $sitio['site'].$rowu['avatar'];?>" class="img-circle" alt="Admin Image">
                  <p>
                    <i class="fas fa-user"></i> <?php echo $_COOKIE['eluser'];?>
                    <small><i class="fas fa-envelope"></i> <?php echo $rowu['email'];?></small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="settings.php" class="btn btn-default btn-flat"><i class="fas fa-edit fa-fw fa-lg"></i> Editar Perfil</a>
                  </div>
                  <?php
                  if ($rowu['gender'] == 'mujer'){
                    ?>
                    <div class="pull-right">
                      <a href="<?php echo $sitio['site']; ?>logout.php" class="btn btn-default btn-flat"><i class="fas fa-sign-out-alt fa-fw"></i> Logout</a>
                    </div>
                    <?php
                  }
                  ?>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      <?php endif ?>
    </nav>
  </header>
  <!-- BARRA LATERAL -->
  <aside class="main-sidebar">
    <?php if (isLogged()):
    // SI EL ADMIN ESTA EN OTRO USUARIo
    if(isset($_COOKIE['returnUser']) AND !empty($_COOKIE['returnUser'])):?>
    <div class="user-panel" style="padding: 20px 10px;height: auto;">
      <span class="text-white">Mirando como <strong><?php echo getFirstWord($rowu['username']) ?></strong>&nbsp;&nbsp; </span><a href="javascript:logoutProfileGuest()" class="btn btn-default btn-flat">Salir</a>
    </div>
    <?php endif; ?>
    <section class="sidebar">
      <div class="user-panel" style="padding: 20px 10px;height: auto;">
        <div class="pull-left image" style="padding:10px 0;">
          <img src="<?php echo $sitio['site'].$rowu['avatar']; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
			<p><?php Echo $_COOKIE['eluser']; ?></p>
			<a id="coins-user-panel" href="#">
				<i class="fas fa-coins"></i>
				<span><?php Echo $rowu['eCreditos'];?> Créditos</span>
			</a>
		<!--	<br/>
			<a href="#">
				<i class="fas fa-coins"></i>
				<?php Echo $rowu['creditos'];?> Créditos Normales
			</a>
			<br/>
			<a><i class="fas fa-thumbs-up"></i> Megustas Recibidos <?php Echo $rowu['LikesRecibidos']; ?></a>
			<br/>
			<a><i class="fas fa-thumbs-up"></i> Megustas Dados <?php Echo $rowu['Likesdados'];; ?></a>-->
        </div>
      </div>
    <?php endif ?>
      <ul class="sidebar-menu" data-widget="tree" id="actualizarmensajes">
        <li class="header">MENU</li>
        <!-- MOSTRAR SI ESTOY LOGUEADO -->
        <?php if (isLogged()): ?>

        <!--TRANSLATE TRADUCTOR-->
          <li style="margin-left: 17px;">
            <div id="google_translate_element"></div>
            <script type="text/javascript">
              function googleTranslateElementInit() {
                new google.translate.TranslateElement({pageLanguage: 'es',includedLanguages: 'en,es,fr,it,pt,ar,de,ru,tr', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
              }
            </script>
            <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
          </li>
          <script type="text/javascript" src="assets/js/translate.js"></script>
        <!--/TRANSLATE-->
        <li <?php
        if (basename($_SERVER['SCRIPT_NAME']) == 'profile.php') {
          echo 'class="active"';
        }
      ?>>
      <a href="profile.php">
        <i class="fas fa-address-card"></i>&nbsp; <span>Mi Perfil</span>
      </a>
    </li>
    <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'messages.php') {
      echo 'class="active"';
    }
  ?> id="msgContent">
  <a href="messages.php">
    <i class="fas fa-envelope"></i>&nbsp; <span>Mensajes
      <?php
      if ($noleidos > 0){
        echo '<span class="count-notify">'.$noleidos.'</span>';
      }
      ?>
			  </span>
           </a>
        </li>


    <!-- OCULTO  <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'packs.php' ? 'class="active"':'';?> id="NotifyPack">
			<a href="packs.php">
				<i class="fas fa-images"></i>&nbsp; <span>Packs en Venta</span>
				<?php Echo get_Notification_pack();	?>
			</a>
        </li> -->

		<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'comprar.php') {
					echo 'class="active"';
				}
			?>>
           <a href="comprar.php">
              <i class="fab fa-paypal"></i>&nbsp; <span>Comprar Créditos</span>
           </a>
        </li>
 <!-- OCULTO     <li <?php echo basename($_SERVER['SCRIPT_NAME']) == 'preguntas.php' ? 'class="active"':'';?> id="NotifyQuestion">
           <a href="preguntas.php">
            <i class="fas fa-question"></i>&nbsp; <span>Preguntas&nbsp;</span>
           </a>
        </li>

       <li <?php
			if (basename($_SERVER['SCRIPT_NAME']) == 'faq.php') {
				echo 'class="active"';
			}
		?>>
           <a href="faq.php">
              <i class="fa fa-question"></i>&nbsp; <span>FAQ 4/7/2020</span>
           </a>
        </li>
       <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'button.php') {
					echo 'class="active"';
				}
			?>>
           <a href="dinero.php">
              <i class="fas fa-play-circle"></i>&nbsp; <span>Créditos Normales Gratis</span>
           </a>
        </li>

 <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'series.php') {
					echo 'class="active"';
				}
			?>>
           <a href="series.php">
              <i class="fas fa-tv"></i></i>&nbsp; <span>Series en Español</span>
           </a>
        </li>


	<li>
           <a href="#" data-toggle="modal" data-target="#changeCode">
              <i class="fas fa-award"></i>&nbsp; <span>Tarjeta de regalo</span>
           </a>
        </li>
		<script>
			$(document).ready(function(){
				$("#submitChangeCode").click(function(){
					var form = $("#FormChangeCode")

					console.log(form.serialize())
					$.ajax({
						url: "ajax.php?changeCode",
						type: "POST",
						data: form.serialize()
					}).done(function(response){
						var data = $.parseJSON(response)
						console.log(response)
						if(data.status){
							swal.fire("Codigo Canjeado", "", "success")
							window.location.reload()
						}else{
							swal.fire("Código ya usado o invalido", "", "error")
						}
					})
				})
			})
		</script>



  <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'comic.php') {
					echo 'class="active"';
				}
			?>>
           <a href="comic.php">
              <i class="far fa-laugh-beam"></i>&nbsp; <span>Cómic en Español</span>
           </a>
        </li>

         <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'comic-ingles.php') {
					echo 'class="active"';
				}
			?>>
           <a href="comic-ingles.php">
              <i class="far fa-laugh-beam"></i>&nbsp; <span>Cómic en Inglés</span>
           </a>
        </li>


        <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'collections.php' ? 'class="active"':'';?>>
           <a href="collections.php">
              <i class="fas fa-box-open"></i>&nbsp; <span>Colecciones</span>
           </a>
        </li>

         <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'pets.php' ? 'class="active"':'';?>>
           <a href="pets.php">
              <i class="fas fa-dog"></i>&nbsp; <span>Mascotas</span>
           </a>
        </li>

   <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'antes.php') {
					echo 'class="active"';
				}
			?>>
           <a href="https://bit.ly/3jeFgRy">
              <i class="far fa-smile"></i>&nbsp; <span>Antes y Depués</span>
           </a>
        </li>

 	<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'galerias.php' ? 'class="active"':'';?> id="fotosContent">
           <a href="galerias.php">
              <i class="fas fa-camera-retro"></i>&nbsp; <span>Fotos</span>
				<?php
					if ($Fotos > 0){
						echo '<span class="count-notify">'.$Fotos.'</span>';
					}
				?>
           </a>
        </li>

        	<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'bellas.php') {
					echo 'class="active"';
				}
			?>>
           <a href="https://bellasgram.com/">
              <i class="fab fa-apple"></i>&nbsp; <span>Volver a BellasGram</span>
           </a>
        </li>


        <li <?php
        if (basename($_SERVER['SCRIPT_NAME']) == 'withdrawal.php') {echo 'class="active"'; }?>><a href="withdrawal.php"><i class="fas fa-coins"></i>&nbsp; <span>Canjear Créditos </span></a>
        </li>

        <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'polls.php' ? 'class="active"':'';?>  id="NotifyPoll">
           <a href="polls.php">
              <i class="fas fa-poll"></i>&nbsp; <span>Encuestas</span>
				<?php Echo get_Notification_encuesta();	?>
           </a>
        </li>

        <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'ayudalas.php' ? 'class="active"':'';?>>
           <a href="https://bit.ly/2L8K7VZ">
              <i class="fas fa-venus-mars"></i>&nbsp; <span>Ayúdalas</span>
           </a>
        </li>

          <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'habitacion.php') {
					echo 'class="active"';
				}
			?>>
           <a href="https://bellasgram.com/chat/apps/escapev2/">
              <i class="fas fa-key"></i>&nbsp; <span>Escapa y Gana </span>
           </a>
        </li>

        <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'habitacion2.php') {
					echo 'class="active"';
				}
			?>>
           <a href="https://bit.ly/39nj0UH">
              <i class="fas fa-key"></i>&nbsp; <span>Escapa y Gana 2</span>
           </a>
        </li>
        <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'casa.php' ? 'class="active"':'';?>>
           <a href="casas.php">
              <i class="fa fa-home"></i>&nbsp; <span>Quién vive aquí?</span>
           </a>
        </li>


                <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'chatbot.php' ? 'class="active"':'';?>>
           <a href="https://bit.ly/2VktwTU">
              <i class="fas fa-comments"></i>&nbsp; <span>Asistente Virtual</span>
           </a>
        </li>


				<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == '3d.php' ? 'class="active"':'';?>>
           <a href="http://bit.ly/2MyHu3Q">
              <i class="fas fa-cube"></i>&nbsp; <span>Crear 3D</span>
           </a>
        </li>

		        <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'ventas.php' ? 'class="active"':'';?>>
           <a href="ventas.php">
              <i class="fas fa-cart-plus"></i>&nbsp; <span>Mercado</span>
           </a>
        </li>

                <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'series.php' ? 'class="active"':'';?>>
           <a href="series.php">
              <i class="fa fa-tv"></i>&nbsp; <span>Series</span>
           </a>
        </li>

        <li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'test3d.php' ? 'class="active"':'';?>>
           <a href="https://bit.ly/2xiiVAS">
              <i class="fas fa-lightbulb"></i>&nbsp; <span>Acierta y Gana! v1</span>
           </a>
        </li>OCULTO -->

		<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'referrers.php' ? 'class="active"':'';?>>
           <a href="referrers.php">
              <i class="fas fa-user-plus"></i>&nbsp; <span>Referidos</span>
           </a>
        </li>




  	<!-- OCULTO

		<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'mass.php' ? 'class="active"':'';?>>
            <a href="mass.php">
				<i class="fas fa-layer-group"></i>&nbsp; <span>Mensaje Masivo</span>
            </a>
		</li>




          <?php
			if ($rowu['gender'] == 'mujer'){
		?>
  	<?php
			}
		?>

		       <li <?php
			if (basename($_SERVER['SCRIPT_NAME']) == 'suscriptores.php') {
				echo 'class="active"';
			}
		?>>
           <a href="suscriptores.php">
              <i class="fas fa-user-plus"></i>&nbsp; <span>Suscripciones</span>
           </a>
        </li>

         <li <?php
			if (basename($_SERVER['SCRIPT_NAME']) == 'calculadora.php') {
				echo 'class="active"';
			}
		?>>
           <a href="/chat/apps/calculadora/">
              <i class="fa fa-calculator"></i>&nbsp; <span>Calculadora</span>
           </a>
        </li>
        OCULTO -->

        <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'friends.php') {
        echo 'class="active"';
    }
?>>
           <a href="friends.php">
              <i class="fas fa-users"></i>&nbsp; <span>Amistades</span>
           </a>
        </li>

		 <li <?php
    if (basename($_SERVER['SCRIPT_NAME']) == 'search.php') {
        echo 'class="active"';
    }
?>>
           <a href="search.php">
              <i class="fas fa-search"></i>&nbsp; <span>Buscar</span>
           </a>
        </li>

		<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'notifications.php' ? 'class="active"':""; ?> id="notifyContent">
           <a href="notifications.php">
              <i class="fas fa-bell"></i>&nbsp; <span>Notificaciones
				<?php
					if ($novistos > 0){
						echo '<span class="count-notify">'.$novistos.'</span>';
					}
				?>
			  </span>
           </a>
        </li>


        		<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'notas.php' ? 'class="active"':'';?>>
           <a href="notas.php">
              <i class="fas fa-comment"></i>&nbsp; <span>Notas</span>
           </a>
        </li>

        <li <?php if (basename($_SERVER['SCRIPT_NAME']) == 'regalos.php') { echo 'class="active"'; } ?>>
<a href="regalos.php">
<i class="fas fa-gift"></i></i>&nbsp; <span>Mis Regalos</span>
</a>
</li>

<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'monetizacion.php' ? 'class="active"':'';?>>
           <a href="monetizacion.php">
              <i class="fas fa-dollar-sign"></i>&nbsp; <span>Ganar Dinero</span>
           </a>
        </li>


	<li <?php
			if (basename($_SERVER['SCRIPT_NAME']) == 'settings.php') {
				echo 'class="active"';
			}
		?>>
           <a href="settings.php">
              <i class="fas fa-wrench"></i>&nbsp; <span>Editar Perfil</span>
           </a>
        </li>


		</ul>
		<ul class="sidebar-menu" data-widget="tree">
            <?php if(isset($_COOKIE['session']) AND !empty($_COOKIE['session'])){ ?>
		<li class="header">JUEGOS</li>
        <li>
            <?php
            $session = $_COOKIE['session']; ?>
            <a href="https://bellasgram.com/includes/third-party-auth.php?site=games&session=<?php echo $session; ?>"><i class="fa fa-city"></i>&nbsp; <span>Ciudad BellasGram</span></a>
        </li>
        <li>
            <a href="https://bellasgram.com/includes/third-party-auth2.php?site=xrace&session=<?php echo $session; ?>"><i class="fa fa-car"></i>&nbsp; <span>Carreras BellasGram</span></a>
        </li>
		<?php }
			//admin panel
			if ($rowu['role'] == 'Admin'){
		?>
			<li class="header">ADMIN PANEL</li>

	<li <?php
					if (basename($_SERVER['SCRIPT_NAME']) == 'adminsettings.php') {
						echo 'class="active"';
					}
				?>>
			   <a href="adminsettings.php">
				  <i class="fas fa-wrench"></i>&nbsp; <span>Configuraciones </span>
			   </a>
			</li>

			<li <?php
					if (basename($_SERVER['SCRIPT_NAME']) == 'create_gifcodes.php') {
						echo 'class="active"';
					}
				?>>
			   <a href="create_gifcodes.php">
				  <i class="fas fa-award"></i>&nbsp; <span>Codigos de Regalo </span>
			   </a>
			</li>
      <li <?php if (basename($_SERVER['SCRIPT_NAME']) == 'admin_questions.php')echo 'class="active"';?>>
        <a href="admin_questions.php">
          <i class="fas fa-question"></i>&nbsp; <span>Preguntas de Usuarios</span>
        </a>
      </li>

			<li <?php
			if (basename($_SERVER['SCRIPT_NAME']) == 'adminreportes.php') {
						echo 'class="active"';
					}
				?>>
		 <a href="adminreportes.php">
				  <i class="fa fa-exclamation-circle"></i>&nbsp; <span>Reporte</span>
			 </a>
			</li>

			<li <?php
					if (basename($_SERVER['SCRIPT_NAME']) == 'requests.php') {
						echo 'class="active"';
					}
				?>>
			   <a href="requests.php">
				  <i class="fab fa-paypal"></i>&nbsp; <span>Solicitudes de pago </span>
			   </a>
			</li>

			<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'adminlistasfotosbot.php') {
					echo 'class="active"';
				}
			?>>
           <a href="adminlistasfotosbot.php">
              <i class="fas fa-images"></i>&nbsp; <span>Listas de fotos (bot) </span>
           </a>
        </li>

			<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'admincopyfriends.php') {
					echo 'class="active"';
				}
			?>>
           <a href="admincopyfriends.php">
              <i class="fas fa-copy"></i>&nbsp; <span>Copiar Amigos </span>
           </a>
        </li>

		<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'admincheckuser.php') {
					echo 'class="active"';
				}
			?>>
           <a href="admincheckuser.php">
              <i class="fas fa-wrench"></i>&nbsp; <span>Revisar Usuario </span>
           </a>
        </li>

        <li <?php
					if (basename($_SERVER['SCRIPT_NAME']) == 'buscaip.php') {
						echo 'class="active"';
					}
				?>>
			   <a href="buscaip.php">
				  <i class="fas fa-search"></i>&nbsp; <span>Busqueda por IP </span>
			   </a>
			</li>



			<ul class="sidebar-menu" data-widget="tree">
		<li class="header">REGISTROS</li>

            <li <?php
                if (basename($_SERVER['SCRIPT_NAME']) == 'hola.php') {
                    echo 'class="active"';
                }
            ?>>
                <a href="hola.php">
                    <i class="fas fa-hand-holding-usd"></i>&nbsp; <span>Quien pago</span>
                </a>
            </li>
            <li <?php
                if (basename($_SERVER['SCRIPT_NAME']) == 'subscriberslist.php') {
                    echo 'class="active"';
                }
            ?>>
                <a href="subscriberslist.php">
                    <i class="fas fa-table"></i>&nbsp; <span>Lista de suscriptores</span>
                </a>
            </li>

	<li <?php
			if (basename($_SERVER['SCRIPT_NAME']) == 'compraspackadmin.php') {
						echo 'class="active"';
					}
				?>>
		 <a href="compraspackadmin.php">
				  <i class="fas fa-dollar-sign"></i>&nbsp; <span>Packs Comprados</span>
			 </a>
			</li>

			<li <?php
					if (basename($_SERVER['SCRIPT_NAME']) == 'requests_historial_admin.php') {
						echo 'class="active"';
					}
				?>>
			   <a href="requests_historial_admin.php">
				  <i class="fab fa-paypal"></i>&nbsp; <span>Historial de pagos </span>
			   </a>
			</li>
      <li <?php currentPage('transacciones.php'); ?> id="fotosContent" align="center">
        <a href="transacciones.php">
          <i class="fas fa-table"></i> Historial de transacciones
        </a>
      </li>

			              <li <?php
			if (basename($_SERVER['SCRIPT_NAME']) == 'mensajesauto.php.php') {
				echo 'class="active"';
			}
		?>>
           <a href="mensajesauto.php">
              <i class="fas fa-envelope"></i>&nbsp; <span>Mensajes Automaticos</span>
           </a>
        </li>


			<li <?php
					if (basename($_SERVER['SCRIPT_NAME']) == 'adminpets.php') {
						echo 'class="active"';
					}
				?>>
			   <a href="adminpets.php">
				  <i class="fas fa-dog"></i>&nbsp; <span>Administrar mascotas </span>
			   </a>
			</li>

			<li <?php Echo basename($_SERVER['SCRIPT_NAME']) == 'admincrearbot.php' ? 'class="active"':""; ?>>
				<a href="admincrearbot.php">
					<i class="fas fa-robot"></i>&nbsp; <span>Crear un nuevo bot </span>
				</a>
			</li>

        <li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'admincollections.php') {
					echo 'class="active"';
				}
			?>>
           <a href="admincollections.php">
              <i class="fas fa-box-open"></i>&nbsp; <span>Crear Colección </span>
           </a>
        </li>

		<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'admincambiarclave.php') {
					echo 'class="active"';
				}
			?>>
           <a href="admincambiarclave.php">
              <i class="fas fa-key"></i>&nbsp; <span>Cambiar Contraseña </span>
           </a>
        </li>

        		<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'habitacion.php') {
					echo 'class="active"';
				}
			?>>
           <a href="https://bellasgram.com/chat/apps/revisar/escapeGame-master/escapeGame.html">
              <i class="fas fa-key"></i>&nbsp; <span>escape sin iframe </span>
           </a>
        </li>

        		<li <?php
				if (basename($_SERVER['SCRIPT_NAME']) == 'habitacion2.php') {
					echo 'class="active"';
				}
			?>>
           <a href="https://bellasgram.com/carreras/">
              <i class="fas fa-key"></i>&nbsp; <span>carreras</span>
           </a>
        </li>
    <?php
      }
    ?>
  <!-- SI NO ESTA LOGUEADO -->
  <?php else: ?>
    <li class="text-white">
      <a href="index.php">
        <i class="fas fa-sign-in-alt"></i>&nbsp; <span>Iniciar Session</span>
      </a>
    </li>
    <li class="text-white">
      <a href="register.php">
        <i class="fas fa-user-plus"></i>&nbsp; <span>Registrarse</span>
      </a>
    </li>
  <?php endif; ?>

      </ul>
    </section>

  </aside>

  <div class="modal fade" id="changeCode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Canjear Tarjeta de regalo</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="FormChangeCode">
					<input class="form-control" type="number" name="code" placeholder="Codigo de la Tarjeta de regalo">
				</form>
			</div>
			<div class="modal-footer">
				<div type="button" class="btn btn-secondary" style="background:#dddddd;" data-dismiss="modal" aria-label="Close">
					Cancelar
				</div>
				<div type="button" class="btn btn-success" id="submitChangeCode">
					Canjear
				</div>
			</div>
		</div>
	</div>
</div>

<?php
}

function footer()
{
	global $player_id, $connect, $rowu;
  include 'config.php';
  include 'assets/js/custom.php';
?>
<style>
.__pets_notifications {
    position: fixed;
    bottom: 15px;
    right: 15px;
    z-index: 9;
}
.__pets_notifications .__pets_item {
    height: 60px;
    width: 60px;
    border: 4px solid white;
    background-size: cover;
    line-height: 50px;
    background-color: #51c277;
    display: block;
    text-align: center;
    font-weight: 700;
    color: white;
    font-size: 25px;
    text-shadow: 2px 2px 4px #00000029;
    border-radius: 50%;
    margin: 10px;
    position: relative;
    box-shadow: 0 1px 4px black;
}
.__pets_item .money {
    height: 18px;
    background-color: #DD1E34;
    border-radius: 30px;
    padding: 0px 3px;
    color: #FFFFFF;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    line-height: 19px;
    position: absolute;
    min-width: 17px;
    top: -5px;
    right: 0;
}
.__pets_item .message {
    position: absolute;
    background-color: #51c277;
    font-size: 11px;
    line-height: 1;
    width: 150px;
    right: 100%;
    top: 50%;
    transform: translateY(-50%);
    margin-right: 10px;
    padding: 5px;
    border-radius: 5px;
    text-shadow: none;
}

</style>
<div class="__pets_notifications">
	<?php

		$PetPlayers = $connect->query("SELECT * FROM `player_pets` WHERE player_id='{$player_id}' AND bonus>0 AND live=1");
		if($PetPlayers && mysqli_num_rows($PetPlayers)):
			while($PetPlayer = mysqli_fetch_assoc($PetPlayers)):

				$pet = $connect->query("SELECT * FROM `pets` WHERE id='{$PetPlayer["pet_id"]}'");
				$pet = mysqli_fetch_assoc($pet);
	?>
	<a href="pets.php?aceptarRegalo=<?php Echo $PetPlayer['id']; ?>">
		<div class="__pets_item" style="background-image: url(<?php echo porcentaje($pet['hp'], $PetPlayer['hp'])<50 ? petImg($pet['image'])->lifelow : petImg($pet['image'])->imgNormal;?>);">
			<div class="money"><?php Echo $PetPlayer['bonus']; ?></div>
			<div class="message">Tu mascota <?php Echo $PetPlayer['name']; ?> encontró <?php Echo $PetPlayer['bonus']; ?> creditos especiales y son tuyos!! Tócala para recibirlos.</div>
		</div>
	</a>
	<?php
			endwhile;
		endif;
	?>
</div>

<!-- OCULTO<footer class="main-footer">

    <strong>&copy; <?php
    echo date("Y");
?> Chat - <a href="https://bellasgram.com/">Volver a BellasGram</a></strong>

</footer>OCULTO -->

</div>

  <!--JAVASCRIPT-->
  <!--=================================================-->
  <!--Bootstrap-->
  <script src="assets/js/bootstrap.min.js"></script>

  <!--Admin-->
  <script src="assets/js/admin.min.js"></script>
  <!--DataTables-->
  <script src="https://cdn.datatables.net/v/bs4/dt-1.10.18/r-2.2.2/datatables.min.js"></script>
	<!--DatePicker-->
  <!--Swiper-->
  <script src="assets/js/swiper.min.js"></script>

	<script src="assets/plugins/datepicker/datepicker.min.js"></script>
  <script src="assets/plugins/datepicker/datepicker.en.js"></script>
  <script src="assets/js/lozad.min.js"></script>

	<script>
	// Initialize library to lazy load images
	var observer = lozad('.lozad', {
		threshold: 0.1,
		load: function(el) {
			console.log("observando a :"+ el.getAttribute("data-src"));
			el.src = el.getAttribute("data-src");
			el.classList.remove("lozad");
		}
	});

	observer.observe();
	var position;
	var ScrollPos = "TOP";
	var LastScroll = "TOP";
    var count = 0;
    var title = document.title;
    function changeTitle(counts) {
        if (counts==0) {
            document.title = title;
        }else{
            count=counts;
            var newTitle = '(' + count + ') ' + title;
            document.title = newTitle;
        }
    }
    function newUpdate() {
    update = setInterval(changeTitle, 1);
    }
    var docBody = document.getElementById('site-body');
	$(document).ready(function(){

		$(".dark-theme").click(function(){
			if($("body").hasClass("--dark")){
				$("body").removeClass("--dark")
			}else{
				$("body").addClass("--dark")
			}
			$.ajax({
				url:'?ChangeTheme=true',
				type:'POST'
			})
		})

		$("#suscribe").click(function(){
			var dHref = $(this).data('href');
			var dusername = $(this).data('username');
			swal.fire({
				title: 'Suscribirse a '+dusername+' por 7 días?',
				buttons: ["No", "Si"],
				showCancelButton: true,
			})
			.then((name) => {
				if(name.isConfirmed){

					window.location.href = dHref;

				}
			});
		})
        $("#no-credits").click(function(){
            var dHref = $(this).data('href');
            var dusername = $(this).data('username');
            swal.fire({
                title: 'No posees suficientes Cerditos Especiales para suscribirte a este perfil',
                confirmButtonText: "Comprar Créditos",
                showCancelButton: true,
            })
            .then((name) => {
                if(name.isConfirmed){

                    window.location.href = "comprar.php";

                }
            });
        })
        $(".no-credits").click(function(){
            var dHref = $(this).data('href');
            var dusername = $(this).data('username');
            swal.fire({
                title: 'No posees suficientes Créditos para apoyar a esta persona',
                confirmButtonText: "Comprar Créditos",
                icon: "info",
                showCancelButton: true,
            })
            .then((name) => {
                if(name.isConfirmed){

                    window.location.href = "comprar.php";

                }
            });
        })
        $(".no-creditsPack").click(function(){
            var dHref = $(this).data('href');
            var dusername = $(this).data('username');
            swal.fire({
                title: 'No posees suficientes Créditos Especiales para comprar este Pack',
                buttons: ["Cancelar", "Comprar Créditos"],
                showCancelButton: true,
            })
            .then((name) => {
                if(name.isConfirmed){

                    window.location.href = "comprar.php";

                }
            });
        })
        $(".deleteMsg").click(function(){
          var dHref = $(this).data('href');
          swal.fire({
            title: '¿Desea eliminar este mensaje?',
            buttons: ["Cancelar", "Eliminar Mensaje!"],
            showCancelButton: true,
          })
          .then((name) => {
            if(name.isConfirmed){

              window.location.href = dHref;

            }
          });
        })
        // HACE UNA PREGUNTA AL USUARIO CON UN FORMULARIO, SI ACEPTA, SE REDIRIGIRA AL LINK
        $(".actionQuest").click(function(){
          var dHref = $(this).data('href');
          var quest = $(this).data('quest');
          var btnAction = $(this).data('btnaction');
          swal.fire({
            title: quest,
            buttons: ["Cancelar", btnAction],
            showCancelButton: true,
          })
          .then((name) => {
            if(name.isConfirmed){

              window.location.href = dHref;

            }
          });
        })
        $(".actionInfo").click(function(){
          let info = $(this).data('info');
          let secondInfo = $(this).data('secondinfo');
          swal.fire({title: info,html: secondInfo,icon: 'info'})
        })
        notifyRequest();
		setInterval(function () {
            notifyRequest();
		}, 5000);

		position = $(".wrapper").scrollTop();

		$(".wrapper").scroll(function() {
			if($(".wrapper").width() <= 3600 && !$("body").hasClass("sidebar-open")){
				var scroll = $(".wrapper").scrollTop();
				if(scroll > position) {
					ScrollPos = "DOWN";
				} else if(ScrollPos) {
					ScrollPos = "TOP";
				}
				if(LastScroll != ScrollPos){
					LastScroll = ScrollPos;

					if(ScrollPos == "TOP"){
						$(".main-header").animate( { "top": "0px" } );
					}

					if(ScrollPos == "DOWN"){
						$(".main-header").animate( { "top": "-70px" } );
					}
				}
				position = scroll;
			}else{
				//console.log('Window is width');
			}
		});
        $(".buy_item").click(function(){
            var dHref = $(this).data('href');
            var cash = $(this).data('cash');
            swal.fire({
                title: 'Desea comprar este item por '+cash+' Creditos Normales o Especiales?',
                buttons: ["No", "Si"],
                showCancelButton: true,
            })
            .then((name) => {
                if(name.isConfirmed){

                    window.location.href = dHref;

                }
            });
        })
        $(".buy_player_item").click(function(){
            var dHref = $(this).data('href');
            var cash = $(this).data('cash');
            swal.fire({
                title: 'Desea comprar este item por '+cash+' Puntos',
                buttons: ["No", "Si"],
                showCancelButton: true,
            })
            .then((name) => {
                if(name.isConfirmed){

                    window.location.href = dHref;

                }
            });
        })
        $(".action_limited").click(function(){

            swal.fire({
                title: 'Esto solo se puede hacer desde la app de BellasGram',
                buttons: ["Luego", "Descarga la App de BellasGram"],
                showCancelButton: true,
                icon: "https://bellasgram.com/chat/assets/img/app.png",
            })
            .then((name) => {
                if(name.isConfirmed){

                    window.location.href = "instrucciones.php";

                }
            });
        })
        <?php if(isLogged()): ?>
        function notifyRequest(){
            //$("#actualizarmensajes").load('ajax.php #actualizarmensajes');
            $.ajax({
                url:'./ajax.php',
                type:'POST',
                data: {
                    'notify': true
                }
            }).done(function(response){
                var response = $.parseJSON(response);
                console.log( response );
                resultnotify=response.Msg + response.notify + response.Fotos + response.Packs + response.Encuesta + response.Questions;
                changeTitle(resultnotify);
                if(response.Msg > 0){
                    $("[id=msgContent]").find(".count-notify").remove();
                    $("[id=msgContent]").find("span").append( $("<span>").addClass("count-notify").html(response.Msg) );
                }else{
                    $("[id=msgContent]").find(".count-notify").remove();
                }
                if(response.notify > 0){
                    console.log( response.notify );
                    $("[id=notifyContent]").find(".count-notify").remove();
                    $("[id=notifyContent]").find("span").append($("<span>").addClass("count-notify").html(response.notify) );
                }else{
                    $("[id=notifyContent]").find(".count-notify").remove();
                }
                if(response.Fotos > 0){
                    //console.log( response.Fotos );
                    $("[id=fotosContent]").find(".count-notify").remove();
                    $("[id=fotosContent]").find("span").append( $("<span>").addClass("count-notify").html(response.Fotos) );
                }else{
                    $("[id=fotosContent]").find(".count-notify").remove();
                }

                if(response.Packs > 0){
                    //console.log( response.Fotos );
                    $("[id=NotifyPack]").find(".count-notify").remove();
                    $("[id=NotifyPack]").find("span").append( $("<span>").addClass("count-notify").html(response.Packs) );
                }else{
                    $("[id=NotifyPack]").find(".count-notify").remove();
                }
                if(response.Encuesta > 0){
                    //console.log( response.Fotos );
                    $("[id=NotifyPoll]").find(".count-notify").remove();
                    $("[id=NotifyPoll]").find("span").append( $("<span>").addClass("count-notify").html(response.Encuesta) );
                }else{
                    $("[id=NotifyPoll]").find(".count-notify").remove();
                }
                if(response.Questions > 0){
                    //console.log( response.Fotos );
                    $("[id=NotifyQuestion]").find(".count-notify").remove();
                    $("[id=NotifyQuestion]").find("span").append($("<span>").addClass("count-notify").html(response.Questions) );
                }else{
                    $("[id=NotifyPoll]").find(".count-notify").remove();
                }
            })
        }
        <?php endif ?>

	})

    function loadgalery(imageRandom = null){
        location.href= '<?php echo $sitio['site']; ?>foto.php?fotoID='+imageRandom;
    }
    function videoPreview(id){
      elv = '#VC-'+id;
      video = '#'+id
      if($(elv).hasClass("show")){
        $(elv).removeClass(`show`)
        $(elv).removeAttr("hidden")
        $(video).get(0).pause()
        getThumb(id)
      }else{
        $(elv).addClass(`show`)
        $(elv).attr("hidden","true")
        $(video).get(0).play()
      }
      //setTimeout(() =>{ getThumb(id)},1500)
    }
	</script>

</body>
</html>
<?php

  // COMPRUEBA SI EXISTE UNA FOTO CON CRÉDITOS DE REGALO (segunda forma)
  if (basename($_SERVER['SCRIPT_NAME']) != 'galerias.php') $givecredits = givecredits(null,2);

}
?>
