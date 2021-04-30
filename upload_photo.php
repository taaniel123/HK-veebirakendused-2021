<?php

    require_once "usesession.php";
    require_once "../../../conf.php";

    $image_file_type = null;
    $photo_upload_error = null;
    $image_file_name = null;
    $file_name_prefix = "vr_";
    $file_size_limit = 1 * 1024 * 1024;
    $image_max_w = 600;
    $image_max_h = 400;

    if (isset($_POST["photo_submit"])) {
       // kontrollime, kas üldse on pilt
       $check = getimagesize($_FILES["file_input"]["tmp_name"]);
       if ($check !== false) {
           // kontrollime, kas aktsepteeritud failivorming ja fikseerime laiendi
           if ($check["mime"] == "image/jpeg") {
                $image_file_type = "jpg";
           } elseif ($check["mime"] == "image/png") {
               $image_file_type = "png";
           } else {
               $photo_upload_error = "Pole sobiv formaat! Ainult jpg ja png on lubatud!";
           }
       } else {
           $photo_upload_error = "Tegemist pole pildifailiga!";
       }

       if (empty($photo_upload_error)) {
           // ega pole liiga suur fail
           if ($_FILES["file_input"]["size"] > $file_size_limit) {
               $photo_upload_error = "Valitud fail on liiga suur! Maksimaalne suurus on 1MB!";
           }

           if (empty($photo_upload_error)) {
                // loome oma failinime
                $timestamp = microtime(1) * 100000;
                $image_file_name = $file_name_prefix .$timestamp ."." .$image_file_type;
                // suuruse muutmine
                // loome pikslikogumi ehk image objekti
                $temp_image = null;
                if ($image_file_type == "jpg") {
                    $temp_image = imagecreatefromjpeg($_FILES["file_input"]["tmp_name"]);
                }
                if ($image_file_type == "png") {
                    $temp_image = imagecreatefrompng($_FILES["file_input"]["tmp_name"]);
                }

                $image_w = imagesx($temp_image);
                $image_h = imagesy($temp_image);

                // kuvasuhte säilitamiseks arvutame suuruse muutuse kordaja lähtudes kõrgusest või laiusest
                if ($image_w / $image_max_w > $image_h / $image_max_h) {
                    $image_size_ratio = $image_w / $image_max_w;
                } else {
                    $image_size_ratio = $image_h / $image_max_h;
                }

                $image_new_w = round($image_w / $image_size_ratio);
                $image_new_h = round($image_h / $image_size_ratio);

                // vähendamiseks loome uue image objekti, kuhu kopeerime vähendatud kujutise
                $new_temp_image = imagecreatetruecolor($image_new_w, $image_new_h);
                imagecopyresampled($new_temp_image, $temp_image, 0, 0, 0, 0, $image_new_w, $image_new_h, $image_w, $image_h);

                // salvestame pikslikogumi faili
                $target_file = "../upload_photos_resized/" .$image_file_name;
                if ($image_file_type == "jpg") {
                    if (imagejpeg($new_temp_image, $target_file, 90)) {
                        $photo_upload_error = "Vähendatud pilt on salvestatud!";
                    } else {
                        $photo_upload_error = "Vähendatud pilti ei salvestatud!";
                    }
                }
                $target_file = "../upload_photos_resized/" .$image_file_name;
                if ($image_file_type == "png") {
                    if (imagepng($new_temp_image, $target_file, 6)) {
                        $photo_upload_error = "Vähendatud pilt on salvestatud!";
                    } else {
                        $photo_upload_error = "Vähendatud pilti ei salvestatud!";
                    }
                }

                $target_file = "../upload_photos_orig/" .$image_file_name;
                if (move_uploaded_file($_FILES["file_input"]["tmp_name"], $target_file)) {
                    $photo_upload_error .= " Foto üleslaadimine õnnestus!";
                } else {
                    $photo_upload_error .= " Foto üleslaadimine ebaõnnestus!";
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Veebirakendused ja nende loomine 2021</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header">
    <h1>Fotode üleslaadimine</h1>
    <p>See leht on valminud õppetöö raames!</p>
  </div>
    <hr>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
        <label for="file_input">Vali foto fail!</label>
        <input id="file_input" name="file_input" type="file">
        <br>
        <label for="alt_input">Alternatiivtekst ehk pildi selgitus</label>
        <input id="alt_text" name="alt_text" type="text" placeholder="Pildil on ...">
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
        <input type="submit" name="photo_submit" value="Lae pilt üles!">
    </form>
    <br>
    <div class="errormessage"><p><?php echo $photo_upload_error; ?></p></div>
</body>
</html>
