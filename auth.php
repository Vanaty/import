<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/config/config.php';

// Instance globale de la base de données
global $userDB;

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Fonction pour rediriger vers la page de login si non connecté
function requireLogin() {
    if (!isLoggedIn()) {
        $currentPage = $_SERVER['REQUEST_URI'];
        header('Location: login.php?redirect=' . urlencode($currentPage));
        exit;
    }
}

// Fonction pour déconnecter l'utilisateur
function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Fonction pour obtenir le nom d'utilisateur connecté
function getUsername() {
    return $_SESSION['username'] ?? 'Inconnu';
}

// Fonction pour obtenir le rôle de l'utilisateur connecté
function getUserRole() {
    return $_SESSION['role'] ?? 'user';
}

// Fonction pour vérifier si l'utilisateur est admin
function isAdmin() {
    return getUserRole() === 'admin';
}

// Fonction pour obtenir le temps de connexion
function getLoginTime() {
    return $_SESSION['login_time'] ?? 0;
}

// Vérifier la durée de session avec la configuration
function checkSessionTimeout($timeout = null) {
    if ($timeout === null) {
        $timeout = SESSION_TIMEOUT;
    }
    
    if (isLoggedIn() && (time() - getLoginTime()) > $timeout) {
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
}

// Fonction pour vérifier les identifiants via la base de données
function verifyUserCredentials($username, $password) {
    global $userDB;
    return $userDB->verifyUser($username, $password);
}

// Traitement de la déconnexion
if (isset($_GET['logout'])) {
    logout();
}
?>
