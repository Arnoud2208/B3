<?php
session_start();
?>

<!doctype html>
<html lang="nl">

<head>
    <title>Inloggen</title>
    <link rel="stylesheet" href="../public_html/css/style.css">
</head>

<body>

    <div class="container">

        <form class="login-form" action="../backend/login.php" method="POST">

            <h2>Inloggen</h2>

            <?php
                if(isset($_SESSION['error'])){
                    echo "<p class='error'>" . $_SESSION['error'] . "</p>";
                    unset($_SESSION['error']);
                }
            ?>

            <div class="form-group">
                <label>Gebruikersnaam</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Wachtwoord</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Inloggen</button>

        </form>

    </div>

</body>
</html>