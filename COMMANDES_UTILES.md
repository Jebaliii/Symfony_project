# ⚙️ COMMANDES UTILES - VALIDATION SYMFONY

## 🚀 DÉMARRAGE RAPIDE

### Démarrer le serveur Symfony
```bash
# Méthode 1 : Avec Symfony CLI (recommandé)
symfony server:start

# Méthode 2 : Avec PHP natif
php -S localhost:8000 -t public/

# Méthode 3 : En arrière-plan
symfony server:start -d
```

### Arrêter le serveur
```bash
symfony server:stop
```

### Vérifier le statut
```bash
symfony server:status
```

---

## 🗄️ BASE DE DONNÉES

### Créer la base de données
```bash
php bin/console doctrine:database:create
```

### Supprimer la base de données
```bash
php bin/console doctrine:database:drop --force
```

### Exécuter les migrations
```bash
# Toutes les migrations
php bin/console doctrine:migrations:migrate

# Sans confirmation
php bin/console doctrine:migrations:migrate --no-interaction
```

### Créer une migration
```bash
php bin/console make:migration
```

### Voir le statut des migrations
```bash
php bin/console doctrine:migrations:status
```

### Rollback (annuler la dernière migration)
```bash
php bin/console doctrine:migrations:migrate prev
```

---

## 🌱 FIXTURES (DONNÉES DE TEST)

### Charger les fixtures
```bash
# Avec confirmation
php bin/console doctrine:fixtures:load

# Sans confirmation (écrase les données)
php bin/console doctrine:fixtures:load --no-interaction
```

### Ajouter des fixtures sans écraser
```bash
php bin/console doctrine:fixtures:load --append
```

---

## 🔧 GÉNÉRATION DE CODE

### Créer une entité
```bash
php bin/console make:entity

# Exemple interactif :
# Entity name: Hotel
# Field name: name
# Field type: string
# Field length: 255
# Can this field be null: no
```

### Créer un contrôleur
```bash
php bin/console make:controller HotelController
```

### Créer un formulaire
```bash
php bin/console make:form HotelType
```

### Créer un CRUD complet
```bash
php bin/console make:crud Hotel
```

### Créer une commande console
```bash
php bin/console make:command app:seed:cities
```

### Créer un repository
```bash
# Automatiquement créé avec l'entité
# Ou manuellement :
php bin/console make:repository Hotel
```

---

## 🔍 DEBUG ET INSPECTION

### Lister toutes les routes
```bash
php bin/console debug:router
```

### Chercher une route spécifique
```bash
php bin/console debug:router admin_hotel_new
```

### Lister toutes les routes d'un contrôleur
```bash
php bin/console debug:router | grep hotel
```

### Voir la configuration de sécurité
```bash
php bin/console debug:firewall
```

### Lister tous les services
```bash
php bin/console debug:container
```

### Chercher un service spécifique
```bash
php bin/console debug:container EntityManager
```

### Voir les paramètres de configuration
```bash
php bin/console debug:config
```

### Voir la configuration d'un bundle
```bash
php bin/console debug:config doctrine
```

### Voir les événements
```bash
php bin/console debug:event-dispatcher
```

---

## 🧹 CACHE

### Vider le cache
```bash
php bin/console cache:clear
```

### Vider le cache de production
```bash
php bin/console cache:clear --env=prod
```

### Réchauffer le cache
```bash
php bin/console cache:warmup
```

---

## 📊 DOCTRINE - REQUÊTES SQL

### Voir le SQL d'une migration
```bash
php bin/console doctrine:migrations:migrate --dry-run
```

### Exécuter du SQL brut
```bash
php bin/console doctrine:query:sql "SELECT * FROM hotel"
```

### Valider le schéma de la base de données
```bash
php bin/console doctrine:schema:validate
```

### Mettre à jour le schéma (ATTENTION : dangereux en prod)
```bash
php bin/console doctrine:schema:update --force
```

### Voir les différences de schéma
```bash
php bin/console doctrine:schema:update --dump-sql
```

---

## 🔐 SÉCURITÉ

### Hasher un mot de passe
```bash
php bin/console security:hash-password
```

---

## 📝 COMMANDES PERSONNALISÉES

### Exécuter une commande personnalisée
```bash
php bin/console app:seed:cities
```

---

## 🧪 TESTS

### Lancer tous les tests
```bash
php bin/phpunit
```

### Lancer un test spécifique
```bash
php bin/phpunit tests/Controller/HotelControllerTest.php
```

---

## 📦 COMPOSER (DÉPENDANCES)

### Installer les dépendances
```bash
composer install
```

### Mettre à jour les dépendances
```bash
composer update
```

### Ajouter une dépendance
```bash
composer require symfony/mailer
```

### Ajouter une dépendance de développement
```bash
composer require --dev symfony/maker-bundle
```

### Supprimer une dépendance
```bash
composer remove symfony/mailer
```

---

## 🎯 COMMANDES POUR LA VALIDATION

### Préparation complète (à exécuter avant la validation)
```bash
# 1. Vider le cache
php bin/console cache:clear

# 2. Créer la base de données (si elle n'existe pas)
php bin/console doctrine:database:create

# 3. Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# 4. Charger les fixtures
php bin/console doctrine:fixtures:load --no-interaction

# 5. Démarrer le serveur
symfony server:start
```

### Script complet (copier-coller)
```bash
php bin/console cache:clear && \
php bin/console doctrine:database:create && \
php bin/console doctrine:migrations:migrate --no-interaction && \
php bin/console doctrine:fixtures:load --no-interaction && \
symfony server:start
```

---

## 🔧 DÉPANNAGE

### Problème de permissions (cache/logs)
```bash
# Linux/Mac
chmod -R 777 var/cache var/log

# Windows (PowerShell en admin)
icacls var\cache /grant Everyone:F /t
icacls var\log /grant Everyone:F /t
```

### Régénérer l'autoload de Composer
```bash
composer dump-autoload
```

### Vérifier la version de Symfony
```bash
php bin/console --version
```

### Vérifier la configuration PHP
```bash
php -v
php -m  # Modules installés
```

---

## 📋 CHECKLIST RAPIDE AVANT VALIDATION

```bash
# 1. Vérifier que le serveur fonctionne
symfony server:status

# 2. Vérifier les routes
php bin/console debug:router | grep admin_hotel

# 3. Vérifier la base de données
php bin/console doctrine:schema:validate

# 4. Vérifier les fixtures
php bin/console doctrine:query:sql "SELECT COUNT(*) FROM hotel"

# 5. Vérifier le cache
php bin/console cache:clear
```

---

## 💡 ASTUCES

### Voir les logs en temps réel
```bash
# Linux/Mac
tail -f var/log/dev.log

# Windows (PowerShell)
Get-Content var\log\dev.log -Wait
```

### Créer un utilisateur admin rapidement
```bash
php bin/console security:hash-password
# Copier le hash
# Insérer manuellement dans la BDD ou via fixture
```

---

## 🎬 COMMANDES À MONTRER PENDANT LA VALIDATION

### 1. Lister les routes
```bash
php bin/console debug:router | Select-String "admin_hotel"
```

### 2. Voir la configuration de sécurité
```bash
php bin/console debug:firewall
```

### 3. Valider le schéma
```bash
php bin/console doctrine:schema:validate
```

### 4. Voir les services
```bash
php bin/console debug:container EntityManager
```

---

**Gardez ce document ouvert pendant la validation ! ⚡**
