<?php

$SandBox = false;
$Paypal_Account = "contacto@diaches-game.com";
$DomainUrl = "https://my-great-talent.com/";
$UrlSendForm = "https://www.paypal.com/cgi-bin/webscr";


if($SandBox){
	$Paypal_Account = "sb-w2imt372224@business.example.com";
	$UrlSendForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
}

$Items = [
	101 => [
		'Creditos' => 1000,
		'price' => 10
	],
	250 => [
		'Creditos' => 2000,
		'price' => 20
	],
	301 => [
		'Creditos' => 5000,
		'price' => 40
	],
	404 => [
		'Creditos' => 10000,
		'price' => 60
	],
	548 => [
		'Creditos' => 30000,
		'price' => 120
	]
];

if(isset($_GET["acreditar"])){
	require("core.php");
	
	$AddCash = $Items[ $_GET["acreditar"] ]['Creditos'];
	
	$uname = $_COOKIE['eluser'];

	//ACTUALIZAR CREDITOS
	updateCredits($rowu['id'],'+',$AddCash,3);

	header("location: ./comprar.php");
	
	exit();
}
	
if (isset($_GET['submitPayment'])) {
	require_once("core.php");
 	//REGISTRAR COMPRA
 	$item = (object) $Items[ $_GET['submitPayment'] ];
    $date = time();
	$id = mysqli_query($connect,"SELECT id from players where username='$uname'")->fetch_assoc();
    $user = mysqli_query($connect, "INSERT INTO `payment_list` (`id`,`userid`, `paid`, `date`) VALUES (NULL,$id[id], '$item->price', '$date')");

?> 
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<div class="loading">One moment, please... Un momento, por favor...</div>

<form id="realizarPago" action="<?php echo $UrlSendForm; ?>" method="post">
	<input name="cmd" type="hidden" value="_cart" />
	<input name="upload" type="hidden" value="1" />
	<input name="business" type="hidden" value="<?php echo $Paypal_Account; ?>" />
	<input name="shopping_url" type="hidden" value="<?php echo $DomainUrl; ?>comprar.php" />
	<input name="currency_code" type="hidden" value="USD" />
	<input name="return" type="hidden" value="<?php echo $DomainUrl; ?>comprar.php?acreditar=<?php echo $_GET['submitPayment']; ?>" />
    <input type="hidden" name="no_shipping" value="1">
	<input name="rm" type="hidden" value="2" />
	<input name="item_number_1" type="hidden" value="<?php echo $_GET['submitPayment']; ?>" />
	<input name="item_name_1" type="hidden" value="<?php echo $item->Creditos; ?> Creditos" />
	<input name="amount_1" type="hidden" value="<?php echo $item->price; ?>" />
	<input name="quantity_1" type="hidden" value="1" /> 

</form>
<script>
$(document).ready(function () {
	$("#realizarPago").submit();
});
</script>
<?php
	exit();
}   
require("core.php");
head();
?>
<div class="content-wrapper" height="10%">
    <div id="content-container">
		<section class="content-header">
		
		  <h4><i class="fab fa-paypal"></i> Comprar Créditos con PayPal </h4> 
<hr />No tienes PayPal? ver <a href="compras.php">AQUÍ</a> como <a href="compras.php">comprar con tarjeta</a> de debito o crédito. <img src="https://bellasgram.com/chat/assets/img/tarjetas.png" width="90" height="23" /><hr />
<h4><i class="fab fa-paypal"></i> Comprar con PayPal </h4> 

		</section>
		<?php				
			foreach($Items as $key => $item){
				$item = (object) $item;
			?>
			
			<div class="col-md-12 row item">
				<div class="col-md-6">
					Creditos<br> <?php Echo $item->Creditos; ?>
				</div>
				<div class="col-md-6">
					<b>Precio</b><br> <?php Echo $item->price; ?> $ Dólares PayPal
					<a href="comprar.php?submitPayment=<?php Echo $key; ?>" target="_blank">
						<button class="float-right btn btn-success">Comprar</button>
					</a>
				</div>
			</div>
			
			<?php
			}				
		?>

	</div>
<hr>
			
		    Al finalizar su compra en PayPal baje y toque donde dice:<br/> 
		  "regresar al sitio web del comercio"<br/> 
		  para que los créditos sean agregados al instante.  
		  <hr>Si sus créditos no se acreditan a su cuenta escríbenos <a href="https://my-great-talent.com/newchat.php?id=46339">AQUÍ</a> y te respondemos al instante y agregaremos tus créditos.<hr>
		  

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
