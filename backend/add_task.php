<?php
require_once 'conn.php';

$titel = $_POST['titel'];
$beschrijving = $_POST['beschrijving'];
$status = $_POST['status'];
$afdeling = $_POST['afdeling'];
$deadline = $_post['deadline'];

$query = "INSERT INTO taken (titel, beschrijving, afdeling, status, deadline) 
          VALUES (:titel, :beschrijving, :afdeling, :status, :deadline)";

$stmt = $conn->prepare($query);
$stmt->execute([
    ':titel' => $titel,
    ':beschrijving' => $beschrijving,
    ':status' => $status,
    ':afdeling' => $afdeling,
    ':deadline' => $deadline
]);

header("Location: ../doc/home.php");