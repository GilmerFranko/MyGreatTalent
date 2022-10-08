<?php
include "../config.php";


$sqlusers = mysqli_query($connect, "SELECT * FROM `players` WHERE refcodigo = 0");
while ($rowuser = mysqli_fetch_assoc($sqlusers)) {
	do {
    $crearcodigoreferidos = rand(111111,999999); 
	$sqlexiste = mysqli_query($connect, "SELECT * FROM `players` WHERE refcodigo='$crearcodigoreferidos'");
  
  } while (mysqli_num_rows($sqlexiste) > 0);
  
        $userupdate = mysqli_query($connect, "UPDATE `players` SET refcodigo='$crearcodigoreferidos' WHERE id='$rowuser[id]'");
    
}


?>