<?php

require_once "usesession.php";
require_once "../../../conf.php";
require_once "classes/Upload_photo.class.php";

$file_size_limit = 1 * 1024 * 1024;
$image_max_w = 600;
$image_max_h = 400;
$photo_upload_error = null;
$notice = null;
$watermark = "../images/vr_watermark.png";

if (isset($_POST["photo_submit"])) {
    function store_photos($photos_userid, $photos_filename, $photos_origname, $photos_alttext, $photos_privacy) {
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        $conn -> set_charset("utf8");
        $stmt = $conn -> prepare("INSERT INTO vr21_photos (vr21_photos_userid, vr21_photos_filename, vr21_photos_origname, vr21_photos_alttext, vr21_photos_privacy) VALUES (?,?,?,?,?) ");
        echo $conn -> error;
        $stmt -> bind_param("isssi", $photos_userid, $photos_filename, $photos_origname, $photos_alttext, $photos_privacy);
        $stmt -> execute();
        $stmt -> close();
        $conn -> close();
    }

        // võtame kasutusele upload photo classi
        $photo_upload = new Upload_photo($_FILES["file_input"], $file_size_limit);
        $photo_upload_error .= $photo_upload->photo_upload_error;

        if (empty($photo_upload_error)) {

            // salvestame pikslikogumi faili, 600x400 max
            if(!isset($_POST["make_thumbnail"])) {
                $photo_upload->resize_image($image_max_w, $image_max_h);
                $photo_upload->add_watermark($watermark);
                $photo_upload->image_date();
                $image_file_name = $photo_upload->image_filename();
                $target_file = "../upload_photos_resized/" .$image_file_name;
                $result = $photo_upload->save_image_to_file($target_file, false);
		        if($result == 1) {
			        $notice = "Vähendatud pilt on salvestatud!";
		        } else {
			        $photo_upload_error = "Vähendatud pilti ei salvestatud!";
		        }
            } else {
                // 100x100 max, thumbnailide jaoks
                $photo_upload->resize_image(100, 100, false);
                $image_file_name = $photo_upload->image_filename();
                $target_file = "../upload_photos_thumbnail/" .$image_file_name;
                $result = $photo_upload->save_image_to_file($target_file, false);
                if($result == 1) {
                    $notice .= "Pisipilt on salvestatud!";
                } else {
                    $photo_upload_error .= "Pisipilti ei salvestatud!";
                }
            }

            $target_file = "../upload_photos_orig/" .$_FILES["file_input"]["name"];
            $result = $photo_upload->save_image_to_file($target_file, true);
            if($result == 1){
                $notice .= " Originaalpilt on salvestatud!";
            } else {
                $photo_upload_error .= " Originaalpilti ei salvestatud!";
            }

            unset($photo_upload);

            if (empty($photo_upload_error)) {
                store_photos($_SESSION['user_id'], $image_file_name, $_FILES['file_input']['name'], $_POST['alt_text'], $_POST['privacy_input']);
                }
    }
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Fotode üleslaadimine</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
    <h1>Fotode üleslaadimine</h1>
    <p>See leht on valminud õppetöö raames!</p>
    <script src="scripts/checkImageSize.js" defer></script>
  </div>
    <hr>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
        <label for="file_input">Vali foto fail!</label>
        <input id="file_input" name="file_input" type="file">
        <br>
        <label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
        <input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ...">
        <br>
        <br>
        <label>Privaatsustase: </label>
        <br>
        <label for="privacy_input_1">Privaatne</label>
        <input id="privacy_input_1" name="privacy_input" type="radio" value="3" checked>
        <br>
        <label for="privacy_input_2">Registreeritud kasutajatele</label>
        <input id="privacy_input_2" name="privacy_input" type="radio" value="2">
        <br>
        <label for="privacy_input_3">Avalik</label>
        <input id="privacy_input_3" name="privacy_input" type="radio" value="1">
        <br>
        <br>
        <label for="make_thumbnail">Muuda pilt pisipildiks? (100x100)</label>
        <input id="make_thumbnail" name="make_thumbnail" type="radio">
        <br>
        <br>
        <input type="submit" id="photo_submit" name="photo_submit" value="Lae pilt üles!">
    </form>
    <br>
    <div class="errormessage"><p id="notice"><?php echo $photo_upload_error; echo $notice ?></p>
    <p><a href="home.php">Avalehele</a></p>
    </div>
</body>
</html>
