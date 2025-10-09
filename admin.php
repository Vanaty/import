<?php
require_once 'auth.php';

// Vérifier que l'utilisateur est connecté et est admin
requireLogin();

if (!isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo "Accès refusé. Seul l'administrateur peut accéder à cette page.";
    exit;
}

global $userDB;

$message = '';
$messageType = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_user':
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            if (empty($username) || empty($password)) {
                $message = 'Tous les champs sont requis.';
                $messageType = 'danger';
            } elseif ($password !== $confirm_password) {
                $message = 'Les mots de passe ne correspondent pas.';
                $messageType = 'danger';
            } elseif ($userDB->userExists($username)) {
                $message = 'Cet utilisateur existe déjà.';
                $messageType = 'danger';
            } elseif ($userDB->addUser($username, $password, $role)) {
                $message = "Utilisateur '$username' ajouté avec succès.";
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de l\'ajout de l\'utilisateur.';
                $messageType = 'danger';
            }
            break;
            
        case 'delete_user':
            $username = $_POST['username'] ?? '';
            if ($userDB->deleteUser($username)) {
                $message = "Utilisateur '$username' supprimé avec succès.";
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la suppression de l\'utilisateur.';
                $messageType = 'danger';
            }
            break;
            
        case 'toggle_status':
            $username = $_POST['username'] ?? '';
            if ($userDB->toggleUserStatus($username)) {
                $message = "Statut de l'utilisateur '$username' modifié avec succès.";
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la modification du statut.';
                $messageType = 'danger';
            }
            break;
            
        case 'change_password':
            $username = $_POST['username'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_new_password = $_POST['confirm_new_password'] ?? '';
            
            if (empty($new_password)) {
                $message = 'Le nouveau mot de passe est requis.';
                $messageType = 'danger';
            } elseif ($new_password !== $confirm_new_password) {
                $message = 'Les nouveaux mots de passe ne correspondent pas.';
                $messageType = 'danger';
            } elseif ($userDB->changePassword($username, $new_password)) {
                $message = "Mot de passe de '$username' modifié avec succès.";
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la modification du mot de passe.';
                $messageType = 'danger';
            }
            break;
    }
}

// Récupérer tous les utilisateurs
$users = $userDB->getAllUsers();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Import App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-users-cog me-2"></i>Administration des Utilisateurs
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à l'application
                            </a>
                        </div>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-user-plus me-2"></i>Ajouter un utilisateur</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="add_user">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Nom d'utilisateur</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Rôle</label>
                                        <select class="form-control" id="role" name="role">
                                            <option value="user">Utilisateur</option>
                                            <option value="admin">Administrateur</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus me-2"></i>Ajouter
                                    </button>
                                </form>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="fas fa-users me-2"></i>Utilisateurs existants</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Utilisateur</th>
                                                <th>Rôle</th>
                                                <th>Statut</th>
                                                <th>Dernière connexion</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-user me-1"></i>
                                                        <?php echo htmlspecialchars($user['username']); ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($user['role'] === 'admin'): ?>
                                                            <span class="badge bg-danger">Admin</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-primary">User</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($user['is_active']): ?>
                                                            <span class="badge bg-success">Actif</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactif</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php 
                                                            if ($user['last_login']) {
                                                                echo date('d/m/Y H:i', strtotime($user['last_login']));
                                                            } else {
                                                                echo 'Jamais';
                                                            }
                                                            ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <?php if ($user['username'] !== 'admin'): ?>
                                                            <div class="btn-group btn-group-sm">
                                                                <!-- Toggle Status -->
                                                                <form method="POST" class="d-inline">
                                                                    <input type="hidden" name="action" value="toggle_status">
                                                                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                                                                    <button type="submit" class="btn btn-outline-warning btn-sm" title="Activer/Désactiver">
                                                                        <i class="fas fa-toggle-on"></i>
                                                                    </button>
                                                                </form>
                                                                
                                                                <!-- Delete User -->
                                                                <form method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                                    <input type="hidden" name="action" value="delete_user">
                                                                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted"><i class="fas fa-lock"></i></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Total : <?php echo count($users); ?> utilisateur(s)
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section changement de mot de passe -->
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-key me-2"></i>Changer le mot de passe</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="change_password">
                                    <div class="mb-3">
                                        <label for="change_username" class="form-label">Utilisateur</label>
                                        <select class="form-control" id="change_username" name="username" required>
                                            <option value="">Sélectionner un utilisateur</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?php echo htmlspecialchars($user['username']); ?>">
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_new_password" class="form-label">Confirmer le nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                                    </div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
