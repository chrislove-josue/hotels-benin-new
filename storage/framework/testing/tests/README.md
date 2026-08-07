# Tests unitaires — Hôtels Bénin

Ce dossier contient les tests unitaires du projet, stockés comme sauvegarde de sécurité.

## Lancement

```bash
# Exécuter tous les tests
php storage/framework/testing/tests/run_all_tests.php

# Exécuter une suite spécifique
php storage/framework/testing/tests/test_reviews_rooms.php
php storage/framework/testing/tests/test_reservation_calculation.php
php storage/framework/testing/tests/test_authorization.php
php storage/framework/testing/tests/test_authenticated_render.php
php storage/framework/testing/tests/test_security_input_validation.php
php storage/framework/testing/tests/test_security_csrf_sessions.php
php storage/framework/testing/tests/test_security_idor.php
php storage/framework/testing/tests/test_security_hardening.php
php storage/framework/testing/tests/test_security_suite.php
```

## Suites de tests

### Fonctionnalités
| Fichier | Couverture |
|---|---|
| `test_reviews_rooms.php` | Avis sur les chambres : relations `Review ↔ Room ↔ Establishment`, création d'avis, multi-avis par utilisateur, recalcul du rating établissement, avis au niveau établissement |
| `test_reservation_calculation.php` | Calcul des réservations : `nightsBetween()`, cas limites (dates identiques/inversées), montant total, commission 10%, génération de référence unique, méthode `nights()` |
| `test_authorization.php` | Sécurité des rôles : `isAdmin()` / `isHote()` / `isVoyageur()`, middlewares `EnsureUserIsAdmin` / `EnsureUserIsHote`, propriété des réservations, présence des routes d'avis protégées |
| `test_authenticated_render.php` | Rendu de la page établissement avec un utilisateur authentifié : présence des formulaires d'avis chambre et établissement |

### Sécurité
| Fichier | Couverture |
|---|---|
| `test_security_input_validation.php` | **Validation des entrées** : XSS (échappement Blade), emails valides/invalides, limites `max:`, types (`integer`, `numeric`), injection SQL par Eloquent, email unique, mot de passe `>= 8`, rôles `in:`, budget négatif rejeté, routes admin bloquées sans auth |
| `test_security_csrf_sessions.php` | **CSRF & sessions** : routes POST enregistrées (groupe `web` → protection CSRF), jeton CSRF généré, hachage bcrypt des mots de passe, données sensibles cachées (`toArray()`), authentification/déconnexion, règle `min:8` mot de passe, config session (`httpOnly`, `SameSite=lax`) |
| `test_security_idor.php` | **Anti-IDOR** : propriété des réservations, isolation voyageur, hôte ne modifie que ses établissements/réservations, appartenance chambre→établissement, middlewares bloquent les accès non autorisés |
| `test_security_hardening.php` | **Durcissement** : contraintes table `reviews` (FK), rating entre 1 et 5, intégrité référentielle, cascade de suppression, valeurs par défaut (`reviews_count>=0`), slugs sans données sensibles, limite commentaire, champs booléens |
| `test_security_suite.php` | **Suite consolidée** : validation (XSS, longueur, types), injection SQL, CSRF/sessions, hash bcrypt, anti-IDOR, middlewares de rôle |

## Format

Chaque script initialise l'application Laravel via `bootstrap/app.php`, exécute ses vérifications
et affiche un résumé du type :

```
RÉSULTAT : 15 réussis, 0 échec(s)
```

Le code de sortie est `0` en cas de succès, `1` si au moins un test échoue.

## Récapitulatif global

```
Suites exécutées  : 9
Tests réussis     : 149
Tests échoués     : 0
```

