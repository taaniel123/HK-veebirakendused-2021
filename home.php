<?php

	require_once "usesession.php";
	require_once "../../../conf.php";
	// leiame andmebaasist sisseloginud kasutaja eesnime ja paneme selle muutujasse
	$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
	$query = 'SELECT vr21_users_firstname FROM vr21_users WHERE vr21_users_id = "'.$_SESSION['user_id'].'"';
	$results = mysqli_query($conn, $query);
	$nimi = 0;
	while($row = mysqli_fetch_array($results)) {
		$nimi = $row['vr21_users_firstname'];
 	}

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebirakendused ja nende loomine 2021</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<h1><!--tervitame kasutajat nimepidi-->
    <p>Tere tulemast,<strong> <?php echo $nimi; ?>!</strong></p>
	</h1>
	<h3><p>Sinu IP aadress on <?php echo $_SESSION["IPaddress"]?></p></h3>
	<h3><p><a href="add_news.php">Lisa mõni uudis</a> või <a href="show_news.php">loe neid.</a></h3></p>
	<hr>
	<h3><p><a href="upload_photo.php">Fotode üleslaadimine</a></p></h3>
	<p><a href="?logout=1">Logi välja</a></p>
</body>
</html>
