<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 	
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
    exit;
}

if (isset($_GET['delete-id'])) {
    $id    = (int) $_GET["delete-id"];
    $query = mysqli_query($connect, "DELETE FROM `pets` WHERE id='$id'");
}
?>
<div class="content-wrapper">

	<!--CONTENT CONTAINER-->
	<!--===================================================-->
	<div id="content-container">
		
		<section class="content-header">
		  <h1><i class="fas fa-paw"></i> Pets</h1>
		  <ol class="breadcrumb">
			 <li><a href="dashboard.php"><i class="fas fa-home"></i> Admin Panel</a></li>
			 <li class="active">Pets</li>
		  </ol>
		</section>


		<!--Page content-->
		<!--===================================================-->
		<section class="content">

<?php
if (isset($_POST['add'])) {
    $name       = $_POST['name'];
    $image      = $_POST['image'];
    $creditos   = $_POST['creditos'];
    $energy   	= $_POST['energy'];
    $hp        	= $_POST['hp'];
    
    $query = mysqli_query($connect, "INSERT INTO `pets` (name, image, creditos, respect, energy, hp) VALUES 
		('$name', '$image', '$creditos', 100, '$energy', '$hp')");
}
?>
                    
<div class="row">
	<div class="col-md-12">

<?php
if (isset($_GET['edit-id'])) {
    $id  = (int) $_GET["edit-id"];
    $sql = mysqli_query($connect, "SELECT * FROM `pets` WHERE id = '$id'");
    $row = mysqli_fetch_assoc($sql);
    if (empty($id)) {
        echo '<meta http-equiv="refresh" content="0; url=pets.php">';
    }
    if (mysqli_num_rows($sql) == 0) {
        echo '<meta http-equiv="refresh" content="0; url=pets.php">';
    }
    
    if (isset($_POST['edit'])) {
        $name       = $_POST['name'];
        $hp   		= $_POST['hp'];
        $energy   	= $_POST['energy'];
        $creditos   = $_POST['creditos'];
        $frases   	= json_encode($_POST['frase']);
        $image      = json_encode($_POST['image']);
        
        $query = mysqli_query($connect, "UPDATE pets SET 
			name='{$name}', 
			image='{$image}', 
			creditos='{$creditos}', 
			frases='{$frases}', 
			hp='{$hp}', 
			energy='{$energy}'
			WHERE id='{$id}'");
		
        echo '<meta http-equiv="refresh" content="0" url="pets.php">';
        
    }
	
?>
	<form class="form-horizontal" action="" method="post">
		<input type="hidden" name="edit" value="true">
		<div class="box">
			<div class="row">
				<div class="col-sm-12 col-md-4">
					<div class="box-header">
						<h3 class="box-title">Pet info</h3>
					</div>
					
					<div class="box-body">
					
						<div class="form-group">
							<label class="col-sm-3 control-label">Pet Name: </label>
							<div class="col-sm-9">
								<input type="text" name="name" class="form-control" value="<?php echo $row['name'];?>" required>
							</div>
						</div>							
						<div class="form-group">
							<label class="col-sm-3 control-label">precio: </label>
							<div class="col-sm-9">
								<input type="number" name="creditos" class="form-control" value="<?php echo $row['creditos']; ?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">hp: </label>
							<div class="col-sm-9">
								<input type="number" name="hp" class="form-control" value="<?php echo $row['hp'];?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">energy: </label>
							<div class="col-sm-9">
								<input type="number" name="energy" class="form-control" value="<?php echo $row['energy'];?>" required>
							</div>
						</div>
							
					</div>
			
				</div>
				<div class="col-sm-12 col-md-4">
					<div class="box-header">
						<h3 class="box-title">Frases</h3>
					</div>
					
					<div class="box-body">
						<div id="FrasesBox">
							<?php 
								$Frases = json_decode($row['frases']);
								if(!is_null($Frases)):
									foreach($Frases as $frase):						
							?>
								<div class="form-group">
									<div class="col-sm-12">
										<input type="text" name="frase[]" class="form-control" value="<?php Echo $frase;?>">
									</div>
								</div>
							<?php 
									endforeach;
								else:
							?>
								<div class="form-group">
									<div class="col-sm-12">
										<input type="text" name="frase[]" class="form-control">
									</div>
								</div>
							<?php 
								endif;
							?>
						</div>
						
						<div class="form-group">
							<div class="col-sm-12">
								<div class="form-control btn-success" id="addFrase">Añadir frase</div>
							</div>
						</div>
							
					</div>
				</div>
				<div class="col-sm-12 col-md-4">
					<div class="box-header">
						<h3 class="box-title">Imagenes</h3>
					</div>
					
					<div class="box-body">
					
						<div class="form-group">
							<label class="col-sm-3 control-label">Normal: </label>
							<div class="col-sm-9">
								<input type="text" name="image[normal]" class="form-control" value="<?php echo petImg($row['image'])->imgNormal; ?>" required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Comiendo: </label>
							<div class="col-sm-9">
								<input type="text" name="image[feed]" class="form-control" value="<?php echo petImg($row['image'])->imgFeed; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">Jugando: </label>
							<div class="col-sm-9">
								<input type="text" name="image[game]" class="form-control" value="<?php echo petImg($row['image'])->imgGame; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-3 control-label">50% de vida: </label>
							<div class="col-sm-9">
								<input type="text" name="image[lifelow]" class="form-control" value="<?php echo petImg($row['image'])->lifelow; ?>">
							</div>
						</div>
							
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<button class="btn btn-flat btn-success" type="submit">Save</button>
				<button type="reset" class="btn btn-flat btn-default">Reset</button>
			</div>
		</div>
	</form>
	

<script>
$(document).ready(function() {
	$("#addFrase").on("click", function(){
		var newItem = '<div class="form-group">'
			+'<div class="col-sm-12">'
				+'<input type="text" name="frase[]" class="form-control">'
			+'</div>'
		+'</div>';
		
		$("#FrasesBox").append(newItem);
	})
} );
</script>
<?php
}
?>
				
				    <div class="box">
						<div class="box-header">
							<h3 class="box-title">Pets</h3>
						</div>
						<div class="box-body">
<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>ID</th>
											<th>Pet Name</th>
											<th>creditos</th>
											<th>Vida</th>
											<th>Energia</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
<?php
$query = mysqli_query($connect, "SELECT * FROM `pets`");
while ($row = mysqli_fetch_assoc($query)) {
    echo '<tr>
		<td>' . $row['id'] . '</td>
		<td><center><img src="' . petImg($row['image'])->imgNormal . '" width="50px" height="50px"> ' . $row['name'] . '</center></td>
		<td>' . $row['creditos'] . '</td>
		<td>' . $row['hp'] . '</td>
		<td>' . $row['energy'] . '</td>
		<td>
		<a href="?edit-id=' . $row['id'] . '" class="btn btn-flat btn-flat btn-primary"><i class="fas fa-edit"></i> Edit</a>
		<a href="?delete-id=' . $row['id'] . '" class="btn btn-flat btn-flat btn-danger"><i class="fas fa-trash"></i> Delete</a>
		</td>
	</tr>';
}
?>
									</tbody>
								</table>
                        </div>
                     </div>
                </div>
                    
				<div class="col-md-12">
					<form class="form-horizontal" action="" method="post">
						<input type="hidden" name="add" value="true">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">Add Pet</h3>
							</div>
							<div class="box-body">
								<div class="form-group">
									<label class="col-sm-4 control-label">Pet Name: </label>
									<div class="col-sm-8">
										<input type="text" name="name" class="form-control" required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Image: </label>
									<div class="col-sm-8">
										<input type="text" name="image" class="form-control" placeholder="images/pets/" required>
									</div>
								</div>								
								<div class="form-group">
									<label class="col-sm-4 control-label">precio: </label>
									<div class="col-sm-8">
										<input type="number" name="creditos" min="0" class="form-control" value="" required>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">hp: </label>
									<div class="col-sm-8">
										<input type="number" name="hp" min="0" class="form-control" value="" required>
									</div>
								</div>	
								<div class="form-group">
									<label class="col-sm-4 control-label">energy: </label>
									<div class="col-sm-8">
										<input type="number" name="energy" min="0" class="form-control" value="" required>
									</div>
								</div>									
							</div>
							<div class="panel-footer">
								<button class="btn btn-flat btn-primary" type="submit">Add</button>
								<button type="reset" class="btn btn-flat btn-default">Reset</button>
							</div>
						</div>
					</form>

				
		</div>
				<!--===================================================-->
				<!--End page content-->


	</div>
	</div>
	</div>
			<!--===================================================-->
			<!--END CONTENT CONTAINER-->
<?php
footer();
?>