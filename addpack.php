<?php
require("core.php");

if($rowu['permission_upload'] == 0){
  echo '<meta http-equiv="refresh" content="0; url=packs.php" />';
  exit;
}

if ($rowu['gender'] == 'hombre'){
  echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
  exit;
}

head();

if (isset($_POST['save'])){
    // PERSONAS A QUIEN MOSTRAR PACKS
  $json = $_POST['json'];
    // DIRECCION DEL VIDEO (Si existe)
  $dirPackVideo = '';
  $precio = $_POST['precio'];
    // CANTIDAD DE IMAGENES(si existe)
  $countImages = isset($_POST['countImages']) ? $_POST['countImages'] : 0;
    // MINUTOS DE VIDEO (si existe)
  $minutes = isset($_POST['minutes']) && is_numeric($_POST['minutes']) ? $_POST['minutes'] * 60 : 0;
    // SEGUNDOS DE VIDEO (si existe)
  $seconds = isset($_POST['seconds']) && is_numeric($_POST['seconds']) ? $_POST['seconds'] : 0;
    // // TOTAL DE TIEMPO DE VIDEO
  $total_time=($minutes + $seconds);

  if ($precio >= 0){

    $descripcion = (isset($_POST['descripcion']) AND !empty($_POST['descripcion']))? $_POST['descripcion'] : '';
    //$link = $_POST['linkdedescarga'];
    $Images = [];

    // SI EXISTE ALGUNA IMAGEN, MOVERLA AL DIRECTORIO
    if($_FILES["files"]) {
      foreach($_FILES["files"]["name"] as $key => $FILE){

        $token = generateUUID(15);
        $target_dir    = "images/packs/";
        $target_file   = $target_dir . basename($_FILES["files"]["name"][$key]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $filename      = $uname. '-' .$token  . '.' . $imageFileType;
        $imagen        = "images/packs/" . $filename;

        // COMPRUEBA QUE ES UNA IMAGEN Y MUEVELA AL DIRECTORIO
        if(in_array($imageFileType,array('png','gif','jpg','jpeg')) AND move_uploaded_file($_FILES["files"]["tmp_name"][$key], "images/packs/" . $filename))
        {
          $Images[] = $imagen;
        }
        else
        {
        }
      }
    }
    // SI EXISTE UN VIDEO, MOVERLO AL DIRECTORIO (DEBE EXISTIR AL MENOS UNA IMAGEN)
    if(isset($_FILES["packVideo"]) AND $_FILES["packVideo"]['name'] != "" AND $_FILES["files"])
    {
      // VIDEO
      $packVideo = $_FILES['packVideo'];
      $token = generateUUID(15);
      $target_dir    = "uploads/packs/videos/";
      $target_file   = $target_dir . basename($packVideo["name"]);
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
      $filename      = $token . $uname . '.' . $imageFileType;
      $dirMain        = $target_dir . $filename;

      // COMPRUEBA QUE ES UN VIDEO Y MUEVELO AL DIRECTORIO PRINCIPAL
      if(in_array($imageFileType,array('mp4','3gp','vid','avi')))
      {
        if(move_uploaded_file($packVideo["tmp_name"], $target_dir . $filename)){
          $dirPackVideo = $dirMain;
        }
        else
        {
          setSwal(array('Se ha producido un error.','El video no se ha podido subir, Porfavor comprueba tu conexión o intenta mas tarde.','info'));
        //echo '<meta http-equiv="refresh" content="3; url=addpack.php" />';
        }
      }
      else
      {
        setSwal(array('El formato de este video no es compatible.','Formatos compatibles: <b>MP4, 3GP, VID, AVI','info'));
      }
    }
    else
    {
    }
    // SI EL PACK TIENE UN LINK, COMPRUEBA QUE EL LINK SEA CORRECTO
    if(isset($_POST['link']) and !empty($_POST['link'])){
      $link=$_POST['link'];
      $viewlink=$_POST['link'];
      $link=filtrourl($link);
      if ($link !="error") {
      } else {
        echo("<script>alert('\"$viewlink\" no es una URL valida');</script>");
        echo '<meta http-equiv="refresh" content="0; url=addpack.php" />';
        exit;
      }
      $link=mysqli_real_escape_string($connect,$link);
    }else{
      $link=" ";
    }
    // CODIFICA LAS IMAGENES A JSON
    $jsonImages = json_encode($Images);

    if($rowu['gender']=='mujer'){
      $hidetochat = $_POST['hidetochat'];
    }else{
      $hidetochat = 'no';
    }
    // GUARDAR PACK
    $insertarcompra = mysqli_query($connect, "INSERT INTO `packsenventa` (`player_id`, `imagens`, `video`, `image_count`, `video_length`, `precio`, `descripcion`, `hidetochat`, `linkdedescarga`, `visible`) VALUES
      (\"". $connect->real_escape_string($rowu['id']) ."\", '$jsonImages', \"". $connect->real_escape_string($dirPackVideo) ."\", '$countImages' , '$total_time' , \"". $connect->real_escape_string($precio) ."\", \"". $connect->real_escape_string($descripcion) ."\",\"". $connect->real_escape_string($hidetochat) ."\", \"". $connect->real_escape_string($link) ."\", \"". $connect->real_escape_string($json) ."\")");
    // ID DEL PACK
      $idPack = mysqli_insert_id($connect);
    // ENVIAR NOTIFICACION DEL PACK PARA LAS PERSONAS SELECCIONADAS
      if($json != '') Notificacion_pack($idPack, $json);
    // ENVIAR NOTIFICACION DEL PACK PARA TODOS
      else Notificacion_pack($idPack);

      echo '<meta http-equiv="refresh" content="3; url=addpack.php" />';
    //mostramos mensaje de exito y link de descarga / cambie precio en linea 20 y linea 199
    /*echo '
    <script type="text/javascript">
    $(document).ready(function() {
      $("#sell-vehicle").modal(\'show\');
      });
      </script>

      <div id="sell-vehicle" class="modal fade">
      <div class="modal-dialog modal-md">
      <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title">Nueva Publicacion</h5>
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">
      <center>

      <h4><span class="badge badge-info">Venta Publicada!</span></h5>
      <a href="packs.php"> <button class="btn btn-success">Ver mi Venta</button></a>
      <br/><br/>

      <button type="button" class="btn btn-success btn-md btn-block" data-dismiss="modal" aria-hidden="true"><i class="fab fa-get-pocket"></i> Ok</button>
      </center>
      </div>
      </div>
      </div>
      </div>'
    }else{


    //mensaje de minimo 100 creditos

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
    <h5 class="modal-title">Nueva Publicacion</h5>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
    <div class="modal-body">
    <center>
    <h4><span class="badge badge-info">Venta no Publicada!, el precio minimo es de 10 credito</span></h5>




    <button type="button" class="btn btn-success btn-md btn-block" data-dismiss="modal" aria-hidden="true"><i class="fab fa-get-pocket"></i> OK</button>
    </center>
    </div>
    </div>
    </div>
    </div>';*/


  }
}
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">



</script>
<div class="content-wrapper">

  <!--CONTENT CONTAINER-->
  <!--===================================================-->
  <div id="content-container">
    <section class="content-header">
      <h1><i class="fas fa-layer-group"></i> Vender</h1>
    </section>
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
                      Agrega foto, descripción mencionado cuantas fotos incluye tu pack y el precio, no usar acortadores con anuncios.
                    </p>
                  </center>
                </tbody>
              </table>
            </div>
            <form id="uploadPack" action="" method="post" class="form-horizontal" enctype="multipart/form-data">
              <div class="card">
                <div class="card-body">
                  <div class="form-group">
                    <label class="col-md-3 control-label" for="inputDefault">Descripción:</label>
                    <div class="col-md-8">
                      <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input autocomplete="off" type="text" class="form-control" name="descripcion" placeholder="Descripcion y poner la url en base de datos">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label" for="inputDefault">Imagenes:</label>
                    <div class="col-md-8">
                      <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input id="inputImages" type="file" class="form-control" name="files[]" accept="image/*" multiple>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label" for="inputDefault">V&iacute;deo opcional:&nbsp;&nbsp;<small>Opcionalmente puedes subir un v&iacute;deo</small></label>
                    <div class="col-md-8">
                      <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input id="inputVideo" type="file" class="form-control" name="packVideo" accept="video/*">
                      </div>
                    </div>
                  </div>
                  <div class="row input-group-addon">
                    <div class="col-xs-4"></div>
                    <div class="col-xs-2">
                      <a class="btn btn-primary" data-toggle="modal" data-target="#modalimagen">Fotos</a>
                    </div>
                    <div class="col-xs-2">
                      <a class="btn btn-primary" data-toggle="modal" data-target="#modalvideo">V&iacute;deo</a>
                    </div>
                    <div class="col-xs-4"></div>
                  </div>

                  <div class="form-group">
                    <label class="col-md-3 control-label" for="inputDefault">Precio m&iacute;nimo 10 Cr&eacute;ditos:</label>
                    <div class="col-md-8">
                      <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input type="number" min="0" class="form-control" name="precio" placeholder="Precio" required>

                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label" for="inputDefault">Link al sitio</label>
                    <div class="col-md-8">
                      <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input type="text" min="0" class="form-control" name="link" placeholder="Link al sitio de descarga(Dejar en blanco si estas subiendo a este servidor)" >

                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label" for="description" style="width: 80%;"><i class="fas fa-eye"></i>Pack solo visible a quienes se registraron desde el Chat
                      <select name="hidetochat">
                        <?php
                        if ($rowu['hidetochat'] == 'no'){

                          $hideoption1 = 'no';
                          $hideoption2 = 'si';

                        }else{

                          $hideoption1 = 'si';
                          $hideoption2 = 'no';

                        }
                        ?>
                        <option value="<?php echo $hideoption1; ?>"><?php echo $hideoption1; ?></option>
                        <option value="<?php echo $hideoption2; ?>"><?php echo $hideoption2; ?></option>
                      </select>
                    </label>

                    <div class="panel-footer text-left">
                      <input class="btn btn-flat btn-success" name="save" type="submit" value="Enviar">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label" for="description"><span>Pack solo visible para:</span>
                    </label>
                    <div class="col-md-8">
                      <div class="input-group">
                        <span class="input-group-addon"></span>
                        <input type="text" min="0" class="form-control" id="aggtags" placeholder="Puedes dejar en blanco" >
                        <input type="text" name="json" id="json" hidden="">
                      </div>
                      <br>
                      <div class="container container-tags input-group">

                      </div>
                      <br>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- MODAL VIDEO -->
  <div class="modal fade" id="modalvideo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Opcionalmente puedes agregar la duraci&oacute;n del V&iacute;deo<br></h5>

          <h6 class="modal-title" id="exampleModalLabel">Puedes dejar la casilla de &quot;Minutos&quot; vac&iacute;a si tu v&iacute;deo no llega a durar mas de 1 minuto<br></h6>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div>
            <input type="number" class="form-control" id="minutes" name="minutes" placeholder="Minutos" style="height:100px!important;margin-bottom:20px;"/>
            <input type="number" class="form-control" id="seconds" name="seconds" placeholder="Segundos" style="height:100px!important;margin-bottom:20px;"/>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL IMAGENES -->
  <div class="modal fade" id="modalimagen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Opcionalmente puedes agregar la cantidad de im&aacute;genes que contenga tu Pack<br></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div>
            <input type="number" class="form-control" id="countImages" name="countImages" placeholder="Numero de Imagenes" style="height:100px!important;margin-bottom:20px;"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
  let id;
  var data = [];
  var file;
  var arrs = [];
  $(document).ready(function() {
            //AGREGAR TAGS
            //DETECTAR SI DEJA DE ESCRIBIR
    if (id==null) {id=0;}
    document.getElementById("aggtags").addEventListener('keypress', function (e) {
      if(e.keyCode == 188 || e.code=="Comma") {
              //NO PERMITIR HACER TAG SI NO SE HA ESCRITO NADA
        if( $("#aggtags").val().length>0 ){

                //ENVIAR AJAX
          datos = {"get_user":$("#aggtags").val()};
          file = {username: "mnb"};
          $.ajax({
            url: "ajax.php",
            type: "POST",
            data: datos
          }).done(function(response){
            var response = $.parseJSON(response);
            console.log(response.state);
            if (response.state=="ok") {
                    //COMPROBAR QUE NO EXISTA EL USUARIO
              if (finduser(data,response.username)==false) {
                    //AGREGAR TAG
                username='"'+response.username+'"';
                $(".container-tags").append("<div id='tag"+id+"' style='' class='s-tag col-lg-12 input-group'> <span class='' title='ID : "+response.id+"'><img src='"+response.avatar+"' width='34px' height='34px'></img>&nbsp;"+ $("#aggtags").val() + "&nbsp;&nbsp; <i class='fa fa-window-close' onclick='deletetag("+ id +", "+ username + ");'></i></span>&nbsp;&nbsp;</div>");
                      //VACIAR INPUT
                $("#aggtags").val("");
                file = {username: response.username};
                data.push(file);

                      //MANTENER ACTUALIZADO EL INPUT JSON
                arrs=[];
                for( var i in data){
                  arrs.push(data[i].username);
                }
                $("#json").val(JSON.stringify(arrs));

                      //CAMBIAR ID
                id=id+192;

                      //NO PERMITIR PRECIONAR "SPACE, COMMA"
                e.preventDefault();
                response=null;
              }

                    //SI EL USUARIO YA EXISTE
              else{
                $("#aggtags").val("");
                e.preventDefault();
              }
            }else{
              $("#aggtags").val("");
              e.preventDefault();
            }
          });

        }else{
          e.preventDefault();
        }
      }
    });
  });
  function deletetag(id,username){
    $("#tag"+id).remove();
    eliminarPorName(username);
    arrs=[];
    for( var i in data){
      arrs.push(data[i].username);
    }
    $("#json").val(JSON.stringify(arrs));
  }
  function finduser(array,user){
    var arr = [];
    for( var i in array){
      arr.push(array[i].username);
    }
    return arr.indexOf(user) > -1;
  }
  function eliminarPorName(username){
    for (var i = 0; i < data.length; i++) {
      if (data[i].username == username) {
        data.splice(i, 1);
        break;
      }
    }
  }

            // NO PERMITIR SUBIR VIDEO SIN IMAGEN
  $('#uploadPack').submit(function(e){
    if($('#inputVideo').get(0).files.length !== 0 && $('#inputImages').get(0).files.length == 0){
      swal.fire('No puedes subir un video sin una imagen. Sube una imagen.','La imagen se usara como portada para el video.','info');
      return false;
    }
  });
</script>
<?php
footer();
?>
<style type="text/css">
  .s-tag{
    display: inline-table;
    align-content: center;
    justify-content: center;
    min-width: 0;
    overflow: hidden;
    margin: 1px;
    padding-left: 4px;
    padding-right: 4px;
    border-style: solid;
    border-width: 1px;
    border-radius: 22px;
    font-size: 12px;
    line-height: 1.84615385;
    text-decoration: none;
    vertical-align: middle;
    border-color: transparent;
    background-color: #e1ecf4;
    color: #39739d;
    box-shadow: 1px 1px 2px -1px white;


  }
  .container-tags{
    border-radius: 22px;
    padding: 10px;
    overflow-wrap: break-word;
    width: 80%;
    background-image: repeating-conic-gradient(#3c3d52  0% 25%, #659fb1  0% 50%);
    background-position: 0 0, 46px 46px;
    /*background-image: radial-gradient(circle at 50% -20.71%, #ffcb27 0, #ffb92d 12.5%, #ffa534 25%, #ff9039 37.5%, #f2793c 50%, #df643e 62.5%, #ce5240 75%, #be4343 87.5%, #b13846 100%);*/

  }
</style>
