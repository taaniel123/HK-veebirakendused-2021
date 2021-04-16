<?php

	//session_start();
	require("classes/SessionManager.class.php");
	SessionManager::sessionStart("vr", 0, "/~taaniel.levin/", "tigu.hk.tlu.ee");
	
	require_once "../../../conf.php";
	//require_once "fnc_general.php";
	require_once "fnc_user.php";

    $myname ="Taaniel Levin";
    $weekdaydet = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
    $currenttime = date("d.m.Y H:i:s");
    $timehtml = "\n <p>Lehe avamise hetkel oli aeg: " .$weekdaydet[date("N") -1] .", " .$currenttime ."</p> \n";
    $semesterbegin = new DateTime("2021-1-25");
    $semesterend = new DateTime("2021-6-30");
    $semesterduration = $semesterbegin->diff($semesterend);
    $semesterdurationdays = $semesterduration->format("%r%a");
    $semesterdurhtml = "\n <p>2021 kevadsemestri kestus on " .$semesterdurationdays ." päeva.</p> \n";
    $today = new DateTime("now");
    $fromsemesterbegin = $semesterbegin->diff($today);
    $fromsemesterbegindays = $fromsemesterbegin->format("%r%a");

    if ($fromsemesterbegindays>0){
        if($fromsemesterbegindays <= $semesterdurationdays) {
            $semesterprogress = "\n" .'<p>Semester edeneb: <meter min="0" max="' .$semesterdurationdays .'" value="' .$fromsemesterbegindays .'"></meter></p>' ."\n";
        }   else {
            $semesterprogress = "\n <p>Semester on lõppenud.</p> \n";
    }
    } elseif($fromsemesterbegindays===0) {
        $semesterprogress = "\n <p>Semester algab täna.</p> \n";
    } else {
        $semesterprogress = "\n <p>Semesteri alguseni jäänud päevi: ". (abs($fromsemesterbegindays) +1) .".</p> \n";
    }

   //loeme piltide kataloogi sisu
    $picsdir = "../pics/";
    $allfiles = array_slice(scandir($picsdir), 2);
    //var_dump($allfiles);
    $allowedphototypes = ["image/jpeg", "image/png"];
    $photocountlimit = 3;
    $picfiles = [];
    $photoshow = [];

    foreach($allfiles as $file) {
        $fileinfo = getimagesize($picsdir .$file);
        if(isset($fileinfo["mime"])) {
            if(in_array($fileinfo["mime"], $allowedphototypes)){
                array_push($picfiles, $file);
            }
        }
    }

    $photocount = count($picfiles);
    if($photocount < 3){
        $photocountlimit = $photocount;
    }
    for ($i = 0; $i < $photocountlimit; $i ++){
        do {
            $photonum = mt_rand(0, $photocount-1);
        } while (in_array($photonum, $photoshow));
        array_push($photoshow, $photonum);
    }

   $randomphotoshtml = "";
   foreach($photoshow as $photoindex){
       $randomphotoshtml .= "\n \t" . '<img src="' .$picsdir .$picfiles[$photoindex] .'" alt=vaade Haapsalus">';
   }

   //sisselogimine
   $notice = null;
   $email = null;
   $email_error = null;
   $password_error = null;
   if (isset($_POST['login_submit'])) {
       //kontrollime, kas email ja pw on olemas
       $notice = sign_in($_POST['email_input'], $_POST['password_input']);
   }


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
    <h2>Logi sisse</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<label>E-mail (kasutajatunnus):</label><br>
		<input type="email" name="email_input" value="<?php echo $email; ?>"><span><?php echo $email_error; ?></span><br>
		<label>Salasõna:</label><br>
		<input name="password_input" type="password"><span><?php echo $password_error; ?></span><br>
		<input name="login_submit" type="submit" value="Logi sisse!"><span><?php echo $notice; ?></span>
	</form>
    <p>Loo endale <a href="add_user.php">kasutajakonto!</a></p>
    <?php
        echo $timehtml;
        echo $semesterdurhtml;
        echo $semesterprogress;
        echo $randomphotoshtml;
    ?>
</body>
</html>