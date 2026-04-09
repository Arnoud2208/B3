<?php
session_start();
require_once 'conn.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: ../doc/index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$titel = $_POST['titel'];
$beschrijving = $_POST['beschrijving'];
$status = $_POST['status'];
$afdeling = $_POST['afdeling'];
$deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

$query = "INSERT INTO taken (titel, beschrijving, afdeling, status, deadline, user_id) 
          VALUES (:titel, :beschrijving, :afdeling, :status, :deadline, :user_id)";

$stmt = $conn->prepare($query);
$stmt->execute([
    ':titel' => $titel,
    ':beschrijving' => $beschrijving,
    ':status' => $status,
    ':afdeling' => $afdeling,
    ':deadline' => $deadline,
    ':user_id' => $user_id
]);

header("Location: ../doc/home.php");
