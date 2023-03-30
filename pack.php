<?php
require("core.php");
head();

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname' LIMIT 1");
$rowu  = mysqli_fetch_assoc($suser);

if (!isset($_GET['ID']) && !is_numeric($_GET['ID'])){
	echo '<meta http-equiv="refresh" content="0; url=packs.php" />';
  exit;
}

$ID = $_GET['ID'];
$UID = $rowu['id'];

$queryccc = mysqli_query($connect, "SELECT * FROM `packscomprados` WHERE foto_id='$ID' AND comprador_id='$UID'");
$countcompradoc = mysqli_num_rows($queryccc);
//SELECCIONAR EL LINK DEL PACK
$querycp = mysqli_query($connect, "SELECT player_id,linkdedescarga FROM `packsenventa` WHERE id='$ID'");
$countcp = mysqli_num_rows($querycp);
if ($countcp > 0) {
  $link = mysqli_fetch_assoc($querycp);
}

// DE NO HABER COMPRADO EL PACK Y NO SER EL DUEÑO
if (!$countcompradoc AND $rowu['id']!=$link['player_id']){
	echo '<meta http-equiv="refresh" content="0; url=packs.php" />';
  exit;
}

?>
<div class="content-wrapper">
  <!--CONTENT CONTAINER-->
  <!--===================================================-->
  <div id="content-container">
    <section class="content-header">
      <h1><i class="fas fa-layer-group"></i> Pack</h1>
    </section>
    <!--Page content-->
    <!--===================================================-->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-body">
              <div class="card">
                <div class="card-body">
                  <div>
                    <?php if($link['linkdedescarga']!=" " and $link['linkdedescarga']!="" and $link['linkdedescarga']!=null and !empty($link['linkdedescarga'])){ ?>
                      <a href="<?php echo $link['linkdedescarga']; ?>"><br/><br/><button class="btn btn-success"><H3>IR AL VIDEO</H3></button> </a><br/><br/><br/><br/>
                    <?php } ?>
                  </div>
                  <?php

                  $querycp = mysqli_query($connect, "SELECT * FROM `packsenventa` WHERE id='$ID'");
                  $countcp = mysqli_num_rows($querycp);
                  if ($countcp > 0) {
                    $rowcp = mysqli_fetch_assoc($querycp);
                    $Images = json_decode($rowcp['imagens']);

                    foreach ($Images as $key => $image) {
                      ?>
                      <div class="card">
                        <div class="card-body comment-emoticons p-r">
                          <center>
                            <img data-src="<?php echo $sitio['site'].$image;?>" width="100%" class="lozad item-zoom">
                            <div class="ghostSdG"></div>
                          </center>
                        </div>
                        <hr />
                      </div>
                      <?php
                    }
                  }
                  ?>
                  <!-- VIDEO -->
                  <?php if ($rowcp['video'] != ''): ?>
                    <div>
                      <?php echo getSource($rowcp['video'],'videoContainer'); ?>
                    </div>
                  <?php endif ?>
                  <!--- SI HAY UN LINK A UN VIDEO --->
                  <?php if($link['linkdedescarga']!=" " and $link['linkdedescarga']!="" and $link['linkdedescarga']!=null and !empty($link['linkdedescarga'])){ ?>
                    <div>
                      <a href="<?php echo $link['linkdedescarga']; ?>"><button class="btn btn-success"><H3>IR AL VIDEO</H3></button> </a>
                    </div>
                  <?php } ?>
                </div>
              </div>
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
         <a href="https://bellasgram.com/index.php?app=site&section=contact">Solicitar verificación</a><br/> Esta nueva medida 1-1-2021 permitirá solo subir fotos a mujeres verificadas.
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
<?php
footer();
?>
