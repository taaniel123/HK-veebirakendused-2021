<?php

require_once "usesession.php";
require_once "../../../conf.php";
require_once "fnc_gallery.php";

$display_image = display_image();

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Galerii</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="scripts/modal.js" defer></script>
</head>
<body>
  <!--Modaalaken fotogalerii jaoks-->
  <div id="modalarea" class="modalarea">
	<!--sulgemisnupp-->
	<span id="modalclose" class="modalclose">&times;</span>
	<!--pildikoht-->
	<div class="modalhorizontal">
		<div class="modalvertical">
			<p id="modalcaption"></p>
			<img id="modalimg" src="../images/empty.png" alt="galeriipilt">
      <br>
			<div id="rating" class="modalRating">
				<label><input id="rate1" name="rating" type="radio" value="1">1</label>
				<label><input id="rate2" name="rating" type="radio" value="2">2</label>
				<label><input id="rate3" name="rating" type="radio" value="3">3</label>
				<label><input id="rate4" name="rating" type="radio" value="4">4</label>
				<label><input id="rate5" name="rating" type="radio" value="5">5</label>
				<button id="storeRating">Salvesta hinnang!</button>
				<br>
				<p id="avgRating"></p>
			</div>
		  </div>
	  </div>
  </div>
  <div class="header">
    <h1>Galerii</h1>
    <p>See leht on valminud õppetöö raames!</p>
    <p style="color:yellow;"><b>Galeriisse ilmuvad vaid need pildid, mis üleslaadimisel on muudetud pisipildiks. Igast pildist pisipilti automaatselt ei teki!</b></p>
  </div>
    <hr>
    <div class="gallery" id="gallery">
    <?php echo $display_image; ?>
	</div>
    <hr>
    <div class="errormessage">
    <a href="home.php">Avalehele</a>
    </div>
</body>
</html>