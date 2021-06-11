<?php

require_once "usesession.php";
require_once "../../../conf.php";

$id = $_REQUEST["photoid"];
$rating = $_REQUEST["rating"];

$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
$conn -> set_charset("utf8");
$stmt = $conn -> prepare("INSERT INTO vr21_photoratings (vr21_photoratings_photoid, vr21_photoratings_userid, vr21_photoratings_rating) VALUES (?,?,?) ");
echo $conn -> error;
$stmt -> bind_param("iii", $id, $_SESSION["user_id"], $rating);
$stmt -> execute();
$stmt -> close();

// loeme keskmise hinde

$stmt = $conn -> prepare("SELECT AVG(vr21_photoratings_rating) AS avgValue FROM vr21_photoratings WHERE vr21_photoratings_photoid = ?");
echo $conn -> error;
$stmt -> bind_param("i", $id);
$stmt -> bind_result($score);
$stmt -> execute();
$stmt -> fetch();
$stmt -> close();
$conn -> close();
echo round($score, 2);