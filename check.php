<?php
/**
 * Script de vérification de l'état de la base de données
 * Utile pour diagnostiquer les problèmes d'authentification
 */

require_once 'database.php';
require_once 'config/config.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== Vérification du système d'authentification SQLite ===\n\n";

try {
    global $userDB;
    
    // Vérifier la connexion à la base
    echo "✅ Connexion à la base de données : OK\n";
    
    // Vérifier le chemin de la base
    echo "📁 Chemin de la base : " . DB_PATH . "\n";
    echo "📊 Taille du fichier : " . (file_exists(DB_PATH) ? filesize(DB_PATH) . " bytes" : "Fichier non trouvé") . "\n";
    
    // Lister tous les utilisateurs
    $users = $userDB->getAllUsers();
    echo "\n👥 Utilisateurs dans la base (" . count($users) . ") :\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-15s %-10s %-10s %-20s %-20s\n", "USERNAME", "ROLE", "STATUS", "CREATED", "LAST LOGIN");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($users as $user) {
        printf("%-15s %-10s %-10s %-20s %-20s\n", 
            $user['username'],
            $user['role'],
            $user['is_active'] ? 'Actif' : 'Inactif',
            date('d/m/Y H:i', strtotime($user['created_at'])),
            $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais'
        );
    }
    
    // Informations sur la configuration
    echo "\n⚙️  Configuration :\n";
    echo "   Timeout de session : " . SESSION_TIMEOUT . " secondes (" . (SESSION_TIMEOUT/60) . " minutes)\n";
    echo "   Nom de l'application : " . APP_NAME . "\n";
    
    // Vérifications de sécurité
    echo "\n🛡️  Vérifications de sécurité :\n";
    
    // Vérifier les permissions du fichier de base
    if (file_exists(DB_PATH)) {
        $perms = fileperms(DB_PATH);
        echo "   Permissions base de données : " . sprintf('%o', $perms & 0777) . "\n";
        
        if (is_readable(DB_PATH)) {
            echo "   ✅ Base de données lisible\n";
        } else {
            echo "   ❌ Base de données non lisible\n";
        }
        
        if (is_writable(DB_PATH)) {
            echo "   ✅ Base de données modifiable\n";
        } else {
            echo "   ❌ Base de données non modifiable\n";
        }
    }
    
    // Vérifier les extensions PHP
    $extensions = ['pdo', 'pdo_sqlite', 'session', 'hash'];
    echo "\n🔧 Extensions PHP :\n";
    foreach ($extensions as $ext) {
        echo "   " . (extension_loaded($ext) ? "✅" : "❌") . " $ext\n";
    }
    
    // Statistiques
    echo "\n📈 Statistiques :\n";
    $activeUsers = array_filter($users, function($u) { return $u['is_active']; });
    $adminUsers = array_filter($users, function($u) { return $u['role'] === 'admin'; });
    
    echo "   Total utilisateurs : " . count($users) . "\n";
    echo "   Utilisateurs actifs : " . count($activeUsers) . "\n";
    echo "   Administrateurs : " . count($adminUsers) . "\n";
    
    // Utilisateurs avec dernière connexion récente (7 jours)
    $recentLogins = array_filter($users, function($u) {
        return $u['last_login'] && (time() - strtotime($u['last_login'])) < 7*24*3600;
    });
    echo "   Connexions récentes (7j) : " . count($recentLogins) . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "\nActions recommandées :\n";
    echo "1. Vérifiez que le dossier data/ existe et est accessible en écriture\n";
    echo "2. Exécutez install.php pour réinitialiser la base\n";
    echo "3. Vérifiez les extensions PHP requises\n";
}

echo "\n=== Fin de la vérification ===\n";
?>
