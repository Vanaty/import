<?php
require_once 'auth.php';
requireLogin(); // Vérifier que l'utilisateur est connecté
checkSessionTimeout(); // Vérifier le timeout de session
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import de Fichiers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-file-import text-primary"></i> Import
                    </h4>
                    
                    <!-- User info and logout -->
                    <div class="user-info mb-3 p-2 bg-white rounded">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                <?php echo htmlspecialchars(getUsername()); ?>
                            </small>
                            <a href="?logout=1" class="btn btn-sm btn-outline-danger" title="Déconnexion">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-section="execute">
                                <i class="fas fa-play"></i> Importer Données
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-section="history">
                                <i class="fas fa-history"></i> Historique
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php" data-section="admin">
                                <i class="fas fa-users-cog"></i> Administration
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Importation des données</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearOutput()">
                                <i class="fas fa-trash"></i> Vider
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Execute Section -->
                <div id="execute-section" class="content-section">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-cog"></i> Configuration d'Import</h5>
                                </div>
                                <div class="card-body p-4">
                                    <form id="pythonForm">
                                        <!-- 
                                            Configuration pour le dossier du script a executer
                                        -->
                                        <input type="hidden" name="script" value="traccar-data">
                                        
                                        <div class="mb-4">
                                            <label for="groupSelect" class="form-label fw-bold">
                                                <i class="fas fa-users text-primary me-2"></i>Groupe
                                            </label>
                                            <select class="form-select form-select-lg" id="groupSelect" name="group">
                                                <option value="8">Rapport</option>
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label for="dateInput" class="form-label fw-bold">
                                                <i class="fas fa-calendar-alt text-danger me-2"></i>Date d'import
                                            </label>
                                            <input type="date" class="form-control form-control-lg" id="dateInput" name="date" required>
                                        </div>
                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-primary py-3 mt-3">
                                                <i class="fas fa-file-import me-2"></i> Lancer l'Import
                                            </button>
                                            <button type="button" class="btn btn-danger py-3 mt-3" id="deleteButton">
                                                <i class="fas fa-trash me-2"></i> Supprimer
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5><i class="fas fa-terminal"></i> Sortie</h5>
                                    <div class="status-indicator" id="statusIndicator">
                                        <span class="badge bg-secondary">Prêt</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="output" class="mt-2 p-3 bg-dark text-light border rounded output-container">
                                        <p class="text-muted">Le résultat de l'import apparaîtra ici...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Section -->
                <div id="history-section" class="content-section d-none">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-history"></i> Historique des Imports</h5>
                        </div>
                        <div class="card-body">
                            <div id="historyContainer">
                                <!-- History will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Section -->
                <div id="admin-section" class="content-section d-none">
                    <?php if (isAdmin()): ?>
                        <iframe src="admin.php" style="width: 100%; height: 80vh; border: none;"></iframe>
                    <?php else: ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> Accès refusé. Vous n'êtes pas administrateur.
                        </div>
                    <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
