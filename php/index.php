<?php
require 'connexion.php';

if (is_logged_in()) {
    switch ($_SESSION['user']['role']) {
        case 'admin':
            header("Location: admin_panel.php");
            break;
        case 'dggpre':
            header("Location: dashboard.php");
            break;
        case 'onas':
            header("Location: onas_home.php"); // à créer
            break;
    }
} else {
    header("Location: login.php");
}
exit;
