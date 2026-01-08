# ⚡ RÉVISION DERNIÈRE MINUTE (5 MINUTES)

## 🎯 PROJET : SmartStay - Réservation d'hôtels (Symfony 6.4)

---

## 📊 ENTITÉS (4 principales)

### User
- email, password (hashé), role
- Implémente UserInterface
- OneToMany → Reservation

### Hotel
- name, description, address, pricePerNight, availableRooms
- **ManyToOne** → City (inversedBy: 'hotels')
- OneToMany → Reservation

### City
- name, latitude, longitude, boundary (JSON)
- **OneToMany** → Hotel (mappedBy: 'city')

### Reservation
- checkInDate, checkOutDate, paymentMethod, status
- ManyToOne → User
- ManyToOne → Hotel

---

## 🛣️ ROUTES CLÉS

| URL | Contrôleur | Accès |
|-----|------------|-------|
| `/` | HomeController | PUBLIC |
| `/login` | AuthController::login | PUBLIC |
| `/dashboard` | DashboardController | ROLE_USER |
| `/admin/hotels` | HotelController::index | - |
| `/admin/hotels/new` | HotelController::new | - |

---

## 🔐 SÉCURITÉ

### security.yaml
```yaml
password_hashers:
    PasswordAuthenticatedUserInterface: 'auto'

providers:
    app_user_provider:
        entity: { class: User, property: email }

firewalls:
    main:
        form_login:
            login_path: app_login
            default_target_path: app_dashboard

access_control:
    - { path: ^/dashboard, roles: ROLE_USER }
```

---

## 💾 DOCTRINE

### Persist vs Flush
```php
$hotel = new Hotel();
$em->persist($hotel);  // Prépare (aucune SQL)
$em->flush();          // Exécute INSERT
```

### Relations
```php
// ManyToOne (propriétaire)
#[ORM\ManyToOne(inversedBy: 'hotels')]
private ?City $city = null;

// OneToMany (inverse)
#[ORM\OneToMany(mappedBy: 'city')]
private Collection $hotels;
```

### Repository
```php
public function findByCity(int $cityId): array
{
    return $this->createQueryBuilder('h')
        ->andWhere('h.city = :cityId')
        ->setParameter('cityId', $cityId)
        ->getQuery()
        ->getResult();
}
```

---

## 📝 FORMULAIRES

### Création
```php
$form = $this->createForm(HotelType::class, $hotel);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $em->persist($hotel);
    $em->flush();
    return $this->redirectToRoute('admin_hotel_index');
}
```

### EntityType
```php
->add('city', EntityType::class, [
    'class' => City::class,
    'choice_label' => 'name'
])
```

---

## 🎨 TWIG

```twig
{{ hotel.name }}                    {# Affichage #}
{{ hotel.city.name }}               {# Relation #}
{{ price|number_format(2) }}        {# Filtre #}

{% for hotel in hotels %}...{% endfor %}  {# Boucle #}
{% if condition %}...{% endif %}          {# Condition #}

{% extends 'base.html.twig' %}      {# Héritage #}
{% block title %}...{% endblock %}  {# Bloc #}

{{ path('route_name', {id: 1}) }}   {# URL #}
{{ csrf_token('delete-hotel-1') }}  {# CSRF #}
```

---

## 🔧 COMMANDES

```bash
# Serveur
symfony server:start

# Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Fixtures
php bin/console doctrine:fixtures:load

# Génération
php bin/console make:entity
php bin/console make:controller
php bin/console make:form

# Debug
php bin/console debug:router
php bin/console cache:clear
```

---

## ❓ QUESTIONS RAPIDES

### Q: persist() vs flush() ?
**R:** persist() = prépare, flush() = exécute SQL

### Q: ManyToOne vs OneToMany ?
**R:** ManyToOne = propriétaire (FK), OneToMany = inverse (collection)

### Q: mappedBy vs inversedBy ?
**R:** mappedBy = inverse, inversedBy = propriétaire

### Q: Comment protéger une route ?
**R:** access_control dans security.yaml

### Q: Comment créer une migration ?
**R:** make:migration puis doctrine:migrations:migrate

### Q: Qu'est-ce qu'un Repository ?
**R:** Classe pour requêtes personnalisées

### Q: Comment valider un formulaire ?
**R:** $form->isSubmitted() && $form->isValid()

### Q: Comment hasher un mot de passe ?
**R:** UserPasswordHasherInterface::hashPassword()

---

## 🎯 CYCLE DE VIE D'UNE REQUÊTE

```
HTTP Request → index.php → Kernel → Router → Security → Controller → Doctrine → Twig → HTTP Response
```

---

## 🔄 CRUD COMPLET

### Create
```php
$hotel = new Hotel();
$em->persist($hotel);
$em->flush();
```

### Read
```php
$hotels = $hotelRepository->findAll();
$hotel = $hotelRepository->find($id);
```

### Update
```php
$hotel->setName('New Name');
$em->flush();  // Pas de persist() car déjà géré
```

### Delete
```php
$em->remove($hotel);
$em->flush();
```

---

## 🎬 PLAN DE PRÉSENTATION (20 MIN)

1. **Introduction** (2 min) : Présenter le projet
2. **Architecture** (3 min) : Montrer les entités
3. **Démo** (8 min) : Login, CRUD hôtels
4. **Code** (5 min) : Contrôleur, formulaire, sécurité
5. **BDD** (2 min) : phpMyAdmin, migrations

---

## ✅ CHECKLIST FINALE

- [ ] Serveur démarré
- [ ] Base de données créée
- [ ] Fixtures chargées
- [ ] Login/Signup testés
- [ ] CRUD testé
- [ ] Fichiers ouverts dans l'IDE
- [ ] Navigateur prêt

---

## 💡 PHRASES CLÉS

- "Dans mon projet, j'ai utilisé..."
- "Symfony gère automatiquement..."
- "L'avantage de cette approche est..."
- "Cela permet de..."
- "Par exemple, dans HotelController..."

---

## 🚨 PIÈGES À ÉVITER

- ❌ Oublier flush() après persist()
- ❌ Confondre mappedBy et inversedBy
- ❌ Oublier nullable: false sur les relations
- ❌ Ne pas valider les formulaires

---

## 🎯 FICHIERS IMPORTANTS

1. `src/Entity/Hotel.php`
2. `src/Controller/Admin/HotelController.php`
3. `src/Form/HotelType.php`
4. `config/packages/security.yaml`
5. `templates/admin/hotel/index.html.twig`

---

**RESPIREZ, VOUS ÊTES PRÊT ! 🚀**
