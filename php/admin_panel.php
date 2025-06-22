<?php
require 'connexion.php';
if (!is_admin()) {
    header("Location: login.php");
    exit;
}

$page_title = "Panneau d'administration";
$message = null;

// Créer un utilisateur
if (isset($_POST['new_user'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($username && $password && in_array($role, ['dggpre', 'onas'])) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $role]);
        $message = "✅ Utilisateur créé.";
    }
}

// Ajouter infos capteur
if (isset($_POST['new_capteur'])) {
    $code = $_POST['code'] ?? '';
    $zone = $_POST['zone'] ?? '';
    $niveau_statique = $_POST['niveau_statique'] ?? '';
    $type_ouvrage = $_POST['type_ouvrage'] ?? '';
    $aquifere = $_POST['aquifere'] ?? '';

    if ($code !== '') {
        $check = $pdo->prepare("SELECT * FROM capteurs WHERE code = ?");
        $check->execute([$code]);

        if ($check->rowCount() === 0) {
            $stmt = $pdo->prepare("INSERT INTO capteurs (code, zone, niveau_statique, type_ouvrage, aquifere) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$code, $zone, $niveau_statique, $type_ouvrage, $aquifere]);
            $message = "✅ Informations du capteur ajoutées.";
        } else {
            $message = "⚠️ Ce capteur est déjà enregistré.";
        }
    } else {
        $message = "⚠️ Veuillez sélectionner un capteur valide.";
    }
}

// Liste des capteurs détectés dans la table niveaux
$capteurs = $pdo->query("SELECT DISTINCT capteur FROM niveaux")->fetchAll(PDO::FETCH_COLUMN);

include 'template.php';
?>

<div class="container mt-4">
  <h2 class="mb-4">Panneau d'administration</h2>

  <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <div class="row">
    <!-- Création utilisateur -->
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">Créer un utilisateur</div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="new_user" value="1">
            <div class="mb-3">
              <label class="form-label">Nom d'utilisateur</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Mot de passe</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Rôle</label>
              <select name="role" class="form-select" required>
                <option value="">-- Choisir --</option>
                <option value="dggpre">Agent DGPRE</option>
                <option value="onas">Agent ONAS</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Ajout capteur -->
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">Ajouter informations capteur</div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="new_capteur" value="1">
            <div class="mb-3">
              <label class="form-label">Capteur détecté</label>
              <select name="code" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                <?php foreach ($capteurs as $c): ?>
                  <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Zone</label>
              <input type="text" name="zone" class="form-control" required placeholder="ex: Yoff, Grand-Yoff, Camberène">
            </div>
            <div class="mb-3">
              <label class="form-label">Niveau statique (m)</label>
              <input type="number" step="0.01" name="niveau_statique" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Type d’ouvrage</label>
              <select name="type_ouvrage" class="form-select" required>
                <option value="">-- Choisir --</option>
                <option value="puits">puits</option>
                <option value="forage">forage</option>
                <option value="piézomètre">piézomètre</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Aquifère</label>
              <select name="aquifere" class="form-select" required>
                <option value="">-- Choisir --</option>
                <option value="infrabasaltique">infrabasaltique</option>
                <option value="sables quaternaires">sables quaternaires</option>
              </select>
            </div>
            <button type="submit" class="btn btn-success">Ajouter le capteur</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
</div>

</body>
</html>
