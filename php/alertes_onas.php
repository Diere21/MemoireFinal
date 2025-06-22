<?php
require 'connexion.php';
if (!is_onas()) {
    header("Location: login.php");
    exit;
}

$page_title = "Alertes - ONAS";

// Requête pour récupérer les alertes avec infos capteurs
$stmt = $pdo->query("
    SELECT n.date, n.niveau, c.zone, c.niveau_statique
    FROM niveaux n
    JOIN capteurs c ON n.capteur = c.code
    ORDER BY n.date DESC
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'template.php';
?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Alertes - Agent ONAS</h3>
    <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
  </div>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>Zone</th>
        <th>Risque d'inondation (%)</th>
        <th>Type d'alerte</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $row): 
        $niveau = floatval($row['niveau']);
        $statique = floatval($row['niveau_statique']);
        $pourcentage = $statique > 0 ? ($niveau / $statique) * 100 : 0;

        if ($pourcentage < 50) continue; // Ne pas afficher si < 50%

        $alerte = '';
        if ($pourcentage >= 90) {
          $alerte = '🔴 Critique';
        } elseif ($pourcentage >= 70) {
          $alerte = '🟠 Avertissement';
        } elseif ($pourcentage >= 50) {
          $alerte = '🟡 Surveillance';
        }
      ?>
      <tr>
        <td><?= htmlspecialchars($row['zone'] ?? '') ?></td>
        <td><?= number_format($pourcentage, 1) ?>%</td>
        <td><?= htmlspecialchars($alerte) ?></td>
        <td><?= htmlspecialchars($row['date'] ?? '') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
