<?php
require_once '../backend/conn.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>takenlijst</title>
  <link rel="stylesheet" href="../public_html/css/home.css" type="text/css">

  <style>
    .popup {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
    }

    .popup-content {
      background: white;
      padding: 20px;
      width: 300px;
      margin: 100px auto;
      border-radius: 10px;
    }

    .tasks {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .task {
      background: white;
      padding: 10px;
      border-radius: 8px;
      color: black;
    }
  </style>
</head>

<body>

<header>
  <div class="logo">
      <img src="../img/logo.png" alt="logo">
  </div>
</header>

<main>
  <div class="wrapper">
    <div class="container columns">

      <!-- TO DO -->
      <div class="column">
        <div class="column-title">
          <h3>To Do</h3>
          <button onclick="openPopup()">+</button>
        </div>

        <div class="tasks">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='todo'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<div class='task'>";

                // Titel van de taak
                echo htmlspecialchars($row['titel']);

                // Verwijderknop
                echo "
                    <form method='POST' action='../backend/delete_task.php' 
                          style='display:inline; margin-left:10px;'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">
                            Verwijderen
                        </button>
                    </form>
                ";

                echo "</div>";
            }
          ?>
        </div>
      </div>
      <!-- DOING -->
      <div class="column">
        <div class="column-title">
          <h3>Doing</h3>
          <button onclick="openPopup()">+</button>
        </div>

        <div class="tasks">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='doing'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<div class='task'>";

                // Titel van de taak
                echo htmlspecialchars($row['titel']);

                // Verwijderknop
                echo "
                    <form method='POST' action='../backend/delete_task.php' 
                          style='display:inline; margin-left:10px;'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">
                            Verwijderen
                        </button>
                    </form>
                ";

                echo "</div>";
            }
          ?>
        </div>
      </div>

      <!-- DONE -->
      <div class="column">
        <div class="column-title">
          <h3>Done</h3>
          <button onclick="openPopup()">+</button>
        </div>

        <div class="tasks">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='done'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<div class='task'>";

                // Titel van de taak
                echo htmlspecialchars($row['titel']);
                echo htmlspecialchars($row['afdeling']);

                // Verwijderknop
                echo "
                    <form method='POST' action='../backend/delete_task.php' 
                          style='display:inline; margin-left:10px;'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">
                            Verwijderen
                        </button>
                    </form>
                ";

                echo "</div>";
            }
          ?>
        </div>
      </div>

    </div>
  </div>
</main>

<!-- POPUP -->
<div id="popup" class="popup">
  <div class="popup-content">
    <h2>Nieuwe taak</h2>

    <form method="POST" action="../backend/add_task.php">
      <input type="text" name="titel" placeholder="Titel" required><br><br>

      <textarea name="beschrijving" placeholder="Beschrijving"></textarea><br><br>
      <select name="afdeling">
        <option value="personeel">Personeel</option>
        <option value="horeca">Horeca</option>
        <option value="techniek">Techniek</option>
        <option value="Groen">Groen</option>
        <option value="Inkoop ">Inkoop</option>
        <option value="Klantenservice">Klantenservice</option>
      </select>



      <select name="status">
        <option value="todo">To Do</option>
        <option value="doing">Doing</option>
        <option value="done">Done</option>
      </select><br><br>

      <button type="submit">Opslaan</button>
      <button type="button" onclick="closePopup()">Sluiten</button>
    </form>
  </div>
</div>

<script>
function openPopup() {
    document.getElementById("popup").style.display = "block";
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}
</script>

</body>
</html>