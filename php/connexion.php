<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=nappe_phreatique", "root", "Log@rithme21", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function is_admin() {
    return is_logged_in() && $_SESSION['user']['role'] === 'admin';
}

function is_dgpree() {
    return is_logged_in() && $_SESSION['user']['role'] === 'dggpre';
}

function is_onas_like() {
    return is_logged_in() && in_array($_SESSION['user']['role'], ['onas', 'da', 'dra', 'hydraulique', 'interieur']);
}

?>
