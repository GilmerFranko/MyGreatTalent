<?php

// INDICACION TEMPORAL
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'com.BelGram.android')
{
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">


<center> <H4> <font color="green">Las fotos son solo visibles desde nuestra nueva app de BellasGram, para ingresar sigue los pasos descritos abajo. 
<br/>(Al instalar la nueva app no perderas nada de lo que tenias ya en BellasGram).

    <hr />
     Si ya tienes la nueva APP pero no recuerdas tu contrase&ntilde;a y se te dificultan los pasos para una nueva, entonces aun no elimines la app y <br/> enviale un mensaje a <a href="https://bellasgram.com/chat/newchat.php?id=196558">ANDREA MARTINEZ</a> y pidele una contrase&ntilde;a nueva, ella te ayudara, (las contrase&ntilde;as estan cifradas con seguridad y no te puede decir tu contrase&ntilde;a actual, pero si te puede dar una nueva).
    <hr />
    La nueva app es mas r&aacute;pida, y salen menos anuncios al ver fotos en BellasGram y el Chat, adem&aacute;s ya no te saldr&aacute;n anuncios de video al tocar la lupa en BellasGram para ver una foto.
        <hr />
    No te olvides darnos 5 estrellas<br/><br/>
    <hr />
    <br/><br/>
    PASOS A SEGUIR:
    <br/><br/>
        <hr />
   <a href="https://play.google.com/store/apps/details?id=com.bellas.gram.app.android">Descarga la APP AQUI</a> ya luego borra la app actual e ingresa solo desde la nueva.
     
    <hr />
    Nuestra aplicaci&oacute;n en la PlayStore o Google Play se llama 
<br/><a href="https://play.google.com/store/apps/details?id=com.bellas.gram.app.android">BellasGram </a><br/>(Puedes buscarla en la PlayStore o toca las letras azules para ir a Google Play) 
<hr />

Instala la APP y la abres e ignora el contenido (a simple vista se ve como si fuera un app de un v&iacute;deojuego, pero lo rico esta oculto)<br/> <br/>
1: toca la lupa que sale arriba a la derecha
<br/><br/>
<img src="https://bellasgram.com/static/images/1.png">

<br/>
<hr /><br/><br/>
2: Toca donde donde "buscar"
<br/><br/>
<img src="https://bellasgram.com/static/images/2.png">
<hr /><br/>
Y escribe bellasgram y espera que salga la manzana (al escribir bellasgram saldr&aacute; una manzana) y la tocas para entrar a BellasGram, Ya puedes entrar con tu correo y contrase&ntilde;a, si no recuerdas la contrase&ntilde;a puedes pedir una nueva como se indica arriba.
<br/><br/>
<img src="https://bellasgram.com/static/images/3.png">
<hr />

';
    exit;
}

/* FIN INDICACION TEMPORAL*/

require("core.php");

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname'");
$rowu  = mysqli_fetch_assoc($suser);
if ($rowu['role']!='Admin') {
    echo '<meta http-equiv="refresh" content="0;url='.$sitio['site'].'messages.php">';
    exit;
}
head();
?>
<link rel="stylesheet" href="assets/css/sortable-tables.min.css">
<script src="assets/js/sortable-tables.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">
</script>
<div class="content-wrapper">

    <div id="content-container">

        <div class="row" style="margin:0;">
            
            <div class="col-sm-12 col-md-6">
            <!-- OCULTO     <a class="btn btn-success" style="width:100%;" href="friendsgalerias.php">
                    <i class="fas fa-camera-retro"></i> Fotos de amigas
                </a>
            </div>OCULTO -->
    
        </div>
        
        <section class="content">

            <div class="row">

                <div style="width: 100%;">

                    <div class="box">
                       <center>
                        <a href="cron/deletesubscriptions.cron.php">
                           <button class="btn btn-success" name="" value="">
                              Ejecutar cron                             
                           </button>
                         </a>
                      
                       <!-- Poner aqui aviso y textos-->
                       
                       <!-- Poner aqui aviso y textos-->
                       <div class="box-body row-list">
<?php


$total_pages = $connect->query("SELECT * FROM `players` WHERE followers IS NOT NULL AND followers !='{}'")->num_rows;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

$num_results_on_page = 40;
$calc_page = ($page - 1) * $num_results_on_page;
//SELECCIONAR A TODOS LOS USUARIOS CON SUSCRIPTORES
$querycp = mysqli_query($connect, "SELECT * FROM `players` WHERE follower IS NOT NULL AND follower !='{}' ORDER BY username");
$countcp = mysqli_num_rows($querycp);
?>
<div id="scroll">
<table class="table table-striped table-bordered table-hover sortable-table" id="players">
<thead>
    <tr>
      <th style="text-align:center;" class="numeric-sort">#</th>
      <th style="text-align:center;" class=""><i class="fa fa-user"></i> Suscriptor</th>
      <th style="text-align:center;" class=""><i class="fa fa-user"></i> Suscrito a</th>
      <th style="text-align:center;" class="numeric-sort"><i class="fa fa-calendar"></i> Restante</th>
    </tr>
</thead>
<tbody>
    <?php
    //DECLARO VARIABLES
    $ArrFollower = [];
    $InFollower = [];
    $InFollower=[];
    $count=0;
if ($countcp > 0) {
    while ($rowcp = mysqli_fetch_assoc($querycp)) {

      //DECODIFICAR Y ALMACENAR SEGUIDORES
        if(!is_null($rowcp['follower'])){
          $ArrFollower = json_decode($rowcp['follower'], true);
        }
        if(count($ArrFollower)){  
          foreach($ArrFollower as $key => $Frs){
            unset($InFollower);
            $InFollower[] = $key;
            if ($InFollower==null){
              continue;
            }
      
        
        $InFollower = implode(',', $InFollower);
        //SELECCIONAR A CUAL SIGUE ESTE USUARIO
        $follower = mysqli_query($connect, "SELECT * FROM `players` WHERE id IN ({$InFollower})");
        $row = mysqli_fetch_assoc($follower);

    ?>
    <tr>
      <td style="text-align:center;"><?php echo $count; ?></td>
      <td style="text-align:center;"><a href="profile.php?profile_id=<?php echo $rowcp['id']; ?>"><?php echo $rowcp['username']; ?></a></td>
      <td style="text-align:center;"><a href="profile.php?profile_id=<?php echo $row['id']; ?>"><?php echo $row['username']; ?></a></td>
      <td style="text-align:center;">
        <div class="">
          <div style="">
          
            <span class="countdown-bar anime-countdown" data-controller="countdown-bar" data-countdown-timestamp="<?php Echo $ArrFollower[ $row['id'] ];?>">
              <span class="u-days">?</span><span>d</span> 
              <span class="u-hours">?</span><span>h</span> 
              <span class="u-minutes">?</span><span>m</span>
              <span class="u-seconds">?</span><span>s</span>
            </span>
            
          </div>
        </div>
      </td>
    </tr>
    <?php
    $count++;
}
    }
        }
?>

</table>
</div>
<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
<ul class="pagination">
    <?php if ($page > 1): ?>
    <li class="prev"><a href="subscriberslist.php?page=<?php echo $page-1 ?>">Anterior</a></li>
    <?php endif; ?>

    <?php if ($page > 3): ?>
    <li class="start"><a href="subscriberslist.php?page=1">1</a></li>
    <li class="dots">...</li>
    <?php endif; ?>

    <?php if ($page-2 > 0): ?><li class="page"><a href="subscriberslist.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
    <?php if ($page-1 > 0): ?><li class="page"><a href="subscriberslist.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

    <li class="currentpage"><a href="subscriberslist.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

    <?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="subscriberslist.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
    <?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="subscriberslist.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

    <?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
    <li class="dots">...</li>
    <li class="end"><a href="subscriberslist.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
    <?php endif; ?>

    <?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
    <li class="next"><a href="subscriberslist.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
    <?php endif; ?>
</ul>
<?php endif; ?>
<?php
} else {
    echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> No tienes suscripciones activas</strong></div>';
}

?>

                                            </td>
                                            </tr>

                                        </div>
                                    </div>
                                </tbody>
                            </table>
                            <br>

                        </div>
                    </div>
                </div>

            </div>

    </div>
    <!--===================================================-->
    <!--End page content-->
</div>
<!-- JavaScript -->
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.11.1/build/alertify.min.js"></script>

<!-- CSS -->
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.1/build/css/alertify.min.css"/>
<script>
$(document).on('submit', 'form', function(e){

    alertify.confirm('Estas seguro que quieres eliminar el historial completo?', 'Mensaje de confirmacion',
    function(){
    //submit
    $.post('subscriberslist.php', { deleteH : 1234 }, function(resp) {
    if (resp=="bien") {
      alertify.success('Historial eliminado con exito');
      setInterval(function () {
        location.reload();
    }, 1000);
    }
    });
    },
    function(){ 
    alertify.error('Cancel')

    });
});


</script>
<script>
  //RELOJ
var CountDown = {

  currentTimeInSeconds: function() {
    return Date.now() / 1e3
  },
  
  days: function(time) {
    return (time - CountDown.currentTimeInSeconds()) / 86400 | 0
  },
  
  hours: function(time) {
    return (time - CountDown.currentTimeInSeconds()) / 3600 % 24 | 0
  },
  
  minutes: function(time) {
    return (time - CountDown.currentTimeInSeconds()) / 60 % 60 | 0
  },
  
  seconds: function(time) {
    return (time - CountDown.currentTimeInSeconds()) % 60 | 0
  },
  
  start: function() {
    setInterval(function(){
      $("[data-controller=countdown-bar]").each(function(){
        var timestamp = $(this).data("countdown-timestamp");
        if((timestamp - CountDown.currentTimeInSeconds()) > 0){
          $(this).find(".u-days").html( CountDown.days(timestamp) );
          $(this).find(".u-hours").html( CountDown.hours(timestamp) );
          $(this).find(".u-minutes").html( CountDown.minutes(timestamp) );
          $(this).find(".u-seconds").html( CountDown.seconds(timestamp) );
        }
      })
    }, 1000);
  }
}
$(document).ready(function(){
  CountDown.start();
});
</script>
<style>
#scroll {
     overflow-x:scroll;
     height:100%;
     width:100%;
}
#scroll table {
    width:100%;
    height: 100%;
}
</style>
<!--===================================================-->
<!--END CONTENT CONTAINER-->
<?php
footer();
?>