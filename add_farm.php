<?php
require("core.php");
head();

$farms = mysqli_query($connect, "SELECT * FROM `farms`");

?>
<style type="text/css">
 .form-group{
  display: inline;
  float: left;
 }
</style>
<div class="content-wrapper">
 <div id="content-container">
  <section class="content-header">
  </section>
  <section class="content">
   <div class="row">
    <div class="col-md-12">
      <div class="row" align="center">
            <a class='btn btn-success' href="worlds.php">Mundos</a>
            <a class='btn btn-success' href="items.php">Items</a>
            <?php if($rowu['role']=="Admin") echo "
            <a class='btn btn-warning' href='agregar_items.php'>Agregar Items</a>
            <a class='btn btn-warning' href='add_farm.php'>Agregar Mundo</a>
            "

            ;
            ?>
          </div>
     <div class="box">
      <div class="card">
       <div class="card-body">
        <form action="" method="post" class="form-horizontal" enctype="multipart/form-data">
         <div class="form-group">
          <label class="col-md-3 control-label" for="inputDefault">Nombre</label>
          <div class="col-md-8">
           <div class="input-group">
            <span class="input-group-addon"></span>
            <input  id="name" autocomplete="off" type="text" class="form-control" name="name" placeholder="" required>
           </div>
          </div>
         </div>
         <div class="form-group">
          <label class="col-md-3 control-label" for="inputDefault">Imagen</label>
          <div class="col-md-8">
           <div class="input-group">
            <span class="input-group-addon"></span>
            <input id="files" type="file" class="form-control" name="files" multiple required="">
           </div>
          </div>
         </div>
         <div class="form-group">
          <label class="col-md-3 control-label" for="inputDefault">Precio</label>
          <div class="col-md-8">
           <div class="input-group">
            <span class="input-group-addon"></span>
            <input id="price" type="number" min="0" class="form-control" name="price" placeholder="" required>
           </div>
          </div>
          <div class="panel-footer text-left">
           <input id="save" class="btn btn-flat btn-success" name="save" type="submit" value="Enviar">
          </div>
         </div>
        </form>
       </div>
      </div>
     </div>
    </div>
   </div>
  </section>
 </div>
</div>
<?php footer(); ?>
<script type="text/javascript">
 $(document).ready(function() {
  $("#save").on("click", function(e){
    e.preventDefault();
    var file = document.getElementById("files").files[0];
    var formData = new FormData();

      //AGREGAR TODOS LOS ARCHIVOS AL ARRAY
        formData.append("files", file);
        //
        formData.append("name", $("#name").val());
        formData.append("price", $("#price").val());
        /*$("#name").val("");
        $("#price").val("");
        $("#produces").val("");
        $("#pos_x").val("");
        $("#pos_y").val("");
        $("#farm").val("");*/

        $.ajax
        ({
          url: "ajax.php?addfarm", 
          type: "POST",
          data: formData,
          contentType:false,
          cache: false,
          processData:false
        }).done(function(response)
        {
          var data = $.parseJSON(response);
          console.log(response);
          if(data.status)
          {
            var element = $(".box-body").find("tbody").find("tr:first-child");
            element.before( data.message );
            window.location.reload()
            swal.fire(data.message, "", "success");
          }
          else
          {
            swal.fire("Error!", data.message, "error");
          }
          console.log(response);
        })
      });
});
</script>
