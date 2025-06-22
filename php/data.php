<?php
require 'db.php';

$query = $pdo->query("SELECT * FROM niveaux ORDER BY date ASC");
$data = $query->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
?>
