# 🛠 Documentation Technique — Nexora Drinks

Cette documentation s'adresse aux développeurs et administrateurs système gérant la plateforme Nexora.

## 1. Stack Technologique
- **Framework Backend** : Laravel 11.x (PHP 8.3+)
- **Framework Frontend** : React 18.x avec TypeScript
- **Adaptateur** : Inertia.js (Lien transparent entre Laravel et React)
- **Styling** : Tailwind CSS + Shadcn UI
- **Animations** : Framer Motion
- **Base de Données** : PostgreSQL (Recommandé pour la production)

## 2. Architecture Multi-Tenant
L'application utilise une architecture multi-tenant basée sur les **Teams** (Équipes) via Laravel Jetstream/Fortify.
- Chaque entreprise possède son propre `current_team_id`.
- Les données (Articles, Ventes, Stocks) sont filtrées globalement par le `team_id` via des Scopes Eloquent ou des middleware.

## 3. Système de Rôles & Permissions
Les rôles sont définis dans `App\Models\TeamRole` (ou une classe de constantes similaire).
- **admin** : Accès complet au dashboard et aux paramètres de l'équipe.
- **gerant** : Accès opérationnel complet, gestion des stocks et rapports.
- **ops** : Gestion logistique et approvisionnements.
- **caissier** : Accès limité au module de vente et aux encaissements.
- **magasinier** : Gestion stricte des entrées/sorties de stock et inventaires.
- **comptable** : Focus sur les charges, les versements banque et les rapports financiers.

### 🛡️ Protection des Rôles Administratifs
Le système implémente une couche de sécurité "Hardcoded" dans `MembresController` pour protéger l'intégrité de l'espace de travail :
- **Verrous de Pivot** : Les rôles `Admin` et `Owner` sont immuables via l'interface de gestion d'équipe.
- **Isolation des Actions** : Les méthodes `updateRole`, `updatePassword`, `updateProfile`, `block` et `remove` rejettent systématiquement toute requête ciblant un administrateur.
- **Scoping des Routes** : Les routes utilisent le paramètre `{member}` lié au modèle `User` pour éviter les conflits de liaison et garantir une résolution 100% fiable (évite les erreurs 404).

## 4. Structure des Composants (Frontend)
Les composants drinks sont situés dans `resources/js/pages/drinks/components/` :
- `DashboardView.tsx` : Point d'entrée principal qui route vers le dashboard spécifique au rôle.
- `dashboards/` : Contient les vues spécialisées par rôle (`DashboardAdmin.tsx`, `DashboardCaissier.tsx`, etc.).
- `ArticleForm.tsx` / `ArticleIndex.tsx` : Gestion du catalogue.
- `SaleForm.tsx` / `SaleIndex.tsx` : Gestion du cycle de vente.

## 5. Endpoints API & Routes
Les routes sont regroupées sous le préfixe `drinks` dans `routes/web.php` :
- `drinks.articles.*`
- `drinks.sales.*`
- `drinks.procurements.*`
- `drinks.payments.*`

## 6. Déploiement & Maintenance
### Commandes Utiles
```bash
# Installation des dépendances
composer install
npm install

# Build production
npm run build

# Migrations
php artisan migrate
```

### Configuration Environnement
Le fichier `.env` doit contenir les clés de configuration pour :
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `APP_URL`
- `FILESYSTEM_DISK` (Recommandé : `public` pour les logos de teams)

---
*Document maintenu par l'équipe Antigravity.*
