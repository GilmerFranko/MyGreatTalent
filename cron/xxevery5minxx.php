<?php
/*
 * Cron para ejecutar acciones cada 5 minutos
 */
include "../config.php";

/* --- REGALAR CREDITOS AL ENTRAR A LA ULTIMA IMAGEN SUBIDA SI ESTA SE SUBIO HACE MAS DE 30MIN ---*/

$sqlLI = $connect->query("SELECT * FROM `fotosenventa` ORDER BY id DESC LIMIT 1");

$sqlPGC = $connect->query("SELECT * FROM `photo_gift_credits`");

$lastImage = $sqlLI->fetch_assoc();

$lastPGC = ($sqlPGC->num_rows > 0) ? $sqlPGC->fetch_assoc() : false;

$time = $lastImage['time'] + (60*30);

//echo strftime("%M:%S", $time) .'///'. strftime("%M:%S", time());


/*COMPROBAR SI LA ULTIMA FOTO DE FOTOSENVENTA FUE HACE 30MIN O MAS*/

// SI EXISTE UNA FOTO DE REGALO, COMPROBAR QUE NO SEA LA MISMA FOTO DE HACE 30MIN (mantiene la misma foto de regalo hasta que suban una nueva)
if(time() >= $time AND $lastPGC != false AND $lastPGC['photo_id'] != $lastImage['id'])
{
	// VACIAR TABLA
	$connect->query("TRUNCATE `photo_gift_credits`");

	// GENERAR NUEVA FOTO DE REGALO
	$connect->query("INSERT INTO `photo_gift_credits` (photo_id, given) VALUES ('$lastImage[id]', '[\"\"]')");
}
// SI NO HAY FOTOS DE REGALO, CREAR UNA
elseif($lastPGC == false AND time() >= $time)
{
	// VACIAR TABLA (por si acaso)
	$connect->query("TRUNCATE `photo_gift_credits`");

	// GENERAR NUEVA FOTO DE REGALO
	$connect->query("INSERT INTO `photo_gift_credits` (photo_id, given) VALUES ('$lastImage[id]', '[\"\"]')");
}

/**
 * Publicar packs programados
 * colocar en cron de 1 minuto
 */

$time = time();

/* Optiene todos los packsprogramados los cuales estén caducados */
$consult = $connect->query('SELECT * FROM `packsprogramados` WHERE `time` < '. $time);

if($consult->num_rows > 0)
{
  while($pack = $consult->fetch_assoc())
  {
    if(publicarPackProgramado($pack['id']))
    {
      error_log($pack['id'] . PHP_EOL);
    }
    else
    {
      error_log('Pack no publicado. Pack: ' . $pack['id']);
    }
  }
}



