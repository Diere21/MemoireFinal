<?php
require 'connexion.php';
if (!is_dgpree() && !is_admin()) {
    header("Location: login.php");
    exit;
}

$page_title = "Alertes";

$stmt = $pdo->query("
    SELECT n.*, c.code AS capteur_nom, c.niveau_statique
    FROM niveaux n
    JOIN capteurs c ON n.capteur = c.code
    ORDER BY n.date DESC
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'template.php';
?>

<h2 class="mb-4">Alertes sur les niveaux</h2>

<a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Retour au tableau de bord</a>

<table class="table table-bordered text-center align-middle">
  <thead class="table-light">
    <tr>
      <th>Capteur</th>
      <th>Niveau (m)</th>
      <th>Seuil statique</th>
      <th>%</th>
      <th>Alerte</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data as $row):
      $niveau = floatval($row['niveau']);
      $statique = floatval($row['niveau_statique']);
      $pourcentage = $statique > 0 ? ($niveau / $statique) * 100 : 0;

      if ($pourcentage < 50) continue;

      if ($pourcentage >= 90) {
        $class = "table-danger";
        $alerte = "🔴 Critique";
      } elseif ($pourcentage >= 70) {
        $class = "table-warning";
        $alerte = "🟠 Avertissement";
      } else {
        $class = "table-warning-subtle";
        $alerte = "🟡 Surveillance";
      }
    ?>
    <tr class="<?= $class ?>">
      <td><?= htmlspecialchars($row['capteur_nom']) ?></td>
      <td><?= number_format($niveau, 2) ?></td>
      <td><?= number_format($statique, 2) ?></td>
      <td><?= number_format($pourcentage, 1) ?>%</td>
      <td><?= $alerte ?></td>
      <td><?= $row['date'] ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</div>
</body>
</html>
