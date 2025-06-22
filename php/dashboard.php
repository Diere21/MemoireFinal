<?php
require 'connexion.php';
if (!is_dgpree() && !is_admin()) {
    header("Location: login.php");
    exit;
}

$page_title = "Tableau de bord";

// Récupérer tous les capteurs pour le menu déroulant
$capteurs = $pdo->query("SELECT DISTINCT capteur FROM niveaux ORDER BY capteur")->fetchAll(PDO::FETCH_COLUMN);

// Lire les filtres
$filtre_capteur = $_GET['capteur'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$heure_debut = $_GET['heure_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';
$heure_fin = $_GET['heure_fin'] ?? '';

// Requête SQL
$sql = "SELECT * FROM niveaux WHERE 1=1";
$params = [];

if ($filtre_capteur) {
    $sql .= " AND capteur = ?";
    $params[] = $filtre_capteur;
}
if ($date_debut) {
    $sql .= " AND date >= ?";
    $params[] = $date_debut . ($heure_debut ? " $heure_debut" : " 00:00:00");
}
if ($date_fin) {
    $sql .= " AND date <= ?";
    $params[] = $date_fin . ($heure_fin ? " $heure_fin" : " 23:59:59");
}

$sql .= " ORDER BY date DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'template.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2>Mesures des capteurs</h2>
  <div>
    <a href="alertes.php" class="btn btn-warning me-2" title="Voir les alertes">🔔 Alertes</a>
    <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
  </div>
</div>

<!-- Formulaire de filtre -->
<form method="GET" class="row g-3 align-items-end mb-3">
  <div class="col-md-3">
    <label class="form-label">Capteur</label>
    <select name="capteur" class="form-select">
      <option value="">-- Tous --</option>
      <?php foreach ($capteurs as $c): ?>
        <option value="<?= $c ?>" <?= ($c === $filtre_capteur) ? 'selected' : '' ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Date début</label>
    <input type="date" name="date_debut" value="<?= $date_debut ?>" class="form-control">
    <input type="time" name="heure_debut" value="<?= $heure_debut ?>" class="form-control mt-1">
  </div>

  <div class="col-md-3">
    <label class="form-label">Date fin</label>
    <input type="date" name="date_fin" value="<?= $date_fin ?>" class="form-control">
    <input type="time" name="heure_fin" value="<?= $heure_fin ?>" class="form-control mt-1">
  </div>

  <div class="col-md-3 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Filtrer</button>
    <a href="dashboard.php" class="btn btn-secondary">Réinitialiser</a>
  </div>
</form>

<!-- Export -->
<form method="POST" action="export.php" class="mb-4">
  <input type="hidden" name="capteur" value="<?= htmlspecialchars($filtre_capteur) ?>">
  <input type="hidden" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>">
  <input type="hidden" name="heure_debut" value="<?= htmlspecialchars($heure_debut) ?>">
  <input type="hidden" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>">
  <input type="hidden" name="heure_fin" value="<?= htmlspecialchars($heure_fin) ?>">
  <button type="submit" class="btn btn-success">📥 Exporter les données filtrées</button>
</form>

<!-- Tableau -->
<table class="table table-bordered table-striped">
  <thead class="table-light">
    <tr>
      <th>Capteur</th>
      <th>Niveau</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['capteur']) ?></td>
        <td><?= htmlspecialchars($row['niveau']) ?></td>
        <td><?= htmlspecialchars($row['date']) ?></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="graph.php?capteur=<?= urlencode($row['capteur']) ?>">Voir graphique</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<script>
  setInterval(() => {
    location.reload();
  }, 30000); // 30000 ms = 30 secondes
</script>

</div> <!-- Fin du container -->
</body>
</html>
