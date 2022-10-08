<?php
 include "core.php";
 head();
?>
<!DOCTYPE html>
<html>
<head>

 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 <title></title>
 <style type="text/css">
 .body{
  margin: 0;
 }

 .container {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 66.66%; /* 3:2 Aspect Ratio */
 }

 /* Then style the iframe to fit in the container div with full height and width */
 .responsive-iframe {
  width: 100%;
  height: 100%;
  padding: 0 !important;
  margin: 0 !important;
  overflow:hidden;
 }
 .divheight{
  height: 100%;
 }
</style>
</head>
<div class="content-wrapper divheight">
 <div id="content-container" class="divheight">
  <div class="row divheight" style="margin:0;">      
   <section class="content divheight">
    <div class="row divheight">
      <div class="box divheight">
       <iframe class="responsive-iframe" src="https://chochox.com/es/" sandbox=" allow-scripts allow-same-origin"   frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
     </div>
    </div>
   </section>
  </div>
 </div>
</div>




</body>
</html>