# Guide de Démarrage Rapide - Authentification SQLite

## 🚀 Installation en 3 étapes

### 1. Installation automatique
```
http://localhost/import/install.php
```

### 2. Connexion
```
http://localhost/import/
Utilisateur : admin
Mot de passe : admin123
```

### 3. Vérification (optionnel)
```
http://localhost/import/check.php
```

## 📋 Fonctionnalités principales

### Authentification
- ✅ Login/logout sécurisé
- ✅ Sessions avec timeout (2h)
- ✅ Protection de toutes les pages

### Gestion des utilisateurs (admin)
- ➕ **Ajouter** : utilisateurs avec rôles admin/user
- 🔄 **Activer/Désactiver** : comptes utilisateur
- 🔑 **Changer** : mots de passe
- 🗑️ **Supprimer** : utilisateurs (sauf admin)
- 📊 **Voir** : historique des connexions

### Base de données
- 💾 **SQLite** : stockage persistant dans `data/users.db`
- 🔒 **Sécurisé** : mots de passe hashés, protection fichiers
- 🚫 **Protégé** : accès direct bloqué par .htaccess

## ⚙️ Configuration

### Changer le mot de passe admin par défaut
**Fichier** : `config/config.php`
```php
define('DEFAULT_ADMIN_PASSWORD', 'votre_nouveau_mot_de_passe');
```

### Ajuster le timeout
```php
define('SESSION_TIMEOUT', 3600); // 1 heure
```

## 🔧 Administration

### Accès admin
1. Connectez-vous avec le compte admin
2. Cliquez sur "Administration" dans le menu
3. Gérez les utilisateurs depuis l'interface

### Ajouter un utilisateur
1. **Administration** → **Ajouter un utilisateur**
2. Saisissez : nom, mot de passe, rôle
3. Cliquez "Ajouter"

### Changer un mot de passe
1. **Administration** → **Changer le mot de passe**
2. Sélectionnez l'utilisateur
3. Saisissez le nouveau mot de passe

## 🛠️ Maintenance

### Sauvegarde de la base
```bash
copy "data\users.db" "backup\users_backup.db"
```

### Reset complet
1. Supprimer `data/users.db`
2. Relancer `/install.php`

### Diagnostic
```
http://localhost/import/check.php
```

## 🔐 Sécurité

### ✅ Implémenté
- Hachage bcrypt des mots de passe
- Protection SQLite injection (requêtes préparées)
- Sessions sécurisées
- Protection .htaccess des fichiers sensibles
- Échappement HTML

### 🚀 Production
- [ ] HTTPS obligatoire
- [ ] Permissions fichiers restrictives
- [ ] Monitoring des connexions
- [ ] Sauvegarde automatique

## 🚨 Dépannage

### Problème de connexion
1. Vérifiez `/check.php`
2. Mot de passe dans `config/config.php`
3. Permissions dossier `data/`

### Base corrompue
1. Sauvegarder si possible
2. Supprimer `data/users.db`
3. Relancer `/install.php`

### Erreur permissions
```bash
# Windows
icacls data /grant IIS_IUSRS:M
# Ou donner droits complets au dossier data
```

---

**Support** : Consultez `AUTHENTICATION.md` pour la documentation complète
