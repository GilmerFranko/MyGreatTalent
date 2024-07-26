<?php
require("core.php");
head();

?>

<style>
  body {
    background-color: #f8f9fa;
  }

  .container {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .card {
    width: 400px;
    padding: 20px;
    border-radius: 15px;
    background-color: #fff;
  }

</style>


<div class="content-wrapper" height="10%">

  <!--CONTENT CONTAINER-->
  <!--===================================================-->
  <div id="content-container">
    <center>
      <section class="">
        <div class="" style=""><strong><i class="fa fa-info-circle text-primary"></i> Aquí podrás cambiar tu contraseña</strong></div><br>
        <a href="settings.php" class="btn btn-primary">VOLVER</a>
      </section>
    </center>
    <section class="content">
      <div class="row">
        <div class="container">
          <div class="card">
            <h3 class="text-center mb-4">Cambiar Contraseña</h3>

            <p class="text-center">Este es tu correo: <strong><?php echo $rowu['email'] ?></strong></p>
            <p class="text-center">Este es tu nombre de usuario: <strong><?php echo $rowu['username'] ?></strong></p>

            <form id="cambiarClaveForm">
              <div class="form-group">
                <label for="nuevaClave">Crear nueva contraseña</label>
                <input type="password" class="form-control" id="nuevaClave" required>
              </div>
              <div class="form-group">
                <label for="repetirClave">Repetir nueva contraseña</label>
                <input type="password" class="form-control" id="repetirClave" required>
              </div>
              <button type="submit" class="btn btn-success btn-block">Guardar</button>
              <a href="settings.php" class="btn btn-info btn-block">Atras</a>
            </form>

          </div>
        </div>
      </div>
    </section>
  </div>
</div>


<script>
  $(document).ready(function () {
    /* Evento del botón "Guardar" */
    $("#cambiarClaveForm").submit(function () {
      /* Validar que las contraseñas coincidan */
      var nuevaClave = $("#nuevaClave").val();
      var repetirClave = $("#repetirClave").val();

      if (nuevaClave !== repetirClave) {
        swal.fire("Las contraseñas no coinciden","","error");
        return false;
      }


      $.ajax({
        type: "POST",
        url: "ajax.php?changePassword=true",
        data: {
          password : nuevaClave,
          password2: repetirClave
        },

        success: function(response) {
          r = $.parseJSON(response);

          if(r.status)
          {
            swal.fire(r.message,'','success');
          }
          else
          {
            swal.fire(r.message,'','error');
          }
        }
      });
      return false;
    });
  });
</script>


<?php footer() ?>
