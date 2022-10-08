<?php
require "core.php";
head();

if (isset($_POST['codigoingresado'])) {
    $itemfs_id = $_POST["codigo"];
    
    $queryspi = mysqli_query($connect, "SELECT * FROM `colecciones` WHERE codigo='$itemfs_id' LIMIT 1");
    $countspi = mysqli_num_rows($queryspi);
    $coleccion   = mysqli_fetch_assoc($queryspi);
	$coleccion_id = $coleccion['id'];
	//verificamos que el codigo pertenece a una coleccion
	
    if ($countspi > 0) {
		
		//verificamos que el usuario no tenga ya este item
		
		 $querysrvs = mysqli_query($connect, "SELECT * FROM `player_colecciones` WHERE coleccion_id='$coleccion_id' AND player_id='$player_id' LIMIT 1");
         $countsrvs = mysqli_num_rows($querysrvs);
		 if ($countsrvs < 1) {
        
       $insertar = mysqli_query($connect, "INSERT INTO `player_colecciones` (player_id, coleccion_id)
VALUES ('$player_id', '$coleccion_id')");
                
		
		
        echo '
        <script type="text/javascript">
            $(document).ready(function() {
                $("#sell-item").modal(\'show\');
            });
        </script>

        <div id="sell-item" class="modal fade">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">

						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <center>
                            <h4><span class="badge badge-info"> Conseguiste una coleccion</span></h5><br /><br />
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                    <img src="' . $coleccion['imagen'] . '" width="45%"><br />
    
                                </div>                               
                                <div class="col-md-2"></div>
                            </div><br /><br />
                            <div class="row">
                                
                            </div><br /><br />
                            <button type="button" class="btn btn-success btn-md btn-block" data-dismiss="modal" aria-hidden="true"><i class="fab fa-get-pocket"></i> Bien</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>';
	}
    }
}
?>
<div class="card"><div class="card-header text-white bg-success"><i class="fas fa-box-open"></i></div><br/><br/>

        <div class="col-md-12 card bg-light card-body">
            
            <center><h3><p style="color:white;"><i class="fas fa-box-open"></i> Colecciona todas las mujeres de BellasGram</p></h3></center><br />

<!---->
<!-- Poner aqu aviso y textos-->

           <!-- Poner aqu aviso y textos--> 
<center>
            <div class=" mx-12" style="max-width: 60rem;">
				


            <div class="col-md-6">
                <div class="jumbotron">
                    <center>
                        <h5><a href="https://bit.ly/2KxwNKw">CODIGOS DE REGALO AQUÍ</a>
                        <br/><br/>
                        Todas la que quieran estar en las colecciones envíennos un mensaje para incluirlas.</h5>
                        


                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
						<form method="post" action="">
                            <ul class="list-group">
                                
                                
                               
                            </ul>
							
						
					<div class="">
                        <div class="card bg-light card-body mb-3">
                        

                       
                            <div class="form-group">
                                
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><em class=""></em></div>
									</div>
                                    <input name="codigo" autocomplete="off"  type="text" placeholder="Agrega un Código" class="form-control" required>
                                </div>
                            </div>
                           
                            
                        
                    </div>
                </div>
				
							
							
                       


       <input value="Usar" class="btn btn-success btn-block" name="codigoingresado" type="submit">

              
                    </center>
							</form>
							
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    
                </div>
            </div>

        </div>
		
		
<br><br>

       <center/>
		
		<!--         --->        
                

                <div class="tab-content">
<center><h4><p style="color:white;">Tus colecciones</p>
</h4></center>
                    <br />
<?php

		 
		 $queryi2 = mysqli_query($connect, "SELECT * FROM `player_colecciones` WHERE player_id = '$player_id'");
    $counti2 = mysqli_num_rows($queryi2);
    while ($rowi2 = mysqli_fetch_assoc($queryi2)) {
        
        $coleccion_id = $rowi2['coleccion_id'];
        $querypic = mysqli_query($connect, "SELECT * FROM `colecciones` WHERE id='$coleccion_id' LIMIT 1");
        $rowpic   = mysqli_fetch_assoc($querypic);
        $countpic = mysqli_num_rows($querypic);
        
        ++$i;
        if ($i == 1) {
            echo '<div class="row">';
        }
?>
        <div class="col-md-4">
            <center>
                <ul class="breadcrumb"><li class="active"><h5><b><?php
        echo $rowpic['titulo'];

?></b></h5></li></ul>
            </center>
            <div class="row">
                <div class="col-md-7">
                    <center><img src="<?php
        echo $rowpic['imagen'];
?>" width="55%"></center>
                </div>
              
            </div>
            <hr />
        </div>
<?php
      
    }
?>      
                   
                   
                                </div>
                                <BR>
                     

                </div>

        </div>

<?php
footer();
?>