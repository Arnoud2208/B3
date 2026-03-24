<?php
require_once 'conn.php';

$id = $_POST['id'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE taken SET status = :status WHERE id = :id");
$stmt->execute([
    ':status' => $status,
    ':id' => $id
]);

echo "OK";