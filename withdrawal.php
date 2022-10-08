<?php
require "core.php";
head();

$minimo 		= 10;
$dinero    		= $rowu['creditos'];
$eCredit    	= $rowu['eCreditos'];
$cReditoToDolar = 1000;
$eReditoToDolar = 1000;

$UlikeMinus = $rowu['Likesdados'];
$likeMinus = 5000;

$UlikePlus = $rowu['LikesRecibidos'];
$likePlus = 10000;

if (isset($_GET['CanjeLikes'])) {
    if ($rowu['Likesdados']>=$likeMinus) {

        //RESTAR LIKES
        $canjear=mysqli_query($connect,"UPDATE players SET Likesdados=Likesdados-'$likeMinus' WHERE id='$rowu[id]'");
        // SUMAR CREDITOS ESPECIALES
        updateCredits($rowu['id'],'+',500,10);

        if ($canjear) {
            $connect->query("INSERT INTO `retiros` (usuario, metodo, identificacion, monto, type,status)
                VALUES ('$rowu[id]', '', '', '500 Creditos Especiales', '5,000 Me gustas','Efectuado')");
            echo '<script>swal.fire("El cambio se realizo con exito!", "", "success");</script>';
            echo '<meta http-equiv="refresh" content="1; url=withdrawal.php" />';
        }
    }
}
if (isset($_GET['CanjeCredits'])) {
    if ($rowu['creditos']>=10000) {
        $canjear=mysqli_query($connect,"UPDATE players SET creditos=creditos-'10000' WHERE id='$rowu[id]'");

        // SUMAR CRÉDITOS ESPECIALES
        updateCredits($rowu['id'],'+',1000,6);

        if ($canjear) {
            $connect->query("INSERT INTO `retiros` (usuario, metodo, identificacion, monto, type,status)
                VALUES ('$rowu[id]', '', '', '1,000 Creditos Especiales', '10,000 Creditos Normales','Efectuado')");
            echo '<script>swal.fire("El cambio se realizo con exito!", "", "success");</script>';
            echo '<meta http-equiv="refresh" content="1; url=withdrawal.php" />';
        }
    }
}


?>
<style>
.jumbotron {
    padding: 2rem 1rem;
    margin-bottom: 2rem;
    border-radius: 10px;
    height:300px;
}
.list-group {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    padding-left: 0;
    margin-bottom: 0;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    line-height: 1.5;
    color: #444;
    background-clip: padding-box;
    border: 1px solid #bbb;
    border-radius: 0.25rem;
    -webkit-transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
    transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
}
.btn-danger.disabled, .btn-danger:disabled {
    background-color: #D9831F;
    border-color: #D9831F;
}
.btn-danger.disabled, .btn-danger:disabled {
    background-color: #dc3545;
    border-color: #dc3545;
}
.btn.disabled, .btn:disabled {
    opacity: 0.65;
}
.list-group-item {
    position: relative;
    display: block;
    padding: 0.75rem 1.25rem;
    margin-bottom: -1px;
    border: 1px solid #eeeeee;
    color: #6d6d6d;
}
.list-group-item:first-child {
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}
.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}
.badge-danger {
    background-color: #D9831F;
}
.badge-success {
    background-color: #469408;
}
</style>
<div class="content-wrapper">
<div class="col-md-12" style="float:inherit;">
    <div class="card-header text-white bg-primary" style="padding:15px 30px;border-radius:3px;font-size:17px;text-align: center;">
        <h5 class="card-title"><p>Ver mi <a href="requests_historial.php" style="color:pink">HISTORIAL</a> de retiros.</p>
		</h5>
        
    </div>
    <div class="card-body">
        <div class="row">	
            <br>
            
            <div class="col-md-6">
		
                <div class="jumbotron box">
                    <center>
                        <h4 class="StastLs">Tienes <?php echo number_format($dinero);?> Créditos Normales</h4><hr>
                    </center>

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <form method="POST" action="">
								<center>
                                    <h5>Por cada <span style="color:chocolate ">10,000 Créditos Normales</span> puedes canjear <span style="color:chocolate ">1,000 Creditos Especiales</span></h5>
                                </center>
                                <center>
                                    <hr />
                                    <?php
										if (intval($dinero/$cReditoToDolar) < 10) {
											echo '<button style="" class="btn btn-danger btn-md btn-block" disabled><em class=""></em> Aun no llegas al mínimo</button>';
											} else {
											echo ' <a href="withdrawal.php?CanjeCredits" class="btn btn-primary btn-block" name="request" type="submit">Canjear</a>';
										}
									?>
                                </center>
                            </form>

                        </div>
                        <div class="col-md-2"></div>
                    </div>


                </div>
            </div>
            <div class="col-md-6">	
			
                <div class="jumbotron box"  >
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <form method="POST" action="">
								<center>
                                    <h4 class="StastLs">Tienes <?php echo number_format($UlikeMinus);?> Me gustas dados</h4>
                                </center>
                                    <hr />
                                <center>
                                    <h5> Por cada <span style="color:chocolate "> <?php Echo number_format($likeMinus); ?> me gustas dados</span>
                                    obtendras <span style="color:chocolate ">500 Creditos especiales</span>
                                    </h5>
                                </center>
                                <center>
                                    <hr />
                                    <?php
										if (intval($UlikeMinus/$likeMinus) < 1) {
											echo '<button style="" class="btn btn-danger btn-md btn-block" disabled><em class=""></em> Aun no llegas al mínimo</button>';
											} else {
											echo ' <a href="withdrawal.php?CanjeLikes" class="btn btn-primary btn-block" name="request" type="submit">Canjear</a>';
										}
									?>
                                </center>
                            </form>

                        </div>
                        <div class="col-md-2"></div>
                    </div>


                </div>
            </div>
        </div>
		
    </div>

</div>
</div>
<?php
footer();
?>
