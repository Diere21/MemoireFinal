<?php
require 'connexion.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$capteur = $_GET['capteur'] ?? '';
if (!$capteur) {
    echo "Aucun capteur sélectionné.";
    exit;
}

// Récupérer les infos du capteur
$infoStmt = $pdo->prepare("SELECT * FROM capteurs WHERE code = ?");
$infoStmt->execute([$capteur]);
$capteurInfo = $infoStmt->fetch();
if (!$capteurInfo) {
    echo "Capteur introuvable.";
    exit;
}

// Filtres date et heure
$date_debut = $_GET['date_debut'] ?? '';
$heure_debut = $_GET['heure_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';
$heure_fin = $_GET['heure_fin'] ?? '';

// Requête dynamique
$sql = "SELECT date, niveau FROM niveaux WHERE capteur = ?";
$params = [$capteur];

if ($date_debut) {
    $sql .= " AND date >= ?";
    $params[] = $date_debut . ($heure_debut ? " $heure_debut" : " 00:00:00");
}
if ($date_fin) {
    $sql .= " AND date <= ?";
    $params[] = $date_fin . ($heure_fin ? " $heure_fin" : " 23:59:59");
}

$sql .= " ORDER BY date ASC";
$mesureStmt = $pdo->prepare($sql);
$mesureStmt->execute($params);
$mesures = $mesureStmt->fetchAll(PDO::FETCH_ASSOC);

$dates = array_column($mesures, 'date');
$niveaux = array_column($mesures, 'niveau');

$page_title = "Graphique - $capteur";
include 'template.php';
?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <a href="dashboard.php" class="btn btn-secondary">⬅ Retour</a>
      <a href="logout.php" class="btn btn-outline-danger">🔓 Déconnexion</a>
    </div>
    <form class="d-flex align-items-end" method="GET">
      <input type="hidden" name="capteur" value="<?= htmlspecialchars($capteur) ?>">
      <div class="me-2">
        <label class="form-label">De</label>
        <input type="date" class="form-control" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>">
        <input type="time" class="form-control" name="heure_debut" value="<?= htmlspecialchars($heure_debut) ?>">
      </div>
      <div class="me-2">
        <label class="form-label">à</label>
        <input type="date" class="form-control" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>">
        <input type="time" class="form-control" name="heure_fin" value="<?= htmlspecialchars($heure_fin) ?>">
      </div>
      <button type="submit" class="btn btn-primary me-2">Filtrer</button>
    </form>
    <form method="POST" action="export.php">
      <input type="hidden" name="capteur" value="<?= htmlspecialchars($capteur) ?>">
      <input type="hidden" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>">
      <input type="hidden" name="heure_debut" value="<?= htmlspecialchars($heure_debut) ?>">
      <input type="hidden" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>">
      <input type="hidden" name="heure_fin" value="<?= htmlspecialchars($heure_fin) ?>">
      <button type="submit" class="btn btn-success">📥 Exporter</button>
    </form>
  </div>

  <div class="card mb-4">
    <div class="card-header">Informations du capteur</div>
    <div class="card-body">
      <p><strong>Capteur :</strong> <?= htmlspecialchars($capteur) ?></p>
      <p><strong>Type d'ouvrage :</strong> <?= htmlspecialchars($capteurInfo['type_ouvrage']) ?></p>
      <p><strong>Aquifère :</strong> <?= htmlspecialchars($capteurInfo['aquifere']) ?></p>
      <p><strong>Niveau statique :</strong> <?= htmlspecialchars($capteurInfo['niveau_statique']) ?> m</p>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Graphique des mesures</div>
    <div class="card-body">
      <canvas id="graph" width="1000" height="400"></canvas>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = <?= json_encode($dates) ?>;
  const data = <?= json_encode(array_map('floatval', $niveaux)) ?>;

  new Chart(document.getElementById('graph'), {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Niveau (m)',
        data: data,
        borderColor: 'green',
        borderWidth: 2,
        tension: 0.3
      }]
    },
    options: {
      scales: {
        x: { display: true },
        y: { beginAtZero: true }
      }
    }
  });
</script>
</body>
</html>
