<?php
// Configuration par défaut de l'administrateur
// Ce fichier ne contient que le mot de passe par défaut de l'admin
// Les utilisateurs sont stockés dans la base de données SQLite

define('DEFAULT_ADMIN_PASSWORD', 'admin123');

// Configuration de la base de données
define('DB_PATH', __DIR__ . '/../data/users.db');

// Autres configurations
define('SESSION_TIMEOUT', 28800); // 8 heures en secondes
define('APP_NAME', 'Import App');
?>
