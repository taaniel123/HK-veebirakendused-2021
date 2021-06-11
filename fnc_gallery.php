<?php

function display_image() {
    $privacy = 2;
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $stmt = $conn->prepare("SELECT vr21_photos.vr21_photos_id, vr21_photos.vr21_photos_filename, vr21_photos.vr21_photos_origname, vr21_photos.vr21_photos_alttext, vr21_users.vr21_users_firstname, vr21_users.vr21_users_lastname FROM vr21_photos JOIN vr21_users ON vr21_photos.vr21_photos_userid = vr21_users.vr21_users_id WHERE vr21_photos.vr21_photos_privacy <= ? AND vr21_photos.vr21_photos_deleted IS NULL GROUP BY vr21_photos.vr21_photos_id");
    echo $conn -> error;
    $stmt -> bind_param("i", $privacy);
    $stmt -> bind_result($photo_id_from_db, $photo_filename_from_db, $photo_origname_from_db, $photo_alttext_from_db, $user_firstname_from_db, $user_lastname_from_db);
    $stmt -> execute();
    $display_image = null;

    while ($stmt -> fetch()) {
        $display_image .= '<div class="pildid">';
        $display_image .= '<img src="../upload_photos_thumbnail/' .$photo_filename_from_db .'" alt="' .$photo_alttext_from_db .'" class="thumb" data-fn="' .$photo_origname_from_db .'" data-id=' .$photo_id_from_db .'">';
		$display_image .= '<p>'.$user_firstname_from_db ." " .$user_lastname_from_db .'</p></div>';
    }
    $stmt -> close();
    $conn -> close();
    return $display_image;
}