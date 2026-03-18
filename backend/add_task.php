<?php
require_once 'conn.php';

$titel = $_POST['titel'];
$beschrijving = $_POST['beschrijving'];
$status = $_POST['status'];

$sql = "INSERT INTO taken (titel, beschrijving, afdeling, status) 
        VALUES (:titel, :beschrijving, 'algemeen', :status)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':titel' => $titel,
    ':beschrijving' => $beschrijving,
    ':status' => $status
]);

header("Location: ../doc/home.php");