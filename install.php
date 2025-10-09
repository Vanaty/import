<?php
/**
 * Script d'installation pour le système d'authentification avec SQLite
 * Exécutez ce script une seule fois pour initialiser l'application
 */

echo "=== Installation du système d'authentification SQLite ===\n\n";

// Vérifier les prérequis
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    die("❌ Erreur : PHP 7.4 ou supérieur requis. Version actuelle : " . PHP_VERSION . "\n");
}

echo "✅ Version PHP compatible : " . PHP_VERSION . "\n";

// Vérifier les extensions PHP requises
$requiredExtensions = ['session', 'hash', 'pdo', 'pdo_sqlite'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        die("❌ Erreur : Extension PHP '$ext' requise mais non disponible.\n");
    }
}
echo "✅ Extensions PHP requises disponibles\n";

// Créer les dossiers nécessaires
$directories = ['config', 'data'];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Dossier '$dir' créé\n";
        } else {
            die("❌ Erreur : Impossible de créer le dossier '$dir'\n");
        }
    } else {
        echo "✅ Dossier '$dir' existe déjà\n";
    }
}

// Vérifier les permissions d'écriture
$writableDirectories = ['.', 'config', 'data', 'api'];
foreach ($writableDirectories as $dir) {
    if (!is_writable($dir)) {
        echo "⚠️  Attention : Le dossier '$dir' n'est pas accessible en écriture\n";
    }
}

// Initialiser la base de données en incluant le fichier database.php
require_once 'database.php';
echo "✅ Base de données SQLite initialisée\n";

// Récupérer la configuration
require_once 'config/config.php';

echo "\n=== Utilisateur administrateur créé ===\n";
echo "Nom d'utilisateur : admin\n";
echo "Mot de passe      : " . DEFAULT_ADMIN_PASSWORD . "\n\n";

// Afficher des informations sur la base de données
global $userDB;
$users = $userDB->getAllUsers();
echo "✅ " . count($users) . " utilisateur(s) dans la base de données\n";

echo "\n=== Configuration de sécurité ===\n";

// Vérifier la configuration de session PHP
if (ini_get('session.cookie_httponly') != 1) {
    echo "⚠️  Recommandation : Activez session.cookie_httponly dans php.ini\n";
}
if (ini_get('session.cookie_secure') != 1 && isset($_SERVER['HTTPS'])) {
    echo "⚠️  Recommandation : Activez session.cookie_secure pour HTTPS\n";
}

// Vérifier la sécurité du dossier data
$dataPath = realpath('data');
if ($dataPath) {
    echo "� Base de données SQLite : $dataPath/users.db\n";
    if (is_readable($dataPath . '/users.db')) {
        echo "✅ Base de données accessible\n";
    }
}

echo "\n=== Installation terminée ===\n";
echo "🌐 Accédez à votre application et connectez-vous\n";
echo "🔒 L'application utilise maintenant SQLite pour l'authentification\n";
echo "👤 Connectez-vous avec : admin / " . DEFAULT_ADMIN_PASSWORD . "\n\n";

echo "=== Prochaines étapes ===\n";
echo "1. Testez la connexion avec le compte admin\n";
echo "2. Accédez à l'administration pour créer d'autres utilisateurs\n";
echo "3. Consultez AUTHENTICATION.md pour plus d'informations\n";
echo "4. En production, configurez HTTPS et sécurisez le dossier data/\n";
echo "5. Changez le mot de passe par défaut de l'admin\n\n";

echo "=== Fonctionnalités SQLite ===\n";
echo "✅ Stockage persistant des utilisateurs\n";
echo "✅ Gestion des rôles (admin/user)\n";
echo "✅ Historique des connexions\n";
echo "✅ Activation/désactivation des comptes\n";
echo "✅ Changement de mots de passe\n";
echo "✅ Suppression d'utilisateurs\n";
?>
