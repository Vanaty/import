# Système d'Authentification SQLite - Import App

## Vue d'ensemble

Cette application dispose maintenant d'un système d'authentification complet utilisant SQLite pour stocker les utilisateurs de manière persistante.

## Architecture

### 🗃️ Base de données SQLite
- **Fichier** : `data/users.db`
- **Table** : `users` avec colonnes :
  - `id` : Identifiant unique auto-incrémenté
  - `username` : Nom d'utilisateur unique
  - `password_hash` : Mot de passe hashé avec bcrypt
  - `role` : Rôle (admin/user)
  - `created_at` : Date de création
  - `last_login` : Dernière connexion
  - `is_active` : Statut actif/inactif

### 📁 Configuration
- **Fichier** : `config/config.php`
- **Contenu** : Mot de passe par défaut admin et paramètres système
- **But** : Configuration centralisée, pas de stockage d'utilisateurs

## Fonctionnalités de sécurité

### 🔐 Authentification
- Connexion obligatoire pour accéder à l'application
- Système de session sécurisé avec timeout configurable
- Hachage des mots de passe avec PHP `password_hash()`
- Protection contre les attaques par force brute

### 👥 Gestion des utilisateurs
- **Utilisateur par défaut** : admin avec mot de passe configurable
- Interface d'administration complète :
  - Création d'utilisateurs avec rôles
  - Activation/désactivation de comptes
  - Changement de mots de passe
  - Suppression d'utilisateurs (sauf admin)
  - Historique des connexions

### 🛡️ Protection des ressources
- Toutes les pages principales protégées par authentification
- API sécurisée avec vérification de session
- Redirection automatique vers login si non authentifié
- Protection de la base de données par .htaccess

## Structure des fichiers

```
/
├── login.php              # Page de connexion
├── auth.php               # Gestion de l'authentification
├── database.php           # Classe de gestion SQLite
├── admin.php              # Interface d'administration
├── install.php            # Script d'installation
├── config/
│   └── config.php         # Configuration (mot de passe admin par défaut)
├── data/
│   └── users.db           # Base de données SQLite
├── api/
│   ├── index.php          # API protégée
│   └── history.php        # Historique protégé
└── ...
```

## Installation et utilisation

### Installation initiale
1. Accédez à `/install.php` dans votre navigateur
2. Le script créera automatiquement :
   - Les dossiers nécessaires (`config/`, `data/`)
   - La base de données SQLite
   - L'utilisateur admin par défaut
3. Notez le mot de passe admin affiché

### Première connexion
1. Accédez à l'application
2. Connectez-vous avec :
   - **Utilisateur** : `admin`
   - **Mot de passe** : voir `config/config.php` (par défaut : `admin123`)

### Administration
1. Connectez-vous en tant qu'admin
2. Accédez à "Administration" dans la sidebar
3. Gérez les utilisateurs :
   - ➕ Ajouter des utilisateurs (admin/user)
   - 🔄 Activer/désactiver des comptes
   - 🔑 Changer les mots de passe
   - 🗑️ Supprimer des utilisateurs

## Configuration

### Modifier le mot de passe admin par défaut
Éditez `config/config.php` :
```php
define('DEFAULT_ADMIN_PASSWORD', 'votre_nouveau_mot_de_passe');
```

### Ajuster le timeout de session
Dans `config/config.php` :
```php
define('SESSION_TIMEOUT', 3600); // 1 heure
```

### Chemin de la base de données
Dans `config/config.php` :
```php
define('DB_PATH', __DIR__ . '/../data/users.db');
```

## Sécurité

### ✅ Bonnes pratiques implémentées
- Hachage sécurisé des mots de passe (bcrypt)
- Requêtes préparées (protection SQL injection)
- Échappement HTML des données utilisateur
- Protection des fichiers sensibles via .htaccess
- Gestion des erreurs et logs sécurisés
- Validation côté serveur
- Sessions sécurisées

### 🔒 Protection de la base de données
- Fichier SQLite dans dossier `data/` protégé
- .htaccess bloque l'accès direct aux fichiers .db
- Permissions système recommandées : 755 pour dossiers, 644 pour fichiers

### 🚀 Améliorations pour la production
- [ ] Chiffrement de la base de données SQLite
- [ ] Authentification à deux facteurs
- [ ] Limitation des tentatives de connexion
- [ ] Logs d'audit complets
- [ ] HTTPS obligatoire
- [ ] Politique de mots de passe renforcée
- [ ] Sauvegarde automatique de la base

## Maintenance

### Sauvegarde
```bash
# Copier la base de données
cp data/users.db backup/users_$(date +%Y%m%d).db
```

### Vérification de l'intégrité
```sql
-- Se connecter à SQLite
sqlite3 data/users.db

-- Vérifier l'intégrité
PRAGMA integrity_check;

-- Voir les utilisateurs
SELECT * FROM users;
```

### Logs d'erreur
Les erreurs sont loggées dans les logs PHP du serveur. Consultez :
- `/var/log/apache2/error.log` (Linux)
- `C:\xampp\apache\logs\error.log` (Windows XAMPP)

## Dépannage

### Base de données corrompue
1. Arrêter le serveur web
2. Restaurer depuis une sauvegarde
3. Ou supprimer `data/users.db` et relancer `/install.php`

### Permissions insuffisantes
```bash
# Linux
chmod 755 data/
chmod 644 data/users.db

# Windows (via propriétés du dossier)
# Donner permissions lecture/écriture au serveur web
```

### Reset admin
```sql
sqlite3 data/users.db
UPDATE users SET password_hash = '[hash_du_nouveau_mdp]' WHERE username = 'admin';
```

## Développement

### Ajout de nouvelles fonctionnalités
- Modifier la classe `UserDatabase` dans `database.php`
- Étendre les fonctions d'authentification dans `auth.php`
- Adapter l'interface admin dans `admin.php`

### Debug
Activez les logs en modifiant temporairement dans `database.php` :
```php
error_log('Debug: ' . $message);
```
