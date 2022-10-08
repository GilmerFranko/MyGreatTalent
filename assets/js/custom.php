<!-- JAVASCRIPT GLOBAL -->
<script type="text/javascript">
	/* VARIABLES GLOBALES */
	var global={

		url:  "<?php echo url_origin() ?>",

	};
	let selectSID;
	let selectSData = [];
	let selectSFile;
	let selectSArrs = [];
	$(document).ready(function() {
		//AGREGAR TAGS
		//DETECTAR SI DEJA DE ESCRIBIR
		if (selectSID==null) {
			selectSID=0;
		}
		document.getElementById("searchUserTAGS").addEventListener('keypress', function (e) {
			if(e.keyCode == 188 || e.code=="Comma") {
				//NO PERMITIR HACER TAG SI NO SE HA ESCRITO NADA
				SearchUser();
				e.preventDefault();
			}
		});
	});
	function deletetag(selectSID,username){
		$("#tag"+selectSID).remove();
		eliminarPorName(username);
		selectSArrs=[];
		for( var i in selectSData){
			selectSArrs.push(selectSData[i].username);
		}
		$("#json").val(JSON.stringify(selectSArrs));
	}
	function finduser(array,user){
		var arr = [];
		for( var i in array){
			arr.push(array[i].username);
		}
		return arr.indexOf(user) > -1;
	}
	function eliminarPorName(username){
		for (var i = 0; i < selectSData.length; i++) {
			if (selectSData[i].username == username) {
				selectSData.splice(i, 1);
				break;
			}
		}
	}
	function SearchUser(userName){
		if( userName.length>0 ){

			//ENVIAR AJAX
			datos = {"get_user":userName};
			selectSFile = {username: "mnb"};
			$.ajax({
				url: "ajax.php",
				type: "POST",
				data: datos
			}).done(function(response){
				var response = $.parseJSON(response);
				console.log(response.state);
				if (response.state=="ok") {
			//COMPROBAR QUE NO EXISTA EL USUARIO
			if (finduser(selectSData,response.username)==false) {
			//AGREGAR TAG
			username='"'+response.username+'"';
			$(".container-tags").append("<div id='tag"+selectSID+"' class='s-tag col-lg-12 input-group' style='box-shadow: 1px 1px 3px -1px;margin: 1px;color: var(--text); height: 32px;display: flex;align-items: center;'><div style='background: url("+response.avatar+")no-repeat center center;width: 32px;height: 32px;background-size: cover;display: inline-block;'><img></div> <span class='' title='ID : "+response.id+"'>&nbsp;"+ userName + "&nbsp;&nbsp;</span> <i class='fa fa-window-close' onclick='deletetag("+ selectSID +", "+ username + ");' style='color: var(--colorPrimary);'></i>&nbsp;&nbsp;</div>");
			//VACIAR INPUT
			$("#searchUserTAGS").val("");
			selectSFile = {username: response.username};
			selectSData.push(selectSFile);

			//MANTENER ACTUALIZADO EL INPUT JSON
			selectSArrs=[];
			for( var i in selectSData){
				selectSArrs.push(selectSData[i].username);
			}
			$("#json").val(JSON.stringify(selectSArrs));

			//CAMBIAR ID
			selectSID=selectSID+192;

			response=null;
		}

			//SI EL USUARIO YA EXISTE
			else{
				$("#searchUserTAGS").val("");
			}
		}else{
			$("#searchUserTAGS").val("");
		}
	});
		}
	}
	// Abre una notificacion simple al usuario
	function openNewGiftMoneyAll(idGift = null)
	{
		$.ajax({
			url: "ajax.php?getGiftCredits",
			type: "POST",
			data: {'idGift': idGift},

		}).done(function(response){

			data = $.parseJSON(response)

			swal.fire({html: 'Te han regalado <b>' + data.amount + ' Créditos Especiales</b>', imageUrl: global.url + 'assets/img/GiftCredits.png', imageWidth: 200, heightAuto: false})

		})

	}
// SUBIR UNA FOTO Y PREVISUALIZARLA EN UN DIV
function readImage (input, div) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
				$(div).attr('src', e.target.result); // Renderizamos la imagen
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	<?php // SALIR DE UN PERFIL (SALE DE UN PERFIL QUE ESTÁ MIRANDO EL ADMIN) ?>
	function logoutProfileGuest(){
		$.ajax({
			url: "ajax.php?logoutProfileGuest",
			type: "POST",
		}).done(function(response){
			r = $.parseJSON(response)
			if(r.status)
			{
				location.reload()
			}
			else
			{
				swal.fire('Ha ocurrido un error', '','error')
			}
		})
	}
</script>
<?php // FUNCIONES ADMINISTRATIVAS ?>
<?php if ($rowu['role'] == 'Admin'): ?>
	<script type="text/javascript">
		<?php // LOGUEARSE COMO X USUARIO DE MANERA INSTANTANEA ?>
		function loginAsThisUser(userTo){
			$.ajax({
				url: "ajax.php?loginAsThisUser",
				type: "POST",
				data: {'userTo': userTo},

			}).done(function(response){
				r = $.parseJSON(response)

				if(r.status)
				{
					location.reload()
				}
				else
				{
					swal.fire('Ha ocurrido un error', '','error')
				}
			})
		}
	</script>
<?php endif ?>
<script>
		// DONAR CREDITOS
		var idDonate = null;
		var submitDonarCreditos = (idDonate) => {
			var formData = new FormData();
			formData.append("id", idDonate);
			formData.append("Creditos", $("#Creditos").val());

			$.ajax({
				url: "ajax.php?DonarCreditos",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false
			}).done(function(response){
				var data = $.parseJSON(response);
				console.log(response);
				if(data.status){
					swal.fire(data.message, "Podras ver su contenido durante 7 días", "success");
				}else{
					swal.fire({title: data.message, html: '<a class="btn btn-success" href="comprar.php">Comprar Créditos</a> ', icon: "warning"});
				}
				cancelDonarCreditos();
			})
		}
		var openDonarCreditos = (idD,userName) => {
			idDonate = idD;
			$("#textUsernameDonate").html(userName)
			$("#DonarCreditos").css('display','flex');
		}
		var cancelDonarCreditos = () => {
			$("#DonarCreditos").hide();
		}

		$(document).ready(function(){
		// COPIAR TEXTOS (en referrers.php)
		//agregado ID copyClip
		$("#copyClipLink").click(function(){
			var clipboard = new Clipboard('#copyClipLink');
			clipboard.on('success', function(event) {
				$("#copyClipLink").html('¡COPIADO!');
				window.setTimeout(function() {
					$("#copyClipLink").html('Copiar Link');
				}, 1500);
			});
			clipboard.on('error', function(e) {
				document.getElementById("copyClipLink").innerHTML = 'Usa Control + C';
				document.getElementById("copyClipLink").style.backgroundColor = "#e86032";
				window.setTimeout(function() {
					document.getElementById("copyClipLink").innerHTML = "Copiar Link";
					document.getElementById("copyClipLink").style.backgroundColor = "";
				}, 1500);
			});
		})
		//agregado ID copyClip
		$("#copyUrlProfile").click(function(){
			var clipboard = new Clipboard('#copyUrlProfile');
			clipboard.on('success', function(event) {
				$("#copyUrlProfile").html('¡COPIADO!');
				window.setTimeout(function() {
					$("#copyUrlProfile").html('Copiar Link');
				}, 1500);
			});
		})

		//RECORDAR POSISION DEL SCROLL //EN REVICION
		if (localStorage.getItem("my_app_name_here-quote-scroll") != null) {
			$(window).scrollTop(localStorage.getItem("my_app_name_here-quote-scroll"));
			console.log('HOLA'+localStorage.getItem("my_app_name_here-quote-scroll"))
		}
		$(window).scroll(function() {
			console.log("hOL")
		});


	})
</script>

<!-- MODALES -->

<!-- Modal Donar -->
<div id="DonarCreditos" class="modal fade in" style="flex-flow: column wrap; place-content: center space-around; align-items: center; backdrop-filter: blur(16px);">
	<div class="modal-dialog modal-md" style="max-width: 48vw;min-width: 300px;">
		<div class="modal-content" style="animation: animateTop .5s;-webkit-transition: all .5s;-moz-transition: all .5s;transition: all .5s;border-radius: 12px;border:none;/* background-color: #09000059; *//* backdrop-filter: blur(13px); */">
			<div class="modal-header" align="center" style="padding: 0px;border: none; padding: 0;">
				<h5 class="modal-header" style="/* color: var(--foreground); */">Apoya a <strong id="textUsernameDonate" style="color:var(--colorPrimary);"></strong></h5>
				<div class="modal-title" style="font-size: 38px;padding: 0;/* position: relative; */color: darkorange;top: -5px;left: 0px;display: inline;"><img src="assets/img/donate.svg" width="100" style="background: transparent;filter: drop-shadow(1px 1px 0 var(--colorPrimary)) drop-shadow(1px 1px 0 var(--colorPrimary));"></div>
			</div>
			<div class="modal-body">
				<center>
					<object data="ejemplo.svg" type="image/svg+xml">
						<select class="" type="number" name="Creditos" id="Creditos" value="0" style="padding: 10px 15px;outline: none;width: 70%;min-width: 100px;border-radius: 16px;border: solid 1px;border-color: #b33e96;background-color: unset;color: #323232;">
							Selecciona un monto
							<option value="1000">1.000 Créditos</option>
							<option value="2000">2.000 Créditos</option>
							<option value="3000">3.000 Créditos</option>
							<option value="5000">5.000 Créditos</option>
							<option value="10000">10.000 Créditos</option>
						</select>
						<center>
							<br>
							<button type="submit" name="comprar" class="btn btn-success" onclick="submitDonarCreditos(idDonate)" style="border-radius: 30px;width: 25vw;min-width: 100px;margin-top: 2px;"><b><i>Donar</i></b></button>

							<button type="button" class="btn btn-primary " onclick="cancelDonarCreditos()" style="border-radius: 30px;margin-top: 2px;background-color: unset;border-color: var(--colorPrimary);color: var(--colorPrimary);"><b><i>Cancelar</i></b></button>

							<!--<a class="btn btn-info" href="comprar.php" style="background-color:#606060;border-color: #606060;border-radius: 30px;"">Comprar Creditos</a>-->
						</center>
					</center>
				</div>
			</div>
		</div>
	</div>
</object></center></div></div></div></div>
<!-- MODAL Y FUNCIONES - CREDITOS DE REGALOS SEMANALES -->
<?php if($connect->query("SELECT * FROM giftcredits_weekly WHERE `player_id` = '$rowu[id]' ")->num_rows > 0): ?>


	<?php
			/**
		 * TEMPORALMENTE DESACTIVADO
		 * Muestra el modal de regalo al usuario en cualquier parte solo una vez
		 */

		/*/ DECIDE SI SE DEBE MOSTRAR EL MODAL AUTOMATICAMENTE
		$consult = $connect->query("SELECT `toid`,`not_key`,`read_time` FROM `players_notifications` WHERE `toid` = '$rowu[id]' AND `not_key` = 'giftWeekly' AND `read_time` = '0'");
		//
		if ($consult AND $consult->num_rows > 0)
		{

		 	// ALMACENAR CODIGO CSS
		 	$showModal = 'display: flex;';

		 	// ACTUALIZAR NOTIFICACION A "VISTO" (para no volver a repetir)
		 	$connect->query("UPDATE `players_notifications` SET `read_time` = \"". time() ."\" WHERE `toid` = '$rowu[id]' AND `not_key` = 'giftWeekly'");
		}
		else
		{
			$showModal = '';
		}

	*/
		$showModal = '';
		?>

	<!--
		----------
	 SI EL USUARIO NO ESTA EN notifications.php (que es donde se abre el modal manualmente)
	 NO CARGAR EL MODAL (ahorra al no cargar )
	-->
	<?php if (basename($_SERVER['SCRIPT_NAME']) == 'notifications.php'): ?>



		<script type="text/javascript">
			var acceptGiftCreditsW = () => {
				var formData = new FormData();
				formData.append("id", '<?php echo $rowu['id']; ?>');

				$.ajax({
					url: "ajax.php?acceptGiftCreditsW",
					type: "POST",
					data: formData,
					processData: false,
					contentType: false
				}).done(function(response){
					var data = $.parseJSON(response);
					console.log(response);
					if(data.state){
						swal.fire(data.message, "Recuerda que estamos regalando Créditos Especiales semanalmente.", "success");
					}else{
						swal.fire({title: data.message, html: '', icon: "warning"});
					}
					$('#giftCreditsW').hide();
				})
			}

			var openModalGiftCredits = () => {
				$("#giftCreditsW").css('display','flex');
			}
		</script>


		<div id="giftCreditsW" class="modal fade in" style="<?php echo $showModal ?>flex-flow: column wrap; place-content: center space-around; align-items: center; backdrop-filter: blur(16px);">
			<div class="modal-dialog modal-md" style="max-width: 48vw;min-width: 300px;">
				<div class="box" style="animation: animateTop .5s;-webkit-transition: all .5s;-moz-transition: all .5s;transition: all .5s;border-radius: 12px;border:none;">
					<div class="modal-header" align="center" style="padding: 0px;border: none; padding: 0;">
						<h5 class="modal-header" style="color: var(--text)">Adquiere tus <strong style="color: orange;">Cr&eacute;ditos de Regalo</strong> cada semana!<strong id="textUsernameDonate" style="color:orange;"></strong></h5>
						<div class="modal-title" style="font-size: 38px;padding: 0;color: darkorange;top: -5px;left: 0px;display: inline;">
							<img src="assets/img/GiftCredits.png" width="200" style="background: transparent;">
						</div>
					</div>
					<div class="modal-body">
						<center>
							<br>
							<button type="submit" name="comprar" class="btn btn-success" onclick="acceptGiftCreditsW()" style="border-radius: 30px;width: 25vw;min-width: 130px;margin-top: 2px; background-color: orange; border-color: orange;"><b><i>Aceptar Regalo</i></b></button>

							<button type="button" class="btn btn-primary " onclick="$('#giftCreditsW').hide();" style="border-radius: 30px;margin-top: 2px;background-color: unset;border-color: var(--text);color: var(--text);"><b><i>Despues</i></b></button>
						</center>
					</div>
				</div>
			</div>
		</div>
	<?php endif ?>

<?php endif ?>

<!--//-->

<!--
		----------
	 SI EL USUARIO NO ESTA EN notifications.php (que es donde se abre el modal manualmente)
	 NO CARGAR EL MODAL (ahorra al no cargar )
	-->
	<?php if (in_array(basename($_SERVER['SCRIPT_NAME']),array('notifications.php','regalos.php'))): ?>


	<script type="text/javascript">

		var openModalViewGiftFromUser = (id) => {
			$.ajax({
				url: "ajax.php?getGiftFromUser",
				type: "POST",
				data: {'idGift': id},

			}).done(function(response)
			{
				console.log(response)
				r = $.parseJSON(response)

					// SI ES UN REGALO CON CREDITOS
					if(r.state && r.giftCredits > 0){
						// ESTABLECER PARAMETROS AL MODAL
						$("#MGiftName").text(r.gift.username)
						$("#descriptionModalGift").text("Te envió " + r.giftCredits + " Créditos Especiales")
						$("#MGiftDescription").text(r.gift.comment)
						$("#MGiftDate").text(r.gift.time)
						$("#MGiftImage").hide()
						$("#MViewGiftFromUser").css('display','flex');
					}
					// SI ES UN REGALO CON IMAGEN
					else if(r.state){
						// ESTABLECER PARAMETROS AL MODAL
						$("#MGiftName").text(r.gift.username)
						$("#descriptionModalGift").text("Te ha enviado un regalo")
						$("#MGiftDescription").text(r.gift.comment)
						$("#MGiftDate").text(r.gift.time)
						$("#MGiftImage").show();
						$("#MGiftImage").css('background', 'url("'+r.gift.files+'")no-repeat center center');
						$("#MGiftImage").css('background-size', 'cover');
						//$("#MGiftImage").attr('data-src',r.gift.files)
						$("#MGiftImage").click(function(){
							openImage(r.gift.files)
						})
						$("#MViewGiftFromUser").css('display','flex');
					}
					else
					{
						swal.fire(r.msg,'','info');
					}
				});

		}
	</script>


	<div id="MViewGiftFromUser" class="modal fade in" style="<?php echo $showModal ?>;overflow-y: auto;flex-direction: column;justify-content: center;flex-wrap: nowrap;align-items: center;backdrop-filter: blur(16px);">
		<div class="modal-dialog modal-md" style="max-width: 48vw;min-width: 300px;">
			<div class="box" style="animation: animateTop .5s;-webkit-transition: all .5s;-moz-transition: all .5s;transition: all .5s;border-radius: 12px;border:none;">
				<div class="modal-header" align="center" style="padding: 0px;border: none; padding: 0;">
					<h5 class="modal-header" style="color: var(--text);"><strong id="MGiftName" style="color: orange;"></strong> <span id="descriptionModalGift">te ha enviado un regalo</span><strong id="textUsernameDonate" style="color:orange;"></strong></h5>
					<div class="modal-body" style="font-size: 38px;padding: 0;;top: -5px;left: 0px;display: inline;">
						<!-- Foto -->
						<div id="MGiftImage" style="display: flex;height: 200px;align-items: center;justify-content: space-around;">
							<a class="btn btn-primary" href="#">Ver foto</a>
						</div>
						<!-- /Foto -->
						<blockquote id="MGiftDescription" class=" blockquote-footer" style="border-left: 5px solid var(--colorPrimary);width: 80%;color: var(--colorPrimary);font-size: 11pt;background-color: aliceblue;margin: 10px;"></blockquote>
						<?php if (basename($_SERVER['SCRIPT_NAME']) == 'notifications.php'): ?>
							<span class="font-small" style="font-size: 10pt;display: block; color: unset;padding: 4px;">Puedes ver cuando quieras este y todos los <strong>regalos</strong> que te hayan enviado en <a href="regalos.php">Regalos</a>.</span>
						<?php endif ?>
					</div>
				</div>
				<div class="modal-footer">
					<center>
						<button type="button" class="btn btn-primary " onclick="$('#MViewGiftFromUser').hide();" style="border-radius: 30px;margin-top: 2px;background-color: unset;border-color: var(--text);color: var(--text);"><b><i>Cerrar</i></b></button>
					</center>
				</div>
			</div>
		</div>
	</div>
<?php endif ?>


<!-- MODAL ENVIAR REGALO A USUARIO -->
<script>
	$(document).ready(function(){
		$("#selectSentGift").change(function(){
			console.log($("#selectSentGift").val())
			if ($("#selectSentGift").val() == '0') {
				$("#sentGiftSelect").hide()
				$(".container-tags").hide()
			}
			if ($("#selectSentGift").val() == '1') {
				$("#sentGiftSelect").show()
				$(".container-tags").show()
			}
		})
		$('#searchUserTAGS').typeahead({
			source: function (search, result) {
				var formData = new FormData();
				formData.append("search", search);
				$.ajax({
					url: "ajax.php",
					type: "POST",
					data: formData,
					contentType:false,
					cache: false,
					processData:false,

				}).done(function(response)
				{
					console.log(response);
					var response = $.parseJSON(response);
					result($.map(response, function (item) {
						return item;
					}));
				});
			},
			updater : function(item) {
				SearchUser(item)
				return item;
			}
		})
	})
	var openModalGiveGift = (name) => {
		// SI SE PRECIONO EL BOTON ENVIAR REGALO
		if(name == null){
			$("#MGiveGift").css('display','flex');
		}
		// SI HAY UN NOMBRE ESPECIFICO
		else{
			// CAMBIAR SELECT
			$("#selectSentGift").val('1');
			$("#selectSentGift").trigger('change');
			// COLOCAR NOMBRE EN LA BUSQUEDA
			$("#searchUserTAGS").val(name)
			// BUCAR Y AGREGAR
			SearchUser(name);
			// MOSTRAL MODAL
			$("#MGiveGift").css('display','flex');
		}
	}
	function closeModalGiveGift() {
		let selectSID = null
		let selectSData = []
		let selectSFile = null
		let selectSArrs = []
		$('#MGiveGift').hide();
	}
	// ENVIAR REGALOS
	function sentGiftSelect(){

		// COMPRUEBA SI HA SELECCIONADO UN ARCHIVO
		if ($("#selectSentGift").val() != null && $('#inputUploadFileGift').get(0).files.length !== 0) {

			// COMPRUEBA QUE ESTE AL MENOS UN USUARIO SELECCIONADO
			if($("#selectSentGift").val() == '0' || $('.container-tags').html() != ''){

				let file = document.getElementById("inputUploadFileGift").files[0];
				let comment = $("#descriptionGift").val()

					// DATOS A ENVIAR
					let formData = new FormData();
					if ($("#selectSentGift").val() == '0')
						formData.append("usernames", '');
					else
						formData.append("usernames", selectSArrs);

					formData.append("gift", file);
					formData.append("comment", comment);
					formData.append("optionSelect", $("#selectSentGift").val())


					selectSArrs = [];
					file = null;
					comment = null;
					$('#inputUploadFileGift').get(0).files = null;
					$('#fileGift').attr('src','')
					$('#descriptionGift').val('')
					$("#selectSentGift").val('0')
					$('.container-tags').html('')
					// Cerrar Modal
					closeModalGiveGift()

					$.ajax({
						url: "ajax.php?sentGift",
						type: "POST",
						data: formData,
						contentType:false,
						cache: false,
						processData:false
					}).done(function(response){
						console.log(response)
						a = $.parseJSON(response)

						swal.fire(a[0],a[1],a[2])

					})
				}
				else
				{
					swal.fire('Aun no has seleccionado ningún usuario','Selecciona al menos un usuario para enviarle(s) el regalo.','info')
				}
			}
			else
				swal.fire('No hay imagen que enviar','Debes subir al menos una imagen','info')
		}
		var openModalGiveGift2 = () => {
			// SI SE DEBE ENVIAR A TODOS LOS USUARIOS
			$("#MGiveGift2").css('display','flex');
		}
		function closeModalGiveGift2() {
			$('#MGiveGift2').hide();
		}
		// ENVIAR REGALOS
		function sentGiftSelect2(){

			let comment = $("#descriptionGift2").val()

			// DATOS A ENVIAR
			let formData = new FormData();
			formData.append("comment", comment);
			formData.append("amount", $("#totalDonate").val())

			// Cerrar Modal
			closeModalGiveGift2()

			$.ajax({
				url: "ajax.php?sentGiftMoney",
				type: "POST",
				data: formData,
				contentType:false,
				cache: false,
				processData:false
			}).done(function(response){
				console.log(response)
				a = $.parseJSON(response)

				swal.fire(a[0],a[1],a[2])

			})
		}
		function InputStepChange(input){
			if(input == '+') $("#totalDonate").val(parseInt($("#totalDonate").val()) + 1)
				else $("#totalDonate").val($("#totalDonate").val() - 1)
					$("#totalDonateInfo").text(parseInt($("#totalDonate").val()).toLocaleString('eu', 0))
			}
		</script>
		<style type="text/css">
			input[type="number"] {
				-webkit-appearance: textfield;
				-moz-appearance: textfield;
				appearance: textfield;
			}

			input[type=number]::-webkit-inner-spin-button,
			input[type=number]::-webkit-outer-spin-button {
				-webkit-appearance: none;
			}

			.number-input {
				border: 1px solid darkorange;
				border-radius: 8px;
				display: inline-flex;
			}

			.number-input,
			.number-input * {
				box-sizing: border-box;
			}

			.number-input button {
				outline:none;
				-webkit-appearance: none;
				background-color: transparent;
				border: none;
				align-items: center;
				justify-content: center;
				width: 3rem;
				height: 3rem;
				cursor: pointer;
				margin: 0;
				position: relative;
			}

			.number-input button:before,
			.number-input button:after {
				display: inline-block;
				position: absolute;
				content: '';
				width: 1rem;
				height: 2px;
				background-color: darkorange;
				transform: translate(-50%, -50%);
			}
			.number-input button.plus:after {
				transform: translate(-50%, -50%) rotate(90deg);
			}

			.number-input input[type=number] {
				font-family: sans-serif;
				max-width: 10rem;
				padding: .5rem;
				border: solid #ddd;
				border-width: 0 2px;
				font-size: 18px;
				height: 3rem;
				font-weight: bold;
				text-align: center;
			}
		</style>
		<div id="MGiveGift" class="modal fade in" style="flex-flow: column wrap; place-content: center space-around; align-items: center; backdrop-filter: blur(16px);">
			<div class="modal-dialog modal-md" style="width: 90vw;min-width: 300px; overflow-y: auto;">
				<div class="box" style="animation: animateTop .5s;-webkit-transition: all .5s;-moz-transition: all .5s;transition: all .5s;border-radius: 12px;border:none;">
					<div class="modal-header" align="center" style="padding: 0px;border: none; padding: 0;">
						<h5 class="modal-header" style="color: var(--text);">Env&iacute;ales<strong style="color: var(--colorPrimary);"> regalos</strong> a tus amigos!</h5>
						<div class="modal-body" style="color: var(--text);">
							Subir imagen:
							<div>
								<img id="fileGift" src="" style="width: 40%;">
								<input id="inputUploadFileGift" onchange="readImage(this,'#fileGift');" type="file" class="custom-file-input" name="avafile" accept="image/*" id="imgInp" style="display: none !important;"><br><br>
								<a class="btn btn-primary" href="#" onclick="$('#inputUploadFileGift').trigger('click');">Subir</a>
							</div>
						</div>
						<div class="modal-title" style="font-size: 38px;padding: 0;color: var(--colorPrimary);top: -5px;left: 0px;display: inline;"></div>
						<span style="color: var(--colorPrimary);">Enviar a:</span><br>
						<select class="" id="selectSentGift" style="padding: 10px 15px;outline: none;width: 70%;min-width: 100px;border-radius: 16px;border: solid 1px;border-color: gray;background-color: unset;color: var(--text);">
							<option value="0">Todos</option>
							<option value="1">Seleccionar a quien enviar</option>
						</select>
						<br><br>
						<div id="sentGiftSelect" class="input-group" style="display: none;">
							<span class="input-group-addon"></span>
							<input id="searchUserTAGS" type="text" class="form-control" placeholder="Busca por nombre. Ejmp: Juan, Maria, Ana" name="searchUserTAGS" autocomplete="off">
							<input type="text" name="json" id="json" hidden="">
						</div>
					</div>
					<div class="modal-body">
						<div class="container-tags" style="display: flex;flex-direction: row;flex-wrap: wrap;justify-content: flex-start;overflow: scroll;max-height: 80px; overflow-x: auto;"></div><br><br>
						<span style="color: var(--text);">Puedes enviar una peque&ntilde;a descripci&oacute;n</span>
						<textarea id="descriptionGift" placeholder="Escribir" class="form-control" name="" id="" rows="3" spellcheck="false" maxlength="255"></textarea>
						<center>
							<br>
							<button type="submit" name="comprar" class="btn btn-success" onclick="sentGiftSelect()" style="border-radius: 30px;width: 25vw;min-width: 100px;margin-top: 2px;"><b><i>Enviar</i></b></button>

							<button type="button" class="btn btn-primary " onclick="closeModalGiveGift()" style="border-radius: 30px;margin-top: 2px;background-color: unset;border-color: var(--text);color: var(--text);"><b><i>Cancelar</i></b></button>
						</center>
					</div>
				</div>
			</div>
		</div>

		<div id="MGiveGift2" class="modal fade in" style="flex-flow: column wrap; place-content: center space-around; align-items: center; backdrop-filter: blur(16px);">
			<div class="modal-dialog modal-md" style="width: 90vw;min-width: 300px; overflow-y: auto;">
				<div class="box" style="animation: animateTop .5s;-webkit-transition: all .5s;-moz-transition: all .5s;transition: all .5s;border-radius: 12px;border:none;">
					<div class="modal-header" align="center" style="padding: 0px;border: none; padding: 0;">
						<h5 class="modal-header" style="color: var(--text);">Envia <strong style="color: orange;"> regalos</strong> a todos los usuarios</h5>
						<div class="modal-title" style="font-size: 38px;padding: 0;color: darkorange;top: -5px;left: 0px;display: inline;"></div>
					</div>
					<div class="modal-body">
						<div style="display: flex;flex-direction: column;align-items: center;">
							<span style="color: var(--text);">Puedes enviar una peque&ntilde;a descripci&oacute;n</span>
							<textarea id="descriptionGift2" placeholder="Escribir" class="form-control" name="" id="" rows="3" spellcheck="false" maxlength="255" style="width: 80%;"></textarea>
						</div>
						<div id="amountToSend" class="tab-pane" role="tabpanel" style="text-align: center;">
							<h5 style="color: var(--text);"><strong style="color: orange;">Monto</strong> a enviar</h5>
							<div class="number-input">
								<button onclick="InputStepChange('-')"></button>
								<input id="totalDonate" class="quantity" min="0" name="quantity" value="0" type="number" style="color: darkorange">
								<button onclick="InputStepChange('+')" class="plus"></button>
							</div>
						</div>
						<center>
							<br>
							<button type="submit" name="comprar" class="btn btn-success" onclick="sentGiftSelect2()" style="border-radius: 30px;width: 25vw;min-width: 100px;margin-top: 2px; background-color: orange; border-color: orange;"><b><i>Enviar</i></b></button>

							<button type="button" class="btn btn-primary " onclick="closeModalGiveGift2()" style="border-radius: 30px;margin-top: 2px;background-color: unset;border-color: var(--text);color: var(--text);"><b><i>Cancelar</i></b></button>
						</center>
					</div>
				</div>
			</div>
		</div>
