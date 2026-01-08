# 📚 GUIDE DE RÉVISION - VALIDATION SYMFONY

## 🎯 Vue d'ensemble du projet : **SmartStay** (Système de réservation d'hôtels)

### Technologies utilisées
- **Framework** : Symfony 6.4 (LTS)
- **PHP** : >= 8.1
- **Base de données** : MySQL (MariaDB 10.4) - Base : `booking`
- **ORM** : Doctrine
- **Template Engine** : Twig
- **Frontend** : Bootstrap 5.3, Stimulus, Turbo
- **Sécurité** : Symfony Security Bundle

---

## 📁 STRUCTURE DU PROJET

### Entités principales (src/Entity/)
1. **User** - Gestion des utilisateurs
2. **Hotel** - Gestion des hôtels
3. **City** - Gestion des villes tunisiennes (24 villes)
4. **Reservation** - Gestion des réservations
5. **Dashboard** - Entité pour le tableau de bord

### Contrôleurs (src/Controller/)
1. **HomeController** - Page d'accueil (`/`)
2. **AuthController** - Authentification (login, signup, logout)
3. **DashboardController** - Tableau de bord utilisateur (`/dashboard`)
4. **Admin/AdminDashboardController** - Dashboard admin (`/admin`)
5. **Admin/HotelController** - CRUD Hôtels (`/admin/hotels`)
6. **Admin/ReservationController** - CRUD Réservations (`/admin/reservations`)

### Formulaires (src/Form/)
1. **LoginFormType** - Formulaire de connexion
2. **SignupFormType** - Formulaire d'inscription
3. **HotelType** - Formulaire hôtel (avec EntityType pour City)
4. **ReservationType** - Formulaire réservation

---

## 🔐 SÉCURITÉ (config/packages/security.yaml)

### Configuration de sécurité
```yaml
password_hashers:
    - PasswordAuthenticatedUserInterface: 'auto'

providers:
    - app_user_provider (Entity: User, property: email)

firewalls:
    - main:
        - form_login (login_path: app_login, check_path: app_login_check)
        - logout (path: app_logout, target: app_login)

access_control:
    - /dashboard → ROLE_USER
    - /login, /signup → PUBLIC_ACCESS
```

### Authentification
- **Login** : `/login` (email + password)
- **Signup** : `/signup` (création compte avec hash password)
- **Logout** : `/logout`
- **Redirection** : Après login → `/dashboard`

---

## 🗄️ BASE DE DONNÉES

### Configuration (.env)
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/booking?serverVersion=10.4&charset=utf8mb4"
```

### Migrations (migrations/)
- Version20251017204019.php
- Version20251018082745.php
- Version20251028120000.php
- Version20251120000000.php
- Version20251204000000.php

### Commandes Doctrine importantes
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures
php bin/console doctrine:fixtures:load --append

# Vider le cache
php bin/console cache:clear
```

---

## 🏨 ENTITÉ HOTEL (Détails)

### Propriétés
- `id` : int (auto-increment)
- `name` : string(100)
- `description` : text (nullable)
- `address` : string(255) (nullable)
- `pricePerNight` : decimal(10,2)
- `availableRooms` : int
- `imageUrl` : string(500) (nullable)
- `city` : ManyToOne → City (NOT NULL)

### Relations
- **ManyToOne** avec City (inversedBy: 'hotels')
- **OneToMany** avec Reservation (mappedBy: 'hotel')

---

## 🏙️ ENTITÉ CITY (Détails)

### Propriétés
- `id` : int
- `name` : string(100)
- `latitude` : decimal(10,6) (nullable)
- `longitude` : decimal(10,6) (nullable)
- `boundary` : text (JSON - polygone de frontières)

### Relations
- **OneToMany** avec Hotel (mappedBy: 'city', cascade: persist, remove)

### 24 Villes tunisiennes
Tunis, Sfax, Sousse, Kairouan, Bizerte, Gabès, Ariana, Gafsa, Monastir, Nabeul, Hammamet, Mahdia, Tozeur, Djerba, Jendouba, Kasserine, Kébili, Manouba, Médenine, Sidi Bouzid, Siliana, Tataouine, Zaghouan, Ben Arous

---

## 👤 ENTITÉ USER (Détails)

### Propriétés
- `id` : int
- `email` : string(255) - UNIQUE
- `password` : string(255) - HASHED
- `role` : string(30) - ROLE_USER / ROLE_ADMIN

### Implémente
- `UserInterface`
- `PasswordAuthenticatedUserInterface`

### Relations
- **OneToMany** avec Reservation (mappedBy: 'user', cascade: persist, remove)

### Méthodes importantes
- `getRoles()` : retourne `[$this->role]`
- `eraseCredentials()` : vide les données sensibles temporaires
- `getUserIdentifier()` : retourne l'email

---

## 📅 ENTITÉ RESERVATION (Détails)

### Propriétés
- `id` : int
- `user` : ManyToOne → User (NOT NULL)
- `hotel` : ManyToOne → Hotel (NOT NULL)
- `checkInDate` : DateTime (DATE_MUTABLE)
- `checkOutDate` : DateTime (DATE_MUTABLE, nullable)
- `paymentMethod` : string(50) - credit_card, paypal, cash
- `status` : string(20) - pending, confirmed, cancelled
- `createdAt` : DateTime (DATETIME_MUTABLE)

### Relations
- **ManyToOne** avec User (inversedBy: 'reservations')
- **ManyToOne** avec Hotel (inversedBy: 'reservations')

---

## 🎨 FORMULAIRES SYMFONY

### HotelType (src/Form/HotelType.php)
```php
- name : TextType
- description : TextareaType (optional)
- address : TextType (optional)
- pricePerNight : MoneyType (currency: USD)
- availableRooms : IntegerType
- city : EntityType (class: City, choice_label: 'name')
```

### ReservationType (src/Form/ReservationType.php)
```php
- user : EntityType (class: User, choice_label: 'email')
- hotel : EntityType (class: Hotel, choice_label: 'name')
- checkInDate : DateType (widget: single_text)
- checkOutDate : DateType (widget: single_text, optional)
- paymentMethod : ChoiceType (credit_card, paypal, cash)
- status : ChoiceType (pending, confirmed, cancelled)
```

### SignupFormType
```php
- email : EmailType
- plainPassword : PasswordType (non mappé)
- Validation : contraintes de validation
```

---

## 🛣️ ROUTES PRINCIPALES

### Routes publiques
- `/` → `app_home` (HomeController::index)
- `/login` → `app_login` (AuthController::login)
- `/signup` → `app_signup` (AuthController::signup)
- `/logout` → `app_logout` (AuthController::logout)

### Routes protégées (ROLE_USER)
- `/dashboard` → `app_dashboard` (DashboardController::index)

### Routes Admin
- `/admin` → `admin_dashboard` (AdminDashboardController::index)
- `/admin/hotels` → `admin_hotel_index` (liste)
- `/admin/hotels/new` → `admin_hotel_new` (création)
- `/admin/hotels/{id}` → `admin_hotel_show` (détails)
- `/admin/hotels/{id}/edit` → `admin_hotel_edit` (modification)
- `/admin/hotels/{id}/delete` → `admin_hotel_delete` (suppression POST)
- `/admin/reservations` → `admin_reservation_index`
- `/admin/reservations/new` → `admin_reservation_new`
- `/admin/reservations/{id}` → `admin_reservation_show`
- `/admin/reservations/{id}/edit` → `admin_reservation_edit`
- `/admin/reservations/{id}/delete` → `admin_reservation_delete`

---

## 🔧 COMMANDES PERSONNALISÉES

### SeedCitiesCommand (app:seed:cities)
```bash
php bin/console app:seed:cities
```
- Crée 24 villes tunisiennes
- Génère 3-5 hôtels par ville
- Ajoute des images Unsplash aléatoires

### UpdateHotelImagesCommand (app:update:hotel-images)
```bash
php bin/console app:update:hotel-images
```
- Met à jour les images des hôtels existants
- Utilise Unsplash avec seed unique

---

## 📦 FIXTURES (DataFixtures/)

### CityCoordinatesFixture
- Charge les coordonnées GPS (latitude/longitude) pour 24 villes
- Utilise le CityRepository pour mise à jour ou création

### CityBoundariesFixture
- Charge les polygones de frontières pour les villes
- Format JSON : array de coordonnées [lat, lng]
- Stocké dans le champ `boundary` (TEXT)

---

## 🎯 CONCEPTS SYMFONY IMPORTANTS À CONNAÎTRE

### 1. Injection de dépendances
```php
public function __construct(
    private EntityManagerInterface $em,
    private HotelRepository $repository
) {}
```

### 2. Attributs PHP 8 (Annotations)
```php
#[Route('/admin/hotels', name: 'admin_hotel_index')]
#[IsGranted('ROLE_USER')]
#[ORM\Entity(repositoryClass: HotelRepository::class)]
```

### 3. Doctrine - Persist & Flush
```php
$em->persist($entity);  // Prépare l'entité
$em->flush();           // Exécute les requêtes SQL
```

### 4. Flash Messages
```php
$this->addFlash('success', 'Message de succès');
$this->addFlash('error', 'Message d\'erreur');
```

### 5. Redirection
```php
return $this->redirectToRoute('route_name', ['id' => $id]);
```

### 6. Rendu de template
```php
return $this->render('template.html.twig', [
    'variable' => $value
]);
```

### 7. ParamConverter (automatique)
```php
public function show(Hotel $hotel): Response
// Symfony récupère automatiquement l'hôtel par ID
```

### 8. CSRF Protection
```php
$this->isCsrfTokenValid('delete-hotel-'.$hotel->getId(), $token)
```

---

## 🔍 QUESTIONS FRÉQUENTES DU PROF

### Q1 : Quelle est la différence entre persist() et flush() ?
**R** : `persist()` indique à Doctrine de gérer l'entité (la prépare), `flush()` exécute réellement les requêtes SQL en base de données.

### Q2 : Comment fonctionne l'authentification dans Symfony ?
**R** : Via le Security Bundle :
1. Firewall intercepte les requêtes
2. form_login gère le formulaire
3. UserProvider charge l'utilisateur depuis la DB
4. Password hasher vérifie le mot de passe
5. Session stocke l'authentification

### Q3 : Qu'est-ce qu'une relation ManyToOne ?
**R** : Plusieurs entités (Many) pointent vers une seule entité (One). Exemple : Plusieurs hôtels appartiennent à une ville.

### Q4 : Comment créer une migration ?
**R** :
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### Q5 : Qu'est-ce qu'un Repository ?
**R** : Classe qui gère les requêtes personnalisées pour une entité. Hérite de ServiceEntityRepository.

### Q6 : Comment valider un formulaire ?
**R** :
```php
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    // Traitement
}
```

### Q7 : Qu'est-ce que Twig ?
**R** : Moteur de template de Symfony. Syntaxe : `{{ variable }}`, `{% if %}`, `{% for %}`

### Q8 : Comment protéger une route ?
**R** : Via `access_control` dans security.yaml ou `#[IsGranted('ROLE_USER')]` sur le contrôleur.

---

## ✅ CHECKLIST AVANT VALIDATION

- [ ] Le serveur démarre sans erreur (`symfony server:start`)
- [ ] La base de données est créée et migrée
- [ ] Les fixtures sont chargées (villes + hôtels)
- [ ] Login/Signup fonctionnent
- [ ] CRUD Hôtels fonctionne (Create, Read, Update, Delete)
- [ ] CRUD Réservations fonctionne
- [ ] Dashboard admin s'affiche correctement
- [ ] Les relations entre entités sont correctes
- [ ] La sécurité fonctionne (redirection si non connecté)

---

## 🚀 COMMANDES ESSENTIELLES

```bash
# Démarrer le serveur
symfony server:start

# Créer la base
php bin/console doctrine:database:create

# Migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Fixtures
php bin/console doctrine:fixtures:load

# Créer une entité
php bin/console make:entity

# Créer un contrôleur
php bin/console make:controller

# Créer un formulaire
php bin/console make:form

# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router

# Voir la configuration de sécurité
php bin/console debug:firewall
```

---

## 💡 CONSEILS POUR LA VALIDATION

1. **Soyez précis** : Expliquez avec des exemples concrets de votre code
2. **Montrez le code** : Ouvrez les fichiers pendant l'explication
3. **Démonstration live** : Montrez l'application qui fonctionne
4. **Expliquez les choix** : Pourquoi ManyToOne ? Pourquoi cette validation ?
5. **Connaissez vos entités** : Relations, propriétés, types
6. **Maîtrisez les routes** : Sachez quelle route fait quoi
7. **Comprenez la sécurité** : Firewall, roles, authentication

---

## 🎓 POINTS CLÉS À RETENIR

✅ **Symfony 6.4** = Version LTS (Long Term Support)
✅ **Doctrine** = ORM pour gérer la base de données
✅ **Twig** = Moteur de templates
✅ **Security Bundle** = Gestion authentification/autorisation
✅ **Form Component** = Création et validation de formulaires
✅ **Maker Bundle** = Génération de code (make:entity, make:controller...)
✅ **Fixtures** = Données de test pour la base de données
✅ **Migrations** = Versioning du schéma de base de données

---

**Bonne chance pour votre validation ! 🍀**


