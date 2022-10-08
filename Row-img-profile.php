<?php
$cw = 4;
$class = 'col-6';
// SI ESTOY EN PC
if (its_in()=='in_pc')
{
  $class = 'col-xs-4'; // COLOCA LAS FILAS DE 4
  ?><style type="text/css">.row-image{height: 50vh;}</style><?php
  $cw = 4;
}
else
{
 $cw = 3;
}
//-----
$linkGalery = (isLogged()) ? $sitio['site'] . 'foto.php?fotoID=' . $rowcp['id'] : $sitio['site'] . 'index.php';
$linkDownload = (isLogged()) ? $rowcp["linkdedescarga"] : $sitio['site'] . 'index.php';
?>

<!-- PERFIL RECOMENDADO -->
<?php if ($countWhile == $cw): ?>

  <?php if ($recommendation AND $recommendation->num_rows > 0 AND isLogged()): ?>
    <?php

      $r = mysqli_fetch_assoc($recommendation);

      $profileRecommend = getUser($r['toid']);

      // SI NO SOY AMIGO DE LA PERSONA RECOMENDADA O SI ESTOY EN MI PERFIL O SI LA PERSONA RECOMENDADA SOY YO
      if ($profileRecommend AND !areFriends($rowu['id'], $r['toid']) OR getRecommendations($rowu['id'], $r['toid'])->num_rows > 0 OR $r['id'] == $rowu['id']):

        $pr = $profileRecommend->fetch_assoc();

        // SI EL USUARIO NO TIENE EL PERFIL OCULTO
        if($pr['perfiloculto']=='no' AND ($pr['hidetochat'] == 'no' OR $pr['hidetochat'] == 'si' AND $iamfrom =='chat') OR $pr['id'] == $rowu['id']):?>

          <div class="col no-padding col-xs-12" onclick="location.href = '<?php echo createLink('profile','',array('profile_id' => $pr['id']),true); ?>'">
            <br>
            <h5>También puedes seguir a <?php echo createLink('profile',$pr['username'],array('profile_id' => $pr['id'])); ?></h5>
            <div class="img-thumbnail" style="border-radius: 16px;">
              <img src="<?php Echo $sitio['site'] . $pr['avatar']; ?>" style="border-radius: 16px;">
            </div>
            <br><br>
          </div><br><br>

        <?php endif ?>
      <?php endif ?>
    <?php endif ?>
  <?php endif ?>
<!---->

<div class="col no-padding <?php echo $class; ?>" <?php Echo getSourceType($image[0]) == 'film' ? 'id="VC-VIDEO-TEMP-'. $rowcp['id'] .'"':'';?>>
  <!--SI EL POST SOLO ES UN ENLACE EXTERNO, COLOCAR EN VEZ DE LA IMAGEN, UN DIV GRANDE QUE AL TOCARLO SE DIRIGA AL LINK EXTERNO;-->
  <?php if($rowcp["linkdedescarga"]!=" " and $rowcp["linkdedescarga"]!=null){  ?>
    <div class="row-image <?php Echo getSourceType($image[0]) == 'film' ? 'videoPreview':'';?>"        
      onclick="window.location.href=`<?php Echo $linkDownload;?>`" style="overflow:hidden;">
      <div style="background-image: url(<?php Echo $sitio['site'] . ($thumbnail[0] ? strdr($thumbnail[0]):strdr($image[0])); ?>); background-size:cover; width:100%;height:100%;overflow:hidden;" class="<?php echo $sub; ?>"></div>
      <!--TAMBIEN COLOCAR UN BOTON DE LINK PARA PODER REDIRIGIRSE A LA PUBLICACION PARA OTRAS OPCIONES, COMO ELIMINARLA-->
      <div style="position: absolute;top: 0;right: 0;">
        <a href="<?php echo $linkGalery; ?>">
          <button class="btn btn-primary" data-toggle="modal" style="">
            <i class="fa fa-link"></i>                            
          </button>
        </a>
      </div>
      <!---SI NO, COLCOAR LA IMAGEN NORMAL-->
    <?php }else{ ?>
      <div class="row-image <?php Echo getSourceType($image[0]) == 'film' ? 'videoPreview':'';?>"        
        onclick="window.location.href=`<?php Echo $linkGalery; ?>`" style="overflow:hidden;">
        <div style="background-image: url(<?php Echo $sitio['site'] . ($thumbnail[0] ? strdr($thumbnail[0]):strdr($image[0])); ?>); background-size:cover; width:100%;height:100%;overflow:hidden;" class="<?php echo $sub; ?>"></div>
      <?php } ?>
      <?php if($rowu['role']=="Admin"){ ?>
        <div style="position: absolute;top: 0;right: 40px;">
          <a href="<?php Echo basename($_SERVER['PHP_SELF']) .'?trash_id='.$rowcp["id"]?>">
            <button class="btn btn-danger" data-toggle="modal" style="">
              <i class="fa fa-trash"></i>                            
            </button>
          </a>
        </div>
      <?php } ?>
      <!--LINK DE DESCARGA-->
      
      <!--SI ES FOTO VIP-->
      <?php if($rowcp['type'] == 'suscripciones' and $sub!="" and getSourceType($image[0])!='film' and count($image)<=1 ) {  ?>
        <div class="btn-centered">
          <a href="<?php Echo $linkGalery; ?>">
            <button class="btn btn-success btn-info-content2" data-toggle="modal" style="white-space:normal;font-size:14px;">
              Ver Foto                           
            </button>
          </a>
          <br><br>   
        </div>    
        <!--SI ES LINK EXTERNO--> 
      <?php }else if(getSourceType($image[0])!='film' and $rowcp["linkdedescarga"]!=" " and $rowcp["linkdedescarga"]!=null and count($image)<=1 ){  ?>
        <div class="btn-centered">
          <a href="<?php Echo $linkDownload;?>">
            <button class="btn btn-success btn-info-content2" data-toggle="modal" style="white-space:normal;">
              Ver contenido fuera de BellasGram                            
            </button>
          </a>
          <br><br>
        </div>       
      <?php }
      // SI ES UNA GALERIA DE IMAGENES
      else if(count($image)>1) {  ?>
        <div class="btn-centered">
          <a href="<?php echo $linkGalery; ?>">
            <button class="btn btn-warning btn-info-content2" data-toggle="modal" style="white-space:normal;font-size:14px;">Ver Galeria</button>
          </a>
          <br><br>   
        </div>    
<?php } ?>
<!--MOSTRAR ESTRELLA-->
<?php 
if($rowcp['type'] == 'suscripciones'){  
  ?>
  <div class="extencion" style="color: unset;font-size: unset;width:27px;height: 30px;">
    <i class="fa fa-star" style="font-size:25px;color:#f39c12;vertical-align:center; line-height: 1.3;"></i>
  </div>
  <?php if ($sub!=""): ?>
    <div class="extencion" style="left:0;color: unset;font-size: unset;width:27px;height: 30px;">
      <i class="" style="font-size:25px;color:#f39c12;vertical-align:center; line-height: 1.3;background-image: url(<?php Echo $sitio['site'] . "assets/img/candado.png"; ?>);"><img src="https://bellasgram.com/chat/assets/img/candado.png" style="width: 100%;margin-top: -8px;"></i>
    </div>
  <?php endif ?>
  <?php 
}else{
  ?>
  <div class="extencion">
    <i class="fa fa-<?php Echo getSourceType($image[0]); ?>"></i>
  </div>
<?php } ?>
<?php if (count($image)>1): ?>
  <div style="width:100%;position: absolute;bottom: 5px;">
    <span class="extencionGalery"><?php echo count($image); ?> Fotos</span>
  </div>
<?php endif ?>
<?php 
if(getSourceType($image[0]) == 'film'){
  Echo '<video src="'. $sitio['site'] . $image[0] .'" 
  id="VIDEO-TEMP-'. $rowcp['id'] .'" 
  style="opacity: 0;" 
  type="video/mp4"></video>
  <div class="btn-centered">
  <button class="btn btn-success btn-info-content3" data-toggle="modal" style="" onclick="window.location.href=<?php Echo $sitio["site"] ."foto.php?fotoID=". $rowcp["id"]; ?>
  VER VIDEO                             
  </button>
  </div>
  <script>
  $(document).ready(() => {
    setTimeout(() => {
      getThumb("VIDEO-TEMP-'. $rowcp['id'] .'")
      }, 1500)
      })
      </script>';
    }
    ?>
    <?php
    if(isset($RandImage)) {
      Echo '<div class="imageRandom">Post Aleatorio</div>';

    }
    ?>
  </div>
</div>
<style>
  @media (max-width: 460px){
    .btn-info-content{
      transform: scale(0.5);
      top: -14vw;
      font-size: 12px;
    }
  }
  @media (max-width: 295px) {
    .btn-info-content{
      font-size: 10px;
      top: -7vw;
      width: 100%;
      display: none;
    }
    .btn-info-content2, .btn-info-content3{
      font-size: 6px;
    }

  }
</style>

<?php 
//ELIMINAR PACK
if (isset($_GET['trash_id']) and !empty($_GET['trash_id']) ) {
  $idtrash = mysqli_real_escape_string($connect,$_GET['trash_id']);
  $select = mysqli_query($connect,"SELECT id,player_id,imagen FROM `fotosenventa` WHERE id=$idtrash");
  if ($select) {
    if (mysqli_num_rows($select)>0) {
      $row_delete=mysqli_fetch_assoc($select);

//SI ES PROPIETARIO DE LA NOTIFICACION O SI EL USUARIO ES ADMIN
      if ($row_delete['player_id']==$rowu['id'] or $rowu['role']=="Admin") {
//BORRAR
        $imagenJSON=json_decode($row_delete['imagen']);
        unlinkJSON($imagenJSON);
        $thumbJSON=json_decode($row_delete['thumb']);
        unlinkJSON($thumbJSON);
        $delete = mysqli_query($connect, "DELETE FROM `fotosenventa` WHERE id=$idtrash");
        echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'galerias.php" />';
      }
    }
  }
}
?>
