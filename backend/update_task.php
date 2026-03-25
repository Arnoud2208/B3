<?php
require_once 'conn.php';

$id = $_POST['id'];
$titel = $_POST['titel'];
$beschrijving = $_POST['beschrijving'];
$afdeling = $_POST['afdeling'];
$status = $_POST['status'];
$deadline = $_POST['deadline'];

$stmt = $conn->prepare("
    UPDATE taken 
    SET titel = ?, beschrijving = ?, afdeling = ?, status = ?,deadline = ?
    WHERE id = ?
");

$stmt->execute([$titel, $beschrijving, $afdeling, $status, $deadline, $id]);

header("Location: ../doc/home.php");