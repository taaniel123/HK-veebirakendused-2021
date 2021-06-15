<?php

require_once "usesession.php";
require_once "../../../conf.php";
require_once "fnc_general.php";
require_once "classes/Upload_photo.class.php";

$news_input_error = null;
$news_title = null;
$news_content = null;
$news_author = null;
$file_size_limit = 1 * 1024 * 1024;
$photo_upload_error = null;
$notice = null;

function store_news($news_title, $news_content, $news_author) {
    // loome andmebaasis serveriga ja baasiga ühenduse
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    //määrame suhtluseks kodeeringu
    $conn -> set_charset("utf8");
    //valmistan ette SQL käsu
    $stmt = $conn -> prepare("INSERT INTO vr21_news (news_title, news_content, news_author) VALUES (?,?,?)");
    echo $conn -> error;
    $stmt -> bind_param("sss", $news_title, $news_content, $news_author);
    $stmt -> execute();
    $stmt -> close();
    $conn -> close();
}

function store_news_photo($picture_filename, $picture_alttext, $uploader_id) {
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn -> set_charset("utf8");
    $stmt = $conn -> prepare("INSERT INTO vr21_news_pictures (picture_filename, picture_alttext, uploader_id) VALUES (?,?,?)");
    echo $conn -> error;
    $stmt -> bind_param("ssi", $picture_filename, $picture_alttext, $uploader_id);
    $stmt -> execute();
    $pic_id = $conn->insert_id;
    $stmt2 = $conn -> prepare("INSERT INTO vr21_news (picture_id) VALUES (?)");
    $stmt2 -> bind_param("i", $pic_id);
    $stmt2 -> execute();
    $stmt2 -> close();
    $stmt -> close();
    $conn -> close();
}

if (isset($_POST["news_submit"])) {
    if(file_exists($_FILES["file_input"]["tmp_name"]) || is_uploaded_file($_FILES["file_input"]['tmp_name'])) {
        $photo_upload = new Upload_photo($_FILES["file_input"], $file_size_limit);
        $photo_upload_error .= $photo_upload->photo_upload_error;
        if (empty($photo_upload_error)) {
            $image_file_name = $photo_upload->image_filename();
            $target_file = "../news_photos/" .$image_file_name;
            $result = $photo_upload->save_image_to_file($target_file, true);
            if($result == 1) {
                $notice = " Pilt on salvestatud!";
            } else {
                $photo_upload_error = " Pilti ei salvestatud!";
            }
            unset($photo_upload);
            if (empty($photo_upload_error)) {
                store_news_photo($image_file_name, $_POST["alt_text"], $_SESSION["user_id"]);
            }
        }
    }
    if (empty($_POST["news_title_input"])) {
        $news_input_error = "Uudise pealkiri on puudu! ";
    } else {
        $news_title = test_input($_POST["news_title_input"]);
    }
    if (empty($_POST["news_content_input"])) {
        $news_input_error .= "Uudise tekst on puudu!";
    } else {
        $news_content = test_input($_POST["news_content_input"]);
    }
    if (!empty($_POST["news_author_input"])){
        $news_author = test_input($_POST["news_author_input"]);
    }
    if (empty($news_input_error)) {
        $news_input_error .= "Uudis edukalt lisatud!";
        //salvestame andmebaasi
        store_news($news_title, $news_content, $news_author);
    }
}

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Uudiste lisamine</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="header">
<h1>Uudiste lisamine</h1>
<p>See leht on valminud õppetöö raames!</p>
</div>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
    <label for="news_title_input">Uudise pealkiri</label>
    <br>
    <input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value="<?php echo isset($_POST["news_title_input"]) ? $_POST["news_title_input"] : "" ?>">
    <br>
    <label for="news_content_input">Uudise tekst</label>
    <br>
    <textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo isset($_POST["news_content_input"]) ? htmlspecialchars($_POST["news_content_input"]) : ""; ?></textarea>
    <br>
    <label for="news_author_input">Uudise lisaja nimi</label>
    <br>
    <input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi">
    <br>
    <br>
    <label for="file_input">Lisa uudise juurde ka pilt!</label>
    <input id="file_input" name="file_input" type="file">
    <br>
    <br>
    <label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
    <input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ...">
    <br>
    <br>
    <input type="submit" name="news_submit" value="Salvesta uudis">
</form>
<br>
<div class="errormessage"><p><?php echo $news_input_error; echo $notice ?></p>
<br>
<p><a href="home.php">Avalehele</a></p>
</div>
</body>
</html>
