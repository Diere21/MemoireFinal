<?php
require 'connexion.php';

$page_title = "Connexion";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (login($_POST['username'], $_POST['password'])) {
    // Rediriger automatiquement selon le rôle
    switch ($_SESSION['user']['role']) {
      case 'admin':
        header('Location: admin_panel.php'); break;
      case 'dggpre':
        header('Location: dashboard.php'); break;
      case 'onas':
        header('Location: onas_home.php'); break; // à créer
    }
    exit;
  } else {
    $error = "Identifiants invalides";
  }
}
?>

<?php include 'template.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow">
      <div class="card-body">
        <h4 class="card-title mb-4">Connexion</h4>

        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
      </div>
    </div>
  </div>
</div>

</div> <!-- Fermeture du container ouvert dans template.php -->
</body>
</html>
