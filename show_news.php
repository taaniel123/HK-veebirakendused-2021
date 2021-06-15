<?php

    require_once "usesession.php";
    require_once "../../../conf.php";

    function read_news() {
        // loome andmebaasis serveriga ja baasiga ühenduse
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluseks kodeeringu
        $conn -> set_charset("utf8");
        //valmistan ette SQL käsu
        $stmt = $conn -> prepare("SELECT vr21_news.news_id, vr21_news.news_title, vr21_news.news_content, vr21_news.news_author, vr21_news.news_added, vr21_news.picture_id, vr21_news_pictures.picture_id, vr21_news_pictures.picture_filename, vr21_news_pictures.picture_alttext FROM vr21_news LEFT JOIN vr21_news_pictures ON vr21_news.picture_id = vr21_news_pictures.picture_id ORDER BY vr21_news.news_id DESC LIMIT ?");
        echo $conn -> error;
        if (isset($_POST["news_output_num"])) {
            $news_limit = $_POST["news_output_num"];
        } 
        $stmt -> bind_result($news_id_from_db, $news_title_from_db, $news_content_from_db, $news_author_from_db, $news_added_from_db, $news_pic_id_from_db, $pic_id_from_db, $pic_filename_from_db, $pic_alttext_from_db);
        $stmt -> bind_param("i", $news_limit);
        $stmt -> execute();
        $raw_news_html = null;
        while ($stmt -> fetch()) {
            $raw_news_html .= "\n <h2>" .$news_title_from_db ."</h2>";
            $news_date = new DateTime($news_added_from_db);
            $raw_news_html .= "\n <p>Lisatud: " .$news_date->format("d.m.Y H:i:s");
            $raw_news_html .= "\n <p>" .nl2br($news_content_from_db) ."</p>";
            $raw_news_html .= "\n <p>Edastas: ";
            if (!empty($news_author_from_db)) {
                $raw_news_html .= $news_author_from_db;
            } else {
                $raw_news_html .= "Tundmatu reporter <br>";
            }
            $raw_news_html .= '<img src="../news_photos/' .$pic_filename_from_db .'" alt="' .$pic_alttext_from_db .'" class="thumb" data-fn="'.$pic_filename_from_db .'" data-id="'.$pic_id_from_db.'">';
            $raw_news_html .= '<br><a href="edit_news.php?news_id='.$news_id_from_db.'">Muuda uudist</a>';
            $raw_news_html .= "</p>";
        }
        $stmt -> close();
        $conn -> close();
        return $raw_news_html;
    }

    $news_html = read_news();


?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Uudiste lugemine</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
    <h1>Uudiste lugemine</h1>
    <p>See leht on valminud õppetöö raames!</p>
  </div>
    <form name="form" action="" method="post">
    <label for="num">Mitut uudist soovite näha?</label><br>
    <input type="number" min="1" max="10" value="3" name="news_output_num">
    <input type="submit" value="Vali">
    </form>
    <hr>
    <br>
    <?php echo $news_html; ?>
    <div class="errormessage">
    <?php if(isset($_SESSION['success'])){
            echo "Uudis edukalt uuendatud!";
            unset($_SESSION['success']);
    }?>
    </div>
    <br>
    <br>
    <div class="errormessage"><p><a href="home.php">Avalehele</a></p></div>
</body>
</html>
