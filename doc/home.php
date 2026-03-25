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

    /* Basis taak */
    .task {
      background: white;
      padding: 10px;
      border-radius: 8px;
      color: black;
      transition: transform 0.15s ease, background 0.2s ease;
    }

    /* Mooie badges */
    .task-label {
      display: inline-block;
      margin-top: 6px;
      padding: 4px 10px;
      font-size: 12px;
      font-weight: bold;
      border-radius: 20px;
      text-transform: capitalize;
    }

    /* Personeel */
    .task.personeel {
        border-left: 6px solid #ff6b6b;
        background: #ffecec;
    }
    .task.personeel .task-label {
        background: #ff6b6b22;
        color: #b30000;
        border: 1px solid #ff6b6b55;
    }

    /* Horeca */
    .task.horeca {
        border-left: 6px solid #ffa94d;
        background: #fff3e6;
    }
    .task.horeca .task-label {
        background: #ffa94d22;
        color: #a65e00;
        border: 1px solid #ffa94d55;
    }

    /* Techniek */
    .task.techniek {
        border-left: 6px solid #4dabf7;
        background: #e7f3ff;
    }
    .task.techniek .task-label {
        background: #4dabf722;
        color: #0b4f91;
        border: 1px solid #4dabf755;
    }

    /* Groen */
    .task.Groen {
        border-left: 6px solid #51cf66;
        background: #e9ffe9;
    }
    .task.Groen .task-label {
        background: #51cf6622;
        color: #1b7d2d;
        border: 1px solid #51cf6655;
    }

    /* Inkoop */
    .task.Inkoop {
        border-left: 6px solid #845ef7;
        background: #f3e9ff;
    }
    .task.Inkoop .task-label {
        background: #845ef722;
        color: #3d1fa3;
        border: 1px solid #845ef755;
    }

    /* Klantenservice */
    .task.Klantenservice {
        border-left: 6px solid #f06595;
        background: #ffe6f0;
    }
    .task.Klantenservice .task-label {
        background: #f0659522;
        color: #a30047;
        border: 1px solid #f0659555;
    }

    /* Drag & drop animaties */
    .sortable-chosen {
      transform: rotate(2deg) scale(1.05);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .sortable-ghost {
      opacity: 0.4;
    }

    .sortable-drag {
      background: #d0ebff !important;
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

        <div class="tasks" id="todo">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='todo'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' data-id='" . $row['id'] . "'>";

                echo "<strong>" . htmlspecialchars($row['titel']) . "</strong>";
                echo "<div class='task-label'>" . htmlspecialchars($row['afdeling']) . "</div>";

                echo "
                <form method='GET' action='edit_task.php' style='display:inline; margin-left:10px;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit'>✏️</button>
                </form>
                ";

                echo "
                    <form method='POST' action='../backend/delete_task.php' 
                          style='display:inline; margin-left:10px;'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">
                            Verwijderen
                        </button>
                    </form>
                ";
                echo "<br><small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
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

        <div class="tasks" id="doing">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='doing'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' data-id='" . $row['id'] . "'>";

                echo "<strong>" . htmlspecialchars($row['titel']) . "</strong>";
                echo "<div class='task-label'>" . htmlspecialchars($row['afdeling']) . "</div>";

                echo "
                <form method='GET' action='edit_task.php' style='display:inline; margin-left:10px;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit'>✏️</button>
                </form>
                ";

                echo "
                    <form method='POST' action='../backend/delete_task.php' 
                          style='display:inline; margin-left:10px;'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">
                            Verwijderen
                        </button>
                    </form>
                ";
                echo "<br><small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
                echo "</div>";

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

        <div class="tasks" id="done">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='done'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' data-id='" . $row['id'] . "'>";

                echo "<strong>" . htmlspecialchars($row['titel']) . "</strong>";
                echo "<div class='task-label'>" . htmlspecialchars($row['afdeling']) . "</div>";

                echo "
                <form method='GET' action='edit_task.php' style='display:inline; margin-left:10px;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit'>✏️</button>
                </form>
                ";

                echo "
                    <form method='POST' action='../backend/delete_task.php' 
                          style='display:inline; margin-left:10px;'>
                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                        <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">
                            Verwijderen
                        </button>
                    </form>
                ";
                echo "<br><small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
                echo "</div>";

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
        <option value="Inkoop">Inkoop</option>
        <option value="Klantenservice">Klantenservice</option>
      </select>

      <select name="status">
        <option value="todo">To Do</option>
        <option value="doing">Doing</option>
        <option value="done">Done</option>
      </select><br><br>
      <input  type="date" name ="deadline"><br><br>

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

<!-- SORTABLEJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
  const columns = ["todo", "doing", "done"];

  columns.forEach(col => {
    new Sortable(document.getElementById(col), {
      group: "shared",
      animation: 150,
      ghostClass: "sortable-ghost",
      chosenClass: "sortable-chosen",
      dragClass: "sortable-drag",
      onEnd: function (evt) {
        const taskId = evt.item.dataset.id;
        const newStatus = evt.to.id;

        fetch("../backend/update_status.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${taskId}&status=${newStatus}`
        });
      }
    });
  });
</script>

</body>
</html>