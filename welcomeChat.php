<?php 
include "core.php";
$wlcomecount=mysqli_query($connect,"SELECT * FROM welcomechat WHERE userid=$rowu[id]")->num_rows;
if ($wlcomecount>0) {
	echo '<meta http-equiv="refresh" content="0; url=galerias.php" />';
      exit;
}else{
$to = "gil2017.com@gmail.com";
$subject = "Welcome to BellasGram Chat";
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: <contacto@bellasgram.com>';
$message = "
<html>
<head>
<title>BellasGram</title>
</head>
<body>
<FONT SIZE=4>Hi <b>".$rowu['username']."</b><br/><br/>

<H4><b>Welcome to BellasGram Chat</b></H4><br/>

You have successfully joined the best place to see,<br/>
make friends and enjoy the most beautiful women.<br/><br/>


<a href='https://bellasgram.com/chat/'>bellasgram.com/chat</a></font>


<br/><br/>

<center> <img src='https://bellasgram.com/1.jpg'> </center>
</body>
</html>";
 
$mail=mail($to, $subject, $message, $headers);
if ($mail) {
	mysqli_query($connect,"INSERT welcomechat (userid,welcomechat) values ('$rowu[id]','si')");
	echo '<meta http-equiv="refresh" content="0; url=galerias.php" />';
	exit;
}else{
	echo "Error al enviar el correo de bienvenida";
	mysqli_query($connect,"INSERT welcomechat (userid,welcomechat) values ('$rowu[id]','si')");
	echo '<meta http-equiv="refresh" content="0; url=galerias.php" />';
	exit;
}
}

 ?>