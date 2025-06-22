<?php
$host = 'localhost';
$dbname = 'nappe_phreatique';
$user = 'root';
$password = 'Log@rithme21';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
