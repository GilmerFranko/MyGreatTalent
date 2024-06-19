<?php

include '../config.php';

$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa`");
$countcp = mysqli_num_rows($querycp);
if ($countcp > 0) {
  while ($rowcp = mysqli_fetch_assoc($querycp)) {

    if(!$rowcp['thumb'] or $rowcp['thumb']==''){
      $thumb = 'thumb/'. (explode('/', $rowcp['imagen'])[2]);
      $thumb = '["' . $thumb . '"]';
      $id = $rowcp['id'];
			$connect->query("UPDATE `fotosenventa` SET thumb='{$thumb}' WHERE id='{$id}'");		
      
      Echo $thumb .'<br>';
    }
  }
}