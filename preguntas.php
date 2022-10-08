<?php
//
require("core.php");
head();
$page = (isset($_GET['page']) AND !empty($_GET['page'])) ? $_GET['page'] : 1;
/**
 * // OPTIENE TODAS LAS PREGUNTAS
 * @var [ARRAY]
 */
$allQuest = getAllQuestFROM($rowu['id'], $page, 40);
$consult = $connect->query("UPDATE `players_questions` SET `read_time` = \"". time() ."\" WHERE 1");
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
						<div id="listQuestions">
							<?php if($allQuest):foreach($allQuest['data'] AS $quest): ?>

							<div id="listQuestions<?php echo $quest['id']; ?>" class="listQuestions col <?php if (its_in()=='in_pc') echo 'col-xs-6';else echo 'col-xs-12'; ?>" style="padding: 8px;">
								<div class="center item">
									<div style="position:absolute;top:15px;left:15px;">
										<?php if ($quest['read_time']=='0'): ?>
											<span style="color: red;">*</span>
										<?php endif ?>
										<?php if ($quest['answer']!=''): ?>
											<a class="" href="#" onclick="" style="color: gray;"><i>Respondida</i></a>
										<?php endif ?>
									</div>
									<img class="img-avatar" src="<?php echo $quest['avatar']; ?>" style="width: 16px; height: 16px;">
									<div class="username inline">
										<?php echo createLink('profile', $quest['username'], array('profile_id' => $quest['pid'])); ?>
									</div>
									<blockquote id="textQuestion<?php echo $quest['id'] ?>" class="bg-white blockquote-footer" style="border-left: 5px solid var(--colorPrimary);width: 80%;color: var(--colorPrimary);font-size: 11pt;background-color: aliceblue;margin: 10px;height: 50px;overflow: hidden;overflow-y: auto;transition: all .2s;"><?php echo $quest['question'] ?></blockquote>
										<div class="blockquote-footer">
											<form onsubmit="sendAnswer(<?php echo $quest['id'] ?>, $('#inputAnswer<?php echo $quest['id']; ?>').val());">
												<?php if ($quest['answer'] == ''): ?>
													<input id="inputAnswer<?php echo $quest['id'] ?>" class="input-text" type="text" name="" hidden placeholder="Escribe aqui tu respuesta">
													<a id="btnAnswer<?php echo $quest['id'] ?>" class="btn btn-success" href="#" onclick="ClickAnswer(<?php echo $quest['id']; ?>);">Responder</a>
												<?php else: ?>
													<a id="btnShowAnswer<?php echo $quest['id'] ?>" class="btn btn-success" href="#" onclick="showAnswer(<?php echo $quest['id']; ?>,'<?php echo $quest['answer']; ?>')">Ver mi respuesta</a>
												<?php endif ?>
											</div>
										</form>
									</div>
								</div>
							<?php endforeach;?>
							<?php else: echo '<div class="alert alert-success"><strong><i class="fa fa-info-circle"></i> Actualmente no tienes preguntas para responder</strong></div>'; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<center>
				<?php echo $allQuest['paginator']; ?>
			</center>
		</section>
	</div>
</div>
<script type="text/javascript">
	function ClickAnswer(id)
	{
		a = '#inputAnswer'+ id;
		$(a).fadeIn(1000)
		a = '#btnAnswer'+ id;
		$(a).removeAttr("onclick")
		$(a).replaceWith($('<button class="btn btn-success">' + $(a).html() + '</button>'))
	}
	function sendAnswer(id, answer)
	{
		$.ajax({
			url: 'ajax.php?sentAnswer',
			data: {'idQuest': id, 'answer' : answer},
			method: 'POST',
		}).done(function(response){
			var data = $.parseJSON(response);
			console.log(response);
			if(data.state){
				//swal.fire('Borrada', '','success');
				tag = '#listQuestions' + id;
				$(tag).fadeOut(500)
			}else{
				swal.fire({title: 'Ha ocurrido un problema.', html: '', icon: "info"});
			}
		})
	}
	function showAnswer(id, answer)
	{
		a = '#textQuestion'+id;
		textAnswer = $(a).clone().children().remove().end().text();
		$(a).html(answer)
		$(a).css('background-color', '#d7fffa')
		$(a).css('color', '#0c6eaf')
		$(a).css('border-left','5px solid #0c6eaf')
		a = '#btnShowAnswer'+id;
		$(a).replaceWith($(`<a id="btnShowAnswer${id}" class="btn btn-info" href="#" onclick="showQuestion(${id},'${textAnswer}')"> Ver pregunta </a>`))
	}
	function showQuestion(id, answer)
	{
		a = '#textQuestion'+id;
		const textAnswer = $(a).html()
		$(a).html(answer)
		$(a).css('background-color', 'aliceblue')
		$(a).css('color', 'var(--colorPrimary)')
		$(a).css('border-left','5px solid var(--colorPrimary)')
		a = '#btnShowAnswer'+id;
		$(a).replaceWith($(`<a id="btnShowAnswer${id}" class="btn btn-success" href="#" onclick="showAnswer(${id},'${textAnswer}')"> Ver mi respuesta </a>`))
	}
</script>
<?php footer(); ?>
