# 📂 FICHIERS IMPORTANTS À CONNAÎTRE - VALIDATION

## 🎯 FICHIERS À OUVRIR PENDANT LA DÉMONSTRATION

### 1️⃣ ENTITÉS (src/Entity/)

#### ⭐ Hotel.php - L'entité principale
**Chemin** : `src/Entity/Hotel.php`
**Points clés à montrer** :
- Attributs PHP 8 : `#[ORM\Entity]`, `#[ORM\Column]`
- Relation ManyToOne avec City : `#[ORM\ManyToOne(inversedBy: 'hotels')]`
- Relation OneToMany avec Reservation
- Méthode `__toString()` pour EntityType

#### ⭐ User.php - Authentification
**Chemin** : `src/Entity/User.php`
**Points clés à montrer** :
- Implémente `UserInterface` et `PasswordAuthenticatedUserInterface`
- Méthode `getRoles()` : retourne `[$this->role]`
- Méthode `getUserIdentifier()` : retourne l'email
- Hash du mot de passe

#### City.php - Villes tunisiennes
**Chemin** : `src/Entity/City.php`
**Points clés** :
- Coordonnées GPS (latitude, longitude)
- Boundary (polygone JSON)
- Relation OneToMany avec Hotel

#### Reservation.php - Réservations
**Chemin** : `src/Entity/Reservation.php`
**Points clés** :
- Relations ManyToOne avec User et Hotel
- Dates (checkInDate, checkOutDate)
- Status (pending, confirmed, cancelled)

---

### 2️⃣ CONTRÔLEURS (src/Controller/)

#### ⭐ Admin/HotelController.php - CRUD complet
**Chemin** : `src/Controller/Admin/HotelController.php`
**Points clés à montrer** :
- Route préfixe : `#[Route('/admin/hotels')]`
- Injection de dépendances : `EntityManagerInterface`, `HotelRepository`
- CRUD complet : index, new, show, edit, delete
- Gestion formulaire : `handleRequest()`, `isSubmitted()`, `isValid()`
- Persist & Flush
- Flash messages
- Protection CSRF sur delete

#### AuthController.php - Authentification
**Chemin** : `src/Controller/AuthController.php`
**Points clés** :
- Login : `AuthenticationUtils`
- Signup : `UserPasswordHasherInterface`
- Hash du mot de passe : `hashPassword()`
- Redirection si déjà connecté

#### DashboardController.php - Dashboard utilisateur
**Chemin** : `src/Controller/DashboardController.php`
**Points clés** :
- Protection : `#[IsGranted('ROLE_USER')]`
- Récupération des villes avec hôtels
- Transformation des données pour la carte

---

### 3️⃣ FORMULAIRES (src/Form/)

#### ⭐ HotelType.php - Formulaire hôtel
**Chemin** : `src/Form/HotelType.php`
**Points clés à montrer** :
- Hérite de `AbstractType`
- `buildForm()` : construction du formulaire
- Types de champs : TextType, TextareaType, MoneyType, IntegerType
- **EntityType** : sélection de City avec `choice_label: 'name'`
- `configureOptions()` : `data_class: Hotel::class`

#### ReservationType.php - Formulaire réservation
**Chemin** : `src/Form/ReservationType.php`
**Points clés** :
- EntityType pour User et Hotel
- DateType avec `widget: 'single_text'`
- ChoiceType pour paymentMethod et status

---

### 4️⃣ CONFIGURATION (config/)

#### ⭐ packages/security.yaml - Sécurité
**Chemin** : `config/packages/security.yaml`
**Points clés à montrer** :
- `password_hashers` : algorithme de hash
- `providers` : chargement des utilisateurs (Entity User, property email)
- `firewalls` : 
  - `form_login` : configuration du login
  - `logout` : configuration de la déconnexion
- `access_control` : protection des routes par rôle

#### packages/doctrine.yaml - Doctrine
**Chemin** : `config/packages/doctrine.yaml`
**Points clés** :
- `dbal.url` : connexion à la base de données
- `orm.auto_mapping` : mapping automatique des entités
- `orm.naming_strategy` : convention de nommage

#### services.yaml - Services
**Chemin** : `config/services.yaml`
**Points clés** :
- `_defaults.autowire: true` : injection automatique
- `_defaults.autoconfigure: true` : configuration automatique
- Exclusions : Entity, Kernel

#### routes.yaml - Routes
**Chemin** : `config/routes.yaml`
**Points clés** :
- Chargement automatique des contrôleurs avec attributs

---

### 5️⃣ TEMPLATES (templates/)

#### ⭐ admin/dashboard.html.twig - Dashboard admin
**Chemin** : `templates/admin/dashboard.html.twig`
**Points clés** :
- Design moderne avec Bootstrap 5
- Sidebar de navigation
- Cartes de gestion (Hôtels, Réservations)
- Statistiques

#### admin/hotel/index.html.twig - Liste des hôtels
**Chemin** : `templates/admin/hotel/index.html.twig`
**Points clés** :
- Tableau avec tous les hôtels
- Boutons d'action (Voir, Modifier, Supprimer)
- Formulaire de suppression avec CSRF token

#### base.html.twig - Template de base
**Chemin** : `templates/base.html.twig`
**Points clés** :
- Structure HTML de base
- Blocs Twig : `{% block title %}`, `{% block body %}`
- Inclusion de Bootstrap et assets

---

### 6️⃣ REPOSITORIES (src/Repository/)

#### HotelRepository.php - Requêtes personnalisées
**Chemin** : `src/Repository/HotelRepository.php`
**Points clés à montrer** :
- Hérite de `ServiceEntityRepository`
- Méthode `findByCity()` : QueryBuilder
- `createQueryBuilder('h')` : alias 'h' pour Hotel
- `setParameter()` : paramètres sécurisés

---

### 7️⃣ FIXTURES (src/DataFixtures/)

#### CityCoordinatesFixture.php - Coordonnées GPS
**Chemin** : `src/DataFixtures/CityCoordinatesFixture.php`
**Points clés** :
- 24 villes tunisiennes avec latitude/longitude
- Méthode `load()` : chargement des données
- Utilisation du CityRepository

#### CityBoundariesFixture.php - Frontières des villes
**Chemin** : `src/DataFixtures/CityBoundariesFixture.php`
**Points clés** :
- Polygones de frontières en JSON
- Stockage dans le champ `boundary`

---

### 8️⃣ COMMANDES (src/Command/)

#### SeedCitiesCommand.php - Seeding
**Chemin** : `src/Command/SeedCitiesCommand.php`
**Points clés** :
- Attribut `#[AsCommand]`
- Méthode `execute()`
- Création de villes et hôtels
- SymfonyStyle pour l'affichage

---

### 9️⃣ MIGRATIONS (migrations/)

#### Version20251204000000.php - Exemple de migration
**Chemin** : `migrations/Version20251204000000.php`
**Points clés** :
- Méthode `up()` : ajout de colonnes latitude/longitude
- Méthode `down()` : rollback
- SQL brut : `$this->addSql()`

---

### 🔟 CONFIGURATION RACINE

#### .env - Variables d'environnement
**Chemin** : `.env`
**Points clés** :
- `APP_ENV=dev`
- `DATABASE_URL` : connexion MySQL
- `APP_SECRET` : clé secrète

#### composer.json - Dépendances
**Chemin** : `composer.json`
**Points clés** :
- Symfony 6.4.*
- Doctrine ORM
- Twig
- Security Bundle
- Maker Bundle (dev)
- Fixtures Bundle (dev)

---

## 📋 ORDRE DE PRÉSENTATION RECOMMANDÉ

### 1. Vue d'ensemble (2 min)
- Montrer l'arborescence du projet
- Expliquer la structure MVC

### 2. Entités (3 min)
- Ouvrir `Hotel.php` → Montrer les attributs, relations
- Ouvrir `User.php` → Montrer UserInterface, getRoles()

### 3. Contrôleurs (4 min)
- Ouvrir `HotelController.php` → Expliquer le CRUD
- Montrer une méthode complète (new ou edit)
- Expliquer persist/flush

### 4. Formulaires (2 min)
- Ouvrir `HotelType.php` → Montrer EntityType

### 5. Sécurité (2 min)
- Ouvrir `security.yaml` → Expliquer firewall, access_control

### 6. Templates (2 min)
- Ouvrir `admin/hotel/index.html.twig` → Montrer Twig

### 7. Base de données (2 min)
- Montrer phpMyAdmin ou MySQL Workbench
- Montrer les tables et relations

### 8. Démonstration live (3 min)
- Créer un hôtel
- Le modifier
- Le supprimer

---

## 🎯 FICHIERS À AVOIR OUVERTS DANS L'IDE

1. `src/Entity/Hotel.php`
2. `src/Controller/Admin/HotelController.php`
3. `src/Form/HotelType.php`
4. `config/packages/security.yaml`
5. `templates/admin/hotel/index.html.twig`

---

## 💡 ASTUCES

- **Ctrl+P** (VSCode) : Recherche rapide de fichiers
- **Ctrl+Shift+F** : Recherche dans tous les fichiers
- **F12** : Aller à la définition
- Préparer des onglets avec les fichiers importants
- Avoir phpMyAdmin ouvert dans un onglet navigateur

---

**Vous êtes prêt ! 🚀**
