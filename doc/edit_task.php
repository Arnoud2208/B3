<?php
require_once '../backend/conn.php';

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM taken WHERE id = ?");
$stmt->execute([$id]);
$taak = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bewerk taak</title>

    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .edit-container {
            background: white;
            width: 380px;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #b6e2c8;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
        }

        h2 {
            margin-top: 0;
            color: #2a7f62;
            text-align: center;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 2px solid #b6e2c8;
            border-radius: 6px;
            margin-bottom: 15px;
            outline: none;
            transition: 0.2s;
            font-size: 15px;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: #4da3ff;
        }

        textarea {
            height: 90px;
            resize: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #4da3ff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        button:hover {
            background: #1e7fe6;
        }

        .back-btn {
            margin-top: 10px;
            background: #2a7f62;
        }

        .back-btn:hover {
            background: #1f5c4b;
        }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Taak bewerken</h2>

    <form method="POST" action="../backend/update_task.php">

        <input type="hidden" name="id" value="<?= $taak['id'] ?>">

        <input type="text" name="titel"
               value="<?= htmlspecialchars($taak['titel']) ?>" required>

        <textarea name="beschrijving"><?= htmlspecialchars($taak['beschrijving']) ?></textarea>

        <select name="afdeling">
            <option value="personeel" <?= $taak['afdeling']=='personeel'?'selected':'' ?>>Personeel</option>
            <option value="horeca" <?= $taak['afdeling']=='horeca'?'selected':'' ?>>Horeca</option>
            <option value="techniek" <?= $taak['afdeling']=='techniek'?'selected':'' ?>>Techniek</option>
            <option value="Groen" <?= $taak['afdeling']=='Groen'?'selected':'' ?>>Groen</option>
            <option value="Inkoop" <?= $taak['afdeling']=='Inkoop'?'selected':'' ?>>Inkoop</option>
            <option value="Klantenservice" <?= $taak['afdeling']=='Klantenservice'?'selected':'' ?>>Klantenservice</option>
        </select>

        <select name="status">
            <option value="todo" <?= $taak['status']=='todo'?'selected':'' ?>>To Do</option>
            <option value="doing" <?= $taak['status']=='doing'?'selected':'' ?>>Doing</option>
            <option value="done" <?= $taak['status']=='done'?'selected':'' ?>>Done</option>
        </select>

        <button type="submit">Opslaan</button>
    </form>

    <form action="home.php">
        <button class="back-btn">Terug</button>
    </form>
</div>

</body>
</html>