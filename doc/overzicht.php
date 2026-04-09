<?php
session_start();
require_once '../backend/conn.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Haal filter- en zoekwaarden op
$filterAfdeling = $_GET['afdeling'] ?? 'all';
$filterStatus = $_GET['status'] ?? 'all';
$zoek = $_GET['zoek'] ?? '';

// Bouw de query dynamisch op
$sql = "SELECT * FROM taken WHERE user_id = :user_id";
$params = [':user_id' => $user_id];

if ($filterAfdeling !== 'all') {
    $sql .= " AND afdeling = :afdeling";
    $params[':afdeling'] = $filterAfdeling;
}

if ($filterStatus !== 'all') {
    $sql .= " AND status = :status";
    $params[':status'] = $filterStatus;
}

if (!empty($zoek)) {
    $sql .= " AND (titel LIKE :zoek OR beschrijving LIKE :zoek2)";
    $params[':zoek'] = '%' . $zoek . '%';
    $params[':zoek2'] = '%' . $zoek . '%';
}

$sql .= " ORDER BY deadline ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$taken = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Overzicht Taken</title>
  <link rel="stylesheet" href="../public_html/css/home.css" type="text/css">

  <style>
    /* ── Overzicht styling ── */
    .overzicht-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 20px;
    }

    .overzicht-header h2 {
      margin: 0;
      font-size: 22px;
      color: #333;
    }

    .overzicht-filters {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
    }

    .overzicht-filters select,
    .overzicht-filters input[type="text"] {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #dee2e6;
      background: white;
      font-size: 14px;
      font-weight: 500;
      color: #333;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      transition: all 0.2s ease;
    }

    .overzicht-filters select:hover,
    .overzicht-filters input[type="text"]:hover {
      border-color: #4dabf7;
      box-shadow: 0 4px 10px rgba(77, 171, 247, 0.2);
    }

    .overzicht-filters select:focus,
    .overzicht-filters input[type="text"]:focus {
      outline: none;
      border-color: #4dabf7;
      box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.2);
    }

    .overzicht-filters button {
      padding: 8px 16px;
      border-radius: 8px;
      border: none;
      background: #4dabf7;
      color: white;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .overzicht-filters button:hover {
      background: #1e7fe6;
    }

    .btn-reset {
      background: #868e96 !important;
    }

    .btn-reset:hover {
      background: #495057 !important;
    }

    /* ── Tabel styling ── */
    .taken-tabel {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .taken-tabel thead th {
      background: #f1f3f5;
      padding: 12px 16px;
      text-align: left;
      font-size: 13px;
      font-weight: 700;
      color: #495057;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #dee2e6;
    }

    .taken-tabel tbody tr {
      transition: background 0.15s ease;
    }

    .taken-tabel tbody tr:hover {
      background: #f8f9fa;
    }

    .taken-tabel tbody td {
      padding: 12px 16px;
      font-size: 14px;
      color: #333;
      border-bottom: 1px solid #f1f3f5;
      vertical-align: middle;
    }

    /* ── Status badges ── */
    .status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      text-transform: capitalize;
    }

    .status-badge.todo {
      background: #fff3bf;
      color: #e67700;
      border: 1px solid #ffd43b55;
    }

    .status-badge.doing {
      background: #d0ebff;
      color: #1864ab;
      border: 1px solid #4dabf755;
    }

    .status-badge.done {
      background: #d3f9d8;
      color: #2b8a3e;
      border: 1px solid #51cf6655;
    }

    /* ── Afdeling labels (zelfde als home) ── */
    .afdeling-label {
      display: inline-block;
      padding: 4px 10px;
      font-size: 12px;
      font-weight: bold;
      border-radius: 20px;
      text-transform: capitalize;
    }

    .afdeling-label.personeel { background: #ff6b6b22; color: #b30000; border: 1px solid #ff6b6b55; }
    .afdeling-label.horeca { background: #ffa94d22; color: #a65e00; border: 1px solid #ffa94d55; }
    .afdeling-label.techniek { background: #4dabf722; color: #0b4f91; border: 1px solid #4dabf755; }
    .afdeling-label.Groen { background: #51cf6622; color: #1b7d2d; border: 1px solid #51cf6655; }
    .afdeling-label.Inkoop { background: #845ef722; color: #3d1fa3; border: 1px solid #845ef755; }
    .afdeling-label.Klantenservice { background: #f0659522; color: #a30047; border: 1px solid #f0659555; }

    /* ── Actie knoppen ── */
    .tabel-actions {
      display: flex;
      gap: 6px;
    }

    .tabel-actions form {
      margin: 0;
    }

    .tabel-actions button {
      padding: 6px 10px;
      font-size: 13px;
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

    /* ── Telling ── */
    .taken-count {
      font-size: 14px;
      color: #868e96;
      margin-bottom: 12px;
    }

    .taken-count strong {
      color: #333;
    }

    /* ── Geen resultaten ── */
    .geen-taken {
      text-align: center;
      padding: 40px 20px;
      color: #868e96;
      font-size: 16px;
    }

    /* ── Deadline styling ── */
    .deadline-verlopen {
      color: #e03131;
      font-weight: 600;
    }

    .deadline-vandaag {
      color: #e67700;
      font-weight: 600;
    }

    /* ── Navigatie link ── */
    .nav-links {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .nav-links a {
      text-decoration: none;
      color: #4dabf7;
      font-weight: 600;
      font-size: 14px;
      transition: color 0.2s;
    }

    .nav-links a:hover {
      color: #1e7fe6;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .overzicht-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .taken-tabel {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>

<body>

<header>
  <div class="logo">
    <img src="../img/logo.png" alt="logo">
  </div>

  <div class="nav-links">
    <a href="home.php">Terug naar bord</a>
  </div>
</header>

<main>
  <div class="wrapper">
    <div class="overzicht-header">
      <h2>Mijn taken</h2>

      <form method="GET" class="overzicht-filters">
        <input type="text" name="zoek" placeholder="Zoek op titel..." value="<?= htmlspecialchars($zoek) ?>">

        <select name="afdeling">
          <option value="all" <?= $filterAfdeling === 'all' ? 'selected' : '' ?>>Alle afdelingen</option>
          <option value="personeel" <?= $filterAfdeling === 'personeel' ? 'selected' : '' ?>>Personeel</option>
          <option value="horeca" <?= $filterAfdeling === 'horeca' ? 'selected' : '' ?>>Horeca</option>
          <option value="techniek" <?= $filterAfdeling === 'techniek' ? 'selected' : '' ?>>Techniek</option>
          <option value="Groen" <?= $filterAfdeling === 'Groen' ? 'selected' : '' ?>>Groen</option>
          <option value="Inkoop" <?= $filterAfdeling === 'Inkoop' ? 'selected' : '' ?>>Inkoop</option>
          <option value="Klantenservice" <?= $filterAfdeling === 'Klantenservice' ? 'selected' : '' ?>>Klantenservice</option>
        </select>

        <select name="status">
          <option value="all" <?= $filterStatus === 'all' ? 'selected' : '' ?>>Alle statussen</option>
          <option value="todo" <?= $filterStatus === 'todo' ? 'selected' : '' ?>>To Do</option>
          <option value="doing" <?= $filterStatus === 'doing' ? 'selected' : '' ?>>Doing</option>
          <option value="done" <?= $filterStatus === 'done' ? 'selected' : '' ?>>Done</option>
        </select>

        <button type="submit">Filteren</button>
        <a href="overzicht.php"><button type="button" class="btn-reset">Reset</button></a>
      </form>
    </div>

    <div class="taken-count">
      <strong><?= count($taken) ?></strong> <?= count($taken) === 1 ? 'taak' : 'taken' ?> gevonden
    </div>

    <?php if (count($taken) > 0): ?>
    <table class="taken-tabel">
      <thead>
        <tr>
          <th>#</th>
          <th>Titel</th>
          <th>Beschrijving</th>
          <th>Afdeling</th>
          <th>Status</th>
          <th>Deadline</th>
          <th>Acties</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($taken as $index => $taak): ?>
        <?php
          // Bepaal of deadline verlopen is
          $deadlineClass = '';
          if (!empty($taak['deadline'])) {
              $deadlineDate = new DateTime($taak['deadline']);
              $vandaag = new DateTime('today');
              if ($deadlineDate < $vandaag && $taak['status'] !== 'done') {
                  $deadlineClass = 'deadline-verlopen';
              } elseif ($deadlineDate == $vandaag) {
                  $deadlineClass = 'deadline-vandaag';
              }
          }
        ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><strong><?= htmlspecialchars($taak['titel']) ?></strong></td>
          <td><?= htmlspecialchars($taak['beschrijving'] ?? '-') ?></td>
          <td>
            <span class="afdeling-label <?= htmlspecialchars($taak['afdeling']) ?>">
              <?= htmlspecialchars($taak['afdeling']) ?>
            </span>
          </td>
          <td>
            <span class="status-badge <?= htmlspecialchars($taak['status']) ?>">
              <?php
                $statusLabels = ['todo' => 'To Do', 'doing' => 'Doing', 'done' => 'Done'];
                echo $statusLabels[$taak['status']] ?? $taak['status'];
              ?>
            </span>
          </td>
          <td class="<?= $deadlineClass ?>">
            <?= !empty($taak['deadline']) ? htmlspecialchars($taak['deadline']) : '-' ?>
            <?php if ($deadlineClass === 'deadline-verlopen'): ?>
              <br><small>(verlopen)</small>
            <?php elseif ($deadlineClass === 'deadline-vandaag'): ?>
              <br><small>(vandaag)</small>
            <?php endif; ?>
          </td>
          <td>
            <div class="tabel-actions">
              <form method="GET" action="edit_task.php">
                <input type="hidden" name="id" value="<?= $taak['id'] ?>">
                <button type="submit" class="btn-edit">Bewerken</button>
              </form>
              <form method="POST" action="../backend/delete_task.php">
                <input type="hidden" name="id" value="<?= $taak['id'] ?>">
                <button type="submit" class="btn-delete" onclick="return confirm('Taak verwijderen?');">Verwijderen</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <div class="geen-taken">
        Geen taken gevonden met de huidige filters.
      </div>
    <?php endif; ?>

  </div>
</main>

</body>
</html>
