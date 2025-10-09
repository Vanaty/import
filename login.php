<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$info = '';

// Gestion des messages
if (isset($_GET['timeout'])) {
    $info = 'Votre session a expiré. Veuillez vous reconnecter.';
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $userInfo = verifyUserCredentials($username, $password);
        
        if ($userInfo) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $userInfo['id'];
            $_SESSION['username'] = $userInfo['username'];
            $_SESSION['role'] = $userInfo['role'];
            $_SESSION['login_time'] = time();
            
            // Redirection vers la page demandée ou index.php
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Import de Fichiers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="login-header">
                        <h3 class="mb-0">
                            <i class="fas fa-file-import me-2"></i>
                            Import App
                        </h3>
                        <p class="mb-0 mt-2 opacity-75">Connectez-vous pour continuer</p>
                    </div>
                    <div class="login-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($info): ?>
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <?php echo htmlspecialchars($info); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nom d'utilisateur
                                </label>
                                <input type="text" class="form-control" id="username" name="username" required 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Mot de passe
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Utilisateur par défaut : admin / <?php echo htmlspecialchars(DEFAULT_ADMIN_PASSWORD); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
