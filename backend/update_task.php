<?php
require_once 'conn.php';

$id = $_POST['id'];
$titel = $_POST['titel'];
$beschrijving = $_POST['beschrijving'];
$afdeling = $_POST['afdeling'];
$status = $_POST['status'];

$stmt = $conn->prepare("
    UPDATE taken 
    SET titel = ?, beschrijving = ?, afdeling = ?, status = ?
    WHERE id = ?
");

$stmt->execute([$titel, $beschrijving, $afdeling, $status, $id]);

header("Location: ../doc/home.php");