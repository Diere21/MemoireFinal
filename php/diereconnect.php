<?php
require 'connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (login($_POST['username'], $_POST['password'])) {
    header('Location: dashboard.php');
    exit;
  } else {
    $error = "Identifiants invalides";
  }
}
?>
<form method="POST">
  <input name="username" placeholder="Nom d'utilisateur">
  <input type="password" name="password" placeholder="Mot de passe">
  <button type="submit">Connexion</button>
  <?php if (isset($error)) echo "<p>$error</p>"; ?>
</form>
