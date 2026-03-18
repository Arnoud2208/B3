<?php
require_once 'conn.php';

$titel = $_POST['titel'];
$beschrijving = $_POST['beschrijving'];
$status = $_POST['status'];
$afdeling = $_POST['status']

$query = "INSERT INTO taken (titel, beschrijving, afdeling, status) 
        VALUES (:titel, :beschrijving, 'algemeen', :status)";

$stmt = $conn->prepare($query);
$stmt->execute([
    ':titel' => $titel,
    ':beschrijving' => $beschrijving,
    ':status' => $status,
    ':afdeling' => $afdeling

]);

header("Location: ../doc/home.php");