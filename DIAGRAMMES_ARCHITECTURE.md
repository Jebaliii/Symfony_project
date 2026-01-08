# 📊 DIAGRAMMES D'ARCHITECTURE - SMARTSTAY

## 🏗️ ARCHITECTURE GLOBALE

```
┌─────────────────────────────────────────────────────────────────┐
│                         NAVIGATEUR WEB                          │
│                    (Client HTTP - Bootstrap 5)                  │
└────────────────────────────┬────────────────────────────────────┘
                             │ HTTP Request/Response
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      SYMFONY APPLICATION                        │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │   ROUTER     │→ │   SECURITY   │→ │  CONTROLLER  │         │
│  │ (Routes)     │  │ (Firewall)   │  │ (Logique)    │         │
│  └──────────────┘  └──────────────┘  └──────┬───────┘         │
│                                              │                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────▼───────┐         │
│  │     TWIG     │← │     FORM     │  │   DOCTRINE   │         │
│  │ (Templates)  │  │ (Validation) │  │    (ORM)     │         │
│  └──────────────┘  └──────────────┘  └──────┬───────┘         │
│                                              │                  │
└──────────────────────────────────────────────┼──────────────────┘
                                               │ SQL
                                               ▼
                                    ┌──────────────────┐
                                    │   MySQL (BDD)    │
                                    │  Database: booking│
                                    └──────────────────┘
```

---

## 🗄️ MODÈLE DE DONNÉES (RELATIONS)

```
┌─────────────────┐
│      USER       │
│─────────────────│
│ id (PK)         │
│ email (UK)      │
│ password (hash) │
│ role            │
└────────┬────────┘
         │ 1
         │
         │ N
         │
┌────────▼────────┐         N         ┌─────────────────┐
│  RESERVATION    │◄──────────────────►│     HOTEL       │
│─────────────────│                    │─────────────────│
│ id (PK)         │                    │ id (PK)         │
│ user_id (FK)    │                    │ name            │
│ hotel_id (FK)   │                    │ description     │
│ checkInDate     │                    │ address         │
│ checkOutDate    │                    │ pricePerNight   │
│ paymentMethod   │                    │ availableRooms  │
│ status          │                    │ city_id (FK)    │
│ createdAt       │                    └────────┬────────┘
└─────────────────┘                             │ N
                                                │
                                                │ 1
                                                │
                                    ┌───────────▼────────┐
                                    │       CITY         │
                                    │────────────────────│
                                    │ id (PK)            │
                                    │ name               │
                                    │ latitude           │
                                    │ longitude          │
                                    │ boundary (JSON)    │
                                    └────────────────────┘

Légende :
  PK = Primary Key (Clé primaire)
  FK = Foreign Key (Clé étrangère)
  UK = Unique Key (Clé unique)
  1  = Un
  N  = Plusieurs
```

---

## 🔄 CYCLE DE VIE D'UNE REQUÊTE HTTP

```
1. REQUÊTE HTTP
   │
   ▼
┌──────────────────────────────────────────────────────────────┐
│ public/index.php                                             │
│ - Point d'entrée unique                                      │
│ - Charge l'autoloader Composer                               │
│ - Initialise le Kernel                                       │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
2. KERNEL
   │ - Charge la configuration (config/)
   │ - Initialise les bundles
   │ - Crée le conteneur de services
   │
   ▼
3. ROUTER
   │ - Analyse l'URL
   │ - Trouve la route correspondante
   │ - Exemple : /admin/hotels/new → admin_hotel_new
   │
   ▼
4. SECURITY (Firewall)
   │ - Vérifie l'authentification
   │ - Vérifie les autorisations (roles)
   │ - Redirige vers /login si nécessaire
   │
   ▼
5. CONTROLLER
   │ - Méthode du contrôleur appelée
   │ - Injection des dépendances (Request, EntityManager, etc.)
   │ - Logique métier
   │
   ├─► 6a. FORMULAIRE (si applicable)
   │   │ - Création du formulaire
   │   │ - Traitement de la requête
   │   │ - Validation
   │   │
   │   ▼
   ├─► 6b. DOCTRINE (si applicable)
   │   │ - Requêtes en base de données
   │   │ - persist() / flush() / remove()
   │   │ - Retourne des entités
   │   │
   │   ▼
   └─► 7. TWIG
       │ - Rendu du template
       │ - Injection des variables
       │ - Héritage de templates
       │
       ▼
8. RÉPONSE HTTP
   │ - HTML généré
   │ - Headers HTTP
   │ - Cookies/Session
   │
   ▼
9. NAVIGATEUR
   - Affichage de la page
```

---

## 🔐 PROCESSUS D'AUTHENTIFICATION

```
┌─────────────────────────────────────────────────────────────────┐
│                    UTILISATEUR NON CONNECTÉ                     │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    Accède à /dashboard
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    FIREWALL (Security)                          │
│  - Vérifie si l'utilisateur est authentifié                     │
│  - NON → Redirige vers /login                                   │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    Affiche formulaire /login
                             │
                             ▼
                    User entre email + password
                             │
                             ▼
                    POST vers /login_check
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    USER PROVIDER                                │
│  - Charge l'utilisateur par email depuis la BDD                 │
│  - SELECT * FROM user WHERE email = ?                           │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    PASSWORD HASHER                              │
│  - Compare le hash du mot de passe                              │
│  - password_verify($plainPassword, $hashedPassword)             │
└────────────────────────────┬────────────────────────────────────┘
                             │
                    ┌────────┴────────┐
                    │                 │
                    ▼                 ▼
                 VALIDE           INVALIDE
                    │                 │
                    │                 └─► Retour au login
                    │                     avec message d'erreur
                    ▼
            Création de session
                    │
                    ▼
            Redirection vers /dashboard
                    │
                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                    UTILISATEUR CONNECTÉ                         │
│  - Session active                                               │
│  - Token de sécurité stocké                                     │
│  - Accès aux routes protégées                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📝 FLUX CRUD - CRÉATION D'UN HÔTEL

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER CLIQUE "NOUVEL HÔTEL"                   │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    GET /admin/hotels/new
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│              HotelController::new() - PREMIÈRE FOIS             │
│                                                                 │
│  $hotel = new Hotel();                                          │
│  $form = $this->createForm(HotelType::class, $hotel);           │
│  $form->handleRequest($request);                                │
│                                                                 │
│  // $form->isSubmitted() = FALSE (GET)                          │
│                                                                 │
│  return $this->render('admin/hotel/new.html.twig', [            │
│      'form' => $form->createView()                              │
│  ]);                                                            │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    Affiche formulaire vide
                             │
                             ▼
                    User remplit le formulaire
                    - Nom : "Grand Hotel"
                    - Ville : Tunis
                    - Prix : 150 USD
                             │
                             ▼
                    User clique "Enregistrer"
                             │
                             ▼
                    POST /admin/hotels/new
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│              HotelController::new() - DEUXIÈME FOIS             │
│                                                                 │
│  $hotel = new Hotel();                                          │
│  $form = $this->createForm(HotelType::class, $hotel);           │
│  $form->handleRequest($request);  ◄── Remplit $hotel            │
│                                                                 │
│  if ($form->isSubmitted() && $form->isValid()) {  ◄── TRUE      │
│      $em->persist($hotel);  ◄── Prépare (aucune SQL)            │
│      $em->flush();          ◄── INSERT INTO hotel...            │
│                                                                 │
│      $this->addFlash('success', 'Hotel created');               │
│      return $this->redirectToRoute('admin_hotel_index');        │
│  }                                                              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                         DOCTRINE                                │
│                                                                 │
│  persist($hotel) :                                              │
│    - Marque l'entité comme "MANAGED"                            │
│    - Aucune requête SQL                                         │
│                                                                 │
│  flush() :                                                      │
│    - Détecte les changements                                    │
│    - Génère les requêtes SQL                                    │
│    - Exécute en transaction                                     │
│    - INSERT INTO hotel (name, city_id, ...) VALUES (...)        │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    Redirection vers /admin/hotels
                             │
                             ▼
                    Affiche liste avec message
                    "Hotel created successfully"
```

---

## 🎨 SYSTÈME DE TEMPLATES TWIG

```
┌─────────────────────────────────────────────────────────────────┐
│                    base.html.twig (PARENT)                      │
│─────────────────────────────────────────────────────────────────│
│  <!DOCTYPE html>                                                │
│  <html>                                                         │
│  <head>                                                         │
│      <title>{% block title %}SmartStay{% endblock %}</title>   │
│  </head>                                                        │
│  <body>                                                         │
│      {% block body %}{% endblock %}                             │
│  </body>                                                        │
│  </html>                                                        │
└────────────────────────────┬────────────────────────────────────┘
                             │ extends
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│            admin/hotel/index.html.twig (ENFANT)                 │
│─────────────────────────────────────────────────────────────────│
│  {% extends 'base.html.twig' %}                                 │
│                                                                 │
│  {% block title %}Liste des Hôtels{% endblock %}               │
│                                                                 │
│  {% block body %}                                               │
│      <h1>Hôtels</h1>                                            │
│      {% for hotel in hotels %}                                  │
│          <p>{{ hotel.name }} - {{ hotel.city.name }}</p>        │
│      {% endfor %}                                               │
│  {% endblock %}                                                 │
└─────────────────────────────────────────────────────────────────┘
                             │
                             ▼
                    HTML FINAL GÉNÉRÉ
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  <!DOCTYPE html>                                                │
│  <html>                                                         │
│  <head>                                                         │
│      <title>Liste des Hôtels</title>                            │
│  </head>                                                        │
│  <body>                                                         │
│      <h1>Hôtels</h1>                                            │
│      <p>Grand Hotel - Tunis</p>                                 │
│      <p>Beach Resort - Sousse</p>                               │
│  </body>                                                        │
│  </html>                                                        │
└─────────────────────────────────────────────────────────────────┘
```

---

**Ces diagrammes vous aideront à visualiser et expliquer l'architecture ! 🚀**
