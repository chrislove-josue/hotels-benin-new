# TODO — Système de réservation & transactions

## Plan approuvé
But : rendre fonctionnel le système de réservation côté voyageur (actuellement simulé), créer l'espace voyageur, et gérer les transactions (paiement simulé).

## Étapes
- [ ] 1. `app/Models/Reservation.php` : ajouter `reference` fillable + helper `nights()`
- [ ] 2. `app/Http/Controllers/ReservationController.php` : `store()`, `myReservations()`, `show()`
- [ ] 3. `app/Http/Controllers/TransactionController.php` : `checkout()`, `process()` (paiement simulé)
- [ ] 4. `routes/web.php` : groupe `espace-voyageur` + routes réservation/checkout
- [ ] 5. `resources/views/reservations/index.blade.php` : espace "Mes réservations"
- [ ] 6. `resources/views/reservations/show.blade.php` : détail réservation + transaction
- [ ] 7. `resources/views/reservations/checkout.blade.php` : page de paiement simulé
- [ ] 8. `resources/views/reservations/confirm.blade.php` : confirmation après réservation
- [ ] 9. `resources/views/establishments/show.blade.php` : vrai formulaire POST + choix chambre
- [ ] 10. Header : lien "Mes réservations" pour les voyageurs connectés
- [ ] 11. Vérifier que les routes retournent 200 et tester le flux complet
