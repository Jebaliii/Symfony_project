# ⚡ ANTISÈCHE RAPIDE - VALIDATION SYMFONY

## 🎯 PROJET : SmartStay (Réservation d'hôtels)

### Technologies
- **Symfony 6.4** (LTS) + **PHP 8.1+** + **MySQL** + **Doctrine** + **Twig** + **Bootstrap 5**

---

## 📊 ENTITÉS ET RELATIONS

```
┌──────────┐         ┌──────────────┐         ┌──────────┐         ┌──────────┐
│   User   │         │ Reservation  │         │  Hotel   │         │   City   │
├──────────┤         ├──────────────┤         ├──────────┤         ├──────────┤
│ id       │◄───────┤│ id           │         │ id       │         │ id       │
│ email    │ 1    N ││ user_id      │         │ name     │         │ name     │
│ password │         ││ hotel_id     │────────►│ city_id  │────────►│ latitude │
│ role     │         ││ checkInDate  │ N    1 │ price    │ N    1 │ longitude│
└──────────┘         ││ status       │         │ rooms    │         │ boundary │
                     └──────────────┘         └──────────┘         └──────────┘
```

---

## 🛣️ ROUTES PRINCIPALES

| URL | Route | Contrôleur | Accès |
|-----|-------|------------|-------|
| `/` | app_home | HomeController::index | PUBLIC |
| `/login` | app_login | AuthController::login | PUBLIC |
| `/signup` | app_signup | AuthController::signup | PUBLIC |
| `/logout` | app_logout | AuthController::logout | PUBLIC |
| `/dashboard` | app_dashboard | DashboardController::index | ROLE_USER |
| `/admin` | admin_dashboard | AdminDashboardController::index | - |
| `/admin/hotels` | admin_hotel_index | HotelController::index | - |
| `/admin/hotels/new` | admin_hotel_new | HotelController::new | - |
| `/admin/hotels/{id}` | admin_hotel_show | HotelController::show | - |
| `/admin/hotels/{id}/edit` | admin_hotel_edit | HotelController::edit | - |
| `/admin/hotels/{id}/delete` | admin_hotel_delete | HotelController::delete | POST |

---

## 🔐 SÉCURITÉ

### Configuration (security.yaml)
```yaml
password_hashers:
    PasswordAuthenticatedUserInterface: 'auto'  # bcrypt/argon2

providers:
    app_user_provider:
        entity: { class: User, property: email }

firewalls:
    main:
        form_login:
            login_path: app_login
            check_path: app_login_check
            default_target_path: app_dashboard
        logout:
            path: app_logout
            target: app_login

access_control:
    - { path: ^/dashboard, roles: ROLE_USER }
    - { path: ^/login, roles: PUBLIC_ACCESS }
```

### Authentification
1. User entre email + password
2. Firewall intercepte
3. UserProvider charge User depuis DB
4. PasswordHasher vérifie le hash
5. Session créée → Redirection vers dashboard

---

## 💾 DOCTRINE

### Persist vs Flush
```php
$hotel = new Hotel();
$hotel->setName('Grand Hotel');

$em->persist($hotel);  // ✅ Prépare (aucune requête SQL)
$em->flush();          // ✅ Exécute INSERT en base
```

### Relations
```php
// ManyToOne (côté propriétaire)
#[ORM\ManyToOne(inversedBy: 'hotels')]
#[ORM\JoinColumn(nullable: false)]
private ?City $city = null;

// OneToMany (côté inverse)
#[ORM\OneToMany(targetEntity: Hotel::class, mappedBy: 'city')]
private Collection $hotels;
```

### Repository - Requête personnalisée
```php
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

---

## 📝 FORMULAIRES

### Création
```php
// Dans le contrôleur
$hotel = new Hotel();
$form = $this->createForm(HotelType::class, $hotel);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $em->persist($hotel);
    $em->flush();
    return $this->redirectToRoute('admin_hotel_index');
}

return $this->render('admin/hotel/new.html.twig', [
    'form' => $form->createView()
]);
```

### Type de formulaire
```php
class HotelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name'
            ]);
    }
}
```

---

## 🎨 TWIG

### Syntaxe de base
```twig
{# Commentaire #}

{{ variable }}                          {# Affichage #}
{{ hotel.name }}                        {# Propriété #}
{{ hotel.city.name }}                   {# Relation #}
{{ price|number_format(2) }}            {# Filtre #}

{% if condition %}...{% endif %}        {# Condition #}
{% for item in items %}...{% endfor %}  {# Boucle #}

{% extends 'base.html.twig' %}          {# Héritage #}
{% block title %}Mon titre{% endblock %} {# Bloc #}

{{ path('route_name', {id: 1}) }}       {# Génération URL #}
{{ csrf_token('delete-hotel-1') }}      {# Token CSRF #}

{% for message in app.flashes('success') %} {# Flash messages #}
    {{ message }}
{% endfor %}
```

---

## 🔧 COMMANDES ESSENTIELLES

```bash
# Serveur
symfony server:start
php -S localhost:8000 -t public/

# Base de données
php bin/console doctrine:database:create
php bin/console doctrine:database:drop --force

# Migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Fixtures
php bin/console doctrine:fixtures:load
php bin/console doctrine:fixtures:load --append

# Génération
php bin/console make:entity
php bin/console make:controller
php bin/console make:form
php bin/console make:crud

# Debug
php bin/console debug:router
php bin/console debug:firewall
php bin/console debug:container

# Cache
php bin/console cache:clear

# Commandes custom
php bin/console app:seed:cities
php bin/console app:update:hotel-images
```

---

## ❓ QUESTIONS FRÉQUENTES (RÉPONSES COURTES)

### Q: Différence persist() et flush() ?
**R:** persist() = prépare, flush() = exécute SQL

### Q: ManyToOne vs OneToMany ?
**R:** ManyToOne = côté propriétaire (clé étrangère), OneToMany = côté inverse (collection)

### Q: mappedBy vs inversedBy ?
**R:** mappedBy = côté inverse (OneToMany), inversedBy = côté propriétaire (ManyToOne)

### Q: Comment protéger une route ?
**R:** access_control dans security.yaml ou #[IsGranted('ROLE_USER')]

### Q: Comment créer une migration ?
**R:** make:migration puis doctrine:migrations:migrate

### Q: Qu'est-ce qu'un Repository ?
**R:** Classe pour requêtes personnalisées sur une entité

### Q: Comment valider un formulaire ?
**R:** $form->isSubmitted() && $form->isValid()

### Q: Comment hasher un mot de passe ?
**R:** UserPasswordHasherInterface::hashPassword()

---

## ✅ CHECKLIST DERNIÈRE MINUTE

- [ ] Serveur démarré
- [ ] Base de données créée et migrée
- [ ] Fixtures chargées
- [ ] Login/Signup testés
- [ ] CRUD Hôtels testé
- [ ] CRUD Réservations testé
- [ ] Dashboard admin affiché
- [ ] Aucune erreur dans les logs

---

## 🎯 STRUCTURE DU PROJET

```
Project1_sf6/
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml       # Config Doctrine
│   │   ├── security.yaml       # Config Sécurité
│   │   └── framework.yaml      # Config Framework
│   ├── routes.yaml             # Routes
│   └── services.yaml           # Services
├── migrations/                 # Migrations DB
├── public/
│   └── index.php              # Point d'entrée
├── src/
│   ├── Controller/            # Contrôleurs
│   ├── Entity/                # Entités Doctrine
│   ├── Form/                  # Formulaires
│   ├── Repository/            # Repositories
│   ├── DataFixtures/          # Fixtures
│   └── Command/               # Commandes console
├── templates/                 # Templates Twig
├── var/
│   ├── cache/                 # Cache
│   └── log/                   # Logs
├── .env                       # Variables d'environnement
└── composer.json              # Dépendances PHP
```

---

## 🚀 DÉMARRAGE RAPIDE

```bash
# 1. Installer les dépendances
composer install

# 2. Créer la base de données
php bin/console doctrine:database:create

# 3. Exécuter les migrations
php bin/console doctrine:migrations:migrate

# 4. Charger les fixtures
php bin/console doctrine:fixtures:load

# 5. Démarrer le serveur
symfony server:start
```

---

**BONNE CHANCE ! 🍀**
