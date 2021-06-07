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
</head>
<body>
  <div class="header">
    <h1>Galerii</h1>
    <p>See leht on valminud õppetöö raames!</p>
    <p style="color:yellow;"><b>Galeriisse ilmuvad vaid need pildid, mis üleslaadimisel on muudetud pisipildiks. Igast pildist pisipilti automaatselt ei teki!</b></p>
  </div>
    <hr>
    <div class="gallery">
    <?php echo $display_image; ?>
	</div>
    <hr>
    <div class="errormessage">
    <a href="home.php">Avalehele</a>
    </div>
</body>
</html>