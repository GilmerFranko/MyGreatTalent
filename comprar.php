<?php

$SandBox = false;
$Paypal_IDClient = "Adqlpy5FFy9G-ClEOckkhUMfgtiQT3qS7qVWK3JIjI2z3klpsfmHsnBSYpdfiML9eo7P9n3zi9cWXi6m";

if($SandBox){
	$Paypal_IDClient = "AeWtdFOl3RnAvVRRnpcmL0nPLf5zTVQzFzElc5otBpP1UpVN2IIutbyJtTI5GNkLGU0gVWBZ2hkcKNqA";
}

$Items = [
	101 => [
		'Creditos' => 2000,
		'price' => 20
	],
	250 => [
		'Creditos' => 5000,
		'price' => 40
	],
	301 => [
		'Creditos' => 10000,
		'price' => 60
	],
	404 => [
		'Creditos' => 30000,
		'price' => 120
	],
	548 => [
		'Creditos' => 50000,
		'price' => 150
	]
];

if(isset($_GET["acreditar"])){
	require("core.php");
	
	$AddCash = $Items[ $_GET["acreditar"] ]['Creditos'];
	
	$uname = $_COOKIE['eluser'];

	//ACTUALIZAR CREDITOS
	updateCredits($rowu['id'],'+',$AddCash,3);
	
	exit();
}


require("core.php");
head();
?>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $Paypal_IDClient ?>&enable-funding=venmo&currency=USD" data-sdk-integration-source="button-factory"></script>
<div class="content-wrapper" height="10%">
	<div id="content-container">
		<section class="content-header">
			<h4> Comprar Créditos </h4>
			
			<h4>(comunícate <a href="https://my-great-talent.com/newchat.php?id=46339">AQUÍ</a> con nosotros si tienes algún inconveniente)</h4>
		</section>
		<?php				
		foreach($Items as $key => $item){
			$item = (object) $item;
			?>
			<div class="col-md-12 row item" style="display: flex;justify-content: space-between;align-items: center;">
				<div class="col-md-4" style="font-size: clamp(14px, 4vw, 22px);font-family: system-ui;">
					Cr&eacute;ditos Especiales <b><?php Echo number_format($item->Creditos, 0, ',', '.'); ?></b>
				</div>
				<div class="col-md-6" style="display: ;flex-direction: column;align-items: center;">
					<div class="" style="border-bottom: solid 1px#dbdfe5;display: flex;justify-content: space-between;flex-direction: row;align-content: flex-start;width: 160px;align-items: center;padding: 4px;margin: 4px;border-radius: 8px;font-size: clamp(14px, 4vw, 27px);">
						<span>Precio</span><b><?php echo number_format($item->price, 0, '.',','); ?>$</b></div>
						<div onclick="registerClickButtom('<?php echo $item->price ?>')">

							<div id="smart-button-container<?php echo $key ?>">
								<div style="text-align: center;">
									<div id="paypal-button-container<?php echo $key ?>"></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<script>
					function initPayPalButtone<?php echo $key ?>() {
						paypal.Buttons({
							style: {
								shape: 'pill',
								color: 'blue',
								layout:'vertical',
								label: 'buynow',

							},

							createOrder: function(data, actions) {
								return actions.order.create({
									purchase_units: [{
										"description":"<?php echo $item->Creditos ?> Creditos",
										"reference_id": <?php echo $key ?>,
										"amount":{
											"currency_code":"USD",
											"value":<?php echo $item->price ?>
										},
									}],
									application_context: {
										shipping_preference: 'NO_SHIPPING'
									}
								});
							},

							onApprove: function(data, actions) {
								return actions.order.capture().then(function(orderData) {

									$.ajax({
										url: "ajax.php?ProcessPayment",
										type: "POST",
										data: {data: JSON.stringify(orderData)}
									}).done(function(response){
										data = $.parseJSON(response)

										if(data.status)
										{
											// Actualizar créditos en el panel
											$("#coins-user-panel span").html((data.coinsTotal) + ' Créditos')
											swal.fire("Gracias por su compra", "Hemos agregado los Créditos a tu cuenta", "success")
										}
										else(data.status == false)
										{
											if(data.error == 1)
											{
												swal.fire("Parece que la compra se efectu&oacute; pero no hemos podido acreditar tu cuenta", "Por favor escr&iacute;benos <a href='https://bellasgram.com/chat/newchat.php?id=196558'>AQU&Iacute;</a> y te respondemos al instante y agregaremos tus cr&eacute;ditos.", "error")
											}
											else if(data.error == 2){
												swal.fire("Transacci&oacute;n cancelada", "", "warning")
											}
										}
									})
								// Muestra mensage de agradecimiento
								$('#paypal-button-container<?php echo $key ?>').html('<div>Gracias por su compra</div>');
							});
							},

							onError: function(err) {
								console.log(err);
							},
							onClick: function(e)  {
      				// REGISTRA CUANDO DAN CLICK A CUALQUIER BOTON PAYPAL
      				$.ajax({
      					url: "ajax.php?registerClickButtomPaypal",
      					type: "POST",
      					data: {buttomPrice: '<?php echo $item->price ?>'}
      				}).done(function(response){

      				})
      			}
      		}).render('#paypal-button-container<?php echo $key ?>');
					}
					initPayPalButtone<?php echo $key ?>();
				</script>
				<?php
			}
			?>

	</div>
	<hr>

	<!--Al finalizar su compra en PayPal baje y toque donde dice:<br/>
	"regresar al sitio web del comercio"<br/>
	para que los créditos sean agregados al instante.-->
	<hr>Si sus créditos no se acreditan a su cuenta escríbenos <a href="https://my-great-talent.com/newchat.php?id=46339">AQUÍ</a> y te respondemos al instante y agregaremos tus créditos.<hr>

	<br><br>


Al realizar la compra quizá se te pida el código postal de tu ciudad.
<br>
Buscar mi <a href="https://worldpostalcode.com/">Código postal</a>
<hr />
<br></div>
</div>

<style type="text/css">
	.col-md-6 {
		position: initial;
	}
	.item .btn {
		top: 50%;
		position: absolute;
		transform: translateY(-50%);
		right: 20px;
	}
	.float-right { float: right; }
	.item {
		position: relative;
		padding: 20px;
		margin: 10px;
		box-sizing: border-box;
		background: white;
		float: inherit;
		width: auto;
		border-radius: 3px;
		box-shadow: 0px 0px 4px #222d323b;
	}
	.black-background {background-color:#000000;}
	.white {color:#ffffff;}

</style>

<?php
footer();
?>
