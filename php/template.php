<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'connexion.php';

$page_title = $page_title ?? "Surveillance Nappe Phréatique";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding-top: 70px;
      background-color: #f8f9fa;
    }
  </style>
</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Nappe Phréatique</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav ms-auto">
        <?php if (is_logged_in()): ?>
          <?php if (is_dgpree() || is_admin()): ?>
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Tableau de bord</a></li>
            <li class="nav-item"><a class="nav-link" href="alertes.php">Alertes</a></li>
          <?php endif; ?>
          <?php if (is_admin()): ?>
            <li class="nav-item"><a class="nav-link" href="admin_panel.php">Admin</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
