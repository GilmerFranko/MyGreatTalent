<?php
require("core.php");

if(isset($_POST['action'])){
	
	$Action = $_POST['action'];
	
	if($Action == 'create'){
		$pregunta = $_POST['pregunta'];
		$respuestas = $_POST['respuestas'];
		mysqli_query($connect, "INSERT INTO `respuesta_automatica` (`uid`, `pregunta`, `respuesta`) VALUES ('{$player_id}', '{$pregunta}', '{$respuestas}')");
	}
	elseif($Action == 'update'){
		$ID = $_POST['id'];
		$pregunta = $_POST['pregunta'];
		$respuestas = $_POST['respuestas'];
		mysqli_query($connect, "UPDATE `respuesta_automatica` SET pregunta='{$pregunta}', respuesta='{$respuestas}' WHERE id='{$ID}'");
	}
	elseif($Action == 'delete'){
		$ID = $_POST['id'];
		mysqli_query($connect, "DELETE FROM `respuesta_automatica` WHERE id='{$ID}'");
	}
	
}
head();

?>
<style>
.dsdrrt input[type=text] {
    width: 50%;
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 20px 15px;
	margin-right: 10px;
}
.saved, .delete {
    background: #097332;
    color: white;
    border: 0;
    border-radius: 10px;
    padding: 10px 15px;
}
.delete {
    background: red;
}
.dsdrrt {
    display: flex;
    width: 100%;
	margin-bottom: 15px;
}
.dsdrrt>form:first-child {
    width: 100%;
    display: flex;
    margin-right: 10px;
}
</style>
<div class="content-wrapper">
    <!--CONTENT CONTAINER-->
     <!--EL TIEMPO DE RESPUESTA LO CAMBIO EN ajax.php BUSCAR $DelayTime = time() Y CAMBIAR LOS MINUTOS-->
    <!--===================================================-->
    <div id="content-container">

        <!--Page content-->
        <!--===================================================-->
        <section class="content">

			<h2 style="margin-top:0;">
				Nueva Respuesta automatica
			</h2>
			<div class="dsdrrt">
				<form action="" method="post" style="margin:0;">
					<input type="hidden" name="action" value="create">
					<input type="text" name="pregunta" placeholder="Pregunta">
					<input type="text" name="respuestas" placeholder="Respuesta">
					<button type="submit" class="saved">Guardar</button>
				</form>
			</div>
			
			<br>
			<h2>
				Mis Respuestas automaticas
			</h2>
            <?php
				$query = mysqli_query($connect, "SELECT * FROM `respuesta_automatica` WHERE uid='{$player_id}' ORDER BY id DESC");
				if($query){
					while ($RSPDa = mysqli_fetch_assoc($query)) {	
					?>
					
					<div class="dsdrrt">
						<form action="" method="post">
							<input type="hidden" name="action" value="update">
							<input type="hidden" name="id" value="<?php Echo $RSPDa["id"]; ?>">
							<input type="text" name="pregunta" placeholder="Pregunta" value="<?php Echo $RSPDa["pregunta"]; ?>">
							<input type="text" name="respuestas" placeholder="Respuesta" value="<?php Echo $RSPDa["respuesta"]; ?>">
							<button type="submit" class="saved">Guardar</button>
						</form>
						<form action="" method="post">
							<input type="hidden" name="action" value="delete">
							<input type="hidden" name="id" value="<?php Echo $RSPDa["id"]; ?>">
							<button type="submit" class="delete">Eliminar</button>
						</form>
					</div>
					
					<?php
					}
				}
			?>
			
		</section>
	</div>
</div>

<?php
footer();
?>