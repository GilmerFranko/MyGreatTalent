<?php
$Where = !isset($RandomID) ? '':" WHERE id!='{$RandomID}'";

    /**
     * Decide si mostrar fotos aleatorias de todos los usuarios
     * o de uno en especifico
     */
    if(isset($ToID)){
      $Where .= $Where == '' ? ' WHERE':' AND';
      $Where .= " player_id='{$ToID}'";
    }


    //SOLO FOTOS PUBLICAS Y SU GENERO Y QUE EL USUARIO TENGA LA OPCION MOSTRAR_EN_GALERIA ACTIVA
    if ($Where=='')
    {
      $Where .=" WHERE AND category='$prefer'";
    }
    else
    {
      $Where .=" AND category='$prefer'";
    }


    //$querycpd  = mysqli_query($connect, "SELECT * FROM `fotosenventa`{$Where} ORDER BY RAND() LIMIT 1");

    /**
     * Selecciona solo fotos de mis amigos, evita mostrar fotos de personas que no tienen amistad conmigo
     */
    $querycpd  = mysqli_query($connect,
      "SELECT f.`id`,f.`category`,f.`descripcion`, f.`downloadable`,f.`imagen`, f.`linkdedescarga`, f.`player_id`, f.`thumb`, f.`time`, f.`type`, p.`id`
       as pid,p.`hidden_for_old`, p.`username`
       FROM `fotosenventa`
       AS f
       INNER JOIN `players`
       AS `p`
       ON `p`.id = f.`player_id`
       AND IF(p.`hidden_for_old` != 0
       AND p.`id` != '$rowu[id]', p.`hidden_for_old` <= '$rowu[time_joined]', 1=1)
       LEFT JOIN `friends`
       AS b
       ON ((b.`player1` = f.`player_id` && b.`player2` = '$rowu[id]') || (b.`player2` = f.`player_id` && b.`player1` ='$rowu[id]'))
       AND p.`mostrar_en_galeria` = 1
       WHERE f.`category`='$prefer'
       AND IF(f.`player_id` != '$rowu[id]',b.`id`
       IS NOT NULL,1=1)
       ORDER BY RAND()
       LIMIT 1");

    $rowcp    = mysqli_fetch_assoc($querycpd);
    $author_id = $rowcp['player_id'];
    $querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
    $iamfrom = ($rowu['registerfrom'] == 'chat' ? 'chat' : "other");
    $pasar=true;
    $rowimg    = mysqli_fetch_assoc($querycpd);
//SI EL USUARIO TIENE EL PERFIL OCULTO Y YO NO SOY UN USUARIO REGISTRADO DESDE EL CHAT
    if($rowimg['perfiloculto']!='no' or $rowimg['hidetochat']=='si' and $iamfrom!='chat'){

      $friend = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$player_id' AND player2='$author_id'");
      $friend01 = mysqli_num_rows($friend);

      $friend2 = mysqli_query($connect, "SELECT * FROM `friends` WHERE player1='$author_id' AND player2='$player_id'");
      $friend02 = mysqli_num_rows($friend2);
    ////NO EJECUTAR LO DE ABAJO
      if($friend02==false && $friend01==false){
        $pasar=false;
      }
    }
    if ($pasar==true) {
    # code...

      $sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$author_id'");
      $hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);

      $sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$author_id' AND fromid='$player_id'");
      $hayunbloqueo2 = mysqli_num_rows($sqlbuscarbloqueo2);

      if ($hayunbloqueo < 1 && $hayunbloqueo2 < 1){
        $sub = '';
        if(!isFollow($rowimg['id']) && $rowimg['id'] != $player_id){
          $sub = $rowcp['type'] == 'suscripciones' ? ' noSub': '';
        }
        $RandImage = true;
        $thumbnail=json_decode($rowcp['thumb']);
        $image=json_decode($rowcp['imagen']);
        include "./Row-img.php";
        unset($RandImage);
      }
    }
    ?>
