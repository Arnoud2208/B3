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
</head>
<body>

<h2>Taak bewerken</h2>

<form method="POST" action="../backend/update_task.php">

    <input type="hidden" name="id" value="<?= $taak['id'] ?>">

    <input type="text" name="titel"
           value="<?= htmlspecialchars($taak['titel']) ?>" required><br><br>

    <textarea name="beschrijving"><?= htmlspecialchars($taak['beschrijving']) ?></textarea><br><br>

    <select name="afdeling">
        <option value="personeel" <?= $taak['afdeling']=='personeel'?'selected':'' ?>>Personeel</option>
        <option value="horeca" <?= $taak['afdeling']=='horeca'?'selected':'' ?>>Horeca</option>
        <option value="techniek" <?= $taak['afdeling']=='techniek'?'selected':'' ?>>Techniek</option>
        <option value="Groen" <?= $taak['afdeling']=='Groen'?'selected':'' ?>>Groen</option>
        <option value="Inkoop" <?= $taak['afdeling']=='Inkoop'?'selected':'' ?>>Inkoop</option>
        <option value="Klantenservice" <?= $taak['afdeling']=='Klantenservice'?'selected':'' ?>>Klantenservice</option>
    </select><br><br>

    <select name="status">
        <option value="todo" <?= $taak['status']=='todo'?'selected':'' ?>>To Do</option>
        <option value="doing" <?= $taak['status']=='doing'?'selected':'' ?>>Doing</option>
        <option value="done" <?= $taak['status']=='done'?'selected':'' ?>>Done</option>
    </select><br><br>

    <button type="submit">Opslaan</button>

</form>

</body>
</html>