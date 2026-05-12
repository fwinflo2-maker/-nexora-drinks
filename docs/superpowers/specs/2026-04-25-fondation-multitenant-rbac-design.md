# Fondation Multi-tenant + RBAC — Spec Design

**Date :** 2026-04-25
**Statut :** Approuvé

## Objectif

Mettre en place la fondation SaaS multi-tenant de NEXORA : isolation des données par tenant via sous-domaine, système RBAC complet avec Spatie Permission, et les pages d'administration nécessaires.

## Architecture

### Identification du Tenant
- **Mécanisme :** Sous-domaine wildcard — `{slug}.nexora.cm`
- **Dev local :** Wildcard DNS `*.nexora.test` (Laragon/Herd)
- **Domaine racine `nexora.cm` :** Réservé au super_admin NEXORA
- **Résolution :** Middleware `ResolveTenant` extrait le slug, charge le `Tenant`, bind dans le container Laravel

### Isolation des Données
- Toutes les tables métier ont une colonne `tenant_id BIGINT UNSIGNED`
- Trait `BelongsToTenant` ajoute un `GlobalScope` Eloquent automatique filtrant par `tenant_id`
- `creating` Observer injecte automatiquement le `tenant_id` courant à la création

### Authentification
- Laravel Fortify (déjà installé) gère login/register/2FA
- Session cookie scopée au sous-domaine (`SESSION_DOMAIN=.nexora.cm`)
- Après login : redirect vers le dashboard approprié selon le rôle

### Espaces Applicatifs
1. `nexora.cm` → Super Admin NEXORA (gestion tenants, plans)
2. `{slug}.nexora.cm` → Application tenant NEXORA
3. `api.nexora.cm` → API REST (Phase 2 — PWA terrain)

## RBAC avec Spatie Permission

### Configuration
- `teams` activé dans Spatie (team = tenant)
- `team_foreign_key = 'tenant_id'`
- Les rôles sont scoped par tenant → chaque tenant a ses propres instances de rôles

### Rôles Système (9)
| Rôle | Description |
|------|-------------|
| `super_admin` | Administration NEXORA (hors tenant) |
| `admin` | Directeur entreprise — accès total |
| `manager` | Responsable opérations |
| `magasinier` | Responsable entrepôt |
| `commercial` | Agent terrain |
| `livreur` | Chauffeur livreur |
| `caissier` | Agent caisse |
| `comptable` | Responsable finances |
| `client_api` | Intégration externe |

### Permissions par Module (Phase 1)
Format : `{module}.{action}`
- **stock** : `stock.view`, `stock.create`, `stock.update`, `stock.delete`
- **orders** : `orders.view`, `orders.create`, `orders.update`, `orders.delete`
- **clients** : `clients.view`, `clients.create`, `clients.update`, `clients.delete`
- **users** : `users.view`, `users.create`, `users.update`, `users.delete`
- **settings** : `settings.view`, `settings.update`
- **finance** : `finance.view`, `finance.create`, `finance.update`
- **deliveries** : `deliveries.view`, `deliveries.create`, `deliveries.update`
- **consignment** : `consignment.view`, `consignment.create`, `consignment.update`
- **pos** : `pos.view`, `pos.create`
- **invoices** : `invoices.view`, `invoices.create`, `invoices.update`
- **reports** : `reports.view`

### Personnalisation par Tenant
Les tenants peuvent via `tenant_settings` :
- Renommer les rôles (label affiché uniquement, pas le slug système)
- Ajouter/retirer des permissions d'un rôle dans les limites de leur plan

## Modèle de Données

### Table `tenants`
```sql
id, name, slug (unique), domain (nullable),
plan ENUM('starter','pro','enterprise') DEFAULT 'starter',
settings_json TEXT,
logo_path VARCHAR(255),
is_active BOOLEAN DEFAULT true,
trial_ends_at TIMESTAMP,
created_at, updated_at, deleted_at
```

### Modifications Table `users`
```sql
+ tenant_id BIGINT UNSIGNED NULLABLE (null = super_admin NEXORA)
+ phone VARCHAR(30) NULLABLE
+ is_active BOOLEAN DEFAULT true
+ last_login_at TIMESTAMP NULLABLE
```

### Table `tenant_settings`
```sql
id, tenant_id, key VARCHAR(100), value TEXT, created_at
UNIQUE(tenant_id, key)
```

## Pages & Routes

### Domaine racine (super_admin)
- `GET /` → `SuperAdmin/Dashboard`
- `GET /tenants` → `SuperAdmin/Tenants/Index`
- `POST /tenants` → Créer tenant + admin
- `GET /tenants/{id}` → `SuperAdmin/Tenants/Show`
- `PATCH /tenants/{id}` → Modifier tenant
- `DELETE /tenants/{id}` → Désactiver tenant

### Domaine tenant
- `GET /login` → `Auth/Login` (Fortify — déjà existant)
- `GET /register` → `Tenant/Register` (inscription publique — crée tenant + admin)
- `GET /dashboard` → Redirect selon rôle
- `GET /settings/users` → `Settings/Users/Index`
- `POST /settings/users` → Inviter utilisateur
- `PATCH /settings/users/{id}` → Modifier rôle
- `DELETE /settings/users/{id}` → Désactiver utilisateur
- `GET /settings/roles` → `Settings/Roles/Index`
- `PATCH /settings/roles/{role}` → Personnaliser permissions

## Sécurité

- Tenant inexistant ou inactif → HTTP 404
- Utilisateur d'un autre tenant → HTTP 403 (middleware `EnsureTenantMember`)
- Super_admin sans tenant résolu → accès limité à l'espace super_admin uniquement
- Rate limiting : `throttle:6,1` sur `/login`

## Testing

- Test : résolution tenant par sous-domaine ✅/❌
- Test : isolation données inter-tenant (un user ne voit pas les données d'un autre tenant)
- Test : chaque rôle accède uniquement à ses modules
- Test : inscription publique crée bien tenant + admin
- Test : super_admin crée tenant manuellement

## Stack Technique

- Laravel 13 + Fortify v1 + Inertia.js v3 + React 19 + TailwindCSS v4
- `spatie/laravel-permission` avec teams activés
- `spatie/laravel-activitylog` pour l'audit trail
- Pest v4 pour tous les tests
