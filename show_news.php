<?php

    require_once "../../../conf.php";
    error_reporting(E_ALL ^ E_NOTICE);
    function read_news() {
        // loome andmebaasis serveriga ja baasiga ühenduse
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluseks kodeeringu
        $conn -> set_charset("utf8");
        //valmistan ette SQL käsu
        $stmt = $conn -> prepare("SELECT news_title, news_content, news_author, news_added FROM vr21_news ORDER BY news_id DESC LIMIT ?");
        echo $conn -> error;
        $news_limit = $_POST["news_output_num"];
        $stmt -> bind_result($news_title_from_db, $news_content_from_db, $news_author_from_db, $news_added_from_db);
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
                $raw_news_html .= "Tundmatu reporter";
            }
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
    <title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
    <h1>Uudiste lugemine</h1>
    <p>See leht on valminud õppetöö raames!</p>
    <form name="form" action="" method="post">
    <label for="num">Mitut uudist soovite näha?</label><br>
    <input type="number" min="1" max="10" value="3" name="news_output_num">
    <input type="submit" value="Vali">
    </form>
    <hr>
    <?php echo $news_html; ?>
</body>
</html>