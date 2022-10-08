<?php
include "config.php";
setcookie("eluser", $username, time()-10000);
echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'index.php" />';
?>