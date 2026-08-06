# TODO — Refonte design Hôtels Bénin (Tailwind + Alpine, sans Vite)

Approche : Play CDN Tailwind v3 + Alpine CDN + Bootstrap Icons, config inline dans le layout.

## Phase 1 — Design system & base
- [x] 1. `layouts/app.blade.php` : Play CDN Tailwind v3 + config inline + Alpine + Bootstrap Icons + bloc design system (text/tailwindcss)
- [x] 2. `partials/header.blade.php` : header sticky premium, menu mobile & dropdown Alpine
- [x] 3. `partials/footer.blade.php` : footer premium (newsletter + réseaux sociaux)
- [x] 4. `home.blade.php` : hero premium, stats, catégories, meilleures notes
- [x] 5. `establishments/index.blade.php` : filtres Alpine (accordéon mobile, tri)
- [x] 6. `establishments/show.blade.php` : galerie lightbox Alpine, booking sticky
- [x] 7. `partials/establishment-card.blade.php` : carte modernisée
- [x] 8. `about.blade.php` : harmonisation du design system
- [x] 9. `contact.blade.php` : harmonisation (icônes Bootstrap)
- [x] 10. `auth/login.blade.php` + `auth/register.blade.php` : harmonisation (icônes Bootstrap)

## Phase 2 — Améliorations sections & menus
- [x] 11. `app/Providers/AppServiceProvider.php` : View Composer pour partager types + localités à toutes les vues
- [x] 12. `resources/views/partials/header.blade.php` : menu déroulant "Établissements" (sous-liens par type), barre de recherche dépliante Alpine, bouton "Devenir hôte", menu mobile avec catégories
- [x] 13. `home.blade.php` : section "Comment ça marche" (3 étapes)
- [x] 14. `home.blade.php` : section "Pourquoi choisir" (4 atouts)
- [x] 15. `home.blade.php` : section "Catégories avec images"
- [x] 16. `home.blade.php` : carrousel "Témoignages" (Alpine)

Vérification : toutes les routes retournent 200 après les modifications.
