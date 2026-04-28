<?php
session_start();

// Durée max d'inactivité (3 minutes)
$timeout = 180;

// Si l'utilisateur n'est pas connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Si une activité précédente existe
if (isset($_SESSION['last_activity'])) {
    // Temps écoulé depuis la dernière activité
    if (time() - $_SESSION['last_activity'] > $timeout) {
        // Session expirée
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

// Mise à jour de l'activité
$_SESSION['last_activity'] = time();