<?php
require("core.php");
head();

if ($rowu['role'] != 'Admin'){ 
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
    exit;
}else{

if (isset($_POST['copyFriends'])) {
	$userId01 = $_POST['userId01'];
	$userId02 = $_POST['userId02'];
    $query = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='{$userId02}' OR player2='{$userId02}'");
	
	$FriendList = [];
	if(mysqli_num_rows($query)){
		while($friend = mysqli_fetch_assoc($query)){
			
			if($friend['player1'] != $userId02){
				$friend = $friend['player1'];
			}
			elseif($friend['player2'] != $userId02){
				$friend = $friend['player2'];
			}
			
			$FriendList[ $friend ] = $friend;
		}
	}
	
    $query = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE fromid='{$userId02}' OR toid='{$userId02}'");
	
	$lockList = [];
	if(mysqli_num_rows($query)){
		while($look = mysqli_fetch_assoc($query)){
			
			if($look['fromid'] != $userId02){
				$fid = $look['fromid'];
			}
			elseif($look['toid'] != $userId02){
				$fid = $look['toid'];
			}
			
			$lockList[ $fid ] = $fid;
		}
	}
	
    $query = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='{$userId01}' OR player2='{$userId01}'");
	
	$TFriendList = [];
	if(mysqli_num_rows($query)){
		while($friend = mysqli_fetch_assoc($query)){
			
			if($friend['player1'] != $userId01){
				$friend = $friend['player1'];
			}
			elseif($friend['player2'] != $userId01){
				$friend = $friend['player2'];
			}
			
			$TFriendList[ $friend ] = $friend;
		}
	}
	
	foreach($TFriendList as $userId){
		if(isset($lockList[$userId])){
			continue;
		}
		if(isset($FriendList[$userId])){
			continue;
		}
		if($userId == $userId02){
			continue;
		}
		
		$post_mensaje = mysqli_query($connect, "INSERT INTO `friends` (player1, player2) VALUES ('{$userId}', '{$userId02}')");
		
	}
}

?>
<div class="content-wrapper">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			  <h1><i class="fas fa-copy"></i> Copiar Amigos</h1>
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">

					<div class="row">
						
						<div class="col-md-12">
							<div class="box">
								
								<div class="box-body">
									 <form action="" method="POST">
										<input type="hidden" name="copyFriends" value="1">
										<p>
											<h3 class="box-title">ID del usuario a copiar lista de amigos</h3>										
											<input type="text" name="userId01" style="padding:10px 20px;border-radius:7px;border:1px solid #ddd;width:95%;max-width:350px;">
										</p>
										<br/>
										<p>
											<h3 class="box-title">ID del usuario al cual se le agregara la lista de amigos</h3>		
											<input type="text" name="userId02" style="padding:10px 20px;border-radius:7px;border:1px solid #ddd;width:95%;max-width:350px;">
										</p>
										<input type="submit" value="Enviar">
										</p>
									</form>
								</div>							

							</div>
						</div>
					</div>
                    
				</div>
			</div>

<?php
footer();
}
?>