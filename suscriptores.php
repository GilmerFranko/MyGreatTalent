<?php
require("core.php");
head();

$id = $player_id;
$timeonline = time() - 60;

$Followers = [];
if(!is_null($rowu['followers'])){
	$Followers = json_decode($rowu['followers'], true);
}

$InFollowers = [];
if(count($Followers)){	
	foreach($Followers as $key => $Frs){
		$InFollowers[] = $key;
	}
}

$InFollowers = implode(',', $InFollowers);

$followers = mysqli_query($connect, "SELECT * FROM `players` WHERE referer_id='$player_id'");
$followers = mysqli_num_rows($followers);

//requests añadir codigo de referer

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<div class="content-wrapper" height="10%">
	<div id="content-container">
	
		<section class="content">

			<div class="row">                  

				<div class="col-12 col-md-6">
					
					<?php
						
						$ArrFollower = [];
						if(!is_null($rowu['follower'])){
							$ArrFollower = json_decode($rowu['follower'], true);
						}

						$InFollower = [];
						if(count($ArrFollower)){	
							foreach($ArrFollower as $key => $Frs){
								$InFollower[] = $key;
							}
						}

						$InFollower = implode(',', $InFollower);
					?>
					
					<section class="content-header">
						<h1><i class="fas fa-user-plus"></i> Mis Suscripciones (<?php Echo count($ArrFollower); ?>)</h1>    			  
					</section>
					
					<?php
						$follower = mysqli_query($connect, "SELECT * FROM `players` WHERE id IN ({$InFollower})");
						if($follower && mysqli_num_rows($follower)){
							while($row = mysqli_fetch_assoc($follower)){
								?>
									<div class="row" style="margin:10px 0;padding:10px;background:white;border-radius:5px;">
										<div class="col-md-6">
											<img src="<?php Echo $row['avatar'];?>" style="border-radius:50%;width:50px;">
											<span style="margin-left:15px;">
												<strong><?php Echo $row['username'];?></strong>
											</span>
										</div>
										<div class="col-md-6">
											<div style="margin:15px 0;">
											
												<span class="countdown-bar anime-countdown" data-controller="countdown-bar" data-countdown-timestamp="<?php Echo $ArrFollower[ $row['id'] ];?>">
													<span class="u-days">?</span><span>d</span> 
													<span class="u-hours">?</span><span>h</span> 
													<span class="u-minutes">?</span><span>m</span>
													<span class="u-seconds">?</span><span>s</span>
												</span>
												
											</div>
										</div>
									</div>
								<?php
							}
						}else {
							Echo "No tienes suscripciones activas";
						}
					?>

				</div>             

				<div class="col-12 col-md-6">
					
					<?php

						$ArrFollowers = [];
						if(!is_null($rowu['followers'])){
							$ArrFollowers = json_decode($rowu['followers'], true);
						}

						$InFollowers = [];
						if(count($ArrFollowers)){	
							foreach($ArrFollowers as $key => $Frs){
								$InFollowers[] = $key;
							}
						}

						$InFollowers = implode(',', $InFollowers);
					?>
					
					<section class="content-header">
						<h1><i class="fas fa-user-plus"></i> Suscriptores (<?php Echo count($ArrFollowers); ?>)</h1>    			  
					</section>
					
					<?php

						$followers = mysqli_query($connect, "SELECT * FROM `players` WHERE id IN ({$InFollowers})");
						if($followers && mysqli_num_rows($followers)){
							while($row = mysqli_fetch_assoc($followers)){
								?>
									<div class="row" style="margin:10px 0;padding:10px;background:white;border-radius:5px;">
										<div class="col-md-6">
											<img src="<?php Echo $row['avatar'];?>" style="border-radius:50%;width:50px;">
											<span style="margin-left:15px;">
												<strong><?php Echo $row['username'];?></strong>
											</span>
										</div>
										<div class="col-md-6">
											<div style="margin:15px 0;">
											
												<span class="countdown-bar anime-countdown" data-controller="countdown-bar" data-countdown-timestamp="<?php Echo $ArrFollowers[ $row['id'] ];?>">
													<span class="u-days">?</span><span>d</span> 
													<span class="u-hours">?</span><span>h</span> 
													<span class="u-minutes">?</span><span>m</span>
													<span class="u-seconds">?</span><span>s</span>
												</span>
												
											</div>
										</div>
									</div>
								<?php
							}
						}else {
							Echo "Tus suscriptores";
						}
					
					?>
					
				</div>
	
			</div>
            <div class="row">
            	<br><br>
            	<h3>Suscripciones Vencidas</h3>
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">



                                <thead>
                                </thead>
                                <tbody>
                                </tbody>
                                <?php
    //BORRAR NOTIFICACION
    if (isset($_GET['trash_s']) and !empty($_GET['trash_s']) ) {
    	$idtrash = mysqli_real_escape_string($connect,$_GET['trash_s']);
    	$select = mysqli_query($connect,"SELECT id,usera FROM `notificaciones_suscripcionesvencidas` WHERE id=$idtrash");
    	if ($select) {
    		if (mysqli_num_rows($select)>0) {
    			$row_delete=mysqli_fetch_assoc($select);

    			//SI ES PROPIETARIO DE LA NOTIFICACION
    			if ($row_delete['usera']==$rowu['id']) {

    				//BORRAR
$delete = mysqli_query($connect, "UPDATE `notificaciones_suscripcionesvencidas` SET see=1 WHERE id=$idtrash");
    			}
    		}
    	}
    }
    if ($query = $connect->query("SELECT * FROM notificaciones_suscripcionesvencidas WHERE usera = {$player_id} and see=0 ORDER BY id DESC")) {
	while ($friend = mysqli_fetch_assoc($query)){
		
		$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '". $friend['userb'] ."'");
		$rowsuser = mysqli_fetch_assoc($sqluser);
	
		?>
			<tr>
				<td>
					<div class="col-sm-6 col-md-6 text-left" style="padding:11px;">
						Tu suscripción a <a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowsuser['id']; ?>"><?php echo $rowsuser['username']; ?></a> a expirado	
						<?php				
						?>
					</div>
					<div class="col-sm-6 col-md-6 text-center" style="padding:5px 11px;">
						<div class="display:inline-block;">
							<?php if($rowu['eCreditos']>=2000){ ?>
								<a href="#" data-username="<?php echo $rowsuser['username']; ?>" data-href="<?php echo $sitio['site'].'profile.php?profile_id='.$rowsuser['id'].'&Subscribe'; ?>" id="suscribe" class="btn btn-success dropdown-item width" >
								Suscribirme de nuevo
								</a>
							<?php }else{ ?>
								<a href="comprar.php" class="btn btn-danger" style="color:white;background-color:#000000db;border:none;">Comprar créditos especiales
								<?php } ?>
							<a href="<?php echo $sitio['site']; ?>suscriptores.php?trash_s=<?php echo $friend['id']; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<?php }
		}else{ ?>
			<tr>
				<td>
					<div class="col-sm-6 col-md-6 text-left" style="padding:11px;">
						
					</div>
				</td>
			</tr>
		<?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
		</section>
	</div>
</div>

<script>
var CountDown = {

	currentTimeInSeconds: function() {
		return Date.now() / 1e3
	},
	
	days: function(time) {
		return (time - CountDown.currentTimeInSeconds()) / 86400 | 0
	},
	
	hours: function(time) {
		return (time - CountDown.currentTimeInSeconds()) / 3600 % 24 | 0
	},
	
	minutes: function(time) {
		return (time - CountDown.currentTimeInSeconds()) / 60 % 60 | 0
	},
	
	seconds: function(time) {
		return (time - CountDown.currentTimeInSeconds()) % 60 | 0
	},
	
	start: function() {
		setInterval(function(){
			$("[data-controller=countdown-bar]").each(function(){
				var timestamp = $(this).data("countdown-timestamp");
				if((timestamp - CountDown.currentTimeInSeconds()) > 0){
					$(this).find(".u-days").html( CountDown.days(timestamp) );
					$(this).find(".u-hours").html( CountDown.hours(timestamp) );
					$(this).find(".u-minutes").html( CountDown.minutes(timestamp) );
					$(this).find(".u-seconds").html( CountDown.seconds(timestamp) );
				}
			})
		}, 1000);
	}
}
$(document).ready(function(){
	CountDown.start();
});
</script>
<?php
footer();
?>