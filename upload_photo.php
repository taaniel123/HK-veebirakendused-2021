<?php

    require_once "usesession.php";
    require_once "../../../conf.php";

    error_reporting(0);
    $image_file_type = null;
    $photo_upload_error = null;
    $image_file_name = null;
    $file_name_prefix = "vr_";
    $file_size_limit = 1 * 1024 * 1024;
    $image_max_w = 600;
    $image_max_h = 400;

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
       // kontrollime, kas üldse on pilt
       $check = getimagesize($_FILES["file_input"]["tmp_name"]);
       if ($check !== false) {
           // kontrollime, kas aktsepteeritud failivorming ja fikseerime laiendi
           if ($check["mime"] == "image/jpeg") {
                $image_file_type = "jpg";
           } elseif ($check["mime"] == "image/png") {
               $image_file_type = "png";
           }  else {
               $photo_upload_error = "Pole sobiv formaat! Ainult jpg ja png on lubatud!";
           }
       } else {
           $photo_upload_error = "Te ei ole valinud ühtegi faili või tegemist pole pildifailiga!";
       }
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
                if (empty($photo_upload_error)) {
                    store_photos($_SESSION['user_id'], $image_file_name, $_FILES['file_input']['name'], $_POST['alt_text'], $_POST['privacy_input']);
                  }
                // funktsioon pildi suuruse muutmiseks
               function resize_image($temp_image, $image_w, $image_h, $crop=FALSE) {
                    list ($width, $height) = getimagesize($_FILES["file_input"]["tmp_name"]);
                    $pic_measures = $width / $height;
                    if ($crop) {
                        if ($width > $height) {
                            $width = ceil($width-($width*abs($pic_measures-$image_w/$image_h)));
                        } else {
                            $height = ceil($height-($height*abs($pic_measures-$image_w/$image_h)));
                        }
                        $new_width = $image_w;
                        $new_height = $image_h;
                    } else {
                        if ($image_w/$image_h > $pic_measures) {
                            $new_width = $image_h*$pic_measures;
                            $new_height = $image_h;
                        } else {
                            $new_height = $image_w/$pic_measures;
                            $new_width = $image_w;
                        }
                    }
                    $new_temp_image = imagecreatetruecolor($new_width, $new_height);
                    imagecopyresampled($new_temp_image, $temp_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                    return $new_temp_image;
               }

            // salvestame pikslikogumi faili, 600x400 max
            if(!isset($_POST["make_thumbnail"])) {
              $new_temp_image = resize_image($temp_image, 600, 400, false);
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
            } else {
                // 100x100 max, thumbnailide jaoks
                $new_temp_image = resize_image($temp_image, 100, 100, true);
                $target_file = "../upload_photos_thumbnail/" .$image_file_name;
                if ($image_file_type == "jpg") {
                    if (imagejpeg($new_temp_image, $target_file, 90)) {
                        $photo_upload_error = "Vähendatud pilt on salvestatud!";
                    } else {
                        $photo_upload_error = "Vähendatud pilti ei salvestatud!";
                    }
                }
                $target_file = "../upload_photos_thumbnail/" .$image_file_name;
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
        <input type="submit" name="photo_submit" value="Lae pilt üles!">
    </form>
    <br>
    <div class="errormessage"><p><?php echo $photo_upload_error; ?></p></div>
</body>
</html>
