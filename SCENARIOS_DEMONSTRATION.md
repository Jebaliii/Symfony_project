# 🎬 SCÉNARIOS DE DÉMONSTRATION - VALIDATION SYMFONY

## 📋 PLAN DE DÉMONSTRATION (15-20 minutes)

### 1️⃣ PRÉSENTATION GÉNÉRALE (2 min)
- Nom du projet : **SmartStay**
- Objectif : Plateforme de réservation d'hôtels en Tunisie
- Technologies : Symfony 6.4, Doctrine, Twig, Bootstrap 5
- Base de données : MySQL avec 24 villes tunisiennes

---

### 2️⃣ ARCHITECTURE DU PROJET (3 min)

#### Structure des dossiers
```
Project1_sf6/
├── config/              # Configuration (routes, security, services)
├── migrations/          # Migrations de base de données
├── public/              # Point d'entrée (index.php)
├── src/
│   ├── Controller/      # Contrôleurs (logique métier)
│   ├── Entity/          # Entités Doctrine (modèles)
│   ├── Form/            # Formulaires Symfony
│   ├── Repository/      # Requêtes personnalisées
│   ├── DataFixtures/    # Données de test
│   └── Command/         # Commandes console
├── templates/           # Templates Twig
└── var/                 # Cache et logs
```

#### Entités et leurs relations
```
User (1) ----< (N) Reservation (N) >---- (1) Hotel (N) >---- (1) City
```

---

### 3️⃣ DÉMONSTRATION FONCTIONNELLE (8 min)

#### A. Page d'accueil et Authentification
1. **Accéder à la page d'accueil** : `http://localhost:8000/`
2. **S'inscrire** : `/signup`
   - Email : test@example.com
   - Password : password123
   - Montrer le hash du mot de passe en base
3. **Se connecter** : `/login`
   - Montrer la redirection vers `/dashboard`
4. **Tester la sécurité** : Se déconnecter et essayer d'accéder à `/dashboard`
   - Montrer la redirection automatique vers `/login`

#### B. Dashboard Utilisateur
1. **Afficher le dashboard** : `/dashboard`
   - Carte interactive avec les 24 villes tunisiennes
   - Marqueurs cliquables
   - Affichage des hôtels par ville

#### C. Administration des Hôtels
1. **Accéder au panel admin** : `/admin`
2. **Lister les hôtels** : `/admin/hotels`
   - Tableau avec tous les hôtels
3. **Créer un hôtel** : `/admin/hotels/new`
   - Nom : "Grand Hotel Tunis"
   - Ville : Tunis (sélection via EntityType)
   - Prix : 150 USD
   - Chambres disponibles : 25
   - Montrer la validation du formulaire
4. **Modifier un hôtel** : `/admin/hotels/{id}/edit`
   - Changer le prix
   - Montrer la mise à jour en base
5. **Supprimer un hôtel** : `/admin/hotels/{id}/delete`
   - Montrer la protection CSRF

#### D. Gestion des Réservations
1. **Créer une réservation** : `/admin/reservations/new`
   - Sélectionner un utilisateur
   - Sélectionner un hôtel
   - Date d'arrivée / départ
   - Méthode de paiement
   - Statut : pending
2. **Lister les réservations** : `/admin/reservations`
3. **Modifier le statut** : pending → confirmed

---

### 4️⃣ EXPLICATION DU CODE (5 min)

#### A. Montrer une Entité (Hotel.php)
```php
#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'hotels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;
    
    // Expliquer : Attributs PHP 8, relation ManyToOne, nullable: false
}
```

#### B. Montrer un Contrôleur (HotelController.php)
```php
#[Route('/admin/hotels')]
class HotelController extends AbstractController
{
    #[Route('/new', name: 'admin_hotel_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($hotel);
            $em->flush();
            
            $this->addFlash('success', 'Hotel created successfully');
            return $this->redirectToRoute('admin_hotel_index');
        }

        return $this->render('admin/hotel/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

// Expliquer : Route, injection de dépendances, gestion formulaire, persist/flush
```

#### C. Montrer la Sécurité (security.yaml)
```yaml
access_control:
    - { path: ^/dashboard, roles: ROLE_USER }
    - { path: ^/login, roles: PUBLIC_ACCESS }
```

#### D. Montrer un Template Twig
```twig
{% extends 'base.html.twig' %}

{% block title %}Hotels{% endblock %}

{% block body %}
    <h1>Liste des Hôtels</h1>
    {% for hotel in hotels %}
        <div class="card">
            <h3>{{ hotel.name }}</h3>
            <p>{{ hotel.city.name }}</p>
            <p>{{ hotel.pricePerNight }} USD/nuit</p>
        </div>
    {% endfor %}
{% endblock %}
```

---

### 5️⃣ BASE DE DONNÉES (2 min)

#### Montrer la structure
```sql
-- Ouvrir phpMyAdmin ou MySQL Workbench
SHOW TABLES;

-- Montrer la table hotel
DESCRIBE hotel;

-- Montrer les relations
SELECT h.name, c.name as city_name, h.price_per_night
FROM hotel h
JOIN city c ON h.city_id = c.id
LIMIT 5;

-- Montrer les utilisateurs avec mot de passe hashé
SELECT id, email, password, role FROM user;
```

#### Montrer une migration
```php
// migrations/Version20251204000000.php
public function up(Schema $schema): void
{
    $this->addSql('ALTER TABLE city ADD latitude DECIMAL(10, 6) DEFAULT NULL');
    $this->addSql('ALTER TABLE city ADD longitude DECIMAL(10, 6) DEFAULT NULL');
}
```

---

## 🎤 QUESTIONS PROBABLES ET RÉPONSES

### Q1 : "Expliquez le cycle de vie d'une requête Symfony"
**Réponse** :
1. **Requête HTTP** arrive sur `public/index.php`
2. **Kernel** charge la configuration et les bundles
3. **Router** analyse l'URL et trouve la route correspondante
4. **Firewall** vérifie la sécurité (authentification/autorisation)
5. **Contrôleur** est appelé avec les paramètres
6. **Logique métier** s'exécute (formulaires, base de données)
7. **Template Twig** est rendu
8. **Réponse HTTP** est retournée au client

### Q2 : "Comment Doctrine gère-t-il les relations ?"
**Réponse** :
- **ManyToOne** : Plusieurs hôtels → une ville (clé étrangère `city_id` dans `hotel`)
- **OneToMany** : Une ville → plusieurs hôtels (collection dans City)
- **Cascade** : `cascade: ['persist', 'remove']` propage les opérations
- **Lazy Loading** : Les relations sont chargées à la demande
- **Eager Loading** : Utiliser `fetch: 'EAGER'` pour charger immédiatement

### Q3 : "Qu'est-ce que l'injection de dépendances ?"
**Réponse** :
Symfony injecte automatiquement les services nécessaires dans les constructeurs ou méthodes.
```php
public function index(HotelRepository $repository): Response
{
    // $repository est automatiquement injecté par Symfony
    $hotels = $repository->findAll();
}
```
Avantages : testabilité, découplage, pas de `new` manuel.

### Q4 : "Comment sécurisez-vous les formulaires ?"
**Réponse** :
1. **CSRF Token** : Protection contre les attaques CSRF
   ```php
   $this->isCsrfTokenValid('delete-hotel-'.$id, $token)
   ```
2. **Validation** : Contraintes sur les entités
   ```php
   #[Assert\NotBlank]
   #[Assert\Email]
   private ?string $email = null;
   ```
3. **Sanitization** : Twig échappe automatiquement les variables
4. **Type Hinting** : Validation des types de données

### Q5 : "Quelle est la différence entre persist() et flush() ?"
**Réponse** :
- **persist($entity)** : Indique à Doctrine de "surveiller" cette entité. Elle sera insérée/mise à jour lors du prochain flush(). Aucune requête SQL n'est exécutée.
- **flush()** : Exécute toutes les requêtes SQL en attente (INSERT, UPDATE, DELETE) en une seule transaction.
- **Analogie** : persist() = ajouter au panier, flush() = passer la commande

### Q6 : "Comment créer une requête personnalisée ?"
**Réponse** :
Dans le Repository :
```php
// HotelRepository.php
public function findByCity(int $cityId): array
{
    return $this->createQueryBuilder('h')
        ->andWhere('h.city = :cityId')
        ->setParameter('cityId', $cityId)
        ->orderBy('h.name', 'ASC')
        ->getQuery()
        ->getResult();
}
```
Utilisation dans le contrôleur :
```php
$hotels = $hotelRepository->findByCity($cityId);
```

### Q7 : "Comment gérez-vous les erreurs 404 ?"
**Réponse** :
- **ParamConverter** : Si l'entité n'existe pas, Symfony lance automatiquement une 404
  ```php
  public function show(Hotel $hotel): Response // 404 si hotel non trouvé
  ```
- **Manuel** :
  ```php
  if (!$hotel) {
      throw $this->createNotFoundException('Hotel not found');
  }
  ```

### Q8 : "Expliquez le système de templates Twig"
**Réponse** :
- **Héritage** : `{% extends 'base.html.twig' %}`
- **Blocs** : `{% block title %}Mon titre{% endblock %}`
- **Variables** : `{{ hotel.name }}`
- **Structures** : `{% for %}`, `{% if %}`
- **Filtres** : `{{ price|number_format(2) }}`
- **Fonctions** : `{{ path('route_name', {id: hotel.id}) }}`
- **Auto-escaping** : Protection XSS automatique

---

## 🔧 COMMANDES À CONNAÎTRE PAR CŒUR

```bash
# Démarrer le serveur de développement
symfony server:start
# ou
php -S localhost:8000 -t public/

# Base de données
php bin/console doctrine:database:create
php bin/console doctrine:database:drop --force
php bin/console doctrine:schema:update --force  # ⚠️ Dev seulement !

# Migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:status

# Fixtures
php bin/console doctrine:fixtures:load
php bin/console doctrine:fixtures:load --append  # Sans vider la DB

# Génération de code
php bin/console make:entity
php bin/console make:controller
php bin/console make:form
php bin/console make:crud
php bin/console make:auth

# Debug
php bin/console debug:router                    # Liste des routes
php bin/console debug:router app_login          # Détails d'une route
php bin/console debug:container                 # Liste des services
php bin/console debug:autowiring                # Services auto-wirables
php bin/console debug:firewall                  # Configuration sécurité

# Cache
php bin/console cache:clear
php bin/console cache:warmup

# Commandes personnalisées
php bin/console app:seed:cities
php bin/console app:update:hotel-images
```

---

## 📊 DIAGRAMME DES RELATIONS

```
┌─────────────┐
│    User     │
│─────────────│
│ id          │
│ email       │◄────────┐
│ password    │         │
│ role        │         │
└─────────────┘         │
                        │ ManyToOne
                        │
                  ┌─────────────┐
                  │ Reservation │
                  │─────────────│
                  │ id          │
                  │ user_id     │
                  │ hotel_id    │
                  │ checkInDate │
                  │ checkOutDate│
                  │ status      │
                  └─────────────┘
                        │
                        │ ManyToOne
                        │
                        ▼
┌─────────────┐   ┌─────────────┐
│    City     │   │    Hotel    │
│─────────────│   │─────────────│
│ id          │◄──│ id          │
│ name        │   │ name        │
│ latitude    │   │ city_id     │
│ longitude   │   │ pricePerNight│
│ boundary    │   │ availableRooms│
└─────────────┘   └─────────────┘
   OneToMany          ManyToOne
```

---

## 🎯 POINTS FORTS À METTRE EN AVANT

### 1. Architecture MVC bien structurée
- Séparation claire : Modèle (Entity), Vue (Twig), Contrôleur
- Respect des conventions Symfony
- Code organisé et maintenable

### 2. Sécurité robuste
- Authentification avec hash de mot de passe (bcrypt/argon2)
- Protection CSRF sur les formulaires
- Contrôle d'accès par rôles (ROLE_USER)
- Validation des données

### 3. Base de données bien conçue
- Relations cohérentes (ManyToOne, OneToMany)
- Migrations versionnées
- Fixtures pour les données de test
- 24 villes tunisiennes avec coordonnées GPS

### 4. Fonctionnalités complètes
- CRUD complet pour Hôtels et Réservations
- Dashboard interactif avec carte
- Panel d'administration
- Système d'authentification complet

### 5. Bonnes pratiques Symfony
- Utilisation des attributs PHP 8
- Injection de dépendances
- Repository pattern
- Form component
- Flash messages
- Commandes console personnalisées

---

## ⚠️ PIÈGES À ÉVITER

### 1. Ne pas confondre
- `persist()` ≠ `flush()`
- `ManyToOne` ≠ `OneToMany`
- `mappedBy` ≠ `inversedBy`

### 2. Erreurs courantes
- Oublier `flush()` après `persist()`
- Ne pas définir `inversedBy` et `mappedBy` correctement
- Oublier `nullable: false` sur les relations obligatoires
- Ne pas valider les formulaires avant de sauvegarder

### 3. Sécurité
- Ne JAMAIS stocker les mots de passe en clair
- Toujours valider les données utilisateur
- Utiliser les tokens CSRF
- Protéger les routes sensibles

---

## 📝 CHECKLIST FINALE AVANT VALIDATION

### Préparation technique
- [ ] Serveur Symfony démarré (`symfony server:start`)
- [ ] Base de données créée et migrée
- [ ] Fixtures chargées (villes + hôtels)
- [ ] Cache vidé (`php bin/console cache:clear`)
- [ ] Aucune erreur dans les logs (`var/log/dev.log`)

### Préparation de la démonstration
- [ ] Compte utilisateur de test créé
- [ ] Quelques hôtels créés
- [ ] Quelques réservations créées
- [ ] Navigateur ouvert sur la page d'accueil
- [ ] IDE ouvert sur les fichiers principaux

### Connaissances à maîtriser
- [ ] Expliquer le cycle de vie d'une requête
- [ ] Expliquer les relations Doctrine
- [ ] Expliquer l'authentification Symfony
- [ ] Expliquer persist() vs flush()
- [ ] Expliquer les routes et contrôleurs
- [ ] Expliquer les formulaires Symfony
- [ ] Expliquer Twig et l'héritage de templates

### Fichiers à avoir sous la main
- [ ] `src/Entity/Hotel.php`
- [ ] `src/Entity/User.php`
- [ ] `src/Controller/Admin/HotelController.php`
- [ ] `src/Form/HotelType.php`
- [ ] `config/packages/security.yaml`
- [ ] `templates/admin/dashboard.html.twig`

---

## 🎬 SCRIPT DE DÉMONSTRATION (Minute par minute)

### Minute 0-2 : Introduction
"Bonjour, je vais vous présenter **SmartStay**, une plateforme de réservation d'hôtels développée avec Symfony 6.4. Le projet permet de gérer 24 villes tunisiennes avec leurs hôtels et réservations."

### Minute 2-5 : Architecture
"Voici la structure du projet [montrer l'arborescence]. Nous avons 5 entités principales : User, Hotel, City, Reservation et Dashboard. Les relations sont : User → Reservation ← Hotel ← City."

### Minute 5-8 : Démonstration Authentification
"Je vais créer un compte [/signup], puis me connecter [/login]. Vous voyez que le mot de passe est hashé en base de données [montrer phpMyAdmin]. Si je me déconnecte et essaie d'accéder au dashboard, je suis redirigé vers le login [montrer]."

### Minute 8-12 : CRUD Hôtels
"Accédons au panel admin [/admin]. Je vais créer un nouvel hôtel [/admin/hotels/new]. Le formulaire utilise EntityType pour sélectionner la ville. Après soumission, l'hôtel est persisté et flushed en base [montrer le code]. Je peux le modifier [/edit] ou le supprimer avec protection CSRF."

### Minute 12-15 : Code et Explications
"Regardons le code du contrôleur [HotelController.php]. Vous voyez l'injection de dépendances, la gestion du formulaire, persist/flush. Voici l'entité Hotel [Hotel.php] avec la relation ManyToOne vers City."

### Minute 15-17 : Sécurité
"La sécurité est configurée dans security.yaml [montrer]. Le firewall intercepte les requêtes, form_login gère l'authentification, et access_control protège les routes."

### Minute 17-20 : Questions
"Je suis prêt à répondre à vos questions."

---

**Vous êtes maintenant prêt pour votre validation ! 💪🚀**


