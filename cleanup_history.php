<?php
/**
 * Script de nettoyage automatique de l'historique
 * À exécuter périodiquement via cron ou tâche planifiée
 */

require_once __DIR__ . '/database.php';

// Nettoyer l'historique plus ancien que 90 jours
$cleaned = $userDB->cleanOldHistory(90);

if ($cleaned) {
    echo "Nettoyage de l'historique effectué avec succès.\n";
} else {
    echo "Erreur lors du nettoyage de l'historique.\n";
}
?>
