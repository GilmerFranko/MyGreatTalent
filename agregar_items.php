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
      <div class="box-header">
       <h3 class="box-title"></h3>
      </div>
      <div class="box-body">
       <table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
        <tbody>
         <center>
          <p>
           Agrega Items
          </p>
         </center>
        </tbody>
       </table>
      </div>
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
         </div>
         <div class="form-group">
          <label class="col-md-3 control-label" for="inputDefault">Produccion</label>
          <div class="col-md-8">
           <div class="input-group">
            <span class="input-group-addon"></span>
            <input id="produces" type="number" min="0" class="form-control" name="produces" placeholder="" required>
           </div>
          </div>
         </div>
         <div class="form-group">
          <label class="col-md-3 control-label" for="inputDefault">Tamaño</label>
          <div class="col-md-8">
           <div class="input-group">
            <span class="input-group-addon"></span>
            <input id="size" type="text" min="0" class="form-control" name="size" placeholder="SMALL 32px | MEDIUM 84px | BIG 164px" required>
           </div>
          </div>
         </div>
         <div class="form-group">
          <label class="col-md-3 control-label" for="inputDefault">Posicion X</label>
          <div class="col-md-8">
           <div class="input-group">
            <span class="input-group-addon"></span>
            <input id="pos_x" type="number" min="0" class="form-control" name="pos_x" placeholder="" required>
           </div>
          </div>
         </div>
         <div class="form-group">
          <label class="col-md-3 control-label" for="inputDefault">Posicion Y</label>
          <div class="col-md-8">
           <div class="input-group">
            <span class="input-group-addon"></span>
            <input id="pos_y" type="number" min="0" class="form-control" name="pos_y" placeholder="" required>
           </div>
          </div>
         </div>
         <div class="form-group">
          <label class="col-md-3 control-label" for="description" style="width: 80%;">Agregar item para:
           <select id="farm" name="farm">
            <?php while ($rowfarms=mysqli_fetch_assoc($farms)) { ?>
            <option value="<?php echo $rowfarms['name']; ?>"><?php echo $rowfarms['name']; ?></option>
            <?php } ?>
           </select>
          </label>
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
<script type="text/javascript">
 $(document).ready(function() {
  $("#save").on("click", function(e){
    e.preventDefault();
    var file = document.getElementById("files").files[0];
    var formData = new FormData();
    
        formData.append("files", file);
        //
        formData.append("name", $("#name").val());
        formData.append("price", $("#price").val());
        formData.append("produces", $("#produces").val());
        formData.append("size", $("#size").val());
        formData.append("pos_x",  $("#pos_x").val());
        formData.append("pos_y",  $("#pos_y").val());
        formData.append("farm",  $("#farm").val());
        /*$("#name").val("");
        $("#price").val("");
        $("#produces").val("");
        $("#pos_x").val("");
        $("#pos_y").val("");
        $("#farm").val("");*/

        $.ajax
        ({
          url: "ajax.php?additem", 
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
