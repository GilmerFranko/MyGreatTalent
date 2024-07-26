<?php
require("core.php");
head();
//$giftSendByMe = getAllGiftSendBy($rowu['id']);

$page = (isset($_GET['page']) and !empty($_GET['page'])) ? $_GET['page'] : 1;
$giftSendToMe = getAllGiftSendTo($rowu['id'], $page, 20);
?>

<div class="content-wrapper" height="10%">
	<div id="content-container">
		<div class="col-md-12">
			<div class="box">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" id="myTab" role="tablist" style="display: flex;flex-direction: row;justify-content: space-between;">
					<!--<li class="nav-item active">
						<a class="nav-link" id="sendByMe" data-toggle="tab" href="#SendByMe" role="tab" aria-controls="SendByMe" aria-selected="true" onclick="resetInputDonate();" style="color:unset;">Regalos que me han enviado</a>
					</li>-->
					<li class="nav-item active">
						<a class="nav-link" id="sendToMe" data-toggle="tab" href="#SendToMe" role="tab" aria-controls="SendToMe" aria-selected="true" onclick="resetInputDonate();" style="color:unset;">Regalos enviados para mi</a>
					</li>
				</ul>
				<br>
				<div class="tab-content">
					<!-- Regalos enviados por mi
					<div class="tab-pane active" id="SendByMe" role="tabpanel" aria-labelledby="sendByMe" style="padding: 10px;">
						<center>
							<?php /*foreach($giftSendByMe AS $giftData): ?>
								<?php
								$SQLUser = getUser($giftData['player_id']);
								$User = $SQLUser->fetch_assoc();
								?>
								<div class="row">
									<div class="col col-xs-8">
										<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $User['id']; ?>">
											<img src="<?php echo $sitio['site'].$User['avatar']; ?>" class="img-circle img-avatar" style="width: 32px;height: 32px;">
											<strong style="margin:0 10px;"><?php echo $User['username']; ?> </strong>
										</a>
										Enviaste un regalo
									</div>
									<div class="col col-xs-4">
										19/20/2001
									</div>
								</div>
								<br>
							</center>
						<?php endforeach;*/ ?>
					</center>
					</div>
					Regalos enviados para mi -->
					<div class="tab-pane active" id="SendToMe" role="tabpanel" aria-labelledby="sendToMe" style="padding: 15px;">
						<?php if ($giftSendToMe != false) : ?>
							<?php foreach ($giftSendToMe['data'] as $giftData) : ?>
								<?php
								$SQLUser = getUser($giftData['fromid']);
								$User = $SQLUser->fetch_assoc();
								?>

								<!-- Regalos giftAllUsers -->
								<?php if ($giftData['anonymous']) : ?>
									<div class="row" onclick="openNewGiftMoneyAll('<?php echo $giftData['gift']; ?>');" align="center" style="display: flex;align-items: center;">
										<div class="col col-sm-6">
											<img src="assets/img/GiftCredits.png" class="" style="width: 32px;height: 32px;">&nbsp;&nbsp
											<span>Has recibido un nuevo <a href="javascript:openNewGiftMoneyAll('<?php echo $notification['action'] ?>')">regalo</a>,</span>
										</div>
									</div>
								<?php else : ?>
									<!-- Regalo con archivo -->
									<div class="row" onclick="openModalViewGiftFromUser('<?php echo $giftData['gift']; ?>');" align="center" style="display: flex;align-items: center;">
										<div class="col col-sm-6">
											<a href="<?php echo $sitio['site'] . 'profile.php?profile_id=' . $User['id']; ?>">
												<img src="<?php echo $sitio['site'] . $User['avatar']; ?>" class="img-circle img-avatar" style="width: 32px;height: 32px;">
												<strong style="margin:0 2px;"><?php echo $User['username']; ?> </strong>
											</a>
											Te envió un regalo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										</div>
										<div class="col col-sm-4">
											<img src="<?php echo 'uploads/thumb_gift/thumb-' . basename($giftData['files']); ?>" width="100" style="max-height: 64; max-width: 64px; margin: 0 0px 0 0px;">
										</div>
										<div class="col col-sm-2">
											<a class="btn btn-primary" href="#">Ver</a>
										</div>
									</div>
								<?php endif ?>
								<br>
							<?php endforeach; ?>
						<?php endif ?>
					</div>
				</div>
			</div>
			<?php if ($giftSendToMe != false) : ?>
				<div align="center">
					<?php echo paginationIndex('regalos', $giftSendToMe['total'], 20) ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>


<?php
footer();
?>