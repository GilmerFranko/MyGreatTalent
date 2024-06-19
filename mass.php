<?php
require("core.php");

if ($rowu['gender'] == 'hombre' AND $rowu['role'] != 'Admin'){
  echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
  exit;
}


if (isset($_GET['delete'])){
  mysqli_query($connect, "DELETE FROM `mensajesprogramados` WHERE id=". $_GET['delete']);
  echo '<meta http-equiv="refresh" content="0; url=mass.php" />';
  exit;
}

if (isset($_GET['edit']) && isset($_POST['descripcion']) && isset($_POST['id'])){
  $id = $_POST['id'];
  $message = $_POST['descripcion'];

  if($_POST['dias']>0 || $_POST['horas']>0 || $_POST['minutos']>0){
    $date = time();
    if($_POST['dias']>0){
      $d = $_POST['dias'] * (60*60*24);
      $date = $date + $d;
    }
    if($_POST['horas']>0){
      $d = $_POST['horas'] * (60*60);
      $date = $date + $d;
    }
    if($_POST['minutos']>0){
      $d = $_POST['minutos'] * (60);
      $date = $date + $d;
    }
  }

  $time = isset($date) ? ", time='{$date}'":'';

  $sql = "UPDATE `mensajesprogramados` SET message='{$message}'{$time} WHERE id='{$id}'";

  mysqli_query($connect, $sql);
  var_dump($sql);
  exit;
}
head();

?>
<style>
  .itemsder {
    display: flex;
    position: relative;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #31393c;
  }
  .itemsder .csdtcover {
    width: 50px;
    height: 50px;
    border-radius: 7px;
    background-color: gray;
    margin-right: 10px;
    background-size: cover;
    background-position: center;
  }
  .itemsder .time {
    position: absolute;
    right: 15px;
    bottom: 10px;
    font-size: 12px;
  }
  .btn.delete {
    background-color: #ff0047;
    color: white;
    margin-right: 10px;
  }
  .btn.edit {
    background-color: #05963d;
    color: white;
    margin-right: 10px;
  }
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<div class="content-wrapper">

  <!--CONTENT CONTAINER-->
  <!--===================================================-->
  <div id="content-container">

    <section class="content-header">
      <h1><i class="fas fa-layer-group"></i> Mensaje Masivo</h1>
    </section>
    <br>
        <!--<div class="">
          <div class="row" align="center">
            <a class="btn btn-primary" href="<?php echo createLink('xxevery10minxx','',array(),true); ?>" >Ejecutar Cron</a>
          </div>
        </div>-->
        <!--Page content-->
        <!--===================================================-->
        <section class="content">
          <div class="row">
            <div class="col-md-12">
              <div class="box">
                <div class="box-body">
                  <table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <tbody>
                      <center>
                        <p>
                          Los mensajes masivos se enviarán a todas las conversaciones, para nombrar a las peronas usar -user- ejemplo, Hola -user- comos estas?
                        </p>
                      </center>
                      <button class="btn btn-success" id="AddMessages" style="position:relative;">
                        Agregar Mensaje
                      </button>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <?php
                  $sqlsala = mysqli_query($connect, "SELECT * FROM `mensajesprogramados` WHERE player_id='$player_id'");
                  while ($sala = mysqli_fetch_assoc($sqlsala)) {
                    ?>
                    <div class="col col-md-12">
                      <div class="itemsder" data-massid="<?php Echo $sala['id'];?>">
                        <a class="btn delete" href="?delete=<?php Echo $sala['id'];?>">
                          <i class="fa fa-trash"></i>
                        </a>
                        <a class="btn edit" onclick="EditMessage(this)"
                        data-id="<?php Echo $sala['id'];?>"
                        data-content="<?php Echo $sala['message'];?>"
                        data-time="<?php Echo $sala['time'];?>">
                        <i class="fa fa-pencil-alt"></i>
                      </a>
                      <div class="csdtcover"
                      onclick="openImage(`<?php Echo $sala['rutadefoto'];?>`)"
                      style="background-image: url(<?php Echo $sala['rutadefoto'];?>);"></div>
                      <div class="details"><?php Echo $sala['message'];?></div>
                      <div class="time"><?php Echo TimeNext($sala['time']);?></div>
                    </div>
                  </div>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- MODAL AGREGAR MENSAJES MASIVOS -->
      <div class="modal fade" id="MensajessProgramadosModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Nueva Foto <b>No fotos normales que se usan en instagram, no GIF, no fotos ya usadas en BellasGram</b> </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" style="overflow-y: scroll;overflow-x: hidden;max-height: 72vh;">
              <!-- BOTON AGREGAR IMAGENES -->
              <div align="center">
                <span class="btn btn-primary btn-file float-left sdt-btn">
                  <i class="fas fa-camera"></i>
                  <div>
                    <input id="InputFiles" class="float-left" type="file" multiple>
                  </div>
                </span>
                <form></form>
              </div>
            </div>
            <div class="modal-footer">
              <label for="typeMP">Enviar mensaje a:</label>
              <select id="typeMP" class="form-control">
                <option value="1">Todos</option>
                <option value="2">Usuarios con 5 o mas d&iacute;as de inactividad</option>
                <option value="3">Solo si el destinatario ha respondido</option>
              </select>
              <br>
              <button type="button" class="btn btn-secondary" style="background:#dddddd;" onclick="$('#MensajessProgramadosModal').toggleClass('show')">
                Cancelar
              </button>
              <button type="button" class="btn btn-success" id="addMessage">
                Agregar Mensaje
              </button>
              <button type="button" class="btn btn-success" id="sendForm">
                Enviar
              </button>
            </div>
          </div>
        </div>
      </div>


      <!-- MODAL EDITAR MENSAJES MASIVOS -->
      <div class="modal fade" id="EditMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Nueva Foto <b>No fotos normales que se usan en instagram, no GIF, no fotos ya usadas en BellasGram</b>    </h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" style="overflow-y: scroll;overflow-x: hidden;max-height: 72vh;">
              <form method="POST" action="./mass.php?edit">
                <input type="hidden" name="id">
                <div>
                  <div><textarea class="form-control" name="descripcion" placeholder="Descripcion (opcional)" style="height:100px!important;margin-bottom:20px;"></textarea></div><br><b>Programa cuando quieres que se publique la foto</b>
                  <div style="display: flex;margin-top: 15px;">
                    <div style="width:33%;"><input class="form-control fpt-day" type="number" name="dias" placeholder="dias" value="0"></div>
                    <div style="width:33%;"><input class="form-control fpt-horas" type="number" name="horas" placeholder="horas" value="0"></div>
                    <div style="width:33%;"><input class="form-control fpt-minutos" type="number" name="minutos" placeholder="minutos" value="0"></div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <div type="button" class="btn btn-secondary" style="background:#dddddd;" onclick="$('#EditMessage').toggleClass('show')">
                Cancelar
              </div>
              <div type="button" class="btn btn-success" onclick="EditSubmit()">
                Enviar
              </div>
            </div>
          </div>
        </div>
      </div>

      <style>
        .sdt-btn {
          align-items: center;
          display: flex;
          margin-left: 10px;
          width: 49px;
          justify-content: center;
          border-radius: 10px;
        }

        .modal-body .item {
          display: flex;
          margin-bottom: 5px;
        }

        .modal-body .item .preview {
          display: none;
          width: 46px;
          height: 46px;
          border-radius: 7px;
          margin-left: 10px;
        }

        .date-time input {
          height: 35px !important;
        }
      </style>
      <script>
        const EditSubmit = () => {
          const c = $('#EditMessage')
          const form = c.find(`form`)

          $(".fpt-day").each(function(){
            const e = $(this)
            if(e.val() == '')
              e.val(0)
          })
          $(".fpt-horas").each(function(){
            const e = $(this)
            if(e.val() == '')
              e.val(0)
          })
          $(".fpt-minutos").each(function(){
            const e = $(this)
            if(e.val() == '')
              e.val(0)
          })

          const id = form.find(`input[name=id]`).val()
          const descripcion = form.find(`textarea[name=descripcion]`).val()
          const dias = form.find(`input[name=dias]`).val()
          const horas = form.find(`input[name=horas]`).val()
          const minutos = form.find(`input[name=minutos]`).val()

          $(`[data-massid=${id}]`).find('.details').html(descripcion)

          $.ajax({
            url: `mass.php?edit`,
            type: `POST`,
            data: {
              id, descripcion, dias, horas, minutos
            }
          }).done(function(r) {
            console.log(r)
            c.removeClass('show').addClass('fade')
          })
        }

        const EditMessage = (ths) => {
          const c = $('#EditMessage')
          const form = c.find(`form`)
          const e = $(ths)
          const time = e.data('time')
          const content = e.data('content')
          const id = e.data('id')
          console.log(content, time, id)
          form.find(`input[name=id]`).val(id)
          form.find(`textarea[name=descripcion]`).val(content)
          c.addClass('show').removeClass('fade')
        }

    // FORMULARIO DE MENSAJE
        const Form = (image,index) => {

          const f = `<div class="item" id="item-${index}">
          <textarea placeholder="Escribe tu mensaje" class="form-control" name="content[${index}]"></textarea>
          <img src="${image}" class="preview">
          <input type="hidden" name="image[${index}]" value="${image}">
          <span class="btn btn-primary btn-file float-left sdt-btn">
          <i class="fas fa-camera"></i>
          <div>
          <input class="float-left" type="file" onchange="readImage(this, ${index})" multiple>
          </div>
          </span>
          </div>
          <div style="display: flex;margin-bottom: 15px;" class="date-time">
          <div style="width:33%;"><input class="form-control fpt-day" type="number" name="dias[${index}]" placeholder="dias"></div>
          <div style="width:33%;"><input class="form-control fpt-horas" type="number" name="horas[${index}]" placeholder="horas"></div>
          <div style="width:33%;"><input class="form-control fpt-minutos" type="number" name="minutos[${index}]" placeholder="minutos"></div>
          </div>`;

          return f
        }

        const FormModal = $(`#MensajessProgramadosModal`)
        const InputFiles = $(`#InputFiles`)
    // LEE LA IMAGEN DE UN FORMULARIO DE FORMA INDEPENDIENTE
        function readImage(input, index) {
          if (input.files && input.files[0]) {
            const ele = $(input).parent().parent()
            const cont = ele.parent()
            var reader = new FileReader();
            reader.onload = function(e) {
              const ele = $(`[id=item-${index}]`)
              cont.find('.preview').attr('src', e.target.result).show()
              cont.find('input').val(e.target.result)

              ele.remove()
            }
            reader.readAsDataURL(input.files[0]);
          }
        }
        $(document).ready(function() {
        // AGREGA UN FORMULARIO INDIVIDUAL
          $("#addMessage").click(function() {
            const index = FormModal.find(`form`).find('.item').length
            FormModal.find(`form`).append(Form('',index))
          })
        // MUESTRA EL MODAL
          $("#AddMessages").click(function() {
            FormModal.addClass('show').removeClass('fade')
          })
        // CARGA LAS IMAGENES EN VARIOS FORMULARIOS DISTINTOS
          InputFiles.change(function(e) {
          // ALMACENA LA IMAGEN
            const input = this
            if(input.files && input.files.length) {
              [...input.files].map((File, index) => {
              // ALMACENA EL ID DEL ULTIMO FORMULARIO GENERADO
                index = FormModal.find(`form`).find('.item').length +index

                var reader = new FileReader();
                reader.onload = function (e) {

            // ALMACENA UN NUEVO FORMULARIO CON SU IMAGEN
                  const dataForm = Form(e.target.result
                    , index)
            // IMPRIME EN CONSOLA
                  console.log(index)
            // AGREGA EL FORMULARIO
                  FormModal.find(`form`).append(dataForm)
            // MUESTRA LA IMAGEN AGREGADA EN EL FORMULARIO
                  aidi="#item-" + index;
                  const ele = $(aidi);
                  ele.find('img').show();
                }
                reader.readAsDataURL(File)
              })
            }
          })

          $("#sendForm").click(function() {
            const index = FormModal.find(`form`).find('.item').length
          // ENVIAR AJAX SI EXISTE ALMENOS UN FORMULARIO POR ENVIAR
            if(index>0){
              let day = 0;
              let hour = 0;
              let min = 0;
              /*$(".fpt-day").each(function() {
                  const e = $(this)
                  if (e.val() == '')
                      e.val(day)
                  day++;
              })*/
              $(".fpt-horas").each(function() {
                const e = $(this)
                hour = parseInt((Math.random()) * 24)
                if (e.val() == '')
                  e.val(hour)
              })
              $(".fpt-minutos").each(function() {
                const e = $(this)
                min = parseInt((Math.random()) * 60)
                if (e.val() == '')
                  e.val(min)
              })

              const formData = FormModal.find(`form`).serializeArray()

              console.log(formData)

              $.ajax({
                url: "./ajax.php?addProgramMessagesMass&typeMP=" + $("#typeMP").val(),
                type: "POST",
                data: formData
              }).done(function(response) {
                console.log(response)
                const data = $.parseJSON(response)
                console.log(data)
                if (data.status) {
                  alert('Mensajes programados con exito')
                  window.location.reload()
                } else {
                  alert('Error')
                }
              })
            }
          })
        });
      </script>
      <?php
      footer();
      ?>
