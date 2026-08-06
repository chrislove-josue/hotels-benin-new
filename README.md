# Hôtels Bénin — Partie vitrine (Laravel 13)

Vitrine publique (front-office) de la plateforme de réservation **Hôtels Bénin**.
Ce dépôt contient uniquement la partie visible par les visiteurs et utilisateurs
non connectés : aucune logique de back-office d'administration n'est incluse,
conformément au cahier des charges.

## Stack technique

- Laravel 13 (PHP 8.2+)
- Blade + layouts réutilisables
- Tailwind CSS (via Vite) — charte graphique respectée (bleu #1A3A6B, or #D4AF37, orange #F59A23)
- Police Montserrat (Google Fonts)
- MySQL (ou SQLite pour un test rapide en local)

## Pages livrées

| Page                          | Route                          |
|--------------------------------|---------------------------------|
| Accueil (recherche simplifiée)| `/`                             |
| Liste des établissements      | `/etablissements`               |
| Détail d'un établissement     | `/etablissements/{slug}`        |
| À propos                      | `/a-propos`                     |
| Contact (formulaire + e-mail) | `/contact`                      |
| Connexion (formulaire)        | `/connexion`                    |
| Inscription (formulaire)      | `/inscription`                  |

## Installation

### 1. Prérequis
- PHP >= 8.2 avec les extensions habituelles (mbstring, pdo_mysql, etc.)
- Composer
- Node.js >= 18 et npm
- MySQL (ou SQLite pour tester rapidement)

### 2. Installation des dépendances

```bash
composer install
npm install
```

### 3. Configuration de l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Renseignez ensuite dans `.env` :
- vos identifiants de base de données (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- vos identifiants SMTP pour que le formulaire de contact envoie réellement un e-mail
  (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`)
- l'adresse qui doit recevoir les messages de contact : `CONTACT_TO_ADDRESS`

> Astuce pour tester rapidement sans MySQL : mettez `DB_CONNECTION=sqlite`,
> créez le fichier `database/database.sqlite`, et retirez les autres variables `DB_*`.

### 4. Base de données et jeu de données de démonstration

```bash
php artisan migrate
php artisan db:seed
```

Cela crée automatiquement :
- 10 localités béninoises (Cotonou, Porto-Novo, Ouidah, Abomey, Grand-Popo...)
- 5 types d'établissements (Hôtel, Résidence meublée, Villa, Auberge, Salle de fête)
- 12 équipements (Wifi, Piscine, Climatisation...)
- 5 comptes "hôtes" + 25 comptes "voyageurs" (mot de passe par défaut : `password`)
- 12 établissements réalistes avec photos, chambres/espaces, équipements et avis notés

### 5. Compilation des assets (Tailwind / Vite)

En développement :
```bash
npm run dev
```

Pour la production :
```bash
npm run build
```

### 6. Lancer le serveur

```bash
php artisan serve
```

Le site est alors accessible sur http://localhost:8000

## Organisation du code

```
app/
  Http/Controllers/        Contrôleurs publics (Home, Establishment, About, Contact, Auth)
  Mail/                     Mailable pour le formulaire de contact
  Models/                   Modèles Eloquent (Establishment, Room, Review, Locality, ...)
database/
  migrations/               Schéma complet de la base
  seeders/                  Jeux de données réalistes
  factories/                Factory Faker pour les utilisateurs
resources/
  views/                    Vues Blade (layouts, partials, pages)
  css/app.css               Design system Tailwind (couleurs, boutons, cartes)
  js/app.js                 Interactions front légères (menu mobile)
routes/web.php              Routes publiques uniquement
```

## Notes importantes

- **Aucune route de back-office** n'est présente : ce dépôt correspond uniquement
  au périmètre "vitrine" demandé.
- Les formulaires de **connexion / inscription** effectuent une validation basique
  côté serveur mais ne créent pas de session utilisateur réelle : cette logique
  sera branchée par le back-office.
- Le **formulaire de réservation** sur la fiche établissement est une simulation
  d'interface (aucun traitement de disponibilité réel), comme précisé dans le
  cahier des charges.
- Le **formulaire de contact** est fonctionnel : chaque message est enregistré en
  base (table `contact_messages`) et un e-mail est envoyé via la configuration
  SMTP définie dans `.env`.
- Sécurité de base : protection CSRF (`@csrf` sur tous les formulaires) et
  validation des entrées sur chaque contrôleur.

## Prochaines étapes suggérées

- Brancher l'authentification réelle (Laravel Breeze/Fortify) sur les formulaires
  de connexion/inscription lors du développement du back-office.
- Ajouter la gestion réelle des disponibilités et des réservations.
- Remplacer les images de démonstration (Unsplash) par les vraies photos des
  établissements partenaires.
