<?php
/**Este archivo se encarga de borrar todas las suscipciones vencidas.
ArrFollower:Almacena conjunto de json's que contendran id y tiempo de suscripcion
Infollower:Almacena json unico que contendra id y el tiempo de suscripcion.
 Follower: Almcacena fila de un usuario.
 **/
 include '../config.php';
	//DECLARO VARIABLES
 $ActualTime = time();
 $ArrFollower = [];
 $InFollower = [];
 $InFollower=[];
 $ArrFollower2 = [];
 $InFollower2 = [];
 $InFollower2=[];

    //SELECCIONA TODOS LOS USUARIOS QUE TENGAN SEGUIDORES
 $queryall = mysqli_query($connect, "SELECT * FROM `players` WHERE followers IS NOT NULL");
 while ($subscrip = mysqli_fetch_assoc($queryall)) {
		//DECODIFICAR JSON
 	if(!is_null($subscrip['followers'])){
 		$ArrFollower = json_decode($subscrip['followers'], true);
 	}
 	$jsondecode = $subscrip['followers'];
 	if(count($ArrFollower)){  
 		foreach($ArrFollower as $key => $Frs){
 			unset($InFollower);
            //ALMACENARLO
 			$InFollower[] = $key;

 			$InFollower = implode(',', $InFollower);

	        //SELECCIONAR A QUIEN ESTA SUSCRITO ESTE USUARIO
 			$follower = mysqli_query($connect, "SELECT * FROM `players` WHERE id IN ({$InFollower})");

 			if($follower){
 				$row = mysqli_fetch_assoc($follower);

 				$Follow = true;
 				if ($ArrFollower[ $row['id'] ]<$ActualTime) {
 					$delete="\"$row[id]\":". $ArrFollower[$row['id']];
 					echo $delete."<br><br>";
 					$jsondecode=str_replace(",$delete,", ",", $jsondecode);
 					$jsondecode=str_replace("$delete,", "", $jsondecode);
 					$jsondecode=str_replace(",$delete", "", $jsondecode);
 					$jsondecode=str_replace("$delete", "", $jsondecode);
 					$description=
 					$connect->query("INSERT `notificaciones_suscripcionesvencidas` (usera,userb,see) VALUES ('$row[id]','$subscrip[id]','0')");
 				}else{
 					continue;
 				}

			//echo $jsondecode . "<br>";
 			}

 		}
 	}
 	$connect->query("UPDATE `players` SET followers='$jsondecode' WHERE id='$subscrip[id]'");
 }
 $queryall = mysqli_query($connect, "SELECT * FROM `players` WHERE follower IS NOT NULL");
	//REPETIR LO MISMO PERO AHORA CON LA COLUMNA FOLLOWER
 while ($subscrip = mysqli_fetch_assoc($queryall)) {
 	if(!is_null($subscrip['follower'])){
 		$ArrFollower2 = json_decode($subscrip['follower'], true);
 	}
 	$jsondecode2 = $subscrip['follower'];
 	if(count($ArrFollower2)){  
 		foreach($ArrFollower2 as $key => $Frs){
 			unset($InFollower2);
 			$InFollower2[] = $key;
 			$InFollower2 = implode(',', $InFollower2);
	        //SELECCIONAR A CUAL ESTA SUSCRITO ESTE USUARIO
 			$follower2 = mysqli_query($connect, "SELECT * FROM `players` WHERE id IN ({$InFollower2})");
 			if($follower2){
 				$row2 = mysqli_fetch_assoc($follower2);

 				$Follow2 = true;
 				if ($ArrFollower2[ $row2['id'] ]<$ActualTime) {
 					$delete2="\"$row2[id]\":". $ArrFollower2[$row2['id']];
 					echo $delete2."<br><br>";
 					$jsondecode2=str_replace(",$delete2,", ",", $jsondecode2);
 					$jsondecode2=str_replace("$delete2,", "", $jsondecode2);
 					$jsondecode2=str_replace(",$delete2", "", $jsondecode2);
 					$jsondecode2=str_replace("$delete2", "", $jsondecode2);
 				}
 			}else{
 				continue;
 			}
 		}
 	}
 	$connect->query("UPDATE `players` SET follower='$jsondecode2' WHERE id='$subscrip[id]'");
	    //echo '<meta http-equiv="refresh" content="0;url='.$sitio['site'].'subscriberslist.php">';
 }
 
 
		/*$connect->query("UPDATE `players` SET follower='". json_encode($FollowList, true) ."', eCreditos=eCreditos-2000 WHERE id='{$User->id}'"); // valor de la suscripcion
		$connect->query("UPDATE `players` SET followers='". json_encode($FollowersList, true) ."', eCreditos=eCreditos+1600 WHERE id='{$toUser->id}'"); // cuanto gana quien te sucribes
		
		$return['FollowCount'] = count($FollowList);
		$return['isFollow'] = $Follow;

		return $return;*/
		

		?>
