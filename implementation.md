# 🚀 Nexora — Réimplémentation du secteur "Drinks"

**Session ID :** `0c19a8de-58d8-4349-aff8-8d1cbb2545c0`  
**Date :** 2026-05-03  

---

## Contexte

Nexora est un SaaS multi-tenant **Laravel 13 + Inertia React** (2 secteurs : Boissons, Alimentaire). Le module "drinks" actuel était très limité (5 composants React, 6 méthodes de contrôleur, aucun vrai modèle métier).

Refonte complète basée sur **SYGES** — application PHP procédurale legacy (829 fichiers, 45 tables, 9 types de transactions, 6 rôles) couvrant tout le métier de la distribution de boissons.

### Décisions validées

| Sujet | Choix |
|---|---|
| Scope | **MVP étendu** (~95% SYGES) : master data + procurement + sales + stock + consignes + finance + frigo + cession + pertes |
| Rôles | Rôles SYGES : `Admin`, `Gerant`, `Ops`, `Caissier`, `Comptable`, `Magasinier` → `welcome.tsx` mis à jour |
| PDF | `barryvdh/laravel-dompdf` + templates Blade (reçu A4 + ticket thermique 80mm) |
| Destruction | Effacer toute l'ancienne implémentation drinks |

### Résultat attendu

Un module `drinks` complet, multi-tenant, type-safe (Wayfinder), couvert de tests Pest, avec :
- Master data (Articles, Familles, Catégories, Tarifs, Clients, Fournisseurs, Emballages)
- Approvisionnements (normal / frigo / cession-in)
- Ventes (normale / frigo / cession-out)
- Consignes & déconsignations d'emballages
- Mouvements de stock (articles + emballages)
- Inventaires & pertes
- Charges, apports, versements, règlements clients, régularisations
- Paramètres système (taux, taxes, etc.)
- Reçus / factures / tickets PDF
- Tableaux de bord par rôle (6 rôles SYGES)
- Rapports de base (état des ventes, état des stocks, brouillard)

---

## Volume total estimé

~290 fichiers nouveaux :
- ~24 migrations · ~28 modèles Eloquent · ~28 factories
- ~20 contrôleurs · ~40 form requests · ~20 policies · ~12 services · ~9 enums
- ~80 pages Inertia React (.tsx) · ~12 composants partagés
- ~7 templates Blade PDF · ~25 fichiers de tests Pest

---

## Les 9 lots

### Lot 1 — Suppression & Enums ✅ DONE

**Phase 0 — Suppression**
- 8 fichiers supprimés : 5 composants drinks + 3 composants beverages orphelins + 2 dossiers routes Wayfinder + 1 test legacy
- `DashboardController.php` : 504 lignes retirées (8 méthodes + 2 helpers + props legacy)
- `routes/web.php` : 5 routes drinks/beverages retirées
- `dashboard.tsx`, `dashboards/index.ts`, `AdminDashboard.tsx`, `routes/dashboard/index.ts` : références purgées
- `welcome.tsx` mis à jour avec les 6 rôles SYGES

**Phase 1 — Enums**
- `TeamPermission` : +69 cases drinks
- `TeamRole` : +2 rôles (`Gerant`, `Ops`) + matrice mise à jour (6 rôles)
- 5 enums Drinks créés : `TransactionStatus`, `ProcurementKind`, `SaleKind`, `PaymentMode`, `StockMovementKind`
- Pint passé. PHP lint OK. Tests : 190/197 (7 échecs pré-existants non liés)
- `barryvdh/laravel-dompdf` : ⏸️ différé (Packagist SSL inaccessible)

---

### Lot 2 — Migrations, Models, Factories ⚠️ PARTIEL

**Migrations (~24 tables `drinks_*`)** ✅ 27/27 créées + `migrate fresh` OK

Tables créées :
- **Master data** : `drinks_categories`, `drinks_families`, `drinks_articles`, `drinks_pricing_tiers`, `drinks_clients`, `drinks_suppliers`, `drinks_packagings`, `drinks_settings`
- **Headers transactions** : `drinks_procurements`, `drinks_sales`, `drinks_inventories`, `drinks_losses`, `drinks_expense_types`, `drinks_expenses`, `drinks_cash_inputs`, `drinks_cash_deposits`, `drinks_payments`, `drinks_payment_adjustments`
- **Lignes transactions** : `drinks_procurement_article_lines`, `drinks_procurement_packaging_lines`, `drinks_sale_article_lines`, `drinks_sale_packaging_lines`, `drinks_inventory_lines`, `drinks_loss_lines`
- **Stock & journal** : `drinks_stock_movements`, `drinks_packaging_movements`, `drinks_stock_snapshots`

**Models Eloquent (`app/Models/Drinks/`)** ✅ 27/27 créés

**Factories (`database/factories/Drinks/`)** ❌ 0/19 créées — **interrompu par limite d'usage**

---

### Lot 3 — Services & Form Requests & Policies ⏳ À FAIRE

**Services (`app/Services/Drinks/`)**

| Service | Responsabilité |
|---|---|
| `StockService` | Crée `StockMovement` atomique + met à jour `Article::stock_qty` |
| `PackagingStockService` | Idem pour `Packaging` (consignes) |
| `ProcurementService` | `validate()`, `cancelValidation()` → mouvements de stock |
| `SaleService` | `validate()` → calcule remises, ristournes, consignations |
| `DeconsignmentService` | `process()` → retour emballages |
| `InventoryService` | `validate()` → ajuste stock par diff |
| `LossService` | `validate()` → décrémente stock + journal |
| `CashService` | Apports, charges, versements, règlements |
| `PaymentService` | Allocation paiement → vente, adjustments |
| `PdfService` | Rendu Blade → DomPDF, formats A4 et 80mm |
| `ReportService` | Brouillard, état des ventes, état des stocks, CA client |

**Form Requests** : paire `Store{X}Request` + `Update{X}Request` par ressource sous `app/Http/Requests/Drinks/`

**Policies** : une par ressource sous `app/Policies/Drinks/` (méthodes : `viewAny`, `view`, `create`, `update`, `delete`, `validate`, `cancelValidation`)

---

### Lot 4 — Contrôleurs Master Data + Pages Inertia ⏳ À FAIRE

Contrôleurs sous `app/Http/Controllers/Drinks/` :
`ArticleController`, `FamilyController`, `CategoryController`, `PricingTierController`, `ClientController`, `SupplierController`, `PackagingController`, `SettingController`

Pages Inertia sous `resources/js/pages/drinks/` :
`articles/`, `families/`, `categories/`, `pricing-tiers/`, `clients/`, `suppliers/`, `packagings/`

Tests Pest correspondants.

---

### Lot 5 — Approvisionnements ⏳ À FAIRE

- `ProcurementController` (3 kinds : Normal, Frigo, CessionIn)
- Pages : `procurements/{index,show,create,edit,validate}.tsx`
- Tests : workflow Draft → Validate → mouvements de stock créés → Cancel

---

### Lot 6 — Ventes & Consignes ⏳ À FAIRE

- `SaleController` (3 kinds : Normal, Frigo, CessionOut)
- Consignes & déconsignations d'emballages
- Pages : `sales/{index,show,create,edit,validate,receipt}.tsx`
- Tests : stock décrémenté + consignes créées + ristourne créditée

---

### Lot 7 — Inventaires, Pertes & Mouvements de stock ⏳ À FAIRE

- `InventoryController`, `LossController`, `StockMovementController`
- Pages : `inventories/`, `losses/`
- Tests : ajustement stock par diff, décrémentation stock + journal

---

### Lot 8 — Finance ⏳ À FAIRE

- `ExpenseController`, `ExpenseTypeController`, `CashInputController`, `CashDepositController`, `PaymentController`
- Pages : `expenses/`, `expense-types/`, `cash-inputs/`, `cash-deposits/`, `payments/`
- Tests : charges, apports, versements, règlements clients, régularisations

---

### Lot 9 — PDF, Reports, Dashboard, Finalisation ⏳ À FAIRE

**Templates Blade PDF** (`resources/views/drinks/pdf/`) :
- `procurement-receipt.blade.php` (A4)
- `sale-receipt-a4.blade.php` (A4)
- `sale-receipt-thermal.blade.php` (80mm)
- `payment-receipt.blade.php`
- `cash-deposit-receipt.blade.php`
- `inventory-report.blade.php`
- `brouillard.blade.php`

**Reports** (`ReportController`) : brouillard, état des ventes, état des stocks, CA client

**Dashboard** : `DashboardController` + `DrinksDashboardCards.tsx` par rôle (6 rôles)

**Sidebar** : refonte `drinksNav` dans `app-sidebar.tsx` avec toutes les routes filtrées par rôle

**Finalisation** :
- Installer `barryvdh/laravel-dompdf` (différé depuis Lot 1)
- `npm run build` → régénération Wayfinder + bundle frontend
- `php artisan test --compact --filter=Drinks` → suite complète
- Smoke tests browser (Pest 4) sur pages clés
- Vérification tenant isolation (team A invisible dans team B)

---

## ⚠️ État actuel

| Lot | Statut |
|---|---|
| Lot 1 — Suppression + Enums | ✅ Done |
| Lot 2 — Migrations + Models | ✅ Done (27/27) |
| Lot 2 — Factories | ❌ 0/19 (interrompu) |
| Lot 3 → Lot 9 | ⏳ Non commencés |
| DomPDF install | ⏸️ Différé (Packagist SSL) |

---

## 🔜 Prochaine étape

1. Finir les **19 factories** (`database/factories/Drinks/`)
2. Committer Lot 2
3. Démarrer **Lot 3** (Services + Form Requests + Policies)
