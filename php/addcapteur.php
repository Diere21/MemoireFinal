<?php
require 'connexion.php';
if ($_SESSION['user']['role'] !== 'admin') die("Accès refusé");
$stmt = $pdo->prepare("INSERT INTO capteurs (nom, niveau_statique, type_ouvrage, aquifere) VALUES (?, ?, ?, ?)");
$stmt->execute([
  $_POST['nom'], $_POST['niveau_statique'], $_POST['type_ouvrage'], $_POST['aquifere']
]);
header("Location: admin_panel.php");
exit;
?>