<?php

$username = $_POST['username'];
$password = $_POST['password'];

if($username == "admin" && $password == "1234"){
    echo "Succesvol ingelogd!";
} else {
    echo "Gebruikersnaam of wachtwoord is fout.";
}

?>