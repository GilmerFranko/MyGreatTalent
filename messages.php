<?php

require("core.php");

head();

if(isset($_GET["welcomechat"])){
	echo '<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><script>
	$("document").ready(function(){
		interval = setInterval(function () {
			swal.fire("Bienvenido a My Great Talent, te enviamos un Email con nuestro link para que lo guardes, si no ves el correo búscalo en la carpeta de spam y márcalo como no spam para que lo puedas visualizar.","","success");
			history.pushState({data:true}, "Titulo", "galerias.php");
			clearInterval(interval);
			}, 3000);
			});
			</script>';
		}


		?>

		<div class="content-wrapper" height="10%">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
					<h1><i class="fas fa-envelope"></i> Conversaciones</h1>
				</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">

					<div class="row">

						<div class="col-md-12">


							<div class="box">
								<div class="box-header">
									<h3 class="box-title"></h3>
									<!-- Poner aqui aviso y textos-->

									<!-- Codigo navidad-->
									<script type="text/javascript">
								//<![CDATA[
								var bits=100; // cuantos bits
								var intensity=7; // que tan "poderosa" es la explosión. (recomendado entre 3 y 10)
								var speed=20; // rapidez (a menor numero, mas rapido)
								var colours=new Array("#03f", "#f03", "#0e0", "#93f", "#0cc", "#f93");
																//azul rojo verde purpura cyan, naranjo

																var dx, xpos, ypos, bangheight;
																var Xpos=new Array();
																var Ypos=new Array();
																var dX=new Array();
																var dY=new Array();
																var decay=new Array();
																var colour=0;
																var swide=800;
																var shigh=600;
																function write_fire() {
																	var b, s;
																	b=document.createElement("div");
																	s=b.style;
																	s.position="absolute";
																	b.setAttribute("id", "bod");
																	document.body.appendChild(b);
																	set_scroll();
																	set_width();
																	b.appendChild(div("lg", 3, 4));
																	b.appendChild(div("tg", 2, 3));
																	for (var i=0; i<bits; i++) b.appendChild(div("bg"+i, 1, 1));
																}
															function div(id, w, h) {
																var d=document.createElement("div");
																d.style.position="absolute";
																d.style.overflow="hidden";
																d.style.width=w+"px";
																d.style.height=h+"px";
																d.setAttribute("id", id);
																return (d);
															}
															function bang() {
																var i, X, Y, Z, A=0;
																for (i=0; i<bits; i++) {
																	X=Math.round(Xpos[i]);
																	Y=Math.round(Ypos[i]);
																	Z=document.getElementById("bg"+i).style;
																	if((X>=0)&&(X<swide)&&(Y>=0)&&(Y<shigh)) {
																		Z.left=X+"px";
																		Z.top=Y+"px";
																	}
																	if ((decay[i]-=1)>14) {
																		Z.width="3px";
																		Z.height="3px";
																	}
																	else if (decay[i]>7) {
																		Z.width="2px";
																		Z.height="2px";
																	}
																	else if (decay[i]>3) {
																		Z.width="1px";
																		Z.height="1px";
																	}
																	else if (++A) Z.visibility="hidden";
																	Xpos[i]+=dX[i];
																	Ypos[i]+=(dY[i]+=1.25/intensity);
																}
																if (A!=bits) setTimeout("bang()", speed);
															}

															function stepthrough() {
																var i, Z;
																var oldx=xpos;
																var oldy=ypos;
																xpos+=dx;
																ypos-=4;
																if (ypos<bangheight||xpos<0||xpos>=swide||ypos>=shigh) {
																	for (i=0; i<bits; i++) {
																		Xpos[i]=xpos;
																		Ypos[i]=ypos;
																		dY[i]=(Math.random()-0.5)*intensity;
																		dX[i]=(Math.random()-0.5)*(intensity-Math.abs(dY[i]))*1.25;
																		decay[i]=Math.floor((Math.random()*16)+16);
																		Z=document.getElementById("bg"+i).style;
																		Z.backgroundColor=colours[colour];
																		Z.visibility="visible";
																	}
																	bang();
																	launch();
																}
																document.getElementById("lg").style.left=xpos+"px";
																document.getElementById("lg").style.top=ypos+"px";
																document.getElementById("tg").style.left=oldx+"px";
																document.getElementById("tg").style.top=oldy+"px";
															}
															function launch() {
																colour=Math.floor(Math.random()*colours.length);
																xpos=Math.round((0.5+Math.random())*swide*0.5);
																ypos=shigh-5;
																dx=(Math.random()-0.5)*4;
																bangheight=Math.round((0.5+Math.random())*shigh*0.4);
																document.getElementById("lg").style.backgroundColor=colours[colour];
																document.getElementById("tg").style.backgroundColor=colours[colour];
															}
															window.onscroll=set_scroll;
															function set_scroll() {
																var sleft, sdown;
																if (typeof(self.pageYOffset)=="number") {
																	sdown=self.pageYOffset;
																	sleft=self.pageXOffset;
																}
																else if (document.body.scrollTop || document.body.scrollLeft) {
																	sdown=document.body.scrollTop;
																	sleft=document.body.scrollLeft;
																}
																else if (document.documentElement && (document.documentElement.scrollTop || document.documentElement.scrollLeft)) {
																	sleft=document.documentElement.scrollLeft;
																	sdown=document.documentElement.scrollTop;
																}
																else {
																	sdown=0;
																	sleft=0;
																}
																var s=document.getElementById("bod").style;
																s.top=sdown+"px";
																s.left=sleft+"px";
															}
															window.onresize=set_width;
															function set_width() {
																if (typeof(self.innerWidth)=="number") {
																	swide=self.innerWidth;
																	shigh=self.innerHeight;
																}
																else if (document.documentElement && document.documentElement.clientWidth) {
																	swide=document.documentElement.clientWidth;
																	shigh=document.documentElement.clientHeight;
																}
																else if (document.body.clientWidth) {
																	swide=document.body.clientWidth;
																	shigh=document.body.clientHeight;
																}
															}
															window.onload=function() { if (document.getElementById) {
																set_width();
																write_fire();
																launch();
																setInterval('stepthrough()', speed);
															}}

														</script>
														<!-- Codigo navidad-->
														<!-- Poner aqui aviso y textos-->

													</div>
													<div class="box-body">
														<table id="dt-basic" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
															<thead>
																<tr></tr>

															</thead>
															<tbody>
																<?php
																$timeonline = time() - 60;

																$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

																$num_results_on_page = 10;

																$calc_page = ($page - 1) * $num_results_on_page;
																$total_pages = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1 = '$player_id' OR player2 = '$player_id' ORDER BY time DESC")->num_rows;
																$query = mysqli_query($connect, "SELECT * FROM `nuevochat_rooms` WHERE player1 = '$player_id' OR player2 = '$player_id' ORDER BY time DESC LIMIT {$calc_page}, {$num_results_on_page}");


																if (mysqli_num_rows($query)){
	//$query->bind_param('ii', );
	//$query->execute(); 
	//$result = $query->get_result();

																	while ($room = mysqli_fetch_assoc($query)) {

																		if ($room['player2'] == $player_id){
																			$amigo = $room['player1'];
																		}else{
																			$amigo = $room['player2'];
																		}

																		$sqluser = $connect->query("SELECT `id`,`username`, `avatar`, `timeonline` FROM `players` WHERE id = '{$amigo}'");
																		$rowsuser = mysqli_fetch_assoc($sqluser);

																		// Seleccionar ultimo mensaje
																		$query1 = $connect->query("SELECT `leido`, `foto`, `author`, `mensaje` FROM `nuevochat_mensajes` WHERE `id_chat` = $room[id] ORDER BY `id` DESC LIMIT 1");

																		// OPTIENE LOS MENSAJES NO LEIDOS DE "$player_id"
																		$mnv = $connect->query("SELECT nm.`id` FROM `nuevochat_mensajes` AS nm INNER JOIN nuevochat_rooms AS nr ON (nr.`player1` = '$player_id' || nr.`player2` = '$player_id') AND nr.`id` = nm.`id_chat` WHERE nm.`leido` = IF(nm.`author` = \"". $player_id ."\",false,'no') AND nm.`id_chat` = '$room[id]' LIMIT 10")->num_rows;

																			$msg = $query1->fetch_assoc();

																			//si existe al menos un mensaje se muestra la conversacion de la sala
																			if ($query1->num_rows > 0){
																				$Messages='';
																				$view = '';

																				if($mnv > 0){

																					$view = ' class="NotView"';

																					$Messages = '<span class="count-notify">'. $mnv .'</span>  ';
																				}

																				echo '<tr'. $view .'>
																				<td><center><a href="'.$sitio['site'].'profile.php?profile_id=' . $rowsuser['id'] . '">
																				<img src="' . $sitio['site'].$rowsuser['avatar'] . '" class="img-circle img-avatar"><br><br>
																				<b>' . $Messages . $rowsuser['username'] . '</a></b><br><H3>';
																				//// si está activa la opcion de primer mensaje

																				if ($sitio['mostrarprimermensaje']=='si'){

																					if ($msg['leido'] == 'no' && $msg['author'] != $player_id){
																						if ($msg['foto'] == 'No')
																						{
																							echo '<p style="color:red;word-break:break-word;">' . $msg['mensaje'] . '</p><br>';
																						}else{
																							echo '<span style="color:magenta;">Foto</span><br>';
																						}
																					}else{
																						if ($msg['foto'] == 'No')
																						{
																							echo '<p style="word-break:break-word;">' . $msg['mensaje'] . '</p></H3><br>';
																						}else{
																							echo 'Foto<br>';
																						}
																					}
																				}

																				if ($rowsuser['timeonline'] > $timeonline) {
																					echo '<p style="color:green">Online<p/><br>';
																				}

																				echo '<a href="'.$sitio['site'].'chat.php?chat_id=' . $room['id'] . '#chat" class="btn btn-success">
																				<i class="fa fa-envelope"> Ir a la conversación</i>
																				</a>
																				</center>
																				</td>
																				</tr>';
																			}
																		}
																	}else{

																		echo'<center><H3> Hola! Te damos la bienvenida a My Great Talent!</H3></center>

																	<hr />
																	<b>Algunas recomendaciones:</b><br/><br/>

																	Si envías mensajes se amable y educado<br/><br/>

																	No pongas en tu foto de perfil o de portada fotos que incumplan las normas, tales como fotos con contenido para adultos.<br/><br/>

																	¡Las conductas ofensivas y los contenidos dañinos están prohibidos ¡Los usuarios que violen estas normas serán bloqueados! <br/><br/>


																	<br/><br/>


																		';

																	}
																?>
															</tbody>
														</table>
														<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
															<ul class="pagination">
																<?php if ($page > 1): ?>
																	<li class="prev"><a href="messages.php?page=<?php echo $page-1 ?>">Anterior</a></li>
																<?php endif; ?>

																<?php if ($page > 3): ?>
																	<li class="start"><a href="messages.php?page=1">1</a></li>
																	<li class="dots">...</li>
																<?php endif; ?>

																<?php if ($page-2 > 0): ?><li class="page"><a href="messages.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
																<?php if ($page-1 > 0): ?><li class="page"><a href="messages.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

																<li class="currentpage"><a href="messages.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

																<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="messages.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
																<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="messages.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

																<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
																	<li class="dots">...</li>
																	<li class="end"><a href="messages.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
																<?php endif; ?>

																<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
																	<li class="next"><a href="messages.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
																<?php endif; ?>
															</ul>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>

									</div>
									<!--===================================================-->
									<!--End page content-->


								</div>
								<!--===================================================-->
								<!--END CONTENT CONTAINER-->


								<?php
								footer();
								?>
