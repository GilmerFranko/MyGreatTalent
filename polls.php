<?php
require("core.php");

view_Notification_encuesta();

$uname = $_COOKIE['eluser'];
$suser = mysqli_query($connect, "SELECT * FROM `players` WHERE username='$uname' LIMIT 1");
$rowu  = mysqli_fetch_assoc($suser);

if(isset($_GET['updateDB'])){
	
	$querycp = mysqli_query($connect, "SELECT * FROM `polls`");
	$countcp = mysqli_num_rows($querycp);
	if ($countcp > 0) {
		while ($rowcp = mysqli_fetch_assoc($querycp)) {
			$data = json_decode($rowcp['questions']);
			
			if($data){
				$connect->query("UPDATE `polls` SET questions='". serialize($data) ."' WHERE id='{$rowcp['id']}'");
			}
			
		}
	}
	
	exit();
}

function ModulePoll ($pollID) {
	
	global $connect, $player_id;
	
	$return = '';
	$Poll = $connect->query("SELECT * FROM `polls` WHERE id='{$pollID}'");
	if($Poll){
		$Poll = (object) mysqli_fetch_assoc($Poll);
		$Questions = unserialize($Poll->questions);
		if(is_null($Poll->users_votes)){
			$UsersVotes = [];
		}else{ 
			$UsersVotes = json_decode($Poll->users_votes, true);
		}
		$isVoted = false;
		if($player_id && isset($UsersVotes[$player_id])){
			$isVoted = true;
		}
		$QuestTotalVotes = [];
		foreach ($UsersVotes as $key => $value) {
			if(isset($QuestTotalVotes[ $value ])){
				$QuestTotalVotes[ $value ]++;
			}else{
				$QuestTotalVotes[ $value ]=1;
			}
		}
		$QuestTotal = count($UsersVotes);

		$return = '<h2>'. $QuestTotal .' Votos</h2>

		<ul class="unstyled '. ($isVoted ? 'voting-end voted ':'voting ') .'poll-options" data-post-id="'. $pollID .'" style="padding:0;">';

			foreach($Questions as $key => $Quest){
				$votedItem = false;
				if($isVoted && $Questions[ $UsersVotes[$player_id] ] == $Quest){
					$votedItem = true;
				}                

				$return .= '<li class="poll-item'. ($votedItem ?' voted-item':'') .'" '. (!$isVoted && $player_id?"onclick=AddPollVote(this)":'') .' data-poll-id="'. $key .'">
					<div class="label">
						<div class="bar"'. ($isVoted ? 'style=width:'.pdrc($QuestTotal, @$QuestTotalVotes[ $key ]).'%;':'') .'></div>
						<div class="title" style="font-size:15px;">
							<div class="content">';
							
				if($votedItem){									
					$return .= ' </i>';
				}
	
				$return .= $Quest . '</div>';
				
				if($isVoted){
					$return .= '<span class="TotalVotes">
						'. (@$QuestTotalVotes[ $key ]==''?'0':@$QuestTotalVotes[ $key ]) .'
					</span>
					<span class="partition">
						'. pdrc($QuestTotal, @$QuestTotalVotes[ $key ]) .'%
					</span>';	
				}
				
				$return .=	'</div>
					</div>
				</li>';
			}

		$return .= '</ul>';
	}
	
	return $return;
}

if(isset($_GET['AddPollVote'])) {

	if($player_id){
		$Poll = $connect->query("SELECT * FROM `polls` WHERE id='{$_POST['pollID']}'");
		if($Poll){
			$Poll = (object) mysqli_fetch_assoc($Poll);
			$UsersVotes = json_decode($Poll->users_votes, true);
			$UsersVotes[ $player_id ] = $_POST['option'];
			$connect->query("UPDATE `polls` SET users_votes='". json_encode($UsersVotes) ."' WHERE id='{$_POST['pollID']}'");
			
			Echo json_encode([
				'status' => true,
				'PollResults' => ModulePoll($Poll->id)
			]);
			Exit();
		}
	}

	Echo json_encode([
		'status' => false,
		'message' => 'UserNotLogged'
	]);
	Exit();
}

if(isset($_GET['SendingPoll'])) {
	$_DATA = $_POST;
	if($player_id && $_DATA['title']!=''){
		
		$pased = true;
		foreach ($_DATA['questions'] as $quest) {
			if($quest==''){
				$pased = false;
				break;
			}
		}
		if(!$pased){
			Echo json_encode([
				'status' => false
			]);
			exit();
		}

		$questions = serialize($_DATA['questions']);

		if($connect->query("INSERT INTO `polls` (uid, title, questions, created) VALUES 
			('{$player_id}', '{$_DATA['title']}', '{$questions}', '". time() ."')")){
			Notificacion_encuesta();
			Echo json_encode([
				'status' => true
			]);
			exit();
		}
		
	}
	Echo json_encode([
		'status' => false
	]);
	exit();
}

head();

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<style>
.poll-options .poll-item:first-child {
    margin-top: 0;
}
.poll-options .poll-item {
    font-size: 13px;
    margin-top: 7px;
}
.poll-options.voting .poll-item {
    cursor: pointer;
}
.poll-options .poll-item {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    margin-top: 12px;
}
.poll-options .poll-item>.label {
    position: relative;
    margin: 0 .25em 0 0;
    -webkit-box-flex: 0;
    -ms-flex: 0 1 100%;
    flex: 0 1 100%;
    overflow: hidden;
    text-align: center;
    border-radius: 3px;
    background-color: #4a4a4a;
    color: #fff;
    line-height: 40px;
    padding: 0 .5em;
}
.poll-options .poll-item>.label .title {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
}
.poll-options .poll-item>.label .title>.content {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    height: 40px;
    -webkit-box-flex: 10;
    -ms-flex-positive: 10;
    flex-grow: 10;
    -ms-flex-preferred-size: 0;
    flex-basis: 0;
}
.poll-options .poll-item>.label .title>.content {
    min-height: 33px;
    line-height: 33px;
    display: inline-block;
    padding-top: 0;
}
.poll-options .poll-item>.label .bar {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    background-color: var(--user-background);
    background-image: linear-gradient(90deg,hsla(0,0%,100%,0),hsla(0,0%,100%,.2));
}
.poll-options.voting .poll-item .label:hover .bar {
    background: var(--user-foreground);
}
.poll-options .poll-item.voted-item>.label .bar {
    background-color: #1dda8f;
}
.poll-options.voting .bar {
    width: 100%;
}
.poll-area {
    padding: 25px 0;
}
.poll-options.voted .poll-item>.label .partition {
    text-align: right;
    line-height: 33px;
}
.poll-options.voted .poll-item>.label .TotalVotes {
    line-height: 33px;
    color: gold;
    font-weight: 500;
    position: absolute;
    right: 35px;
}
.poll-options.voted .label {
    text-align: left;
}
</style>
<script>
var PollSending = false;
var AddPollVote = function(ths) {
	if(PollSending){
		alert('hay otra accion en curso');
		return;
	}
	PollSending = true;
	var elementLK = $(ths);
	var option = elementLK.data("poll-id");
	var pollID = elementLK.parent().data("post-id");
	var actBox = $("[id=activity-P"+pollID+"]");
	actBox.find(".Results-loader").show();
	actBox.find(".poll-options").hide();
	$.ajax({
		url: '?AddPollVote',
		type: 'POST',
		data: {
			option: option,
			pollID: pollID
		}
	}).done(function(e){
		e = $.parseJSON(e);
		PollSending = false;
		if(!e.status && e.message == 'UserNotLogged'){
			open_usr(1);
			actBox.find(".poll-options").show();
		}
		else if(e.status){
			actBox.find(".poll-area").html(e.PollResults);
		}
	})   
}
</script>
<div class="content-wrapper">
    <!--CONTENT CONTAINER-->
    <!--===================================================-->
    <div id="content-container">

        <!--Page content-->
        <!--===================================================-->
        <section class="content">

            <div class="row">

                <div class="col-md-12">
Por ahora solo las mujeres y administradores puedes crear encuestas.

                    <div class="box">
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">

                                <tbody>
									<?php
										if ($rowu['role'] == 'Admin' or $rowu['gender'] == 'mujer'){ 
									?> 
                                    <center>
                                        <button class="btn btn-success" data-toggle="modal" data-target="#trailerModal">
											Agregar Encuesta										
										</button>
                                    </center>
									<br>
									<?php
										}
									?> 

                                    <div class="card">
                                        <div class="card-body">
<?php

function pdrc($total, $int){
    $int = (100 * $int) / $total;
    if($int>100){
        $int = 100;
    }
    $int = (string) $int;
    $val = $int[0].@$int[1].@$int[2];
    if(@$val[2] == '.'){
        $val = $val[0].@$val[1];
    }
    return $val;
}

$timeonline = time() - 60;

$total_pages = $connect->query("SELECT * FROM `polls` ORDER BY id DESC")->num_rows;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

$num_results_on_page = 10;
$calc_page = ($page - 1) * $num_results_on_page;

$querycp = mysqli_query($connect, "SELECT * FROM `polls` ORDER BY id DESC LIMIT {$calc_page}, {$num_results_on_page}");
$countcp = mysqli_num_rows($querycp);
if ($countcp > 0) {
    while ($Poll = mysqli_fetch_assoc($querycp)) {
		$Poll = (object) $Poll;
        $author_id = $Poll->uid;
        $querycpd  = mysqli_query($connect, "SELECT * FROM `players` WHERE id='$author_id' LIMIT 1");
        $rowcpd    = mysqli_fetch_assoc($querycpd);
		
		$sqlbuscarbloqueo = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$player_id' AND fromid='$author_id'");
		$hayunbloqueo = mysqli_num_rows($sqlbuscarbloqueo);
	
		$sqlbuscarbloqueo2 = mysqli_query($connect, "SELECT * FROM `bloqueos` WHERE toid='$author_id' AND fromid='$player_id'");
		$hayunbloqueo2 = mysqli_num_rows($sqlbuscarbloqueo2);
	
	if ($hayunbloqueo < 1 && $hayunbloqueo2 < 1){
		
?><tr>

<td>
	<div class="card text-left" style="position: relative;" id="activity-P<?php Echo $Poll->id; ?>">
		<div class="card-header bg-secondary mb-3">
			<a href="<?php echo $sitio['site'].'profile.php?profile_id=' . $rowcpd['id']; ?>">
				<img src="<?php echo $sitio['site'].$rowcpd['avatar']; ?>" class="img-circle" style="width:65px;">
				<strong>
					<div style="display:inline-block;vertical-align:middle;">
					<?php 
						echo  $rowcpd['username'] . '</br>'; 
						if ($rowcpd['timeonline'] > $timeonline) {
							echo '<span style="color:green">online</span>';
						} 
					?> 
					</div>		
				</strong>
			</a>
			<h3 style="padding-top: 12px;"><?php Echo $Poll->title; ?></h3>

			<div class="poll-area">
				<?php Echo ModulePoll($Poll->id);?>
				<div class="Results-loader" style="text-align: center;display: none;">
					<h5>Cargando Resultados</h5>
				</div>  
			</div>

		</div><br>
	</div><br />

<?php
	}
    }
?>
<?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
<ul class="pagination">
	<?php if ($page > 1): ?>
	<li class="prev"><a href="galerias.php?page=<?php echo $page-1 ?>">Anterior</a></li>
	<?php endif; ?>

	<?php if ($page > 3): ?>
	<li class="start"><a href="polls.php?page=1">1</a></li>
	<li class="dots">...</li>
	<?php endif; ?>

	<?php if ($page-2 > 0): ?><li class="page"><a href="polls.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
	<?php if ($page-1 > 0): ?><li class="page"><a href="polls.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

	<li class="currentpage"><a href="polls.php?page=<?php echo $page ?>"><?php echo $page ?></a></li>

	<?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="polls.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
	<?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="polls.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

	<?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
	<li class="dots">...</li>
	<li class="end"><a href="polls.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
	<?php endif; ?>

	<?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
	<li class="next"><a href="polls.php?page=<?php echo $page+1 ?>">Siguiente</a></li>
	<?php endif; ?>
</ul>
<?php endif; ?>
<?php
} else {
    echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Actualmente no hay Encuestas</strong></div>';
}

?>

                                            </td>
                                            </tr>

                                        </div>
                                    </div>
                                </tbody>
                            </table>
                            <br>

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
	if ($rowu['gender'] == 'mujer' or $rowu['role'] == 'Admin'){ 
?> 
<style>
input, select, textarea {
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    font-size: 14px;
    line-height: 20px;
    padding: 9px 10px;
    width: 100%;
    border: 0;
    background-color: #ecf0f5;
    border-radius: 5px;
	outline: none;
}
.js-new-poll .poll-option {
    height: 45px;
    border: 0px;
    display: block;
    text-align: left;
    position: relative;
    margin-bottom: 10px;
    border-radius: 5px;
}
.js-new-poll .poll-option input {
    padding-left: 100px;
}
.js-new-poll .poll-option-nmr {
    background-color: #41e0a1;
    color: white;
    border: 0;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 13px;
    cursor: pointer;
    user-select: none;
}
.js-new-poll .poll-option-nmr {
    position: absolute;
    top: 9px;
    left: 10px;
}
.js-new-poll input {
    height: 45px;
    border: 0px;
}
.js-new-poll .poll-option-remove {
    background: red;
    width: 10px;
    height: 10px;
    position: absolute;
    top: 8px;
    right: 10px;
    text-align: center;
    padding: 10px;
    display: grid;
    box-sizing: content-box;
    border-radius: 50%;
    color: white;
}
</style>
<script>
var xg_cancel = function() {
	$("body").removeClass("s-active");
	$('#ModalNewPost').removeClass('show');
	$('#ModalNewPoll').removeClass('show');
	$("[name=memeID]").val( '' );
	$("[name=PhotoB64]").val( '' );
	$("[name=Youtube]").val( '' );
	var Ft = $(".js-new-post");
	$(".ImgPreview").hide().find('img').removeAttr('src');
	Ft.find(".js-new-activity").show();
	Ft.find(".spoilerAlertButton").show();
	SendingPoll = false;
	$(".input-option-add").remove();
	$(".input-poll").val('');
}
var SendingPoll = false;
var xg_submit_post = function(){
	if(SendingPoll){
		return;
	}
	if(s_quest()){
		SendingPoll = true;
		$("#0W2").append( $(".input-poll").clone() );
		var data = $("#0W2").serializeArray();
		$("#0W2").find(".input-poll").remove();
		xg_cancel();
		$.ajax({
			url: '?SendingPoll',
			type: 'POST',
			data: data
		}).done(function(response){
			SendingPoll = false;
			window.location.reload();
		});
	}    
}
var s_quest = function() {
	var Np = $(".js-new-post");
	var complete = true;
	Np.find(".input-poll").each(function(e){ 
		if($(this).val()==''){
			complete = false;
			return false;
		}
	})
	if(complete){
		$(".xg-submit-post").removeClass('disabled');
	}
	return complete;
}
var xg_addOption = function(ths) {
	var Np = $(".js-new-poll");
	if(Np.find(".poll-option").length!=6){
		var pollNum = Np.find(".poll-option").length + 1;
		var e = $("<label>")
			.addClass("poll-option input-option-add")
			.attr({ 'for' : 'input-poll-'+pollNum })
			.append(
				$("<span>")
					.html("Opción "+pollNum)
					.addClass("poll-option-nmr")
			)
			.append(
				$("<input>")
					.attr({
						'id':'input-poll-'+pollNum,
						'type':'text',
						'name':'questions[]',
						'placeholder':'Opción..',
						'onkeyup':'s_quest()'
					})
					.addClass("input-poll")               
			)
			.append(
				$("<span>")
					.html($('<i class="fa fa-times">'))
					.addClass("poll-option-remove")
					.attr({'onclick':'$(this).parent().remove();'})
			);
		$(ths).before(e);
		s_quest();
		if(pollNum>=6){
			$(ths).hide();  
		}
	}else{
		$(ths).hide();  
	}  
}
</script>
<form id="0W2" style="display: none;" method="POST" action="">
	<input type="hidden" name="title" value="">
</form>
<div class="modal fade" id="trailerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document" style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);width:95%;max-width:600px;margin:0;border-radius:5px;overflow:hidden;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Nueva Encuesta</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;top:0;right:0;padding:16px;">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <div class="js-new-poll">
                    <input type="text" name="title" class="input-poll" 
                        placeholder="Titulo de la encuesta" 
                        style="margin-bottom: 10px;">
                    <div class="poll-option" for="input-poll-1">
                        <span class="poll-option-nmr">
                            Opción 1
                        </span>
                        <input id="input-poll-1" type="text" name="questions[]" class="input-poll" 
                            placeholder="Opción.." onkeyup="s_quest()">
                    </div>
                    <div class="poll-option" for="input-poll-2">
                        <span class="poll-option-nmr">
                            Opción 2
                        </span>
                        <input id="input-poll-2" type="text" name="questions[]" class="input-poll" 
                            placeholder="Opción.." onkeyup="s_quest()">
                    </div>
                    <button class="btn btn-success" onclick="xg_addOption(this)">
                        <i class="fa fa-plus"></i>
                        Añadir Opcion
                    </button>
                </div>
			</div>
			<div class="modal-footer">
                <button class="btn xg-cancel" onclick="$('#ModalNewPoll').toggleClass('show');xg_cancel();">
                    Cancelar
                </button>

                <button class="btn xg-submit-post disabled" onclick="xg_submit_post()">
                    Publicar
                </button>
			</div>
		</div>
	</div>
</div>
<?php
	}
footer();
?>