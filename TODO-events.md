# TODO — Page Événements & Programmes au Bénin

## Objectif
Ajouter une rubrique publique "Événements" pour informer les utilisateurs des événements et programmes culturels, sportifs et économiques du Bénin qui peuvent les motiver à faire des réservations.

## Étapes

### 1. Migration — Table `events`
- [ ] Créer la migration `create_events_table.php`
- [ ] Champs : title, slug, category (festival/culture/sport/affaires), description, date_start, date_end, location, cover_image, is_featured, is_available
- [ ] Exécuter la migration

### 2. Modèle `Event`
- [ ] Créer `app/Models/Event.php`
- [ ] $fillable + $casts (dates, booléens)

### 3. Contrôleur `EventController`
- [ ] Créer `app/Http/Controllers/EventController.php`
- [ ] `index()` : liste paginée des événements disponibles
- [ ] `show()` : détail d'un événement (avec événements similaires)

### 4. Routes
- [ ] Ajouter `GET /evenements` → `events.index`
- [ ] Ajouter `GET /evenements/{slug}` → `events.show`

### 5. Vues
- [ ] Créer `resources/views/events/index.blade.php` (liste avec filtres par catégorie)
- [ ] Créer `resources/views/events/show.blade.php` (détail + CTA réservation)
- [ ] Ajouter le lien "Événements" dans le header + footer

### 6. Seeder
- [ ] Créer `database/seeders/EventSeeder.php` avec des événements béninois réels
- [ ] Ajouter au `DatabaseSeeder`

### 7. Tests
- [ ] Créer `test_events.php` dans la suite de tests
- [ ] Vérifier routes, modèle, relations
</content>

