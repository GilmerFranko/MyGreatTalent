<?php
require("core.php");
head();


view_Notification_pack();
if (isset($_POST['precomprar'])){
  $idPack = $_POST['galeriaid'];
  $consult = $connect->query("SELECT *, players.`username` AS username FROM `packsenventa` AS pack INNER JOIN players ON players.`id` = pack.`player_id` WHERE pack.`id`='$idPack'");
  $name = mysqli_fetch_assoc($consult);
  echo '
  <script type="text/javascript">
  $(document).ready(function() {
    $("#sell-vehicle").modal(\'show\');
    });
    </script>

    <div id="sell-vehicle" class="modal fade">
    <div class="modal-dialog modal-md">
    <div class="modal-content">
    <div class="modal-header">
    <h4 class="modal-title">Confirmar suscripción al Pack por 7 días</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
    <center>
    <div class="badge badge-info" style="white-space: unset;"><h4>Confirmar la suscripción al pack de '.$name['username'].' por 7 días</h4></div>

    <br /><br />

    <center>

    <form method="POST">
    <input type="hidden" name="galeriaid" value="'.$_POST['galeriaid'].'">


    <button type="submit" name="comprar" class="btn btn-success btn-buypack">
    <H3>Confirmar</H3></button>
    </form>

    </center>

    <br /><br />
    <button type="button" class="btn btn-primary btn-md btn-block" data-dismiss="modal" aria-hidden="true">Cancelar</button>
    </center>
    </div>
    </div>
    </div>
    </div>';

  }

  /* Compra un pack por 7 dias */
  if (isset($_POST['comprar']))
  {

    /* ID del pack */
    $idgaleria = $_POST['galeriaid'];

    /* Optiene datos del pack */
    $querygal = mysqli_query($connect, "SELECT * FROM `packsenventa` WHERE id='$idgaleria'");
    $galeria = mysqli_fetch_assoc($querygal);

    /* Verifica que no se haya comprado el pack */
    $queryccc = mysqli_query($connect, "SELECT * FROM `packscomprados` WHERE foto_id='$idgaleria' AND comprador_id='$rowu[id]'");
    $countcompradoc = mysqli_num_rows($queryccc);

    /* Fecha en que vencerá la suscripcion (7 dias)*/
    $vence = time() + 60 * 60 * 24 * 7;

    /* Otras variables */
    $linkc = 'pack.php?ID='. $idgaleria;
    $dolaresdeluserc = $rowu['eCreditos'];
    $costoc = $galeria['precio'];

    /* Verifica si ya ha comprado la foto anteriormente */
    if($countcompradoc > 0)
    {
      /* Verifica si ya está vencido el pack y lo elimina */
/* Eliminar compra de existir
* (puede existir si aun no se ha ejecutado el cron
* que elimina las compras vencidas y se desea comprar
* el mismo pack)
*/

$fechaActual = time();

$connect->query("DELETE FROM `packscomprados` WHERE foto_id = '$idgaleria' AND comprador_id = '$rowu[id]' AND `vence` <= $fechaActual");

if($connect->affected_rows > 0)
{
  /* Le decimos al programa que ya no hay compras con el mismo usuario y foto */
  $countcompradoc = 0;
}
}

/* Verifica que no haya comprado la foto anteriormente */
if($countcompradoc < 1)
{
  /* Revisamos que el usuario tiene el dinero */
  if($dolaresdeluserc >= $costoc)
  {

    /* RESTAR DINERO AL COMPRADOR */
    $restardinero = updateCredits($rowu['id'],'-',intval($costoc),1);

    if ($restardinero)
    {

      /* Optiene el 60% del costo del Pack */
      $sumtotal = ceil(($costoc * 60)/100);

      /* ACREDITAR CREDITOS AL DUEÑO DEL PACK */
      $sumardineroalvendedor = updateCredits($galeria['player_id'],'+',$sumtotal,1);

      /* REGISTRAR COMPRA */
      $insertarcompra = mysqli_query($connect, "INSERT INTO `packscomprados` (foto_id, comprador_id, vence) VALUES ('$idgaleria', '$rowu[id]', '$vence')");

      if($insertarcompra)
      {
        /* SUMAR VENTA */
        $actualizardatos = mysqli_query($connect, "UPDATE `packsenventa` SET ventasrealizadas=ventasrealizadas+1 WHERE id='$idgaleria'");

        /* ENVIAR NOTIFICACION AL DUEÑO DEL PACK */
        $newPurchase = mysqli_query($connect, "INSERT INTO `players_notifications` (fromid, toid,not_key,action,read_time) VALUES ('$rowu[id]', '$galeria[player_id]', 'newPurchasePack' , '$galeria[id]' , '0' )");
        ?>
        <script type="text/javascript">
          $(document).ready(function(){
            Swal.fire({
              title: "Suscripción al pack por 7 días realizada!",
              text: "¡Suscripción exitosa! Ahora tienes acceso al pack durante 7 días.",
              icon: "success",
              showCancelButton: false,
              confirmButtonColor: "#930eac",
              confirmButtonText: '<i class="fab fa-get-pocket"></i> Ver Pack',
              allowOutsideClick: false,
              reverseButtons: true
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = "<?php echo $linkc; ?>";
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


if (isset($_POST['postcomment'])) {
  $comment   = $_POST['comment'];
  $galeria_id = $_POST['galeria_id'];
  $author = $player_id;
  $date      = date('d F Y');
  $time      = date('H:i');

  $querycpc =
  $countcpc = mysqli_num_rows($querycpc);
  if ($countcpc == 0) {
    $post_comment =

    $querycpbrr1 =
    $countcpbrr = mysqli_num_rows($querycpbrr1);
    if ($countcpbrr > 0) {
      $querycpbrr2 = mysqli_query($connect, "SELECT * FROM `player_comments` WHERE galeria_id='$galeria_id' ORDER BY id ASC LIMIT 1");
      $rowbrr = mysqli_fetch_assoc($querycpbrr2);
      $brrcmnt = $rowbrr['id'];
      $brr = mysqli_query($connect, "DELETE FROM `player_comments` WHERE id='$brrcmnt'");

    }


  }

  echo '
  <script type="text/javascript">
  $(document).ready(function() {
    $("#sell-vehicle").modal(\'show\');
    });
    </script>

    <div id="sell-vehicle" class="modal fade">
    <div class="modal-dialog modal-md">
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">



    </center>

    <br /><br />
    <button type="button" class="btn btn-success btn-md btn-block" data-dismiss="modal" aria-hidden="true"><i class="fab fa-get-pocket"></i> OK</button>
    </center>
    </div>
    </div>
    </div>
    </div>';



    $archivoActual = $_SERVER['PHP_SELF'];
    header("refresh:1;url=" + $archivoActual +"");
  }

  /*ELIMINAR PACK */
  if (isset($_GET['trash_id']) and !empty($_GET['trash_id']) ) {
    $idtrash = mysqli_real_escape_string($connect,$_GET['trash_id']);
    $select = mysqli_query($connect,"SELECT id,player_id,imagens,imagen FROM `packsenventa` WHERE id=$idtrash");
    if ($select) {
      if (mysqli_num_rows($select)>0) {
        $row_delete=mysqli_fetch_assoc($select);

        /*SI ES PROPIETARIO DE LA NOTIFICACION O SI EL USUARIO ES ADMIN */
        if ($row_delete['player_id']==$rowu['id'] or $rowu['role']=="Admin") {
          if(!empty($row_delete['imagens']) ){
            $array = json_decode($row_delete['imagens']);
            foreach($array as $key => $Frs){
              unlink($Frs);
            }

          }
          if(!empty($row_delete['imagen']) ){
            $array = json_decode($row_delete['imagen']);
            foreach($array as $key => $Frs){
              unlink($Frs);
            }

          }
          /*BORRAR */
          $delete = mysqli_query($connect, "DELETE FROM `packsenventa` WHERE id=$idtrash");

          /*BORRAR NOTIFICACIONES ENVIADAS ANTERIORMENTE */
          if ($delete)
          {
            $consult=$connect->query("DELETE FROM `players_notifications` WHERE `action`='$idtrash'");
          }
        }
      }
    }
  }

  ?>
  <script src="http:/*ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">



  </script>
  <style type="text/css">
    @media (max-width: 1000px){
      .item button{
        width: 33%;
        margin-top: 5px;
      }
    }
    @media (max-width: 600px){
      .item button{
        width: 50%;
      }
    }
    @media (max-width: 300px){
      .item button{
        width: 100%;
        white-space:unset;
      }
    }
    .items{
      border-radius: 20px;
      box-shadow: 0px 0px 6px -4px black;
    }
    td{
      border: unset !important;
    }
  </style>
  <div class="content-wrapper">

    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">
      <section class="content-header">
        <h1><i class="fas fa-layer-group"></i> Suscripción a Packs</h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box-body">
              <table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                <thead>
                  <tr>
                  </tr>
                </thead>
                <tbody>
                  <h3><div class="loadingpacks">Cargando Packs...</div></h3>
                  <center>
                    <a href="instruccioncompraventapacks.php" class="item"> <button class="btn btn-info"><i class="fas fa-exclamation"></i> Instrucciones</button></a>
                    <?php
                    if ($rowu['gender'] == 'mujer'){
                      Echo $rowu['permission_upload'] == 0 ? '<button class="btn btn-success" data-toggle="modal" data-target="#AlertModal">
                      <i class="fa fa-check"></i> Agregar Pack
                      </button>':'<a href="addpack.php" class="item">
                      <button class="btn btn-success"><i class="fa fa-check"></i> Agregar Pack</button>
                      </a>';
                      ?>

                      <a href="mispacks.php" class="item"> <button class="btn btn-success"><i class="fa fa-image"></i> Mis Packs</button></a>

                      <?php
                    }
                    ?>

                    <a href="mispackscomprados.php" class="item"> <button class="btn btn-success"><i class="fa fa-heart"></i> Suscripciones</button></a>

                    <a href="comprar.php" class="item"> <button class="btn btn-primary"><i class="fas fa-dollar-sign"></i> Comprar Créditos</button></a>

                  </center>
                  <br><br />

                  <div class="card">
                    <div class="card-body">
                      <?php

# DETERMINAR SI HAY QUE FILTRAR LA CONSULTA */

                      $i_p=(isset($_GET['id_profile']) && !empty($_GET['id_profile'])) ? $_GET['id_profile'] : '';

                      $WHERE=(isset($_GET['id_profile']) && !empty($_GET['id_profile'])) ? "WHERE player_id='$i_p'" : "WHERE (f.`id` IS NOT NULL OR pack.`player_id`='$rowu[id]')";

                      $hidden_old = "AND IF(p.`hidden_for_old` != 0 AND p.`id` != '$rowu[id]', p.`hidden_for_old` <= '$rowu[time_joined]', 1=1)";

                      $timeonline = time() - 60;

                      $total_pages = $connect->query("SELECT pack.`id`,pack.`player_id` FROM `packsenventa` as pack INNER JOIN players AS `p` ON p.`id` = pack.`player_id` LEFT JOIN `friends` AS f ON (f.`player1` = pack.`player_id` && f.`player2` = '$rowu[id]') || (f.`player2` = pack.`player_id` && f.`player1` = '$rowu[id]') $WHERE $hidden_old")->num_rows;
                      $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                      $num_results_on_page = 15;
                      $calc_page = ($page - 1) * $num_results_on_page;


# COMPRUEBA SI SE TIENE QUE FILTRAR LA CONSULTA
                      if(isset($_GET['id_profile']) and !empty($_GET['id_profile'])){

# FILTRA LA CONSULTA CON EL ID OBTENIDO
                        $id_profile=$_GET['id_profile'];
                        $querycp = mysqli_query($connect, "SELECT * , pack.`id` AS pack_id FROM `packsenventa` AS pack INNER JOIN players AS `p` ON p.`id` = pack.`player_id` WHERE pack.`player_id`=$id_profile $hidden_old ORDER BY pack.`id` DESC LIMIT {$calc_page}, {$num_results_on_page}");
                        $countcp = mysqli_num_rows($querycp);
                        $queryusername = mysqli_query($connect, "SELECT * FROM `players` WHERE id=$id_profile LIMIT 1");
                        $username = mysqli_fetch_assoc($queryusername);
                        if ($countcp > 0) {
                          echo "<span style='color:#a3adb9'><i class='fa fa-images'></i> Packs de ".$username['username']."</span>";
                        }

/**
* SINO SE OBTUVO RESULTADOS
* SOLO DEVUELVE PACKS DE AMIGOS
**/
else
{
  echo "<span style='color:#a3adb9'><i class='fa fa-info-circle'></i>Este usuario no posee packs</span>";
  $querycp = mysqli_query($connect, "SELECT *, pack.`id` as pack_id FROM `packsenventa` as pack INNER JOIN players AS `p` ON p.`id` = pack.`player_id` LEFT JOIN `friends` AS f ON (f.`player1` = pack.`player_id` && f.`player2` = '$rowu[id]') || (f.`player2` = pack.`player_id` && f.`player1` = '$rowu[id]') $WHERE $hidden_old GROUP BY pack.`id` ORDER BY pack.`id` DESC LIMIT {$calc_page}, {$num_results_on_page}");
}
}
/**
* SINO
* SOLO DEVUELVE PACKS DE AMIGOS
**/
else{
  $querycp = mysqli_query($connect, "SELECT *, pack.`id` as pack_id FROM `packsenventa` as pack INNER JOIN players AS `p` ON p.`id` = pack.`player_id` LEFT JOIN `friends` AS f ON (f.`player1` = pack.`player_id` && f.`player2` = '$rowu[id]') || (f.`player2` = pack.`player_id` && f.`player1` = '$rowu[id]') $WHERE $hidden_old GROUP BY pack.`id` ORDER BY pack.`id` DESC LIMIT {$calc_page}, {$num_results_on_page}");
}

$countcp = mysqli_num_rows($querycp);
if ($countcp > 0) {
  echo "<style>.loadingpacks{display:none;}</style>";
  while ($rowcp = mysqli_fetch_assoc($querycp)) {
    if ($rowcp['visible']!="null" and $rowcp['visible']!="" and $rowcp['visible']!=null and $rowcp['visible']!="[]" ) {
      $json=json_decode($rowcp['visible']);
      if (!in_array($rowu['username'], $json) and $rowu['id']!=$rowcp['player_id'] ) {
        continue;
      }
    }

    $author_id = $rowcp['player_id'];
    $querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
    $rowcpd    = mysqli_fetch_assoc($querycpd);
    $iamfrom = ($rowu['registerfrom'] == 'chat' ? 'chat' : "bellasgram");


    /*SI EL USUARIO TIENE EL PERFIL OCULTO Y YO NO SOY UN USUARIO REGISTRADO DESDE EL CHAT */
    if($rowcpd['perfiloculto']!='no' or $rowcp['hidetochat']=='si' and $iamfrom!='chat')
    {
      /*SI EL USUARIO ES DIFERENTE AL PROPIETARO DEL PACK */
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

    if(!islookContentUser($player_id, $author_id)){
      $total_pages--;
      continue;
    }

    $Image = $rowcp['imagens'] ? json_decode($rowcp['imagens'])[0]:'';

    /** Verifica si el usuario puede ver el pack (Si es dueño o si lo compró)*/
    $canViewPack = canViewPack($rowu['id'], $rowcp['pack_id']);
    /** Enlace directo para ver el pack **/
    $link = 'pack.php?ID='. $rowcp['pack_id'];
    /** Dinero actual del usuario */
    $dolaresdeluser = $rowu['eCreditos'];
    /* Precio del pack */
    $costo = $rowcp['precio'];
    ?>
    <tr>
      <td class="items box">
        <div class="">
          <div class="">
            <img src="<?php echo $sitio['site'].$rowcpd['avatar']; ?>" class="img-circle" style="width:65px;">
            &nbsp;&nbsp;<strong><?php echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpd['id'] . '">' . $rowcpd['username'] . '</a>
            '; if ($rowcpd['timeonline'] > $timeonline) ?> </strong>
            <br>
          </div><br>
          <div class="card-body comment-emoticons">
            <center>
              <div class="" id="" bis_skin_checked="1">
                <!-- Muestra botón de reproducir de ser propietario del pack o si ya esta comprado y si el pack contiene un video -->
                <?php if (($rowcp['video'] != '') AND ($canViewPack OR $rowcp['player_id'] == $rowu['id'])){ ?>
                  <div class="contenedor" onclick="location.href = '<?php echo $link ?>'" bis_skin_checked="1" style="background: url('<?php echo $sitio['site'].$Image;?>'); background-repeat: no-repeat; background-size: contain; background-position: center; position: relative;">
                    <i class="fa fa-play" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);font-size: 40px;background: var(--colorPrimary);color: white;width: 80px;padding: 10px 0;border-radius: 10px;text-align: center;"></i>
                    <img src="<?php echo $sitio['site'].$Image;?>" width="80%" style="opacity: 0;">
                  </div>
                  <br>
                  <?php echo $rowcp['descripcion'];?>


                  <!-- De no estar comprado el pack y si el pack contiene un video, muestra boton Play (Y si se hace click a la imagen, automaticamente se hará click al boton de comprar) -->
                <?php } elseif (($rowcp['video'] != '') AND (!$canViewPack)){ ?>
                  <div class="contenedor" onclick="$('.buy-<?php echo $rowcp['pack_id'] ?>').click()" bis_skin_checked="1" style="background: url('<?php echo $sitio['site'].$Image;?>'); background-repeat: no-repeat; background-size: contain; background-position: center; position: relative;">
                    <i class="fa fa-play" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);font-size: 40px;background: var(--colorPrimary);color: white;width: 80px;padding: 10px 0;border-radius: 10px;text-align: center;"></i>
                    <img src="<?php echo $sitio['site'].$Image;?>" width="80%" style="opacity: 0;">
                  </div>
                  <br>


                  <!-- Si no-->
                <?php }else{ ?>
                  <center>
                    <img src="<?php echo $sitio['site'].$Image;?>" width="80%">
                  </center>
                  <br> <?php
                  echo $rowcp['descripcion'];
                  ?>
                <?php } ?>
              </div>
              <hr />
              <div class="card-footer">
                <b>
                  <span>Precio: <?php echo $rowcp['precio']; ?> Creditos Especiales<br>

                    <!-- SI EL USUARIO INGRESO UNA CANTIDAD DE FOTOS -->
                    <?php if ($rowcp['image_count']>0): ?>

                      <span>Cantidad de Fotos: <?php echo $rowcp['image_count']; ?></span>
                      <br>

                    <?php endif ?>

                    <!-- SI EL USUARIO INGRESO UNA DURACION DEL VIDEO -->
                    <?php if ($rowcp['video_length']>0): ?>
                      <span>Duracion del Video: <?php echo SecondsToHours($rowcp['video_length']); ?></span>

                    <?php endif ?>

                    <form action="" method="post">
                      <input type="hidden" name="galeriaid" value="<?php
                      echo $rowcp['pack_id'];
                      ?>">
                      <?php
                      $querycc = mysqli_query($connect, "SELECT * FROM `packscomprados` WHERE foto_id='$rowcp[pack_id]' AND comprador_id='$rowu[id]'");
                      $countcomprado = mysqli_num_rows($querycc);


                      if ($canViewPack OR $rowcp['player_id'] == $rowu['id'])
                      {
                        echo '<br><a href="' . $link . '" class="btn btn-success float-right btn-buypack buy-'.$rowcp['pack_id'].'"><H5><i class="fa fa-heart"></i> Ir al Pack</H5></a>';
                      }elseif (!$canViewPack && $dolaresdeluser >= $costo){
                        echo '<br/><button type="submit" name="precomprar" class="btn btn-success float-right btn-buypack buy-'.$rowcp['pack_id'].'">
                        <H4>Comprar</H4></button>';
                      }elseif (!$canViewPack && $dolaresdeluser < $costo){
                        echo '<br/><a name="precomprar" class="btn btn-success float-right no-creditsPack btn-buypack buy-'.$rowcp['pack_id'].'">
                        <H4>Comprar</H4></a>';
                      }
                      ?>
                      <div class="top_right">
                        <?php if ($rowu['role'] == 'Admin'): ?>
                          <a href="#" onclick="sendNotifications('<?php echo $rowcpd["id"]?>');" class="btn btn-success header-menu" href="#"><i class="fa fa-bell"></i></a>
                        <?php endif ?>
                        <?php if($rowu['role']=="Admin"): ?>
                          <a href="#" onclick="toAskDelete('<?php  echo $sitio["site"] ?>packs.php?trash_id=<?php echo $rowcp['pack_id']?>');" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        <?php endif ?>

                      </div>


                    </form>
                  </div>
                </div>
              </b>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td><br></td>
      </tr>

      <!---->

      <!--====  FIN DEL PACK ====-->

      <?php
    }
    ?>
  </div>
</div>
</tbody>
</table>

<?php
if (ceil($total_pages / $num_results_on_page) > 0): ?>
  <ul class="pagination">
    <?php if ($page > 1): ?>
      <li class="prev"><a href="packs.php?page=<?php echo $page-1 ?>&id_profile=<?php echo $i_p; ?>">Anterior</a></li>
    <?php endif; ?>

    <?php if ($page > 3): ?>
      <li class="start"><a href="packs.php?page=1&id_profile=<?php echo $i_p; ?>">1</a></li>
      <li class="dots">...</li>
    <?php endif; ?>

    <?php if ($page-2 > 0): ?><li class="page"><a href="packs.php?page=<?php echo $page-2 ?>&id_profile=<?php echo $i_p; ?>"><?php echo $page-2 ?></a></li><?php endif; ?>

    <?php if ($page-1 > 0): ?><li class="page"><a href="packs.php?page=<?php echo $page-1 ?>&id_profile=<?php echo $i_p; ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

    <li class="currentpage"><a href="packs.php?page=<?php echo $page ?>&id_profile=<?php echo $i_p; ?>"><?php echo $page ?></a></li>

    <?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="packs.php?page=<?php echo $page+1 ?>&id_profile=<?php echo $i_p; ?>"><?php echo $page+1 ?></a></li><?php endif; ?>

    <?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="packs.php?page=<?php echo $page+2 ?>&id_profile=<?php echo $i_p; ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

    <?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
      <li class="dots">...</li>
      <li class="end"><a href="packs.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>&id_profile=<?php echo $i_p; ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
    <?php endif; ?>

    <?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
      <li class="next"><a href="packs.php?page=<?php echo $page+1 ?>&id_profile=<?php echo $i_p; ?>">Siguiente</a></li>
    <?php endif; ?>

  </ul>
<?php endif; ?>
<?php
} else {
  echo '<div class="alert alert-success"><strong><i class="fa fa-info-circle"></i> Actualmente no hay packs en venta</strong></div>';
# ocultar loading
  echo "<style>.loadingpacks{display:none;}</style>";
}

?>



<br>

</div>
</div>
</div>

</div>

</div>
<!--===================================================-->
<!--End page content-->



<?php
if ($rowu['permission_upload'] == 0){
  ?>
  <div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Nueva Foto <b>No fotos normales que se usan en instagram, no GIF, no fotos ya usadas en BellasGram</b>    </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div style="text-align: center;font-weight: 600;color: gray;font-size: 15px;">
            Hola, no puedes subir packs, primero debes verificar tu cuenta para confirmar ser quien dices ser. <br/>
            Se responderá por Email<br/>
            <a href="https:/*bellasgram.com/index.php?app=site&section=contact">Solicitar verificación</a><br/> Esta nueva medida 1-1-2021 permitirá solo subir fotos a mujeres verificadas.
          </div>
        </div>
        <div class="modal-footer">
          <div type="button" class="btn btn-secondary" style="background:#dddddd;" data-dismiss="modal" aria-label="Close">
            Ok
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>
</div>
<!--===================================================-->
<!--END CONTENT CONTAINER-->

<script type="text/javascript">
  $(document).ready(function() {
    /*history.pushState({data:true}, "Titulo", "packs.php"); */
  });
  function toAskDelete(href) {
    event.preventDefault();
    swal.fire({
      title: "Estás seguro que quieres eliminar este Pack?",
      text: "No podras recuperar este pack!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: `Si, Borralo!`,
    }).then(function(value) {
      var form = $('#miFormulario');
      if (value.isConfirmed) window.location.href = href;
    });

  }

  /* PREGUNTAR SI ENVIA NOTIFICACIONES */
  function sendNotifications(idUser) {
    event.preventDefault();
    swal.fire({
      title: "Confirmar envío de notificaciones",
      text: "Se enviaran una notificación de este Pack a todos los usuarios de la lista namesActions",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: `Si, Enviar!`,
    }).then(function(value) {
      if (value.isConfirmed) {
        $.post('ajax.php',{ sendNotifications: "", idUser: idUser }, function(response) {
          success: {
            response = $.parseJSON(response);
            if (response.state) {
              swal.fire(response.message, 'Notificaciones enviadas: ' + response.countsend, 'success');
            }
            else{
              swal.fire(response.message, '', 'error');
            }
          }
        });
      }
    });
  }
</script>
<?php
footer();
?>
