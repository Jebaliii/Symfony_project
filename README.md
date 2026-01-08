# 🏨 SmartStay - Plateforme de Réservation d'Hôtels

## 📋 Description

SmartStay est une plateforme web de réservation d'hôtels développée avec Symfony 6.4. L'application permet de gérer 24 villes tunisiennes avec leurs hôtels et réservations, incluant une carte interactive avec Leaflet.js.

## ✨ Fonctionnalités

### 🔐 Authentification
- Inscription et connexion sécurisées
- Hash des mots de passe avec bcrypt/argon2
- Gestion des rôles (ROLE_USER, ROLE_ADMIN)
- Protection CSRF

### 🏨 Gestion des Hôtels (CRUD)
- Créer, lire, modifier, supprimer des hôtels
- Informations : nom, description, adresse, prix, chambres disponibles
- Association avec les villes
- Images des hôtels

### 🗺️ Carte Interactive
- Carte Leaflet.js avec 24 villes tunisiennes
- Marqueurs cliquables avec popups
- Coordonnées GPS précises
- Frontières des villes (polygones GeoJSON)

### 📅 Réservations
- Système de réservation d'hôtels
- Dates de check-in/check-out
- Méthodes de paiement
- Statuts (pending, confirmed, cancelled)

### 👨‍💼 Panel Admin
- Dashboard moderne avec Bootstrap 5
- Statistiques
- Gestion des hôtels et réservations

## 🛠️ Technologies Utilisées

### Backend
- **Framework** : Symfony 6.4 (LTS)
- **Langage** : PHP 8.1+
- **ORM** : Doctrine
- **Base de données** : MySQL
- **Templates** : Twig

### Frontend
- **CSS Framework** : Bootstrap 5
- **Carte** : Leaflet.js
- **Icons** : Font Awesome

### Outils
- **Composer** : Gestion des dépendances PHP
- **Symfony CLI** : Serveur de développement
- **Doctrine Migrations** : Versioning de la BDD
- **Fixtures** : Données de test

## 📦 Installation

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- MySQL 5.7 ou supérieur
- Symfony CLI (optionnel mais recommandé)

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone https://github.com/VOTRE_USERNAME/smartstay.git
cd smartstay
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer la base de données**
```bash
# Copier le fichier .env
cp .env .env.local

# Éditer .env.local et configurer DATABASE_URL
# DATABASE_URL="mysql://root:@127.0.0.1:3306/booking?serverVersion=8.0"
```

4. **Créer la base de données**
```bash
php bin/console doctrine:database:create
```

5. **Exécuter les migrations**
```bash
php bin/console doctrine:migrations:migrate
```

6. **Charger les fixtures (données de test)**
```bash
php bin/console doctrine:fixtures:load
```

7. **Démarrer le serveur**
```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

8. **Accéder à l'application**
```
http://localhost:8000
```

## 👤 Comptes de test

Après avoir chargé les fixtures, vous pouvez utiliser :
- **Email** : demo@smartstay.com
- **Password** : demo123

## 📁 Structure du Projet

```
Project1_sf6/
├── config/              # Configuration Symfony
│   ├── packages/        # Configuration des bundles
│   └── routes/          # Routes
├── migrations/          # Migrations Doctrine
├── public/              # Point d'entrée web
├── src/
│   ├── Command/         # Commandes console
│   ├── Controller/      # Contrôleurs
│   ├── DataFixtures/    # Fixtures (données de test)
│   ├── Entity/          # Entités Doctrine
│   ├── Form/            # Formulaires
│   └── Repository/      # Repositories Doctrine
├── templates/           # Templates Twig
├── var/                 # Cache et logs
└── vendor/              # Dépendances Composer
```

## 🗄️ Modèle de Données

### Entités principales
- **User** : Utilisateurs (email, password, role)
- **Hotel** : Hôtels (name, description, address, price, rooms)
- **City** : Villes (name, latitude, longitude, boundary)
- **Reservation** : Réservations (dates, payment, status)

### Relations
- User (1) → (N) Reservation
- Hotel (1) → (N) Reservation
- City (1) → (N) Hotel

## 🚀 Commandes Utiles

```bash
# Vider le cache
php bin/console cache:clear

# Créer une entité
php bin/console make:entity

# Créer une migration
php bin/console make:migration

# Créer un contrôleur
php bin/console make:controller

# Lister les routes
php bin/console debug:router

# Hasher un mot de passe
php bin/console security:hash-password
```

## 📝 Licence

Ce projet est développé dans un cadre éducatif.

## 👨‍💻 Auteur

Développé avec ❤️ pour la validation Symfony

## 🙏 Remerciements

- Symfony Framework
- Doctrine ORM
- Leaflet.js
- Bootstrap

