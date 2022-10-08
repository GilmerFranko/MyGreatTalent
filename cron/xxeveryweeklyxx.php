<?php
include "../config.php";
/**
 * CRON QUE SE ENCARGARA DE EJECUTAR CIERTAS FUNCIONES SEMANALMENTE
 */

/**
 * FUNCION QUE SE ENCARGA DE REGALAR CREDITOS SEMANALMENTE A LOS USUARIOS
 */
// Selecciona todos los usuarios que no tengan un regalo semanal (usuarios nuevos o usuarios que ya han aceptado su regalo)
$consult0 = $connect->query('SELECT p.`id` AS idPlayer FROM `players` AS p LEFT JOIN `giftcredits_weekly` AS gw ON gw.`player_id` = p.`id` WHERE gw.`id` IS NULL');

// SI HAY MAS DE UN USUARIO
if($consult0 AND $consult0->num_rows > 0)
{
  /* Selecciona a todos los usuarios que no esten en la lista e inserta una nueva fila (inserta un nuevo regalo para el usuario que no este en la lista)
  */
  $consult = $connect->query('INSERT INTO giftcredits_weekly (`player_id`, `credits`, `time`) SELECT b.`id` AS idPlayer,\''. 200 .'\' AS credits, \''. time() .'\' AS time FROM `players` AS b LEFT JOIN `giftcredits_weekly` AS gw ON gw.`player_id` = b.`id` WHERE gw.`id` IS NULL');

  // SI SE INSERTO MAS DE UNA FILA
  if($consult)
  {
    // ENVIA NOTIFICACIONES
    while($allUsers = mysqli_fetch_assoc($consult0))
    {
      // BORRAR NOTIFICACION ANTERIOR DEL USUARIO
      $deleteNotification = $connect->query("DELETE FROM `players_notifications` WHERE toid='$allUsers[idPlayer]' AND not_key='giftWeekly'");
      // ENVIAR NUEVA NOTIFICACIÓN AL USUARIO
      $consult = $connect->query('INSERT INTO `players_notifications` (toid, fromid, not_key, action, read_time) VALUES (\''. $allUsers['idPlayer'] .'\', "0", "giftWeekly", \''. 200 .'\', "0")');
      echo "i";
    }
  }
}



?>
