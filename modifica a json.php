<?php 
include "core.php";
	$sqlfotosenventa = mysqli_query($connect, "SELECT id,imagen FROM `fotosenventa` limit 0,100");
	while($fotosenventa = mysqli_fetch_assoc($sqlfotosenventa) )
	{
		$upd= '["'.$fotosenventa['imagen'].'"]';
		mysqli_query($connect,"UPDATE fotosenventa SET imagen='$upd' WHERE id='$fotosenventa[id]'");
	}
 ?>