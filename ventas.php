<?php
require("core.php");
head();

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname' LIMIT 1");
$rowu  = mysqli_fetch_assoc($suser);

if (isset($_POST['precomprar'])){
	
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
                        <h4 class="modal-title">Confirmar Compra</h4> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <center>
                           <span class="badge badge-info"><h4>Confirmar la Compra del pack</h4></span>
                                
                            <br /><br />
                            
                                <center>
                                   
									<form method="POST">
									 <input type="hidden" name="galeriaid" value="'.$_POST['galeriaid'].'">
								
									
									<button type="submit" name="comprar" class="btn btn-success"> 
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


if (isset($_POST['comprar'])){

	$idgaleria = $_POST['galeriaid'];
 
	$querygal = mysqli_query($connect, "SELECT * FROM `ventasenventa` WHERE id='$idgaleria'");
	$galeria  = mysqli_fetch_assoc($querygal);

	$queryccc = mysqli_query($connect, "SELECT * FROM `ventascompradas` WHERE foto_id='$idgaleria' AND comprador_id='$rowu[id]'");
	$countcompradoc = mysqli_num_rows($queryccc);

	$linkc = $galeria['linkdedescarga'];
	$dolaresdeluserc = $rowu['eCreditos'];
	$costoc = $galeria['precio']; 
	
	//revisamos que el usuario tiene el dinero y que no haya comprado la foto anteriormente
	if($countcompradoc < 1 && $dolaresdeluserc >= $costoc){
		
		$insertarcompra = mysqli_query($connect, "INSERT INTO `ventascompradas` (foto_id, comprador_id) VALUES ('$idgaleria', '$rowu[id]')");
		$actualizardatos = mysqli_query($connect, "UPDATE `ventasenventa` SET ventasrealizadas=ventasrealizadas+1 WHERE id='$idgaleria'");
		$restardinero = mysqli_query($connect, "UPDATE `players` SET eCreditos=eCreditos-'$costoc' WHERE id='$rowu[id]'");
		$sumardineroalvendedor = mysqli_query($connect, "UPDATE `players` SET eCreditos=eCreditos+'$costoc' WHERE id='$galeria[player_id]'");

		//mostramos mensaje de exito y link de descarga
		
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
                        <h5 class="modal-title">Compra del pack realizada!</h5> 
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <center>
                            <span class="badge badge-info"><h4>Compra realizada <br/>puedes ver el link en la sección <br/>"Mis packs Comprados"</h4></span>
                                
                            <br /><br />

								
                            <br /><br />
                            <button type="button" class="btn btn-success btn-md btn-block" data-dismiss="modal" aria-hidden="true"><i class="fab fa-get-pocket"></i> OK</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>';
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

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">



</script>
<div class="content-wrapper">

    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">

        <section class="content-header">
            <h1><i class="fas fa-layer-group"></i> Mercado de servicios profesionales</h1>

        </section>

        <!--Page content-->
        <!--===================================================-->
        <section class="content">

            <div class="row">

                <div class="col-md-12">





                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"></h3>
                        </div>
                        <div class="box-body">
                            <table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">

                                <thead>
                                    <tr>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <center>

                                       <a href="instruccioncompraventapacks.php"> <button class="btn btn-info"><i class="fas fa-exclamation"></i> Instrucciones</button></a>
                                       <?php
										if ($rowu['gender'] == 'mujer'){ 
									?>

                                       <!-- OCULTO  <a href="addventa.php"> <button class="btn btn-success"><i class="fa fa-check"></i> Agregar Venta</button></a>OCULTO -->

                                        <a href="misventas.php"> <button class="btn btn-success"><i class="fa fa-image"></i> Mis Ventas</button></a>

                                        <?php
										}
									?>

                                        <!-- OCULTO <a href="misventascompradas.php"> <button class="btn btn-success"><i class="fa fa-heart"></i> Mis Pedidos Comprados</button></a>

                                       <a href="comprar.php"> <button class="btn btn-primary"><i class="fas fa-dollar-sign"></i> Comprar Créditos</button></a>OCULTO -->

                                    </center>
                                    <br><br />



                                    <div class="card">
                                        <div class="card-body">
<?php

function islookContentUser($from, $to){
	global $connect;
	if($from == $to){
		return true;
	}
	$Look = $connect->query("SELECT * FROM `bloqueos` WHERE fromid='{$from}' AND toid='{$to}'");
	$Look02 = $connect->query("SELECT * FROM `bloqueos` WHERE fromid='{$to}' AND toid='{$from}'");
	if($Look->num_rows or $Look02->num_rows){
		return false;
	}
	return true;
}

$timeonline = time() - 60;

$total_pages = $connect->query("SELECT * FROM `ventasenventa` ORDER BY id DESC")->num_rows;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

$num_results_on_page = 10;
$calc_page = ($page - 1) * $num_results_on_page;

$querycp = mysqli_query($connect, "SELECT * FROM `ventasenventa` ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
$countcp = mysqli_num_rows($querycp);
if ($countcp > 0) {
    while ($rowcp = mysqli_fetch_assoc($querycp)) {
        $author_id = $rowcp['player_id'];
        $querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
        $rowcpd    = mysqli_fetch_assoc($querycpd);
        $iamfrom = ($rowu['registerfrom'] == 'chat' ? 'chat' : "bellasgram");
        //SI EL USUARIO TIENE EL PERFIL OCULTO Y YO NO SOY UN USUARIO REGISTRADO DESDE EL CHAT
        if($rowcpd['perfiloculto']!='no' or $rowcpd['hidetochat']=='si' and $iamfrom!='chat'){
			
			$friend = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$author_id'");
			$friend01 = mysqli_num_rows($friend);
			
			$friend2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$author_id' AND player2='$player_id'");
			$friend02 = mysqli_num_rows($friend2);
			
			if($friend02==false && $friend01==false){
				continue;
			}
		}

		if(!islookContentUser($player_id, $author_id)){
			$total_pages--;
			continue;
		}
		
?><tr>

                                                <td>
                                                    <div class="card">
                                                        <div class="card-header bg-secondary mb-3">
                                                          <!-- OCULTO  <img src="<?php echo $sitio['site'].$rowcpd['avatar']; ?>" class="img-circle" style="width:65px;">
                                                            &nbsp;&nbsp;<strong><?php echo '<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowcpd['id'] . '">' . $rowcpd['username'] . '</a>
'; if ($rowcpd['timeonline'] > $timeonline) ?> </strong>OCULTO -->
                                                            <br>


                                                        </div><br>
                                                        <div class="card-body comment-emoticons">
                                                            <center><img src="<?php
        echo $sitio['site'].$rowcp['imagen'];
?>" width="100%"> </center><br> <?php
        echo $rowcp['descripcion'];
?>
                                                        </div>
                                                        <hr />
                                                        <div class="card-footer">
                                                            <b>
                                                                <?php
echo 'Precio desde: '.$rowcp['precio'].' Dólares en adelante';
        
?></b>
<br />
 <b>
                                                                <?php
echo 'Tiempo de entrega: '.$rowcp['linkdedescarga'].' Días';
        
?></b>
<br /><br />
                                                            <a href="https://bellasgram.com/index.php?app=site&section=contact"> <button class="btn btn-success"><i class="fa fa-check"></i> Realizar un pedido</button></a>
                                                          <br /><br />  <a href="instruccioncompraventapacks.php"> <button class="btn btn-info"><i class="fas fa-exclamation"></i> Instrucciones para pedir</button></a>
                                                            
                                                        </div>
                                                    </div><br />
                                                    <!---->


<?php	
    }
if (ceil($total_pages / $num_results_on_page) > 0): ?>
<ul class="pagination">
	<?php if ($page > 1): ?>
	<li class="prev"><a href="packs.php?page=<?php echo $page-1 ?>">Anterior</a></li>
	<?php endif; ?>

	<?php if ($page > 3): ?>
	<li class="start"><a href="packs.php?page=1">1</a></li>
	<li class="dots">...</li>
	<?php endif; ?>

	<?php if ($page-2 > 0): ?><li class="page"><a href="packs.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
	<?php if ($page-1 > 0): ?><li class="page"><a href="packs.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

	<li class="currentpage"><a href="packs.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

	<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="packs.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
	<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="packs.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

	<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
	<li class="dots">...</li>
	<li class="end"><a href="packs.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
	<?php endif; ?>

	<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
	<li class="next"><a href="packs.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
	<?php endif; ?>
</ul>
<?php endif; ?>
<?php
} else {
    echo '<div class="alert alert-success"><strong><i class="fa fa-info-circle"></i> Actualmente no hay ventas en el mercado</strong></div>';
}

?>
                                            </tr>

                                            </td>

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
<!--===================================================-->
<!--END CONTENT CONTAINER-->


<?php
footer();
?>
