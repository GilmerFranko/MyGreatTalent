<?php
require("core.php");
head();

if(isset($_GET['unBlock']) AND !empty($_GET['unBlock']))
{
	unBlock($rowu['id'], base64_decode( substr($_GET['unBlock'],5)), true);
}
// TOTAL DE PERSONAS BLOQUEADAS POR MI
$total_pages = $connect->query("SELECT * FROM `bloqueos` WHERE fromid='$rowu[id]'")->num_rows;
// OPTIENE EL NUMERO DE PAGINA A MOSTRAR
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
// ESTABLECE LA PAGINACION
$num_results_on_page = 10;
$calc_page = ($page - 1) * $num_results_on_page;
$query = $connect->query("SELECT * FROM `bloqueos` WHERE fromid='$rowu[id]' LIMIT $calc_page, $num_results_on_page");

// Desbloquea todos los perfiles bloqueados por x usuario
if(isset($_GET['unlockAll']) AND !empty($_GET['unlockAll']) AND base64_decode($_GET['unlockAll']) == $rowu['username'])
{
	$consult = $connect->query("DELETE FROM `bloqueos` WHERE `fromid`=\"". $connect->real_escape_string($rowu['id']) ."\"");
	echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'bloqueados.php" />';
}
?>
<div class="content-wrapper" height="10%">
	<center>
		<section class="">
			<?php if ($total_pages > 0): ?>
				<div class="" style=""><strong><i class="fa fa-info-circle text-primary"></i> Has bloqueado a <?php echo $total_pages ?> persona(s)</strong></div><br>
				<a class="actionQuest btn btn-danger"  data-quest="¿Deseas desbloquear a todas las personas que has bloqueado?" data-btnaction="Desbloquear a todos" data-href="<?php echo createLink('bloqueados','',array('unlockAll' => base64_encode($rowu['username'])), true) ?>" class="btn btn-danger" href="#">Desbloquear a todos</a>
			<?php endif ?>
		</section>
	</center>
	<br>
	<center>
		<div class="box" align="center" style="width: 350px; border-radius: 16px;">
			<br>
			<?php
			$timeonline = time() - 60;


			if ($query AND $query->num_rows > 0){

				while ($friend = mysqli_fetch_assoc($query))
				{
					$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$friend[toid]'");
					$rowsuser = mysqli_fetch_assoc($sqluser);?>

					<div class="row">
						<center>
							<img src="<?php echo $sitio['site'].$rowsuser['avatar']?>" class="img-circle img-avatar" style="width: 80px; height: 80px;">
							<br>
							<h5 style="display: inline-block;"><strong><?php echo $rowsuser['username'] ?></strong></h5>
							<br>
							<a class="btn btn-success" href="bloqueados.php?unBlock=User-<?php echo base64_encode($rowsuser['id']); ?>">Desbloquear</a>
						</center>
					</div>
					<br>
					<hr>
					<br>
				<?php }
			}else{
				Echo '<div class="alert alert-success"><strong><i class="fa fa-info-circle"></i> Hasta los momentos no has bloqueado a nadie</strong></div>';
			}

			?>
			<center>
				<?php echo paginationIndex('bloqueados',$total_pages, $num_results_on_page) ?>
			</center>
		</div>
	</center>
	<br><br>
</div>
</div>


<?php
footer();
?>
