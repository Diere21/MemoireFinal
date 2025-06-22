<?php
require 'connexion.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export_donnees.csv"');

$output = fopen('php://output', 'w');

// Titres des colonnes
fputcsv($output, ['Capteur', 'Type d\'ouvrage', 'Aquifère', 'Niveau', 'Date']);

// Lire les filtres
$capteur = $_POST['capteur'] ?? '';
$type_ouvrage = $_POST['type_ouvrage'] ?? '';
$aquifere = $_POST['aquifere'] ?? '';
$date_debut = $_POST['date_debut'] ?? '';
$heure_debut = $_POST['heure_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';
$heure_fin = $_POST['heure_fin'] ?? '';

// Requête SQL avec jointure
$sql = "SELECT n.capteur, c.type_ouvrage, c.aquifere, n.niveau, n.date
        FROM niveaux n
        JOIN capteurs c ON n.capteur = c.code
        WHERE 1=1";
$params = [];

if ($capteur) {
    $sql .= " AND n.capteur = ?";
    $params[] = $capteur;
}
if ($type_ouvrage) {
    $sql .= " AND c.type_ouvrage = ?";
    $params[] = $type_ouvrage;
}
if ($aquifere) {
    $sql .= " AND c.aquifere = ?";
    $params[] = $aquifere;
}
if ($date_debut) {
    $sql .= " AND n.date >= ?";
    $params[] = $date_debut . ($heure_debut ? " $heure_debut" : " 00:00:00");
}
if ($date_fin) {
    $sql .= " AND n.date <= ?";
    $params[] = $date_fin . ($heure_fin ? " $heure_fin" : " 23:59:59");
}

$sql .= " ORDER BY n.date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Écrire les lignes dans le CSV
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['capteur'],
        $row['type_ouvrage'],
        $row['aquifere'],
        $row['niveau'],
        $row['date']
    ]);
}

fclose($output);
exit;
