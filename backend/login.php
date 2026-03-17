<?php

session_start();
require_once "database.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){
        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['naam'] = $user['naam'];

            header("Location: ../doc/dashboard.php");
            exit;

        } else {

            $_SESSION['error'] = "Wachtwoord is incorrect.";
            header("Location: ../doc/index.php");
            exit;

        }

    } else {

        $_SESSION['error'] = "Gebruiker bestaat niet.";
        header("Location: ../doc/index.php");
        exit;

    }

}