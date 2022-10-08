<?php


include "../config.php";

$friends = [];
$connect->query("TRUNCATE `friends`");
$sqlUsers = $connect->query("SELECT id,username FROM players WHERE 1");
$insert= "";
$separator = "";
while($Users = $sqlUsers->fetch_assoc())
{
	$ids[] = $Users['id'];
}

$count = count($ids);
for ($i=0; $i <$count ; $i++) {
	for ($b=0; $b <$count; $b++) {
		$insert .=$separator . ("('$ids[$i]', '$ids[$b]')");
		$separator = ",";
		echo $ids[$b] .'-'. $ids[$i] . PHP_EOL;
	}
}
$connect->query("INSERT INTO `friends` (`player1`,`player2`) VALUES ".$insert );
 ?>
