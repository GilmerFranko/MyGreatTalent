<?php
require("core.php");

if(isset($_GET['AddNote'])){
	
	$Title = $_POST['title'];
	$Des = $_POST['des'];
	
	$connect->query("INSERT INTO `notas` (uid, title, nota, created) VALUES ('{$player_id}', '{$Title}', '{$Des}', '". time() ."')");

	echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'notas.php" />';
	exit;
}

if(isset($_GET['delete'])){
	
	$delete = $_GET['delete'];
	
	$connect->query("DELETE FROM `notas` WHERE id={$delete}");

	echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'notas.php" />';
	exit;
}
head();

?>
<style type="text/css">
.col-md-6,
.col-md-3 {
	position: initial;
}
.item .btn {
	
}
.float-right { float: right; }
.item {
	position: relative;
    padding: 20px;
    margin: 10px;
    box-sizing: border-box;
    background: white;
    float: inherit;
    width: auto;
    border-radius: 3px;
    box-shadow: 0px 0px 4px #222d323b;
}
.black-background {background-color:#000000;}
.white {color:#ffffff;}

</style>

<div class="content-wrapper" height="10%">
	<div id="content-container">
	<?php
	if(isset($_GET['edit'])):
		
		$id = $_GET['edit'];
	
		if(isset($_POST['nid'])){
			$title = $_POST['title'];
			$des = $_POST['des'];
			$connect->query("UPDATE `notas` SET title='{$title}', nota='{$des}' WHERE id='{$id}'");
			echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'notas.php" />';
			exit();
		}
		
		$Nota = $connect->query("SELECT * FROM `notas` WHERE id='{$id}'");
		$Nota = (object) mysqli_fetch_assoc($Nota);
	?>
		<div class="col-md-12" style="float:inherit;">
			<form action="notas.php?edit=<?php Echo $id; ?>" method="POST">
				<input name="nid" type="hidden" value="<?php Echo $Nota->id; ?>">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Editar Nota</h5>
					</div>
					<div class="modal-body">
						<div>
							<input class="form-control" type="text" name="title" placeholder="Titulo de la nota" value="<?php Echo $Nota->title; ?>">
						</div>
						<br/>
						<div>
							<textarea class="form-control" name="des" placeholder="Cuerpo" style="height:100px!important;margin-bottom:20px;"><?php 
								Echo $Nota->nota;
							?></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-success" type="submit">
							Guardar
						</button>
					</div>
				</div>
			</form>
		</div>
	<?php
		exit();
	endif;
	?>
		<div class="col-md-12" style="float:inherit;">
			<form action="notas.php?AddNote" method="POST">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Escribe notas sobre lo que quieras recordar o tener siempre  a mano para leer despues, solo tu puedes ver tus notas.</h5>
					</div>
					<div class="modal-body">
						<div>
							<input class="form-control" type="text" name="title" placeholder="Titulo de la nota">
						</div>
						<br/>
						<div>
							<textarea class="form-control" name="des" placeholder="Escribe aqui" style="height:100px!important;margin-bottom:20px;"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-success" type="submit">
							Guardar
						</button>
					</div>
				</div>
			</form>
		</div>
		
		<br>
	
		<section class="content-header">
		
			<h1><i class="fas fa-comment"></i> Mis Notas</h1>
		  
		</section>
		<div class="row" style="margin: 0;">
		<?php
			
			$Notas = $connect->query("SELECT * FROM `notas` WHERE uid='{$player_id}'");
			
			foreach($Notas as $Nota){
				$Nota = (object) $Nota;
			?>
			
			<div class="col-md-12 row item">
				<div class="col-md-3">
					<b><?php Echo $Nota->title; ?></b>
					<br/>
					<i><em><p style="color:green;"><?php Echo timeAgo($Nota->created); ?></p></em></i>
				</div>
				<div class="col-md-6">
					<p style="word-break:break-word;"><?php Echo $Nota->nota; ?></p>
				</div>
				<div class="col-md-3">
					<a href="notas.php?delete=<?php Echo $Nota->id; ?>">
						<button class="btn btn-danger">Borrar</button>
					</a>
					<a href="notas.php?edit=<?php Echo $Nota->id; ?>">
						<button class="float-right btn btn-warning">editar</button>
					</a>
				</div>
			</div>
			
			<?php
			}				
		?>
		</div>

	</div>
</div>
<?php
footer();
?>