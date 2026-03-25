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
      transition: transform 0.15s ease, background 0.2s ease;
    }
 
    .task-label {
      display: inline-block;
      margin-top: 6px;
      padding: 4px 10px;
      font-size: 12px;
      font-weight: bold;
      border-radius: 20px;
      text-transform: capitalize;
    }
    .task-actions {
      display: flex;
      gap: 6px;
      margin-top: 8px;
    }
    .task-actions button {
      padding: 4px 8px;
      font-size: 14px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      transition: 0.2s;
    }
    .btn-edit {
      background: #4da3ff;
      color: white;
    }
    .btn-edit:hover {
      background: #1e7fe6;
    }
    .btn-delete {
      background: #ff6b6b;
      color: white;
    }
    .btn-delete:hover {
      background: #e03131;
    }
 
    .task.personeel { border-left: 6px solid #ff6b6b; background: #ffecec; }
    .task.personeel .task-label { background: #ff6b6b22; color: #b30000; border: 1px solid #ff6b6b55; }
 
    .task.horeca { border-left: 6px solid #ffa94d; background: #fff3e6; }
    .task.horeca .task-label { background: #ffa94d22; color: #a65e00; border: 1px solid #ffa94d55; }
 
    .task.techniek { border-left: 6px solid #4dabf7; background: #e7f3ff; }
    .task.techniek .task-label { background: #4dabf722; color: #0b4f91; border: 1px solid #4dabf755; }
 
    .task.Groen { border-left: 6px solid #51cf66; background: #e9ffe9; }
    .task.Groen .task-label { background: #51cf6622; color: #1b7d2d; border: 1px solid #51cf6655; }
 
    .task.Inkoop { border-left: 6px solid #845ef7; background: #f3e9ff; }
    .task.Inkoop .task-label { background: #845ef722; color: #3d1fa3; border: 1px solid #845ef755; }
 
    .task.Klantenservice { border-left: 6px solid #f06595; background: #ffe6f0; }
    .task.Klantenservice .task-label { background: #f0659522; color: #a30047; border: 1px solid #f0659555; }
 
    .sortable-chosen { transform: rotate(2deg) scale(1.05); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .sortable-ghost { opacity: 0.4; }
    .sortable-drag { background: #d0ebff !important; }
 
    /* Container van de filter */
      .filter-box {
          width: 260px;
          margin: 20px auto;
          text-align: center;
      }
 
      #filter {
        width: 220px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        background: white;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        cursor: pointer;
 
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
 
        appearance: none; /* verwijdert standaard styling */
}
 
      /* Hover effect */
      #filter:hover {
          border-color: #4dabf7;
          box-shadow: 0 4px 10px rgba(77, 171, 247, 0.2);
      }
 
      /* Focus (wanneer je klikt) */
      #filter:focus {
          outline: none;
          border-color: #4dabf7;
          box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.2);
      }
 
      /* Custom pijltje */
      .filter-box {
          position: relative;
          display: inline-block;
      }
 
      .filter-box::after {
          content: "▼";
          font-size: 12px;
          color: #666;
 
          position: absolute;
          right: 30px;
          top: 38px;
 
          pointer-events: none;
      }
 
      /* Keyframes */
      @keyframes expandDropdown {
          from { max-height: 38px; opacity: 0.7; }
          to { max-height: 200px; opacity: 1; }
      }
 
      @keyframes collapseDropdown {
          from { max-height: 200px; opacity: 1; }
          to { max-height: 38px; opacity: 0.7; }
      }
  </style>
</head>
 
<body>
 
<header>
  <div class="logo">
      <img src="../img/logo.png" alt="logo">
  </div>
 
  <div class="filter-box">
    <label for="filter"><strong>Filter op afdeling</strong></label><br>
    <select id="filter" onchange="filterTasks()">
        <option value="all">Alle afdelingen</option>
        <option value="personeel">Personeel</option>
        <option value="horeca">Horeca</option>
        <option value="techniek">Techniek</option>
        <option value="Groen">Groen</option>
        <option value="Inkoop">Inkoop</option>
        <option value="Klantenservice">Klantenservice</option>
    </select>
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
                echo "<div class='task-actions'>";
                echo "
                <form method='GET' action='edit_task.php'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-edit'>✏️</button>
                </form>";
                echo "
                <form method='POST' action='../backend/delete_task.php'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-delete' onclick=\"return confirm('Taak verwijderen?');\">
                        🗑️
                    </button>
                </form>";
                echo "</div>";
                echo "<small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
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
                echo "<div class='task-actions'>";
                echo "
                <form method='GET' action='edit_task.php'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-edit'>✏️</button>
                </form>";
                echo "
                <form method='POST' action='../backend/delete_task.php'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-delete' onclick=\"return confirm('Taak verwijderen?');\">
                        🗑️
                    </button>
                </form>";
                echo "</div>";
                echo "<small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
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
                echo "<div class='task-actions'>";
                echo "
                <form method='GET' action='edit_task.php'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-edit'>✏️</button>
                </form>";
                echo "
                <form method='POST' action='../backend/delete_task.php'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-delete' onclick=\"return confirm('Taak verwijderen?');\">
                        🗑️
                    </button>
                </form>";
                echo "</div>";
                echo "<small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
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
 
  function filterTasks() {
      const filter = document.getElementById("filter").value;
      const tasks = document.querySelectorAll(".task");
 
      tasks.forEach(task => {
          if (filter === "all") {
              task.style.display = "block";
          } else {
              if (task.classList.contains(filter)) {
                  task.style.display = "block";
              } else {
                  task.style.display = "none";
              }
          }
      });
  }
</script>
 
<script>
const filter = document.getElementById("filter");
 
filter.addEventListener("click", () => {
    if (!filter.classList.contains("open")) {
        filter.classList.remove("close");
        filter.classList.add("open");
    } else {
        filter.classList.remove("open");
        filter.classList.add("close");
    }
});
</script>
 
</body>
</html>
