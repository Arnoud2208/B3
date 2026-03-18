<?php
    require_once 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];

            $stmt = $conn->prepare("DELETE FROM taken WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

// Na het verwijderen terug naar de homepagina
header("Location: ../doc/home.php"); 
exit;