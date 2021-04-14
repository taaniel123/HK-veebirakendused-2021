<?php

    require_once "../../../conf.php";
    $news_input_error = null;
    $news_title = null;
//var_dump($_POST); // on olemas ka $_GET
    if (isset($_POST["news_submit"])) {
        if (empty($_POST["news_title_input"])) {
            $news_input_error = "Uudise pealkiri on puudu! ";
        }
        if (empty($_POST["news_content_input"])) {
            $news_input_error .= "Uudise tekst on puudu!";
        }
        if (empty($news_input_error)) {
            //salvestame andmebaasi
            store_news($_POST["news_title_input"], $_POST["news_content_input"], $_POST["news_author_input"]);
        }
    }

    function store_news($news_title, $news_content, $news_author) {
        // loome andmebaasis serveriga ja baasiga ühenduse
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
        //määrame suhtluseks kodeeringu
        $conn -> set_charset("utf8");
        //valmistan ette SQL käsu
        $stmt = $conn -> prepare("INSERT INTO vr21_news (news_title, news_content, news_author) VALUES (?,?,?) ");
        echo $conn -> error;
        $stmt -> bind_param("sss", $news_title, $news_content, $news_author);
        $stmt -> execute();
        $stmt -> close();
        $conn -> close();
    }


?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>

<?php

$title = "";
$input = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$title = test_input($_POST["news_title_input"]);
$input = test_input($_POST["news_content_input"]);
}

function test_input($data) {
$data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data);
return $data;
}
?>
    <h1>Uudiste lisamine</h1>
    <p>See leht on valminud õppetöö raames!</p>
    <hr>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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
        <input type="submit" name="news_submit" value="Salvesta uudis">
    </form>
    <p><?php echo $news_input_error; ?></p>
</body>
</html>