<?php 
	include "../config.php";
	//OPTENER NUMERO DE PAGINAS
	$page = 41;
	//$total_pages = $connect->query("SELECT * FROM `fotosenventa` ORDER BY id DESC")->num_rows;
	$total_pages = $connect->query("SELECT id FROM `fotosenventa` LIMIT 80, 120")->num_rows;
	$num_results_on_page = 2;
	$calc_page = ($page - 1) * $num_results_on_page;
	if (ceil($total_pages/2)==1) {
		exit;
	}
	for ($i=0; $i <ceil($total_pages/2) ; $i++) {
		$pass=true;
		//Optener todas las fotos de x a x filas
		$suscripciones = mysqli_query($connect, "SELECT * FROM `fotosenventa`ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");

		//Comprobar si en la consulta hay alguna foto VIP
		if ($suscripciones){
			while ($rowcp = mysqli_fetch_assoc($suscripciones)) {
				if ($rowcp['type']=='suscripciones') {
					$pass=false;
				}
			}
		}

		//Si hay fotos VIP en esta consulta
		if($pass==false){
			echo "Esta consulta no sera cambiada <br>";
			echo $rowcp['type'];

		//De no haber, convertir la mitad de esa consulta en fotos VIP
		}else{
			nopublic($calc_page,$num_results_on_page,$connect);
		}

		$page++;
		$calc_page = ($page - 1) * $num_results_on_page;
	}
	function nopublic($calc_page,$num_results_on_page,$connect){
		$querycp = mysqli_query($connect, "SELECT * FROM `fotosenventa` ORDER BY id DESC LIMIT  {$calc_page}, {$num_results_on_page}");
		if($querycp){
			$countcp = mysqli_num_rows($querycp);
			//GENERA N NUMERO ALEATORIOS NO REPETIDOS
			$rand = generaterandom(0,$countcp-1);
			if ($countcp > 0) {
				$b=0;
				$a=0;
				//ALMACENA FILAS Y COLUMNAS DE LA CONSULTA EN UN ARRAY
				while ($rowcp = mysqli_fetch_row($querycp)) {
					for ($i=0; $i < 7; $i++) { 
						$row[$a][$i]=$rowcp[$i];
					}
					$a++;
				}
				/*GENERA LA CONSULTA
				CAMBIARA LA MITAD DE LA PAGE COMO PRETEMINADO(50%)
				*/
				$mitad=ceil($countcp/2);
				$count=0;
				for ($i=0; $i <$mitad; $i++) {
					$imagen=json_decode($row[$rand[$i]][2]);
					$countimage=count($imagen);
					$idd=$row[$rand[$i]][0];
					//SOLO MODIFICARA IMAGENES SIN LINKS
					if($row[$rand[$i]][5]=="" or $row[$rand[$i]][5]==" " or $row[$rand[$i]][5]==null){
						//SOLO IMAGENES, VIDEOS NO Y SOLO IMAGENES QUE NO SON GALERIAS
						if (getSourceType($imagen[0]) != 'film' AND $countimage<=1) {
							$querycp = mysqli_query($connect, "UPDATE `fotosenventa` SET `type`=\"suscripciones\" WHERE id=$idd");
							if($querycp){
								$count++;
							}
						}
					}
				}
				//SI NO SE MODIFICARON FILAS
				if ($count==0) {
					echo 'no se modificaron filas';
				}
				else
				{

				echo '<br>filas modificadas '. $count . '<br>';
				}

			}
			else
			{
				echo "<script>swal('No hay filas que modificar','ERROR','error');</script>";
				//echo '<meta http-equiv="refresh" content="1; url='.$sitio['site'].'profile.php?profile_id='.$_GET['profile_id'].'" />';
			}
		}
	}
?>