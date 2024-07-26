<?php
/**
 * Agrega usuario que hayan comprado créditos (que esten en players_movements) a players_namesactions
 */

include "../config.php";

$a = 0;
$select = $connect->query('SELECT player_id FROM `players_movements` WHERE `description` = 3');

if($select and $select->num_rows > 0)
{

	// Inicia ciclo
	while ($user = $select->fetch_assoc())
	{
		$a++;
		// Verifica si en players_namesactions está este usuario
		$select2 = $connect->query('SELECT player_id FROM `players_namesactions` WHERE `player_id` = \''. $user['player_id'] .'\'');
		if($select2 and $select2->num_rows <= 0)
		{
			$insert = $connect->query('INSERT INTO `players_namesactions`(`player_id`, `player_add`, `time`) VALUES(\''. $user['player_id'] .'\', "Admin", \''. time() .'\')');

			if($insert)
			{
				echo 'Correcto' . PHP_EOL;
			}
		}
		else
		{
			//echo "No esta el usuario " . $user['player_id'];
		}
	}
}
else
{
	echo "Algo falló";
}


?>
