<?php
require 'db.php';

$capteur = $_GET['capteur'] ?? '';
$debut = $_GET['debut'] ?? '';
$fin = $_GET['fin'] ?? '';

$sql = "SELECT * FROM niveaux WHERE 1";
$params = [];

if ($capteur) {
    $sql .= " AND capteur = ?";
    $params[] = $capteur;
}
if ($debut) {
    $sql .= " AND date >= ?";
    $params[] = $debut;
}
if ($fin) {
    $sql .= " AND date <= ?";
    $params[] = $fin;
}

$sql .= " ORDER BY date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
?>
