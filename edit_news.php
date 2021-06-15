<?php

require_once "usesession.php";
require_once "../../../conf.php";
require_once "fnc_general.php";
require_once "classes/Upload_photo.class.php";

$file_size_limit = 1 * 1024 * 1024;
$news_input_error = null;
$news_title = null;
$news_content = null;
$news_author = null;
$photo_upload_error = null;
$notice = null;
$result = null;
$news_id = (int)$_REQUEST["news_id"];
$news_parameters_from_db = show_news($news_id);
$old_title = $news_parameters_from_db[0];
$old_content = $news_parameters_from_db[1];
$old_author = $news_parameters_from_db[2];
$existing_pic = $news_parameters_from_db[3];
$picture_id_from_db = $news_parameters_from_db[4];
$picture_alttext_from_db = $news_parameters_from_db[5];

function update_news($news_title, $news_content, $news_author, $news_picture_id, $news_id) {
    // loome andmebaasis serveriga ja baasiga ühenduse
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    //määrame suhtluseks kodeeringu
    $conn -> set_charset("utf8");
    //valmistan ette SQL käsu
    $stmt = $conn -> prepare("UPDATE vr21_news SET news_title = ?, news_content = ?, news_author = ?, picture_id = ? WHERE news_id = ?");
    echo $conn -> error;
    $stmt -> bind_param("sssii", $news_title, $news_content, $news_author, $news_picture_id, $news_id);
    $stmt -> execute();
    $stmt -> close();
    $conn -> close();
}

function update_news_photo($picture_filename, $picture_alttext, $uploader_id, $picture_id_from_db) {
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn -> set_charset("utf8");
    $stmt = $conn -> prepare("UPDATE vr21_news_pictures SET picture_filename = ?, picture_alttext = ?, uploader_id = ? WHERE picture_id = ?");
    echo $conn -> error;
    $stmt -> bind_param("ssii", $picture_filename, $picture_alttext, $uploader_id, $picture_id_from_db);
    $stmt -> execute();
    $pic_id = $conn->insert_id;
    $stmt2 = $conn -> prepare("UPDATE vr21_news SET picture_id = ? WHERE news_id = ?");
    $stmt2 -> bind_param("ii", $pic_id, $news_id);
    $stmt2 -> execute();
    $stmt2 -> close();
    $stmt -> close();
    $conn -> close();
}

function show_news($news_id){
    //loome andmebaasis serveriga ja baasiga ühenduse
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    //määrame suhtluse kodeeringu
    $conn -> set_charset("utf8");
    $stmt = $conn -> prepare("SELECT vr21_news.news_id, vr21_news.news_title, vr21_news.news_content, vr21_news.news_author, vr21_news.picture_id, vr21_news.news_added, vr21_news_pictures.picture_id, vr21_news_pictures.picture_filename, vr21_news_pictures.picture_alttext FROM vr21_news LEFT JOIN vr21_news_pictures ON vr21_news.picture_id = vr21_news_pictures.picture_id ORDER BY vr21_news.news_id DESC ");
    echo $conn -> error;
    $stmt -> bind_result($news_id_from_db, $news_title_from_db, $news_content_from_db, $news_author_from_db, $news_picture_id_from_db, $news_added_from_db, $picture_id_from_db, $picture_filename_from_db, $picture_alttext_from_db);
    $stmt -> execute();	
    $news_parameters_from_db = null;	
    while ($stmt -> fetch() ) {
        if ($news_id === $news_id_from_db) {
            $old_title = $news_title_from_db;
            $old_content = $news_content_from_db;
            $old_author = $news_author_from_db;
            $picture = $picture_alttext_from_db;
            $picture .= '<img src="../news_photos/' .$picture_filename_from_db .'" alt="' .$picture_alttext_from_db .'" class="thumb" data-fn="'.$picture_filename_from_db .'" data-id="'.$picture_id_from_db.'">';
            $news_parameters_from_db = [$news_title_from_db, $news_content_from_db, $news_author_from_db, $picture, $picture_id_from_db, $picture_alttext_from_db];
        }
    }
    $stmt -> close();
    $conn -> close();
    return $news_parameters_from_db;
}

if (isset($_POST["news_submit"])) {
    $_SESSION["success"] = 1;
    header('location: show_news.php');
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
                update_news_photo($image_file_name, $_POST["alt_text"], $_SESSION["user_id"], $picture_id_from_db);
            }
        }
    }
    $news_id = $_POST["news_id_input"];
    $photo_id = $_POST["photo_id_input"];
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
        update_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"], $picture_id_from_db, $news_id);
    }
}

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Uudiste muutmine</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="header">
<h1>Uudiste muutmine</h1>
<p>See leht on valminud õppetöö raames!</p>
</div>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
    <label for="news_title_input">Uudise pealkiri</label>
    <br>
    <input hidden type="text" id="photo_id_input" name="photo_id_input" value='<?php echo $picture_id_from_db; ?>'>
    <input hidden type="text" id="news_id_input" name="news_id_input" value='<?php echo $news_id; ?>'>
    <input type="text" id="news_title_input" name="news_title_input" placeholder="Pealkiri" value="<?php echo $old_title; ?>">
    <br>
    <label for="news_content_input">Uudise tekst</label>
    <br>
    <textarea id="news_content_input" name="news_content_input" placeholder="Uudise tekst" rows="6" cols="40"><?php echo $old_content; ?></textarea>
    <br>
    <label for="news_author_input">Uudise lisaja nimi</label>
    <br>
    <input type="text" id="news_author_input" name="news_author_input" placeholder="Nimi" value="<?php echo $old_author; ?>">
    <br>
    <?php echo $existing_pic; ?>
    <br>
    <label for="file_input">Lisa uudise juurde ka pilt!</label>
    <input id="file_input" name="file_input" type="file">
    <br>
    <br>
    <label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
    <input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ..." value="<?php echo $picture_alttext_from_db; ?>">
    <br>
    <br>
    <input type="submit" name="news_submit" value="Salvesta uuendatud uudis">
</form>
<br>
<div class="errormessage"><p><?php echo $news_input_error; echo $notice ?></p>
<br>
<p><a href="home.php">Avalehele</a></p>
</div>
</body>
</html>
