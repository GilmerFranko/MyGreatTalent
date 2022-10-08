<?php
include "config.php";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
var token={get_token:"hl"};
	$.ajax({url: "https://bellasgram.com/index.php?app=members&section=logout&get_token=hola", type : "GET", data : token, processData: false, contentType: false
	}).done(function(response){
	})
	token={logout:"hl"};

	$.ajax({url: "https://bellasgram.com/includes/third-party-auth.php?logout=logout", type : "GET", data : token, processData: false, contentType: false
	}).done(function(response){
		
	})
	deleteAllCookies();
	//BORRAR TODAS LAS COOKIES
function deleteAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
}
</script>
<?php
//BORRAR TODAS LAS COOKIES

//FUNCIONA SOLO EN LOCAL
/*if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}*/
echo '<meta http-equiv="refresh" content="0; url='.$sitio['site'].'index.php" />';
?>