<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
	exit;
}else{

	?>
	<div class="content-wrapper">
		<div id="content-container">
			<section class="content-header">
				<h1><i class="fas fa-users"></i> Editar Usuario</h1>
				<ol class="breadcrumb">
					<li><a href="dashboard.php"><i class="fas fa-home"></i> Admin Panel</a></li>
					<li class="active">Editar Usuario</li>
				</ol>
			</section>
			<!--Page content-->
			<!--===================================================-->
			<section class="content">
				<div class="row">
					<div class="col-md-12">
						<?php
						if (isset($_GET['edit-id'])) {
							$id  = (int) $_GET["edit-id"];
							$sql = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$id'");
							$row = mysqli_fetch_assoc($sql);
							?>
							<form class="form-horizontal" action="" method="post">
								<div class="box">
									<div class="box-header">
										<h3 class="box-title">Edit Player</h3>
									</div>
									<div class="box-body">
										<div class="form-group">
											<label class="col-sm-2 control-label">Username: </label>
											<div class="col-sm-10">
												<input type="text" name="username" class="form-control" value="<?php
												echo $row['username'];
											?>" disabled>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">E-Mail Address: </label>
										<div class="col-sm-10">
											<input type="email" name="email" class="form-control" value="<?php
											echo $row['email'];
										?>" disabled>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">Avatar: </label>
									<div class="col-sm-10">
										<input type="text" name="avatar" class="form-control" value="<?php
										echo $row['avatar'];
									?>" required>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Role: </label>
								<div class="col-sm-10">
									<select name="role" class="form-control" required>
										<option value="Player" <?php
										if ($row['role'] == 'Player') {
											echo 'selected';
										}
									?>>Player</option>
									<option value="BOT" <?php
									if ($row['role'] == 'BOT') {
										echo 'selected';
									}
								?>>BOT</option>
								<option value="Admin" <?php
								if ($row['role'] == 'Admin') {
									echo 'selected';
								}
							?>>Admin</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Creditos Normales: </label>
					<div class="col-sm-10">
						<input type="number" name="creditos" min="0" class="form-control" value="<?php
						echo $row['creditos'];
					?>" required>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Creditos Especiales: </label>
				<div class="col-sm-10">
					<input type="text" name="eCreditos" class="form-control" value="<?php
					echo $row['eCreditos'];
				?>" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Género: </label>
			<div class="col-sm-10">
				<select name="gender" class="form-control" required>
					<option value="hombre"<?php if ($row['gender']=='hombre')echo 'selected';?>>
						Hombre
					</option>
					<option value="mujer" <?php if ($row['gender'] == 'mujer') echo 'selected';?> >Mujer </option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">Categoria: </label>
			<div class="col-sm-10">
				<select name="category" class="form-control" required>
					<option value="hetero" <?php
					if ($row['category'] == 'hetero') echo 'selected';?>>Hetero</option>
					<option value="trans" <?php
					if ($row['category'] == 'trans') echo 'selected';?>>Trans</option>
				</select>
			</div>
		</div>


		<div class="form-group">
			<label class="col-sm-2 control-label">Description: </label>
			<div class="col-sm-10">
				<input type="text" name="description" class="form-control" value="<?php
				echo $row['description'];
			?>">
		</div>
	</div>
	<div class="form-group">
		<label class="content-input">
			<input id="convertPrivate" type="checkbox" name="convertPrivate" <?php echo ($row['hidden_for_old'] == 0) ? '' : 'checked'; ?> value="1">
			Solo mostrar a usuarios registrados desde hoy
			<i></i>
		</label>
		<br>
		<?php if ($row['hidden_for_old'] != 0): ?>
			<i>Este perfil esta oculto desde el d&iacute;a <strong><?php echo strftime("%d/%m/%Y %H:%M", $row['hidden_for_old']) ?></strong></i>
		<?php endif ?>
		<i></i>
	</div>
	<div class="form-group">
		<label class="content-input">
			<input id="" type="checkbox" name="permission_send_gift" value="1" <?php echo $row['permission_send_gift'] ? 'checked=""' :''; ?>>Permitir enviar regalos a usuarios
			<i></i>
		</label>
	</div>

	<div class="panel-footer">
		<button class="btn btn-flat btn-success" name="edit" type="submit">Save</button>
		<button type="reset" class="btn btn-flat btn-default">Reset</button>
	</div>
</div>
</div>
</form>
	<?php
	if (isset($_POST['edit'])) {
		$id  = (int) $_GET["edit-id"];
		$avatar  = $_POST['avatar'];
		$role    = $_POST['role'];
		$creditos   = $_POST['creditos'];
		$eCreditos   = $_POST['eCreditos'];
		$gender    = $_POST['gender'];
		$category    = $_POST['category'];
		$description = $_POST['description'];
		$permission_send_gift = isset($_POST['permission_send_gift']) ? $_POST['permission_send_gift'] : '0';
		$convertPrivate = (isset($_POST['convertPrivate']) AND !empty($_POST['convertPrivate']) AND $_POST['convertPrivate'] == 1) ? time() : 0;

		$query = mysqli_query($connect, "UPDATE `players` SET avatar='$avatar', role='$role', creditos='$creditos', eCreditos='$eCreditos', gender='$gender', category='$category', description='$description', hidden_for_old='$convertPrivate', `permission_send_gift` = '$permission_send_gift' WHERE id='$id'");

		echo '<meta http-equiv="refresh" content="0;url=edituser.php?edit-id='. $id .'">';
	}
}
?>
</div>
</div>
</section>
</div>
</div>
<
<script>
	$(document).ready(function() {

		$('#dt-basic').dataTable( {
			"responsive": true,
			"language": {
				"paginate": {
					"previous": '<i class="fas fa-angle-left"></i>',
					"next": '<i class="fas fa-angle-right"></i>'
				}
			}
		});
		var turn = true;
	//- SELECCIONA TODOS LOS CHECKBOX
	$("#convertPrivate").on("click", function() {
		$("#convertPrivate").prop("checked", turn);
		turn = turn==false ? true : false;
	});
});
</script>
<?php
footer();
}
?>
