<?php
//
require("core.php");
// SOLO ADMIN Y BOTS
if($rowu['role'] != 'Admin' AND $rowu['role'] != 'BOT') echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'index.php" />';
//
head();
$page = (isset($_GET['page']) AND !empty($_GET['page'])) ? $_GET['page'] : 1;
// OPTIENE TODAS LAS PREGUNTAS
$allQuest = getAllQuestUsers($page, 40);
?>
<style type="text/css">
.item{
	margin: 3px 3px;
	border-radius: 8px;
	padding: 8px;
	background-color: var(--foreground);
	box-shadow: 2px 2px 5px -4px #1f232d;
}
</style>
<div class="content-wrapper">
	<div id="content-container">
		<section class="content">
			<div class="row">
				<div class="col-md-12">
					<div class="" align="center">
						<form onsubmit="addQuestUser($('#newQuest').val());$('#newQuest').val('');return false;">
							<input id="newQuest" class="input-text" type="" name="">
							<button class="btn btn-success">Nueva Pregunta</button>
						</form>
						<br>
						<div id="listQuestions">
							<?php if($allQuest)foreach($allQuest['data'] AS $quest): ?>
								<div id="listQuestions<?php echo $quest['id']; ?>" class="listQuestions col <?php if (its_in()=='in_pc') echo 'col-xs-4';else echo 'col-xs-6'; ?>" style="padding: 8px;">
									<div class="center item">
										<div style="position:absolute;top:8px;left:8px;">
											<a class="btn btn-danger" href="#" onclick="deleteQuestUser('<?php echo $quest['id']; ?>');" style=""><i class="fa fa-trash"></i></a>
										</div>
										<img class="img-avatar" src="<?php echo $quest['avatar']; ?>" style="width: 16px; height: 16px;">
										<div class="username inline">
											<?php echo createLink('profile', $quest['username'], array('profile_id' => $quest['pid'])); ?>
										</div>
										<blockquote class="bg-white blockquote-footer" style="border-left: 5px solid var(--colorPrimary);width: 80%;color: var(--colorPrimary);font-size: 11pt;background-color: aliceblue;margin: 10px;height: 50px;overflow: hidden;overflow-y: auto;">
											<?php echo $quest['question'] ?></blockquote>
											<div class="blockquote-footer">
												<a href="#" style="color: var(--colorPrimary);">Enviar ahora</a>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
				<div align="center">
					<?php echo $allQuest['paginator']; ?>
				</div>
			</section>
		</div>
	</div>
	<script type="text/javascript">
			const Div = (content, questID) => {
				const d = `<div class="center item"><div style="position:absolute;top:8px;left:8px;"><a class="btn btn-danger" href="#" onclick="deleteQuestUser(${questID});" style=""><i class="fa fa-trash"></i></a></div><img class="img-avatar" src="<?php echo $rowu['avatar']; ?>" style="width: 16px; height: 16px;"><div class="username inline"> <?php echo $rowu['username'] ?></div><blockquote class="bg-white blockquote-footer" style="border-left: 5px solid var(--colorPrimary);width: 80%;color: var(--colorPrimary);font-size: 11pt;background-color: aliceblue;margin: 10px;height: 50px;overflow: hidden;overflow-y: auto;">${content}</blockquote><div class="blockquote-footer"><a href="#" style="color: var(--colorPrimary);">Enviar ahora</a></div></div>`
				return d;
			}
			// AÑADE NUEVA PREGUNTA A LA DATABASE
			function addQuestUser(quest){
				if(quest != null && quest != ''){
					$.ajax({
						url: 'ajax.php?addQuestUser',
						data: {'Quest': quest},
						method: 'POST',
					}).done(function(response){
						var data = $.parseJSON(response);
						console.log(response);
						if(data.state){
							//swal.fire('Nueva pregunta añadida', '','success');
							$("#listQuestions").prepend('<div id="listQuestions'+data.questID+'" class="listQuestions col <?php if (its_in()=='in_pc') echo 'col-xs-4';else echo 'col-xs-6'; ?>" style="padding: 8px;">'+Div(quest, data.questID)+'</div>')
						}else{
							swal.fire({title: 'Ha ocurrido un problema.', html: '', icon: "warning"});
						}
					})
				}
			}
			// BORRA UNA PREGUNTA DE LA DATABASE
			function deleteQuestUser(id){
				tag = '#listQuestions' + id;
				$(tag).fadeOut(300)
				$.ajax({
					url: 'ajax.php?deleteQuestUser',
					data: {'idQuest': id},
					method: 'POST',
				}).done(function(response){
					var data = $.parseJSON(response);
					console.log(response);
					if(data.state){
						//swal.fire('Borrada', '','success');
					}else{
						$(tag).fadeIn(100)
						swal.fire({title: 'Ha ocurrido un problema.', html: '', icon: "info"});
					}
				})

			}
		</script>
		<?php footer(); ?>
