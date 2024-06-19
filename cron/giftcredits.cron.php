<?php 
	include "../config.php";
	
	$rang = 7;

	$consult = $connect->query("TRUNCATE `giftcredits`");
	if ($consult)
	{

		$consult = $connect->query("SELECT * FROM `fotosenventa` ORDER BY id DESC LIMIT 0,$rang");
		if ($consult) {
			if(!empty($consult) AND mysqli_num_rows($consult) >= $rang) {
				$numrand = rand(0,$rang-1);

				//ALMACENA CONSULTA COMPLETA
				$rowfotos = arrayONarray($consult);

				$idfoto = $rowfotos[$numrand][0];

				//INSERTA NUEVA GIFTCREDITS
				$consult = $connect->query("INSERT INTO `giftcredits` (`foto_id`, `used`,`given`) VALUES ('$idfoto',0,'[\"\"]')");
				if ($consult) {
					echo "Agregado";
				}
				
			}
		}
		
	}