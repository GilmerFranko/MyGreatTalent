<?php
require("core.php");
head();


if (isset($_GET['chat_id'])){
	
	$chat = $_GET['chat_id'];
	
	$queryc = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE id = '$chat'");
	$revision = mysqli_fetch_assoc($queryc); 
	
	if ($revision['player1'] == $player_id || $revision['player2'] == $player_id){
		
		if ($revision['player1'] = $player_id){
			$elamigo = $revision['player2'];
		}else{
			$elamigo = $revision['player1'];
		}
	
	}else{
		echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
        exit;		
	}	
	
}else{
	echo '<meta http-equiv="refresh" content="0; url=messages.php" />';
    exit;
}

?>

<!--END CONTENT CONTAINER-->

<div class="content-wrapper">

    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">

        <section class="content-header">
            <h1><i class="fas fa-envelope"></i> Chat</h1>

        </section>


        <!--Page content-->
        <!--===================================================-->
        <section class="content" width="100%">

            <div class="row">

                <div class="col-md-12">

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"></h3>
                        </div>
                        <div class="box-body">
                            <div id="actualizar">
<?php

$query = mysqli_query($connect, "SELECT * FROM `nuevochat_mensajes` WHERE id_chat='{$chat}'");
$marcarcomoleido = mysqli_query($connect, "UPDATE `nuevochat_mensajes` SET leido='si' WHERE leido='no' AND id_chat='{$chat}' AND toid='{$player_id}'");
while ($mensaje = mysqli_fetch_assoc($query)) {
	
	if ($mensaje['author'] == $player_id){
		$amigo = $mensaje['toid'];
		$alinear = 'left';
		$color = 'white';
	}elseif ($mensaje['toid'] == $player_id){
		$amigo = $mensaje['author'];
		$alinear = 'left';
		$color = 'white';
	}
	
	$sqluser = mysqli_query($connect, "SELECT * FROM `players` WHERE id = '$mensaje[author]'");
    $rowsuser = mysqli_fetch_assoc($sqluser);
	
	$class = "";
	if($rowsuser['id'] == $player_id){
		$class = " current_user_message";
	}
	
    echo '<tr>			
		<td>
		<div align="left" class="row_message'.$class.'">
			<a href="'.$sitio['site'].'profile.php?profile_id=' . $rowsuser['id'] . '">		
			<img src="'.$sitio['site'].$rowsuser['avatar'] . '" class="img-circle" width="42">
			<b>' . $rowsuser['username'] . '</b></a><br>'; 
			if($mensaje['mensaje']!=''){
				echo '<p style="word-break:break-word;">' . $mensaje['mensaje'] . '</p>';
			}
			if($mensaje['foto'] == 'Yes'){

				echo '<img src="' . $sitio['site'].$mensaje['rutadefoto'] . '" width="100%" onclick="openImage(`' . $sitio['site'].$mensaje['rutadefoto'] . '`)" /><br><br>';

			} 
			echo '</div>
		</td>
	</tr>';
}

?>

                            </div>

                        </div>

                        <center>
                            <a name="chat"></a>
							<input type="hidden" name="salaid" id="idch" value="<?php echo $chat; ?>">
							<input type="hidden" name="author" id="auth" value="<?php echo $player_id; ?>">
							<input type="hidden" name="toid" id="amig" value="<?php echo $elamigo; ?>">
                            <div class="row" style="margin: 0;">
								<div class="col-md-10">
									<textarea placeholder="Write your message - Escribir" class="form-control" name="mensaje" id="mens" rows="3" spellcheck="false"></textarea>
                                </div>
								<div class="col-md-2" style="padding-top: 5px;text-align: right;">
									<input value="Send - Enviar" type="button" id="postmensaje" onclick="hola();" name="post_chatmessage" class="btn btn-primary btn-md float-right" />

									<span class="btn btn-primary btn-file float-left">
										<i class="fas fa-camera"></i>
										<div id="bbbb" onclick="subirfoto();"><input class="float-left" type="file" name="file1" id="file1"></div>
									</span>
									<img id="preview" height="50px" style="max-width:50px;">
									<input class="btn btn-primary" id="bontondeenviarfoto" style="display:none" type="button" value="Enviar Foto" onclick="uploadFile()">
								</div>
                            </div>

                    </div>
                </div>
            </div>

    </div>

</div>

<script type="text/javascript">

	function readImage (input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#preview').attr('src', e.target.result).show(); // Renderizamos la imagen
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	function mostrarprogreso() {
		$("#proystat").show();
		$("#bontondeenviarfoto").hide();
	}

	function _(el) {
		return document.getElementById(el);
	}

	function uploadFile() {
		var file = _("file1").files[0];
		var formdata = new FormData();
		formdata.append("file1", file);
		var ajax = new XMLHttpRequest();
		ajax.upload.addEventListener("progress", progressHandler, false);
		ajax.addEventListener("load", completeHandler, false);
		ajax.addEventListener("error", errorHandler, false);
		ajax.addEventListener("abort", abortHandler, false);
		ajax.open("POST", "upload4.php?urid=<?php echo $player_id; ?>&chatid=<?php echo $chat; ?>&toid=<?php echo $elamigo; ?>");
		ajax.send(formdata);

		$("#file1").val("");
		$('#preview').attr('src', '').hide();
		$("#bontondeenviarfoto").hide();
	}

	function progressHandler(event) {
		_("loaded_n_total").innerHTML = "Cargado " + event.loaded + " bytes de " + event.total;
		var percent = (event.loaded / event.total) * 100;
		_("progressBar").value = Math.round(percent);
		_("status").innerHTML = Math.round(percent) + "% Cargando... por favor espere";

		if (percent >= 100) {
			document.getElementById("proystat").hide();
		}
	}

	function completeHandler(event) {
		_("status").innerHTML = event.target.responseText;
		_("progressBar").value = 0;
		$("#bontondeenviarfoto").hide();
	}

	function errorHandler(event) {
		_("status").innerHTML = "Upload Failed";
	}

	function abortHandler(event) {
		_("status").innerHTML = "Upload Aborted";
	}

	function hola() {

		var idchat = document.getElementById('idch').value;
		var author = document.getElementById('auth').value;
		var toid = document.getElementById('amig').value;
		var mensaje = document.getElementById('mens').value;
		
		console.log( 'idchat=' + idchat + '&author=' + author + '&toid=' + toid + '&mensaje=' + mensaje );

		$.ajax({
			data: 'idchat=' + idchat + '&author=' + author + '&toid=' + toid + '&mensaje=' + mensaje,
			url: 'ajax.php',
			method: 'POST',
		}).done(function(response){
			console.log( response );
		});

		document.getElementById("mens").value = "";
	}

    $(document).ready(function() {

		setInterval(function() {
			$("#actualizar").load('chat.php?chat_id=<?php echo $chat;?> #actualizar');
		}, 1000);

		$("#file1").change(function () {
			// C贸digo a ejecutar cuando se detecta un cambio de archivO
			readImage(this);
			$("#bontondeenviarfoto").show();
		});

        $('#dt-basic').dataTable({
            "responsive": true,
            "language": {
                "paginate": {
                    "previous": '<i class="fas fa-angle-left"></i>',
                    "next": '<i class="fas fa-angle-right"></i>'
                }
            }
        });
    });
</script>
<?php
footer();
?>