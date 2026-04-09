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
      z-index: 1000;
    }
 
    .popup-content {
      background: white;
      padding: 20px;
      width: 400px;
      max-width: 90vw;
      margin: 50px auto;
      border-radius: 12px;
      max-height: 85vh;
      overflow-y: auto;
      box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    .task-details-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 3px solid #4dabf7;
    }

    .task-details-title {
      margin: 0;
      color: #333;
      font-size: 22px;
      font-weight: bold;
    }

    .close-popup {
      background: #ff6b6b;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 18px;
      font-weight: bold;
      transition: background 0.2s;
    }

    .close-popup:hover {
      background: #e03131;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid #eee;
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-label {
      font-weight: 600;
      color: #555;
      min-width: 120px;
    }

    .detail-value {
      color: #333;
      font-weight: 500;
    }

    .detail-description {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      border-left: 4px solid #4dabf7;
      margin: 15px 0;
      line-height: 1.6;
      white-space: pre-wrap;
    }

    .no-description {
      color: #999;
      font-style: italic;
      text-align: center;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 8px;
      border-left: 4px solid #ccc;
    }

    .task {
      background: white;
      padding: 10px;
      border-radius: 8px;
      color: black;
      transition: all 0.2s ease;
      position: relative;
      cursor: pointer; /* Wijst aan dat het klikbaar is */
    }

    .task:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    /* Rest van je bestaande styles blijven hetzelfde */
    .tasks {
      display: flex;
      flex-direction: column;
      gap: 10px;
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
      align-items: center;
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

    /* Kleuren per afdeling */
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

    /* Filter styles */
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
      appearance: none;
    }
  </style>
</head>
 
<body>

<!-- Taak details popup -->
<div id="taskDetailsPopup" class="popup">
  <div class="popup-content">
    <div class="task-details-header">
      <h2 class="task-details-title" id="popupTitle">Taak details</h2>
      <button class="close-popup" onclick="closeTaskPopup()">✕</button>
    </div>
    
    <div class="detail-row">
      <span class="detail-label">Afdeling:</span>
      <span class="detail-value" id="popupAfdeling">-</span>
    </div>
    
    <div class="detail-row">
      <span class="detail-label">Status:</span>
      <span class="detail-value" id="popupStatus">-</span>
    </div>
    
    <div class="detail-row">
      <span class="detail-label">Deadline:</span>
      <span class="detail-value" id="popupDeadline">-</span>
    </div>
    
    <div class="detail-row">
      <span class="detail-label">Beschrijving:</span>
      <span class="detail-value">-</span>
    </div>
    <div id="popupDescription" class="detail-description">Laadt...</div>
  </div>
</div>

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
          <button onclick="openAddPopup()">+</button>
        </div>
 
        <div class="tasks" id="todo">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='todo'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' ";
                echo "data-id='" . $row['id'] . "' ";
                echo "data-title='" . htmlspecialchars($row['titel']) . "' ";
                echo "data-afdeling='" . htmlspecialchars($row['afdeling']) . "' ";
                echo "data-status='todo' ";
                echo "data-deadline='" . htmlspecialchars($row['deadline'] ?? '') . "' ";
                echo "data-description='" . htmlspecialchars($row['beschrijving'] ?? '') . "' ";
                echo "onclick='showTaskDetails(this)' style='cursor:pointer;'>";
                echo "<strong>" . htmlspecialchars($row['titel']) . "</strong>";
                echo "<div class='task-label'>" . htmlspecialchars($row['afdeling']) . "</div>";
                echo "<div class='task-actions'>";
                echo "
                <form method='GET' action='edit_task.php' style='display: inline;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-edit' title='Bewerken'>✏️</button>
                </form>";
                echo "
                <form method='POST' action='../backend/delete_task.php' style='display: inline;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-delete' onclick=\"return confirm('Taak verwijderen?'); event.stopPropagation();\" title='Verwijderen'>🗑️</button>
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
          <button onclick="openAddPopup()">+</button>
        </div>
 
        <div class="tasks" id="doing">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='doing'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' ";
                echo "data-id='" . $row['id'] . "' ";
                echo "data-title='" . htmlspecialchars($row['titel']) . "' ";
                echo "data-afdeling='" . htmlspecialchars($row['afdeling']) . "' ";
                echo "data-status='doing' ";
                echo "data-deadline='" . htmlspecialchars($row['deadline'] ?? '') . "' ";
                echo "data-description='" . htmlspecialchars($row['beschrijving'] ?? '') . "' ";
                echo "onclick='showTaskDetails(this)' style='cursor:pointer;'>";
                echo "<strong>" . htmlspecialchars($row['titel']) . "</strong>";
                echo "<div class='task-label'>" . htmlspecialchars($row['afdeling']) . "</div>";
                echo "<div class='task-actions'>";
                echo "
                <form method='GET' action='edit_task.php' style='display: inline;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-edit' title='Bewerken'>✏️</button>
                </form>";
                echo "
                <form method='POST' action='../backend/delete_task.php' style='display: inline;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-delete' onclick=\"return confirm('Taak verwijderen?'); event.stopPropagation();\" title='Verwijderen'>🗑️</button>
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
          <button onclick="openAddPopup()">+</button>
        </div>
 
        <div class="tasks" id="done">
          <?php
            $stmt = $conn->query("SELECT * FROM taken WHERE status='done'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' ";
                echo "data-id='" . $row['id'] . "' ";
                echo "data-title='" . htmlspecialchars($row['titel']) . "' ";
                echo "data-afdeling='" . htmlspecialchars($row['afdeling']) . "' ";
                echo "data-status='done' ";
                echo "data-deadline='" . htmlspecialchars($row['deadline'] ?? '') . "' ";
                echo "data-description='" . htmlspecialchars($row['beschrijving'] ?? '') . "' ";
                echo "onclick='showTaskDetails(this)' style='cursor:pointer;'>";
                echo "<strong>" . htmlspecialchars($row['titel']) . "</strong>";
                echo "<div class='task-label'>" . htmlspecialchars($row['afdeling']) . "</div>";
                echo "<div class='task-actions'>";
                echo "
                <form method='GET' action='edit_task.php' style='display: inline;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-edit' title='Bewerken'>✏️</button>
                </form>";
                echo "
                <form method='POST' action='../backend/delete_task.php' style='display: inline;'>
                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                    <button type='submit' class='btn-delete' onclick=\"return confirm('Taak verwijderen?'); event.stopPropagation();\" title='Verwijderen'>🗑️</button>
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

<script>
function showTaskDetails(taskElement) {
    // Haal alle data uit de task
    const title = taskElement.dataset.title;
    const afdeling = taskElement.dataset.afdeling;
    const status = taskElement.dataset.status;
    const deadline = taskElement.dataset.deadline || 'Geen deadline';
    const description = taskElement.dataset.description || '';
    
    // Vul popup velden
    document.getElementById('popupTitle').textContent = title;
    document.getElementById('popupAfdeling').textContent = afdeling;
    document.getElementById('popupStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);
    document.getElementById('popupDeadline').textContent = deadline;
    
    const descElement = document.getElementById('popupDescription');
    if (description.trim()) {
        descElement.textContent = description;
        descElement.className = 'detail-description';
    } else {
        descElement.textContent = 'Geen beschrijving beschikbaar.';
        descElement.className = 'detail-description no-description';
    }
    
    // Toon popup
    document.getElementById('taskDetailsPopup').style.display = 'block';
}

function closeTaskPopup() {
    document.getElementById('taskDetailsPopup').style.display = 'none';
}

// Sluit popup bij klik op achtergrond
document.getElementById('taskDetailsPopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTaskPopup();
    }
});

// ESC toets om popup te sluiten
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTaskPopup();
    }
});

// Voorkom dat klikken op knoppen de popup opent
document.querySelectorAll('.btn-edit, .btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

function filterTasks() {
    // Je filter functie hier
}

function openAddPopup() {
    // Je add popup functie hier
}
</script>

</body>
</html>