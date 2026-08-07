# TODO — Avis sur les chambres

## Objectif
Permettre à tout utilisateur connecté (voyageur) de donner un avis sur une chambre spécifique d'un établissement.

## Étapes

### 1. Migration — Ajouter `room_id` à la table `reviews`
- [x] Créer une migration `add_room_id_to_reviews_table.php`
- [x] Ajouter `room_id` nullable avec contrainte étrangère vers `rooms`
- [x] Ajouter une contrainte unique pour éviter les doublons (user_id + room_id)
- [x] Migration exécutée avec succès

### 2. Modèle `Review` — Ajouter la relation `room()`
- [x] Ajouter `room_id` dans `$fillable`
- [x] Ajouter la méthode `room(): BelongsTo`

### 3. Modèle `Room` — Ajouter la relation `reviews()`
- [x] Ajouter la méthode `reviews(): HasMany`

### 4. Contrôleur `ReviewController` — Créer le contrôleur
- [x] Créer `app/Http/Controllers/ReviewController.php`
- [x] Méthode `store()` : validation, création de l'avis, mise à jour du rating de l'établissement

### 5. Routes
- [x] Ajouter `POST /chambres/{room}/avis` protégé par `auth`
- [x] Route enregistrée et vérifiée (HTTP 419 pour guest, HTTP 200 pour page publique)

### 6. Vue `establishments/show.blade.php` — Ajouter le formulaire d'avis et affichage
- [x] Affichage des avis par chambre avec note moyenne par chambre
- [x] Formulaire d'avis avec notation par étoiles Alpine.js (rating, title, comment)
- [x] Vérification si l'utilisateur a déjà donné son avis (masquer formulaire)
- [x] Message "Connectez-vous" pour les non-connectés
- [x] Section globale des avis conservée

### 7. Mise à jour du seeder `ReviewSeeder`
- [x] Associer ~70% des avis à des chambres aléatoires
- [x] Seed exécuté avec 49 avis liés aux chambres

### 8. Back-office hôte
- [x] `HoteController::dashboard()` : charger les avis reçus sur les chambres
- [x] `dashboard.blade.php` : section "Avis récents sur vos chambres"

### 9. Tests
- [x] Migration exécutée sans erreur
- [x] Route d'avis enregistrée correctement
- [x] Aucun doublon (user_id + room_id) dans les données seed
- [x] Test de création d'avis via script PHP (rating 3.00 → 3.75 après ajout)
- [x] Relations `room->reviews` et `review->room` fonctionnelles
- [x] Pages publiques HTTP 200 (établissement, liste)
- [x] Route POST protégée (419 CSRF pour guest)
