<?php
require_once __DIR__ . '/config/config.php';

/**
 * Classe pour gérer la base de données des utilisateurs
 */
class UserDatabase {
    private $pdo;

    public function __construct() {
        $this->initDatabase();
    }

    /**
     * Initialiser la base de données SQLite
     */
    private function initDatabase() {
        try {
            // Créer le dossier data s'il n'existe pas
            $dataDir = dirname(DB_PATH);
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0755, true);
            }

            // Connexion à SQLite
            $this->pdo = new PDO('sqlite:' . DB_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Créer la table users si elle n'existe pas
            $this->createUsersTable();

            // Créer la table d'historique des imports
            $this->createImportHistoryTable();

            // Créer l'utilisateur admin par défaut s'il n'existe pas
            $this->createDefaultAdmin();

        } catch (PDOException $e) {
            die('Erreur de base de données : ' . $e->getMessage());
        }
    }

    /**
     * Créer la table des utilisateurs
     */
    private function createUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(20) DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login DATETIME NULL,
            is_active BOOLEAN DEFAULT 1
        )";
        
        $this->pdo->exec($sql);
    }

    /**
     * Créer la table d'historique des imports
     */
    private function createImportHistoryTable() {
        $sql = "CREATE TABLE IF NOT EXISTS import_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            username VARCHAR(50) NOT NULL,
            script_name VARCHAR(100) NOT NULL,
            arguments TEXT,
            output TEXT,
            success BOOLEAN NOT NULL,
            server_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            import_date DATE,
            group_id VARCHAR(20),
            action_type VARCHAR(20) DEFAULT 'execute',
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";
        
        $this->pdo->exec($sql);
    }

    /**
     * Créer l'utilisateur admin par défaut
     */
    private function createDefaultAdmin() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            $this->addUser('admin', DEFAULT_ADMIN_PASSWORD, 'admin');
        }
    }

    /**
     * Vérifier les identifiants d'un utilisateur
     */
    public function verifyUser($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, password_hash, role, is_active FROM users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Mettre à jour la dernière connexion
                $this->updateLastLogin($user['id']);
                return [
                    'id' => $user['id'],
                    'username' => $username,
                    'role' => $user['role']
                ];
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Erreur lors de la vérification utilisateur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ajouter un nouvel utilisateur
     */
    public function addUser($username, $password, $role = 'user') {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            return $stmt->execute([$username, $passwordHash, $role]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Contrainte unique violée
                return false; // Utilisateur existe déjà
            }
            error_log('Erreur lors de l\'ajout d\'utilisateur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir tous les utilisateurs
     */
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, role, created_at, last_login, is_active FROM users ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des utilisateurs : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($username) {
        try {
            // Ne pas supprimer l'admin
            if ($username === 'admin') {
                return false;
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE username = ?");
            return $stmt->execute([$username]);
        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression d\'utilisateur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Désactiver/Activer un utilisateur
     */
    public function toggleUserStatus($username) {
        try {
            // Ne pas désactiver l'admin
            if ($username === 'admin') {
                return false;
            }
            
            $stmt = $this->pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE username = ?");
            return $stmt->execute([$username]);
        } catch (PDOException $e) {
            error_log('Erreur lors du changement de statut utilisateur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Changer le mot de passe d'un utilisateur
     */
    public function changePassword($username, $newPassword) {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
            return $stmt->execute([$passwordHash, $username]);
        } catch (PDOException $e) {
            error_log('Erreur lors du changement de mot de passe : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour la dernière connexion
     */
    private function updateLastLogin($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log('Erreur lors de la mise à jour de la dernière connexion : ' . $e->getMessage());
        }
    }

    /**
     * Vérifier si un utilisateur existe
     */
    public function userExists($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log('Erreur lors de la vérification d\'existence utilisateur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les informations d'un utilisateur
     */
    public function getUserInfo($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, role, created_at, last_login, is_active FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des infos utilisateur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enregistrer un entry d'historique d'import
     */
    public function saveImportHistory($userId, $username, $scriptName, $arguments, $output, $success, $importDate = null, $groupId = null, $actionType = 'execute') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO import_history 
                (user_id, username, script_name, arguments, output, success, import_date, group_id, action_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            return $stmt->execute([
                $userId,
                $username,
                $scriptName,
                $arguments,
                $output,
                $success ? 1 : 0,
                $importDate,
                $groupId,
                $actionType
            ]);
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'enregistrement de l\'historique : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer l'historique des imports
     */
    public function getImportHistory($limit = 50) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    id,
                    user_id,
                    username,
                    script_name,
                    arguments,
                    output,
                    success,
                    server_date,
                    import_date,
                    group_id,
                    action_type
                FROM import_history 
                ORDER BY server_date DESC 
                LIMIT ?
            ");
            
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'historique : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer l'historique des imports d'un utilisateur spécifique
     */
    public function getImportHistoryByUser($userId, $limit = 50) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    id,
                    user_id,
                    username,
                    script_name,
                    arguments,
                    output,
                    success,
                    server_date,
                    import_date,
                    group_id,
                    action_type
                FROM import_history 
                WHERE user_id = ?
                ORDER BY server_date DESC 
                LIMIT ?
            ");
            
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'historique utilisateur : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Supprimer les anciens enregistrements d'historique (plus de X jours)
     */
    public function cleanOldHistory($days = 90) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM import_history 
                WHERE server_date < datetime('now', '-' || ? || ' days')
            ");
            
            return $stmt->execute([$days]);
        } catch (PDOException $e) {
            error_log('Erreur lors du nettoyage de l\'historique : ' . $e->getMessage());
            return false;
        }
    }
}

// Instance globale de la base de données
$userDB = new UserDatabase();
?>
