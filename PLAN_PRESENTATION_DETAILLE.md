# 🎬 PLAN DE PRÉSENTATION DÉTAILLÉ - VALIDATION SYMFONY

## ⏱️ TIMING : 20 MINUTES

---

## 📋 PRÉPARATION AVANT LA VALIDATION (10 minutes avant)

### ✅ Checklist technique
- [ ] Serveur Symfony démarré : `symfony server:start` ou `php -S localhost:8000 -t public/`
- [ ] Base de données créée et migrée
- [ ] Fixtures chargées (villes + hôtels)
- [ ] Cache vidé : `php bin/console cache:clear`
- [ ] Navigateur ouvert sur `http://localhost:8000`
- [ ] phpMyAdmin ouvert dans un onglet
- [ ] IDE (VSCode) ouvert avec les fichiers clés

### 📂 Fichiers à avoir ouverts dans l'IDE
1. `src/Entity/Hotel.php`
2. `src/Controller/Admin/HotelController.php`
3. `src/Form/HotelType.php`
4. `config/packages/security.yaml`
5. `templates/admin/hotel/index.html.twig`

### 🌐 Onglets navigateur à préparer
1. Page d'accueil : `http://localhost:8000/`
2. Login : `http://localhost:8000/login`
3. Dashboard : `http://localhost:8000/dashboard`
4. Admin : `http://localhost:8000/admin`
5. phpMyAdmin : `http://localhost/phpmyadmin`

### 📝 Compte de test à créer
- Email : `demo@smartstay.com`
- Password : `demo123`
- Role : `ROLE_USER`

---

## 🎯 MINUTE 0-2 : INTRODUCTION ET PRÉSENTATION GÉNÉRALE

### Ce que vous dites :
> "Bonjour, je vais vous présenter **SmartStay**, une plateforme de réservation d'hôtels développée avec Symfony 6.4. Le projet permet de gérer 24 villes tunisiennes avec leurs hôtels et réservations."

### Ce que vous montrez :
1. **Ouvrir le navigateur** sur la page d'accueil
2. **Montrer l'arborescence** du projet dans l'IDE
3. **Expliquer rapidement** :
   - Symfony 6.4 (LTS)
   - PHP 8.1+
   - MySQL (base : `booking`)
   - Doctrine ORM
   - Twig + Bootstrap 5

### Points clés à mentionner :
- ✅ "Le projet suit l'architecture MVC"
- ✅ "J'ai utilisé Doctrine pour la gestion de la base de données"
- ✅ "La sécurité est gérée par le Security Bundle de Symfony"

---

## 🏗️ MINUTE 2-5 : ARCHITECTURE ET ENTITÉS

### Ce que vous dites :
> "Le projet est structuré selon le pattern MVC. J'ai 5 entités principales : User, Hotel, City, Reservation et Dashboard. Laissez-moi vous montrer l'entité Hotel qui est au cœur du projet."

### Ce que vous montrez :

#### 1. Ouvrir `src/Entity/Hotel.php` (1 min)
**Montrer et expliquer** :
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
}
```

**Points à expliquer** :
- ✅ "J'utilise les attributs PHP 8 au lieu des annotations"
- ✅ "La relation ManyToOne signifie que plusieurs hôtels appartiennent à une ville"
- ✅ "inversedBy: 'hotels' fait référence à la propriété dans City"
- ✅ "nullable: false signifie que la ville est obligatoire"

#### 2. Ouvrir `src/Entity/User.php` (1 min)
**Montrer** :
```php
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function getRoles(): array
    {
        return [$this->role];
    }
}
```

**Points à expliquer** :
- ✅ "User implémente UserInterface pour l'authentification Symfony"
- ✅ "Le mot de passe est hashé avec bcrypt ou argon2"
- ✅ "getRoles() retourne le rôle de l'utilisateur pour la sécurité"

#### 3. Montrer le diagramme des relations (30 sec)
**Dessiner ou montrer** :
```
User (1) ----< (N) Reservation (N) >---- (1) Hotel (N) >---- (1) City
```

---

## 💻 MINUTE 5-8 : DÉMONSTRATION FONCTIONNELLE

### Ce que vous dites :
> "Maintenant, je vais vous montrer l'application en action, en commençant par l'authentification."

### 1. Authentification (1 min 30)

#### A. Inscription
1. Aller sur `/signup`
2. Remplir le formulaire :
   - Email : `test@example.com`
   - Password : `password123`
3. Soumettre
4. **Dire** : "Le mot de passe est automatiquement hashé par Symfony"
5. **Montrer dans phpMyAdmin** : Table `user`, colonne `password` (hash)

#### B. Connexion
1. Aller sur `/login`
2. Se connecter avec le compte créé
3. **Dire** : "Après connexion, je suis redirigé vers le dashboard"
4. **Montrer** : URL = `/dashboard`

#### C. Test de sécurité
1. Se déconnecter
2. Essayer d'accéder à `/dashboard` directement
3. **Dire** : "Je suis automatiquement redirigé vers /login car la route est protégée"

### 2. Dashboard Utilisateur (1 min)
1. Se reconnecter
2. Aller sur `/dashboard`
3. **Montrer** :
   - Carte interactive avec les 24 villes
   - Marqueurs cliquables
   - Liste des hôtels par ville
4. **Dire** : "Les coordonnées GPS sont stockées dans la base de données"

### 3. Panel Admin (30 sec)
1. Aller sur `/admin`
2. **Montrer** :
   - Dashboard moderne
   - Cartes de gestion (Hôtels, Réservations)
   - Statistiques
3. **Dire** : "C'est le panel d'administration pour gérer les hôtels et réservations"

---

## 🔧 MINUTE 8-12 : CRUD HÔTELS (DÉMONSTRATION LIVE)

### Ce que vous dites :
> "Je vais maintenant vous montrer le CRUD complet des hôtels : Create, Read, Update, Delete."

### 1. Liste des hôtels (30 sec)
1. Cliquer sur "Gérer les hôtels" → `/admin/hotels`
2. **Montrer** :
   - Tableau avec tous les hôtels
   - Colonnes : ID, Nom, Ville, Prix, Chambres, Actions
   - Boutons : Voir, Modifier, Supprimer

### 2. Créer un hôtel (2 min)
1. Cliquer sur "Nouvel Hôtel" → `/admin/hotels/new`
2. **Remplir le formulaire** :
   - Nom : "Grand Hotel Tunis"
   - Description : "Hôtel de luxe au cœur de Tunis"
   - Adresse : "Avenue Habib Bourguiba, Tunis"
   - Prix : 150 USD
   - Chambres : 25
   - Ville : Tunis (sélection dans la liste)
3. **Soumettre**
4. **Dire** : "Le formulaire est validé automatiquement par Symfony"
5. **Montrer** : Message flash "Hotel created successfully"
6. **Montrer dans phpMyAdmin** : Nouvelle ligne dans la table `hotel`

### 3. Modifier un hôtel (1 min)
1. Cliquer sur "Modifier" sur l'hôtel créé
2. Changer le prix : 150 → 180
3. Soumettre
4. **Dire** : "Doctrine met à jour automatiquement l'entité en base"
5. **Montrer** : Prix mis à jour dans la liste

### 4. Supprimer un hôtel (1 min)
1. Cliquer sur "Supprimer"
2. **Dire** : "Une confirmation JavaScript s'affiche"
3. Confirmer
4. **Dire** : "La suppression est protégée par un token CSRF"
5. **Montrer** : Hôtel supprimé de la liste

---

## 💻 MINUTE 12-17 : EXPLICATION DU CODE

### Ce que vous dites :
> "Maintenant, je vais vous expliquer le code derrière cette fonctionnalité."

### 1. Contrôleur (3 min)

#### Ouvrir `src/Controller/Admin/HotelController.php`

**Montrer la méthode `new()`** :
```php
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
```

**Expliquer ligne par ligne** :
1. ✅ "Route : /admin/hotels/new"
2. ✅ "Injection de dépendances : Request et EntityManager"
3. ✅ "Création d'une nouvelle instance Hotel"
4. ✅ "Création du formulaire basé sur HotelType"
5. ✅ "handleRequest() traite la requête HTTP"
6. ✅ "isSubmitted() && isValid() vérifie la soumission et la validation"
7. ✅ "persist() prépare l'entité, flush() exécute l'INSERT"
8. ✅ "addFlash() ajoute un message de succès"
9. ✅ "redirectToRoute() redirige vers la liste"

**Montrer la méthode `delete()`** :
```php
if ($this->isCsrfTokenValid('delete-hotel-'.$hotel->getId(), $request->request->get('_token'))) {
    $em->remove($hotel);
    $em->flush();
}
```

**Expliquer** :
- ✅ "isCsrfTokenValid() vérifie le token CSRF pour éviter les attaques"
- ✅ "remove() marque pour suppression, flush() exécute le DELETE"

### 2. Formulaire (1 min 30)

#### Ouvrir `src/Form/HotelType.php`

**Montrer** :
```php
->add('city', EntityType::class, [
    'class' => City::class,
    'choice_label' => 'name',
])
```

**Expliquer** :
- ✅ "EntityType charge automatiquement toutes les villes"
- ✅ "choice_label: 'name' affiche le nom de la ville"
- ✅ "Symfony convertit automatiquement l'ID en entité City"

### 3. Sécurité (30 sec)

#### Ouvrir `config/packages/security.yaml`

**Montrer** :
```yaml
access_control:
    - { path: ^/dashboard, roles: ROLE_USER }
```

**Expliquer** :
- ✅ "Toutes les routes /dashboard/* nécessitent ROLE_USER"
- ✅ "Redirection automatique vers /login si non authentifié"

---

## 🗄️ MINUTE 17-19 : BASE DE DONNÉES

### Ce que vous dites :
> "Voyons maintenant la structure de la base de données."

### 1. Ouvrir phpMyAdmin (1 min)

**Montrer** :
1. Base de données `booking`
2. Tables : `user`, `hotel`, `city`, `reservation`
3. Table `hotel` :
   - Colonnes : id, name, description, address, price_per_night, available_rooms, city_id
   - Clé étrangère : city_id → city.id

**Dire** :
- ✅ "Doctrine a généré automatiquement ces tables via les migrations"
- ✅ "La relation ManyToOne se traduit par une clé étrangère city_id"

### 2. Montrer une migration (1 min)

#### Ouvrir `migrations/Version20251204000000.php`

**Montrer** :
```php
public function up(Schema $schema): void
{
    $this->addSql('ALTER TABLE city ADD latitude DECIMAL(10, 6) DEFAULT NULL');
    $this->addSql('ALTER TABLE city ADD longitude DECIMAL(10, 6) DEFAULT NULL');
}
```

**Expliquer** :
- ✅ "Les migrations permettent de versionner le schéma de base de données"
- ✅ "up() ajoute les colonnes, down() les supprime (rollback)"

---

## ❓ MINUTE 19-20 : QUESTIONS ET CONCLUSION

### Ce que vous dites :
> "Voilà pour la présentation de SmartStay. Je suis prêt à répondre à vos questions."

### Questions probables et réponses courtes :

**Q : Différence entre persist() et flush() ?**
R : "persist() prépare l'entité, flush() exécute les requêtes SQL."

**Q : Comment fonctionne l'authentification ?**
R : "Le firewall intercepte, le UserProvider charge l'utilisateur, le PasswordHasher vérifie le hash."

**Q : Qu'est-ce qu'une relation ManyToOne ?**
R : "Plusieurs entités pointent vers une seule. Exemple : plusieurs hôtels → une ville."

---

## 🎯 CONSEILS FINAUX

### Pendant la présentation :
- ✅ Parlez clairement et pas trop vite
- ✅ Montrez le code en même temps que vous expliquez
- ✅ Utilisez des exemples concrets de votre projet
- ✅ Soyez enthousiaste et confiant

### Si vous ne savez pas répondre :
- ✅ "Je ne suis pas sûr, mais je pense que..."
- ✅ "Je n'ai pas utilisé cette fonctionnalité dans mon projet"
- ✅ "C'est une bonne question, je vais me renseigner"

### Phrases à utiliser :
- "Dans mon projet, j'ai utilisé..."
- "Symfony gère automatiquement..."
- "L'avantage de cette approche est..."
- "Cela permet de..."

---

**BONNE CHANCE ! VOUS ALLEZ RÉUSSIR ! 🚀🍀**
