<?php
require("core.php");
head();
// COLOCAR EN VISTO TODAS LAS NOTIFICACIONES
?>
<style type="text/css">
	.ntification{
		padding: 11px;
		display: flex;
		flex-direction: row;
		align-content: center;
		justify-content: flex-start;
		align-items: baseline;
		flex-wrap: nowrap;
	}
	.margin-top{
		margin-top: 10px;
	}
</style>
<div class="content-wrapper" height="10%">
	<div id="content-container">
		<section class="content-header">
			<h1><i class="fa fa-gift"></i> Enviar regalos a todos los usuarios</h1>
		</section>
	</div>
	<div class="box">
		<?php if ($rowu['permission_send_gift']): ?>
		<a class="btn btn-warning" onclick="openModalGiveGift2('',1)">Enviar regalo</a>
		<?php else: ?>
			<a class="btn btn-warning actionInfo" data-info="Aun no puedes enviar regalos" data-secondinfo="Para enviar regalos debes..." href="#" >Enviar regalo</a>
		<?php endif ?>
	</div>
</div>

	<?php
	footer();
	?>
