<?php

include '../config.php';
//echo deleteAccount1(2);
for ($i=0; $i < 1; $i++) {
  unsolouso(1,27);
}
//unsolouso(38,40);
function deleteAccount1($user_id = null){
  global $connect;

                    /*=====  ELIMINACION DE ARCHIVOS SUBIDOS  ======*/

  /* ELIMINAR AVATAR Y FOTO DE PORTADA */
  $query = $connect->query("SELECT `avatar`, `cover-page` FROM `players` WHERE `id` = \"". $user_id ."\" LIMIT 1");
  //
  if ($query == true && $query->num_rows > 0)
  {
    //ELIMINAR AVATAR
    while($post = $query->fetch_assoc() )
    {
      if(basenameProfile($post['avatar']) != 'default-asvatar.jpg')
        deletePostImagesw(basenameProfile($post['avatar'], true));
      deletePostImagesw($post['cover-page']);
    }
  }

  /* ELIMINA LAS FOTOS PUBLICADAS */
  $query = $connect->query("SELECT `id`, `imagen`, `thumb` FROM `fotosenventa` WHERE player_id = \"". $user_id ."\"");
  // COMPROBAR SI TIENE POSTS
  if ($query == true && $query->num_rows > 0)
  {
  // ELIMINAR PUBLICACIONES(FOTOS EN VENTAS)
    while($post = $query->fetch_assoc() )
    {
      deletePost($post);
    }
  }

  /* ELIMINA FOTOS DE MENSAJES ENVIADAS O RECIBIDAS */
  $query = $connect->query("SELECT `rutadefoto` FROM `nuevochat_mensajes` WHERE (`author` = \"". $user_id ."\" OR `toid` = \"". $user_id ."\") AND (`foto` = 'Yes')");
  // COMPROBAR SI TIENE MENSAJES CON FOTOS
  if ($query == true && $query->num_rows > 0)
  {
  // ELIMINAR FOTOS
    while($msg = $query->fetch_assoc() )
    {
      deletePostImagesw($msg['rutadefoto']);
    }
  }

  /* ELIMINA FOTOS DE MENSAJES PROGRAMDOS CREADO POR EL USUARIO */
  $query = $connect->query("SELECT `rutadefoto` FROM `mensajesprogramados` WHERE (`player_id` = \"". $user_id ."\") AND (`rutadefoto` != '')");
  // COMPROBAR SI TIENE MENSAJES CON FOTOS
  if ($query == true && $query->num_rows > 0)
  {
  // ELIMINAR FOTOS
    while($msg = $query->fetch_assoc() )
    {
      deletePostImagesw($msg['rutadefoto']);
    }
  }

  /* ELIMINA LAS FOTOS PROGRAMADAS */
  $query = $connect->query("SELECT `id`, `imagen` FROM `fotosprogramadas` WHERE `player_id` = \"". $user_id ."\"");
  // COMPROBAR SI TIENE POSTS
  if ($query == true && $query->num_rows > 0)
  {
  //
    while($post = $query->fetch_assoc() )
    {
      deletePostImagesw($post['imagen']);
    }
  }

  /* ELIMINAR TODOS LOS REGALOS HECHOS POR EL USUARIO */
  $query = $connect->query("SELECT `id`, `player_id`, `files` FROM `players_gifts` WHERE `player_id` = \"". $user_id ."\"");
  // COMPROBAR SI TIENE REGALOS
  if ($query == true && $query->num_rows > 0)
  {
  // ELIMINAR REGALOS
    while($gift = $query->fetch_assoc() )
    {
      deleteGift($gift);
    }
  }

  /* ELIMINAR FOTOS Y VIDEOS DE PACKS */
  $query = $connect->query("SELECT `video`, `imagens` FROM `packsenventa` WHERE `player_id` = \"". $user_id ."\"");
  // COMPROBAR SI EXISTEN
  if ($query == true && $query->num_rows > 0)
  {
  // ELIMINAR REGALOS
    while($pack = $query->fetch_assoc() )
    {
      deletePostImagesw($pack['imagens']);
      deletePostImagesw($pack['video']);
    }
  }

                    /*=====  ELIMINACIONES RESTANTES  ======*/

  $array = array(
    // COMENTARIOS
    'DELETE FROM `player_comments` WHERE `author_id` = \''.$user_id.'\'',
    // DESCARGAS
    'DELETE FROM `download` WHERE `uid` = \''.$user_id.'\'',
    // COMPRAS
    'DELETE FROM `fotoscompradas` WHERE `comprador_id` = \''.$user_id.'\'',
    // FOTOS PUBLICADAS
    'DELETE FROM `fotosenventa` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR BLOQUEOS Y BLOQUEADOS
    'DELETE FROM `bloqueos` WHERE `fromid` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // ELIMINAR REGALO SEMANAL
    'DELETE FROM `giftcredits_weekly` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR LIKES
    'DELETE FROM `player_megusta` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR RECOMENDACIONES
    'DELETE FROM `players_recommendations` WHERE `fromid` = \''.$user_id.'\' || `toid` = \''. $user_id .'\'',
    // NOTIFICACIONES
    'DELETE FROM `players_notifications` WHERE `toid` = \''.$user_id.'\' || `fromid` = \''.$user_id.'\'',
    // AMISTADES
    'DELETE FROM `friends` WHERE `player1` = \''.$user_id.'\' || `player2` = \''.$user_id.'\'',
    // PERFIL / CUENTA
    // PREGUNTAS DE USUARIOS
    'DELETE FROM `site_questions` WHERE `player_id` = \''.$user_id.'\'',
    // RETIROS
    'UPDATE `retiros` SET `usuario` = "" WHERE `usuario` = \''.$user_id.'\'',
    // RESPUESTAS AUTOMATICAS DE BOTS
    'DELETE FROM `respuesta_automatica` WHERE `uid` = \''.$user_id.'\'',
    // RESPUESTAS DE BOTS EN ESPERA
    'DELETE FROM `respuestasbot_enespera` WHERE `bot_id` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // REPORTES
    'DELETE FROM `reportes` WHERE `author` = \''.$user_id.'\'',
    // ENCUESTAS DE USUARIOS
    'DELETE FROM `polls` WHERE `uid` = \''.$user_id.'\'',
    // MASCOTAS COMPRADAS
    'DELETE FROM `player_pets` WHERE `player_id` = \''.$user_id.'\'',
    // ITEMS DE GRANJA COMPRADOS
    'DELETE FROM `player_items_bought` WHERE `player_id` = \''.$user_id.'\'',
    // COLECCIONES ADQUIRIDAS
    'DELETE FROM `player_colecciones` WHERE `player_id` = \''.$user_id.'\'',
    // PREGUNTAS REALIZADAS POR EL USUARIO
    'DELETE FROM `players_questions` WHERE `toid` = \''.$user_id.'\'',
    // ELIMINAR DE LA LISTA DE NOMBRES
    'DELETE FROM `players_namesactions` WHERE `player_id` = \''.$user_id.'\'',
    // MOVIMIENTOS REALIZADOS
    'DELETE FROM `players_movements` WHERE `player_id` = \''.$user_id.'\'',
    // REGALOS ENVIADOS Y RECIBIDOS
    'DELETE FROM `players_gift_given` WHERE `fromid` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // ELIMINAR REGALOS CREADOS
    'DELETE FROM `players_gifts` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR GRANJAS ADQUIRIDAS
    'DELETE FROM `players_farms` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR ITEMS COMPRADOS
    'DELETE FROM `players_farm_items` WHERE `player_id` = \''.$user_id.'\'',
    // ELIMINAR ANTECEDENTES EN COMPRAS DE CREDITOS
    'DELETE FROM `players_farm_items` WHERE `player_id` = \''.$user_id.'\'',
    // PACKS COMPRADOS
    'DELETE FROM `packscomprados` WHERE `comprador_id` = \''.$user_id.'\'',
    // SALA DE CHATS
    'DELETE FROM `nuevochat_rooms` WHERE `player1` = \''.$user_id.'\' || `player2` = \''.$user_id.'\'',
    //
    'DELETE FROM `notificaciones_suscripcionesvencidas` WHERE `usera` = \''.$user_id.'\' || userb = \''. $user_id .'\'',
    //
    'DELETE FROM `notificaciones_fotosnuevas` WHERE `player_notificador` = \''.$user_id.'\' || `player_notificado` = \''.$user_id.'\'',
    // NOTAS CREADAS
    'DELETE FROM `notas` WHERE `uid` = \''.$user_id.'\'',
    // MENSAJES PROGRAMADOS
    'DELETE FROM `mensajesprogramados` WHERE `player_id` = \''.$user_id.'\'',
    // FOTOS PROGRAMADAS
    'DELETE FROM `fotosprogramadas` WHERE `player_id` = \''.$user_id.'\'',
    // PACKS EN VENTA
    'DELETE FROM `packsenventa` WHERE `player_id` = \''.$user_id.'\'',
    // MENSAJES ENVIADOS Y RECIBIDOS
    'DELETE FROM `nuevochat_mensajes` WHERE `author` = \''.$user_id.'\' || `toid` = \''.$user_id.'\'',
    // ELIMINAR COMPROBANTE DE "BIENVENIDA"
    'DELETE FROM `welcomechat` WHERE `userid` = \''.$user_id.'\'',
    // ELIMINAR *DESCONOCIDO*
    'DELETE FROM `ventascompradas` WHERE `comprador_id` = \''.$user_id.'\'',
    // ELIMINAR *DESCONOCIDO*
    'DELETE FROM `ventasenventa` WHERE `player_id` = \''.$user_id.'\'',
);
  // RECORRER CONSULTAS
  foreach($array as $sql)
  {
    // EJECUTAR CONSULTA
    if( $connect->query($sql) == false )
    {
      $error[] = '<strong>SQL:</strong> '.$sql.'. <strong>Error:</strong> '.$connect->error;
      break; // DEJAR DE EJECUTAR CONSULTAS
    }
  }
  // SI TODO HA IDO BIEN
  if(empty($error))
  {
    return true;
  }

  // CONVERTIR ERRORES EN TEXTO
  $msg = 'Problema al eliminar usuario: <br/>';
  $msg .= implode('<br/>', $error);

  // SI ALGO FALLÓ, NOTIFICAR AL ADMIN /*EN REVISIÓN */
  error_log($msg);

  // RETORNAR FALSE
  return false;
}

/**
 * Elimina una publicación (fotoenventa) y sus asociados
 * @param  array $post Array asociativo con datos de un post
 * @return boolean
 */
/*
function deletePost($post = null)
{
  global $connect;
  // ELIMINAR IMAGENES DEL POST
  deletePostImagesw($post['imagen']);
  // ELIMINAR THUMB DEL POST
  deletePostImagesw($post['thumb']);
  $array = array(
    // ELIMINAR SHOUT
    'DELETE FROM `fotosenventa` WHERE `id` = \''.$post['id'].'\' LIMIT 1',
    // ELIMINAR COMENTARIOS DE SHOUT
    'DELETE FROM `player_comments` WHERE `galeria_id` = \''.$post['id'].'\'',
    // ELIMINAR LIKES DE SHOUT
    'DELETE FROM `player_megusta` WHERE `galeria_id` = \''.$post['id'].'\'',
    // ELIMINAR DENUNCIAS
    // ELIMINAR NOTIFICACIONES
    // ELIMINAR REGISTRO DE DESCARGAS
    'DELETE FROM `download` WHERE `fotoid` = \''.$post['id'].'\'',
  );
  foreach($array as $sql)
  {
    // EJECUTAR CONSULTA
    if( $connect->query($sql) == false )
    {
      $error[] = '<strong>SQL:</strong> '.$sql.'. <strong>Error:</strong> '.$connect->error;
      break; // DEJAR DE EJECUTAR CONSULTAS
    }
  }
  // SI TODO HA IDO BIEN
  if(empty($error))
  {
    return true;
  }

  // CONVERTIR ERRORES EN TEXTO
  $msg = 'Problema al eliminar usuario: <br/>';
  $msg .= implode('<br/>', $error);

  // SI ALGO FALLÓ, NOTIFICAR AL ADMIN /*EN REVISIÓN
  error_log($msg);
  // RETORNAR FALSE
  return false;
}

/**
 * Elimina las fotos de una publicación
 * @param  string/array  $images Ruta a la imagen
 * @return boolean
 */
/*
function deletePostImagesw($images = null)
{
  // SI ES UN STRING, SE CONVIERTE EN ARRAY
  if(isJson($images))
  {
    $images = json_decode($images);
  }
  elseif( is_string($images) )
  {
    $images = explode(',', $images);
  }
  // BORRA LAS IMAGENES
  foreach ($images as $imgName)
  {
  // BORRA LA IMAGEN
    if( file_exists($imgName) )
    {
      unlink($imgName);
    }
  }
}
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * Elimina un regalo
 * @param  int/$consult  $gift ID del regalo o $consult de un Regalo
 * @return boolean
 */
/*
function deleteGift($gift)
{
  global $connect;
  // CREA UNA CONSULTA A PARTIR DE LA ID PASADA Y BORRA EL REGALO /* EN REVISION
  if(is_numeric($gift))
  {

  }
  // SI EL $gift ES UNA CONSULTA DE MYSQL (ARRAY ASOCIATIVO)
  else
  {
    // BORRA LA IMAGEN (DE EXISTIR)
    if( file_exists($gift['files']) )
    {
      unlink($gift['files']);
    }
  }
  // BORRA REGALO
  $deleteSQL = $connect->query("DELETE FROM `players_gifts` WHERE `id` = \"". $connect->real_escape_string($gift['id']) ."\" LIMIT 1");
  $connect->query("DELETE FROM `players_gift_given` WHERE `gift` = \"". $gift['id'] ."\"");
  if($deleteSQL)
  {
    return true;
  }
  return false;
}


/**
 * Envia una notificación a un usuario
 * @param  int  $to_user   Usuario a quien se le enviara
 * @param  int  $from_user Usuario quien envía
 * @param  int  $key       Tipo de notificación
 * @param  int  $action      ID del objeto que desencadena esta notificación (Ejemplo si es una notificación de un nuevo Pack, entonces se coloca el id de ese Pack)
 * @param boolean $myself Enviarme a mi mismo
 * @return boolean         true

function newNotification($to_user = null, $from_user = null, $key = null, $action = 0, $myself = false)
{
  global $connect;
  // EVITAR ENVIARME A MÍ MISMO
  if($to_user != $from_user || $myself == true)
  {
    // ENVIA NOTIFICACIÓN
    $query = $connect->query('INSERT INTO `players_notifications` (`toid`, `fromid`, `not_key`, `action`, `read_time`) VALUES (\''.$to_user.'\', \''.$from_user.'\', \'' . $key . '\', \''.$action.'\', 0) ');

    if($query == true)
    {
      return true;
    }
  }
  //
  return false;
}*/


 // RELLENA DATOS DE UN USUARIO (SOLO PARA PRUEBAS)
 function unsolouso($user_a, $user_b)
 {
  global $connect;

  // RUTAS EN DONDE IRAN LAS IMAGENES
  // Imagen de Regalo
  $path_file_gift = "uploads/thumb_gift/thumb-".generateUUID().'.jpg' ;
  $path_file_message = "uploads/src_messages/". 1 .'-'. generateUUID().'.jpg' ;
  $path_file_photoprogramed = "shout/galeria/". 1 .'-'. generateUUID().'.jpg' ;
  $path_file_pack = "images/packs/Admin-". generateUUID().'.jpg' ;
  $path_file_videopack = "uploads/packs/videos/". 1 .'-'. generateUUID().'.mp4';
  $path_file_fotosenventa = "shout/galeria/". 1 . '-' . generateUUID(). '.jpg';
  $path_file_fotosenventathumb = "thumb/". 1 .'-'. generateUUID().'.jpg';
  // COPIAR IMAGEN A LAS SIGUIENTES RUTAS
  copy('../assets/img/candado.png', '../'.$path_file_gift);
  copy('../assets/img/medalla1.png', '../'.$path_file_message);
  copy('../assets/img/medalla2.png', '../'.$path_file_photoprogramed);
  copy('../assets/img/addphoto.png', '../'.$path_file_pack);
  copy('../assets/img/normales.png', '../'.$path_file_fotosenventa);
  copy('../assets/img/tarjetas.png', '../'.$path_file_fotosenventathumb);
  copy('../../2.mp4', '../'.$path_file_videopack);


  // MOVIMIENTOS REALIZADOS
  updateCredits($user_a, '-', 100, 1);
  updateCredits($user_a, '+', 100, 1);
  updateCredits($user_b, '-', 100, 1);
  updateCredits($user_b, '+', 100, 1);
  $path_file_fotosenventa = json_encode([$path_file_fotosenventa]);
  $path_file_photoprogramed = json_encode([$path_file_photoprogramed]);
  $path_file_fotosenventathumb = json_encode([$path_file_fotosenventathumb]);
  $path_file_pack = json_encode([$path_file_pack]);

  // CREAR REGALOS
  $insert = $connect->query("INSERT INTO `players_gifts` (`player_id`, `files`, `comment`, `time`) VALUES (\"". $user_a ."\", \"". $path_file_gift ."\", 'Comentario de regalo', UNIX_TIMESTAMP())");

  // FOTOS PROGRAMADAS
  $connect->query("INSERT INTO `fotosprogramadas` (player_id, imagen, descripcion, type, category,time) VALUES ('$user_a', '$path_file_photoprogramed', 'Foto programada!', 'publico', 'hetero', 'UNIX_TIMESTAMP')");
    $connect->error;

  // PACKS EN VENTA
  $connect->query("INSERT INTO `packsenventa` (`player_id`, `imagens`, `video`, `image_count`, `video_length`, `precio`, `descripcion`, `hidetochat`, `linkdedescarga`, `visible`) VALUES (\"". $user_a ."\", '$path_file_pack', \"". $path_file_videopack ."\", \"". 1 ."\" , '10' , \"". 100 ."\", 'Descripción pack', 'no', '', \"". '[\"\"]' ."\")");

  // FOTOS EN VENTA
  $connect->query("INSERT INTO `fotosenventa` (`id`, `player_id`, `imagen`, `thumb`, `descripcion`, `linkdedescarga`, `type`, `downloadable`, `time`, `category`) VALUES (NULL, \"" . $user_a . "\", '$path_file_fotosenventa', '$path_file_fotosenventathumb', 'Foto en venta', '', 'publico', '1', 'UNIX_TIMESTAMP', 'hetero')");

  // RELLENAR TODOS LOS POSIBLES DATOS DE UN USUARIO
  $array = array(
    "INSERT INTO `bloqueos`(`fromid`, `toid`) VALUES ('$user_a', '$user_b')",
    "INSERT INTO `download`(`uid`, `fotoid`) VALUES ('$user_a', '1')",
    // COMENTARIOS
    "INSERT INTO `player_comments` (author_id, galeria_id, date, time, comment)VALUES (\"".$user_a."\", 1, '03-03-2022', 'UNIX_TIMESTAMP', 'Comentario')",
    // COMPRAS
    "INSERT INTO `fotoscompradas` (foto_id, comprador_id) VALUES (1, \"".$user_a."\")",
    // ELIMINAR LIKES
    "INSERT INTO `player_megusta` (player_id, galeria_id) VALUES (\"".$user_a."\", 1)",
    // ELIMINAR REMENDACIONES
    "INSERT INTO `players_recommendations` (fromid, toid, time) VALUES (\"".$user_a."\", \"". $user_b ."\", 'UNIX_TIMESTAMP')",
    // NOTIFICACIONES
    "INSERT INTO `players_notifications` (`toid`, `fromid`, `not_key`, `action`, `read_time`)VALUES (\"".$user_b."\",\"".$user_a."\", 1, 1 , 'UNIX_TIMESTAMP')",
    "INSERT INTO `players_notifications` (`toid`, `fromid`, `not_key`, `action`, `read_time`)VALUES (\"".$user_a."\",\"".$user_b."\", 1, 1 , 'UNIX_TIMESTAMP')",
    // AMISTADES
    "INSERT INTO `friends` (player1, player2) VALUES (\"".$user_a."\", \"".$user_b."\")",
    // PREGUNTAS DE USUARIOS
    "INSERT INTO `site_questions` (`player_id`, `question`, `description`, `time`) VALUES (\"".$user_a."\", 'Pregunta', 'Descripción Pregunta', 'UNIX_TIMESTAMP')",
    // RETIROS
    "INSERT INTO `retiros` (`usuario`, `metodo`, `identificacion`, `monto`, `status`, `type`, `date`) VALUES (\"".$user_a."\", 'pago', 1, '100', 'COMPLETED', 'pago', 'UNIX_TIMESTAMP')",
    // RESPUESTAS AUTOMATICAS DE BOTS
    "INSERT INTO `respuesta_automatica` (`uid`, `pregunta`, `respuesta`, `created`) VALUES (\"".$user_a."\", 'Hola que haces', 'Nada y tu?', 'UNIX_TIMESTAMP')",
    // RESPUESTAS DE BOTS EN ESPERA
    "INSERT INTO `respuestasbot_enespera` (`bot_id`, `chat_id`, `toid`, `mensaje`, `foto`, `respuesta_time`) VALUES (\"".$user_a."\", 1, \"". $user_b ."\", 'Mensaje de Bot', '', 'UNIX_TIMESTAMP')",
    // REPORTES
    "INSERT INTO `reportes` (`author`, `userreportado`, `mensaje`, `date`, `time`) VALUES (\"".$user_a."\", \"". $user_b ."\" , 'Usuario reportado', '03-03-2022', 'UNIX_TIMESTAMP')",
    // ENCUESTAS DE USUARIOS
    "INSERT INTO `polls` (`uid`, `title`, `users_votes`, `questions`, `created`) VALUES (\"".$user_a."\", 'Titulo de encuesta', 800, 'Encuesta personalizada', 'UNIX_TIMESTAMP')",
    // MASCOTAS COMPRADAS
    "INSERT INTO `player_pets` (`player_id`, `pet_id`, `name`, `live`, `hp`, `energy`, `bonus`, `update_bonus`, `xp`, `nivel`, `profile`, `updated`) VALUES (\"".$user_a."\", '1', '..', '0', '0', '0', '0', '1622222677', '0', '0', '1', '1624414661')",
    // ITEMS DE GRANJA COMPRADOS
    "INSERT INTO `player_items_bought` (`item_id`, `player_id`, `time`) VALUES (1, \"".$user_a."\", 'UNIX_TIMESTAMP')",
    // COLECCIONES ADQUIRIDAS
    "INSERT INTO `player_colecciones` (`player_id`, `coleccion_id`) VALUES (\"".$user_a."\", 1)",
    // PREGUNTAS REALIZADAS POR EL USUARIO
    "INSERT INTO `players_questions` (`question`, `answer`, `toid`, `read_time`, `sent_time`) VALUES ('1', 'Respuesta a pregunta', \"". $user_a ."\" , '1635979674', '1635976081')",
    // PREGUNTAS REALIZADAS POR EL USUARIO
    "INSERT INTO `players_questions` (`question`, `answer`, `toid`, `read_time`, `sent_time`) VALUES ('1', 'Respuesta a pregunta', \"". $user_b ."\" , '1635979674', '1635976081')",
    // ELIMINAR DE LA LISTA DE NOMBRES
    "INSERT INTO `players_namesactions` (`id`, `player_id`, `player_add`, `time`) VALUES (NULL, \"". $user_a ."\" , \"". $user_b ."\" , '1635979134')",
    // REGALOS ENVIADOS
    "INSERT INTO `players_gift_given` (`fromid`, `toid`, `gift`, `time`) VALUES (\"".$user_a."\",\"".$user_b."\", 1, 'UNIX_TIMESTAMP')",
    // ELIMINAR GRANJAS ADQUIRIDAS
    "INSERT INTO `players_farms` (`farms_id`, `player_id`, `time`) VALUES (1, \"".$user_a."\", 'UNIX_TIMESTAMP')",
    // ELIMINAR ITEMS COMPRADOS
    "INSERT INTO `players_farm_items` (`id`, `items_id`, `player_id`, `time`) VALUES (NULL, '1', \"". $user_a ."\" , 'UNIX_TIMESTAMP')",
    // ELIMINAR ANTECEDENTES EN COMPRAS DE CREDITOS
    "INSERT INTO `payment_list` (`id`, `userid`, `paid`, `date`) VALUES (NULL, \"". $user_a ."\" , '10', 'UNIX_TIMESTAMP')",
    // PACKS COMPRADOS
    "INSERT INTO `packscomprados` (`foto_id`, `comprador_id`) VALUES (1, \"".$user_a."\")",
    // SALA DE CHATS
    "INSERT INTO `nuevochat_rooms` (`id`, `player1`, `player2`, `time`, `state`, `mensaje_chatbot`) VALUES (1,\"".$user_a."\", \"".$user_b."\", 'UNIX_TIMESTAMP', 'open', '')",
    //
    "INSERT INTO `notificaciones_suscripcionesvencidas` (`usera`, `userb`, `see`) VALUES (\"".$user_a."\", \"". $user_b ."\", 0)",
    //
    "INSERT INTO `notificaciones_fotosnuevas` ( `player_notificador`, `player_notificado`, `visto`) VALUES (\"".$user_b."\", \"".$user_a."\", '0')",
    // NOTAS CREADAS
    "INSERT INTO `notas` (`uid`, `title`, `nota`, `created`) VALUES (\"".$user_a."\", 'Titulo Nota', 'Cuerpo Nota', 'UNIX_TIMESTAMP')",
    // MENSAJES PROGRAMADOS
    "INSERT INTO `mensajesprogramados` (`player_id`, `message`, `rutadefoto`, `type`, `time`) VALUES (\"".$user_a."\", 'Hola que haces, este es un mensaje programado', '', 'type', 'UNIX_TIMESTAMP')",
      // MENSAJES PROGRAMADOS
    "INSERT INTO `ventascompradas` (`foto_id`, `comprador_id`) VALUES ('Hola que haces, este es un mensaje programado', \"".$user_a."\")",
      // MENSAJES PROGRAMADOS
    "INSERT INTO `ventasenventa` (`player_id`, `imagen`, `precio`, `descripcion`, `linkdedescarga`, `ventasrealizadas`) VALUES (\"".$user_a."\", 'Hola que haces, este es un mensaje programado', '10', 'type', 'UNIX_TIMESTAMP','100')",
      // MENSAJES PROGRAMADOS
    "INSERT INTO `welcomechat` (`userid`, `welcomechat`) VALUES (\"".$user_a."\", '0')",
  );
  foreach($array as $sql)
  {
    // EJECUTAR CONSULTA
    if( $connect->query($sql) == false )
    {
      $error[] = '<strong>SQL:</strong> '.$sql.'. <strong>Error:</strong> '.$connect->error;
      break; // DEJAR DE EJECUTAR CONSULTAS
    }
  }

    // MENSAJES
    sendMessage(1, $user_a, 'aa');
    sendMessage(1, $user_a, '', $path_file_message);

  // SI TODO HA IDO BIEN
  if(empty($error))
  {
    return true;
  }

  // CONVERTIR ERRORES EN TEXTO
  $msg = 'Problema al Crear: <br/>';
  $msg .= implode('<br/>', $error);

  // SI ALGO FALLÓ, NOTIFICAR AL ADMIN /*EN REVISIÓN */
  echo($msg);
  // RETORNAR FALSE
  return false;
 }

 /**
  * Devuelve nombre de la foto avatar sin ?xxxx (revisa un avatar de un usuario en la bbdd para entender)
  * @param  string $string Ruta a la imagen
  * @param  boolean $returnPath Evita quitar la ruta
  * @return string

 function basenameProfile($string, $returnPath = false)
 {
  if($string != '' AND $string != null)
  {
    $name = basename($string);
    $return = '';
    for ($i=0; $i < strlen($name); $i++)
    {
      if($name[$i] == '?') break;
      $return .= $name[$i];
    }
    if($returnPath) return dirname($string) .DIRECTORY_SEPARATOR. $return;
    return $return;
  }
 }*/
?>
