<?php
require 'connexion.php';

$username = "admin";
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);
$role = "admin";

// Vérifie si un admin existe déjà
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->rowCount() > 0) {
    echo "⚠️ Un utilisateur 'admin' existe déjà.";
    exit;
}

// Insère le nouvel utilisateur
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->execute([$username, $hash, $role]);

echo "✅ Utilisateur admin créé avec succès. Identifiants :<br>";
echo "Nom d'utilisateur : <b>$username</b><br>";
echo "Mot de passe : <b>$password</b><br>";
echo "<a href='login.php'>Aller à la connexion</a>";
