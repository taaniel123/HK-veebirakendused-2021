<?php
    $myname ="Taaniel Levin";
    $currenttime = date("d.m.Y H:i:s");
    $timehtml = "\n <p>Lehe avamise hetkel oli aeg: " .$currenttime ."</p> \n";
    $semesterbegin = new DateTime("2021-1-25");
    $semesterend = new DateTime("2021-6-30");
    $semesterduration = $semesterbegin->diff($semesterend);
    $semesterdurationdays = $semesterduration->format("%r%a");
    $semesterdurhtml = "\n <p>2021 kevadsemestri kestus on " .$semesterdurationdays ." päeva.</p> \n";

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Veebirakendused ja nende loomine 2021</title>
</head>
<body>
    <h1>
    <?php
        echo $myname;
    ?>
    </h1>
    <p>See leht on valminud õppetöö raames!</p>
    <?php
        echo $timehtml;
        echo $semesterdurhtml;
    ?>
</body>
</html>