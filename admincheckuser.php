<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
		
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;
}else{


if (isset($_POST['suspender'])) {
	$razon=$_POST['razon'];
    if ($_GET['userid']>=1){
$rowua = $_GET['userid'];
}elseif (isset($_GET['username'])){
	
	$usernameu = $_GET['username'];
	
	$sqlusernameu = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$usernameu' LIMIT 1");
    $rowusernameu = mysqli_fetch_assoc($sqlusernameu);
   
   $rowua = $rowusernameu['id'];
	
}
    $query = mysqli_query($connect, "UPDATE `players` SET banned=1, banned_reason='$razon' WHERE id='$rowua'");


}

if (isset($_POST['deleteallref'])) {
    if ($_GET['userid']>=1){
$rowua = $_GET['userid'];
}elseif (isset($_GET['username'])){
	
	$usernameu = $_GET['username'];
	
	$sqlusernameu = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$usernameu' LIMIT 1");
    $rowusernameu = mysqli_fetch_assoc($sqlusernameu);
   
   $rowua = $rowusernameu['id'];
	
}
    $query = mysqli_query($connect, "DELETE FROM `players` WHERE referer_id='$rowua'");


}
?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-dollar-sign"></i> User Check</h1>
				  


    			  <ol class="breadcrumb">
   			         <li><a href="dashboard.php"><i class="fas fa-home"></i> Admin Panel</a></li>
    			     <li class="active">User Check</li>
    			  </ol>
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">

                <div class="row">
                    
				<div class="col-md-12">
                    <div class="box">
						<div class="box-header">
							<h3 class="box-title">User Check</h3>
						</div>
						<div class="box-body">
						     <form action="" method="GET">

  <p>Id: <input type="text" name="userid" value=""></p>
  Or
  <br>
  <p>Username: <input type="text" name="username" value=""></p>
  <p>

    <input type="submit" value="Enviar">
  </p>

</form>

							 <table id="dt-basic" class="table table-bordered table-hover no-margin">
                  <thead>
                  <tr >
				  
				    <th><i class=""></i> ID</th>
                    <th><i class=""></i> Nombre</th>

                  </tr>
                  </thead>
                  <tbody>
<?php
if ($_GET['userid']>=1){
$rowua = $_GET['userid'];
}elseif (isset($_GET['username'])){
	
	$usernameu = $_GET['username'];
	
	$sqlusernameu = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$usernameu' LIMIT 1");
    $rowusernameu = mysqli_fetch_assoc($sqlusernameu);
   
   $rowua = $rowusernameu['id'];
	
}
    $pid   = $rowua;
    
    $sqlsu = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$pid' LIMIT 1");
    $rowsu = mysqli_fetch_assoc($sqlsu);
    $rowsuser = $rowsu['username'];

	$resultr = mysqli_query($connect, "SELECT SUM(monto) as `total` FROM `retiros` WHERE usuario='$rowua'");  
$rowr = mysqli_fetch_array($resultr, MYSQLI_ASSOC);
 


   echo '
                  <tr>
				    <td>' . $rowua . '</td>

                    <td>' . $rowsu['username'] . '</td>

                  </tr>
';

?>				  
                  </tbody>
                </table>
				<br><br>
	<!--segunda tabla-->		
	
				<table id="dt-basic" class="table table-bordered table-hover no-margin">
                  <thead>
                  <tr>

                  </tr>
                  </thead>
                  <tbody>
 
                  </tbody>
                </table>
						<br><br>	 
		
		<!--cuarta tabla-->		
	
				<table id="dt-basic" class="table table-bordered table-hover no-margin">
                  <thead>
                  <tr>
				    <th><i class=""></i> Creditos Normales</th>
                    <th><i class=""></i> Creditos Especiales</th>

                  </tr>
                  </thead>
                  <tbody>
<?php
  $queryreferidos = mysqli_query($connect, "SELECT * FROM `players` WHERE referer_id='$pid'");
  $countreferidos = mysqli_num_rows($queryreferidos);

   echo '
                  <tr>
				    <td>' . $rowsu["creditos"] . '</td>

                    <td>' . $rowsu['eCreditos'] . '</td>

                  </tr>
';

?>				  
                  </tbody>
                </table>
				<br><br>
				<!--quinta tabla-->		
	
				<table id="dt-basic" class="table table-bordered table-hover no-margin">
                  <thead>
                  <tr>
				    <th><i class=""></i> IP</th>


                  </tr>
                  </thead>
                  <tbody>
<?php

   echo '
                  <tr>
				    <td>' . $rowsu["ipaddres"] . '</td>


                  </tr>
';

?>				  
                  </tbody>
                </table>
				
				<br><br>
				<!--sexta tabla-->		
	
				<table id="dt-basic" class="table table-bordered table-hover no-margin">
                  <thead>
                  <tr>
				    <th><i class=""></i> Email</th>





                  </tr>
                  </thead>
                  <tbody>
<?php


   echo '
                  <tr>
				    <td>' . $rowsu["email"] . '</td>

                  </tr>
';

?>				  
                  </tbody>
</table>

						</div><br/>
						<form action="edituser.php" method="GET">

   <input type="hidden" name="edit-id" value="<?php echo $pid; ?>">
  <p>

    <input type="submit"  class="btn btn-primary" value="EDITAR USUARIO">
  </p>

</form>
						<br/><br/>
						<br/>

					

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
}
?>