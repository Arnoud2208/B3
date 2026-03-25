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
    /* --- jouw bestaande styles (licht opgeschoond) --- */
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
      display: block;
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

    /* kleur-categorieën */
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
      position: relative;
      display: inline-block;
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
    #filter:hover { border-color: #4dabf7; box-shadow: 0 4px 10px rgba(77, 171, 247, 0.2); }
    #filter:focus { outline: none; border-color: #4dabf7; box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.2); }

    .filter-box::after {
      content: "▼";
      font-size: 12px;
      color: #666;
      position: absolute;
      right: 30px;
      top: 38px;
      pointer-events: none;
    }

    /* eenvoudige layout columns */
    .wrapper { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .container.columns { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; align-items:start; }
    .column { background: transparent; }
    .column h3 { margin: 0 0 10px 0; font-size: 18px; }
    .empty-state { color: #6b7280; padding: 12px; border-radius: 8px; background: #fbfdff; border: 1px dashed #e6eefc; text-align:center; }

    @media (max-width: 900px) {
      .container.columns { grid-template-columns: 1fr; }
      .filter-box { margin: 10px 0; }
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
      <option value="groen">Groen</option>
      <option value="inkoop">Inkoop</option>
      <option value="klantenservice">Klantenservice</option>
    </select>
  </div>
</header>

<main>
  <div class="wrapper">
    <div class="container columns">

      <!-- TO DO -->
      <div class="column">
        <h3>To Do <small id="count-todo" style="color:#6b7280;font-weight:600;margin-left:8px;"></small></h3>
        <div class="tasks" id="todo">
          <?php
          $stmt = $conn->query("SELECT * FROM taken WHERE status='todo'");
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // veilige, consistente afdeling key (lowercase, geen spaties)
            $afdeling_raw = $row['afdeling'] ?? '';
            $afdeling_key = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/u', '_', $afdeling_raw));
            $titel = htmlspecialchars($row['titel']);
            $afdeling_esc = htmlspecialchars($afdeling_raw);
            $id = (int)$row['id'];

            echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' data-id='{$id}' data-dept='{$afdeling_key}'>";
            echo "<strong>{$titel}</strong>";
            echo "<div class='task-label'>{$afdeling_esc}</div>";

            // ✏️ Bewerken
            echo "
            <form method='GET' action='edit_task.php' style='display:inline; margin-left:10px;'>
              <input type='hidden' name='id' value='{$id}'>
              <button type='submit'>✏️</button>
            </form>";

            // 🗑 Verwijderen
            echo "
            <form method='POST' action='../backend/delete_task.php' style='display:inline; margin-left:10px;'>
              <input type='hidden' name='id' value='{$id}'>
              <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">🗑</button>
            </form>";

            echo "<br><small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
            echo "</div>";
          }
          ?>
        </div>
      </div>

      <!-- DOING -->
      <div class="column">
        <h3>Doing <small id="count-doing" style="color:#6b7280;font-weight:600;margin-left:8px;"></small></h3>
        <div class="tasks" id="doing">
          <?php
          $stmt = $conn->query("SELECT * FROM taken WHERE status='doing'");
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $afdeling_raw = $row['afdeling'] ?? '';
            $afdeling_key = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/u', '_', $afdeling_raw));
            $titel = htmlspecialchars($row['titel']);
            $afdeling_esc = htmlspecialchars($afdeling_raw);
            $id = (int)$row['id'];

            echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' data-id='{$id}' data-dept='{$afdeling_key}'>";
            echo "<strong>{$titel}</strong>";
            echo "<div class='task-label'>{$afdeling_esc}</div>";

            echo "
            <form method='GET' action='edit_task.php' style='display:inline; margin-left:10px;'>
              <input type='hidden' name='id' value='{$id}'>
              <button type='submit'>✏️</button>
            </form>";

            echo "
            <form method='POST' action='../backend/delete_task.php' style='display:inline; margin-left:10px;'>
              <input type='hidden' name='id' value='{$id}'>
              <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">🗑</button>
            </form>";

            echo "<br><small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
            echo "</div>";
          }
          ?>
        </div>
      </div>

      <!-- DONE -->
      <div class="column">
        <h3>Done <small id="count-done" style="color:#6b7280;font-weight:600;margin-left:8px;"></small></h3>
        <div class="tasks" id="done">
          <?php
          $stmt = $conn->query("SELECT * FROM taken WHERE status='done'");
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $afdeling_raw = $row['afdeling'] ?? '';
            $afdeling_key = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/u', '_', $afdeling_raw));
            $titel = htmlspecialchars($row['titel']);
            $afdeling_esc = htmlspecialchars($afdeling_raw);
            $id = (int)$row['id'];

            echo "<div class='task " . htmlspecialchars($row['afdeling']) . "' data-id='{$id}' data-dept='{$afdeling_key}'>";
            echo "<strong>{$titel}</strong>";
            echo "<div class='task-label'>{$afdeling_esc}</div>";

            echo "
            <form method='GET' action='edit_task.php' style='display:inline; margin-left:10px;'>
              <input type='hidden' name='id' value='{$id}'>
              <button type='submit'>✏️</button>
            </form>";

            echo "
            <form method='POST' action='../backend/delete_task.php' style='display:inline; margin-left:10px;'>
              <input type='hidden' name='id' value='{$id}'>
              <button type='submit' onclick=\"return confirm('Taak verwijderen?');\">🗑</button>
            </form>";

            echo "<br><small>Deadline: " . htmlspecialchars($row['deadline'] ?? '') . "</small>";
            echo "</div>";
          }
          ?>
        </div>
        <div id="done-empty" class="empty-state" style="display:none;">Geen voltooide taken</div>
      </div>

    </div>
  </div>
</main>

<script>
/* Filter werkt nu case-insensitive en gebruikt data-dept (gestandaardiseerd) */
function filterTasks() {
  const filter = document.getElementById("filter").value.toLowerCase();
  const tasks = document.querySelectorAll(".task");

  tasks.forEach(task => {
    const dept = (task.dataset.dept || '').toLowerCase();
    if (filter === "all" || dept === filter) {
      task.style.display = "block";
    } else {
      task.style.display = "none";
    }
  });

  updateCounts();
}

/* Update counts en lege-state voor Done */
function updateCounts() {
  const todo = document.querySelectorAll('#todo .task:not([style*="display: none"])').length;
  const doing = document.querySelectorAll('#doing .task:not([style*="display: none"])').length;
  const done = document.querySelectorAll('#done .task:not([style*="display: none"])').length;

  document.getElementById('count-todo').textContent = todo ? '('+todo+')' : '';
  document.getElementById('count-doing').textContent = doing ? '('+doing+')' : '';
  document.getElementById('count-done').textContent = done ? '('+done+')' : '';

  const doneEmpty = document.getElementById('done-empty');
  if (done === 0) {
    doneEmpty.style.display = 'block';
  } else {
    doneEmpty.style.display = 'none';
  }
}

/* initialisatie */
document.addEventListener('DOMContentLoaded', function(){
  filterTasks(); // zorgt dat counts en lege-state meteen goed zijn
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
["todo","doing","done"].forEach(col => {
  const el = document.getElementById(col);
  if (!el) return;
  new Sortable(el, {
    group: "shared",
    animation: 150,
    onEnd: function (evt) {
      // stuur update naar backend (zorg dat update_status.php id en status verwerkt)
      fetch("../backend/update_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${encodeURIComponent(evt.item.dataset.id)}&status=${encodeURIComponent(evt.to.id)}`
      }).catch(err => {
        console.error('Status update mislukt', err);
      });
      // kleine vertraging zodat DOM is bijgewerkt voordat counts geüpdatet worden
      setTimeout(updateCounts, 120);
    }
  });
});
</script>

</body>
</html>