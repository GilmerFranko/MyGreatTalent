<?php
require("core.php");

head();

?>
     
<script>
var user_globalname = '<?php echo $rowu['username']; ?>';
</script>

<div class="content-wrapper" height="10%">

			<!--CONTENT CONTAINER-->
			<!--===================================================-->
			<div id="content-container">
				
				<section class="content-header">
    			</section>


				<!--Page content-->
				<!--===================================================-->
				<section class="content">

                <div class="row">
                    
				<div class="col-md-12">

				    <div class="box">
						<div class="box-header">
							
							<!--textoinicio-->
							
							
							
							
							
							
							
							
							
							
<div class="chatWindow">
<div id="chatText" readonly ></div>

<div class="form_pika">
	<div id="chatBotName" style="display:none;">PIKA</div>
		<form id="chatForm" onsubmit="chat(); return false;">
			<input id="chatInput" autocomplete="off"  type="text"></input>
		</form>
	</div>
</div>						
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							
                         	<!--textofin-->
	
	
						</div>
						<div class="box-body">

										
												</div>
											</div>

											
                        </div>
                       
							
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

<link rel="stylesheet" href="bot/diseno.css" />
<script src="bot/pika1.js"></script>
<script src="bot/ximena.js"></script>
<script src="bot/pika3.js"></script>
<script src="bot/pika4.js"></script>

<script>
var bot = new DeixiBot(botData);
var botName = "XIMENA";
</script>
<style type="text/css">
.user_background {
    background-image: url(<?php echo $rowu['avatar']; ?>)!important;
}
.chatWindow{
    max-width: 900px;
    margin: 0 auto;
}
#chatText{
    width: 96%;
    height: 360px;
    border: 2px solid #e10707;
    margin: 0 0 8px 0;
    padding: 8px;
    overflow: auto;
    color:transparent;
}
#chatText .chat_{
overflow: hidden;clear:both;
margin: 0 0 7px 0;
color: #CC99AD;
}
#chatText .chat_ .user_chat{
overflow: hidden;float: left;
}
#chatText .chat_ .user_chat .user_background{
	width: 50px;height: 50px;
background-image: url(/files/avatar/{$tsUser->uid}_120.jpg);background-size:100% 100%;
float:left;
}
#chatText .chat_ .user_chat .pika_background{
	width: 50px;height: 50px;
background-image: url(images/bellabot.jpg);background-size:100% 100%;
float:left;
}
#chatText .chat_ .text_user{
margin: 3px 0 0 6px;
float: left;
font-size: 20px;
}
#chatText .chat_ .name_user{
clear: none;
font-weight: bold;
margin: 0 0 9px 0;
}

.form_pika{
    border: 2px solid #e10707;
    padding: 4px 8px;
}
.form_pika input[type=submit]{
    padding: 7px 17px;
    width: 8%;
}
.form_pika input[type=text]{
	padding: 6px 8px;
    width: 89%;
    font-size: 17px;
}
</style>
<?php
footer();
?>