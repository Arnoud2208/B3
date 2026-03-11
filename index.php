<!doctype html>
<html lang="nl">

<head>
    <title>Inloggen</title>

    <link rel="stylesheet" href="login.css">
</head>

<body>
    
<div class="container">

    <form class="login-form" action="login.php" method="POST">

        <h2>Inloggen</h2>

        <div class="form-group">
            <label for="username">Gebruikersnaam</label>
            <input type="text" name="username" id="username" required>
        </div>

        <div class="form-group">
            <label for="password">Wachtwoord</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">Inloggen</button>

    </form>

</div>

</body>

</html>