# Documentation Technique : Secteur Drinks - Nexora ERP

Ce document récapitule l'ensemble des fonctionnalités et architectures implémentées pour le secteur spécialisé **"Drinks"** au sein de l'écosystème Nexora.

## 1. Vue d'Ensemble
Le secteur Drinks est un module ERP complet conçu spécifiquement pour la distribution de boissons. Il gère l'intégralité du cycle de vie des produits, de l'achat auprès des fournisseurs jusqu'à la livraison finale aux clients, tout en assurant une traçabilité rigoureuse des emballages (consignes) et des flux financiers.

---

## 2. Modules Fonctionnels

### 📦 Gestion du Catalogue et des Stocks
- **Articles & Produits** : Gestion détaillée des boissons (nom, SKU, catégorie, emballage par défaut, prix de base).
- **Catégories** : Organisation logique des produits par types (Sodas, Bières, Eaux, Spiritueux, etc.).
- **Gestion des Emballages (Consignes)** : Suivi précis des bouteilles et caisses consignées, essentiel pour le secteur des boissons.
- **Mouvements de Stock** : Enregistrement automatique de chaque entrée/sortie avec motif (vente, achat, perte, inventaire).
- **Instantanés de Stock (Snapshots)** : Système de capture de l'état du stock à un instant T pour les audits et rapports de fin de journée.
- **Inventaires** : Outil de réajustement périodique pour faire correspondre le stock théorique au stock physique.

### 💰 Gestion Commerciale et Ventes
- **Ventes (Sales)** : Interface de saisie rapide des commandes avec gestion multi-lignes (Articles + Emballages).
- **Niveaux de Prix (Pricing Tiers)** : Flexibilité tarifaire permettant d'appliquer des prix différents selon le profil du client (Grossiste, Détail, VIP).
- **Gestion des Clients** : Fiches clients complètes incluant les coordonnées, la zone géographique et le suivi des balances (crédits et consignes).

### 🚛 Supply Chain et Opérations
- **Approvisionnements (Procurements)** : Gestion des achats auprès des fournisseurs pour le réapprovisionnement des stocks.
- **Gestion des Fournisseurs** : Base de données des partenaires logistiques et fournisseurs de boissons.
- **Pertes et Casses (Losses)** : Module dédié à l'enregistrement des produits périmés ou cassés pour une comptabilité de stock exacte.
- **Déconsignation** : Processus de retour et de remboursement des emballages vides.

### 💵 Finance et Trésorerie
- **Paiements** : Suivi des règlements clients (espèces, virement, mobile money).
- **Entrées de Caisse (Cash Inputs)** : Enregistrement des flux entrants hors ventes directes.
- **Dépôts de Caisse (Cash Deposits)** : Gestion des transferts de fonds vers la banque ou la caisse centrale.
- **Gestion des Dépenses (Expenses)** : Suivi des frais opérationnels liés au secteur (carburant, entretien, etc.).

---

## 3. Interface Utilisateur (UI/UX)

Le secteur Drinks bénéficie d'une charte graphique "Premium" spécifique :
- **Palette de Couleurs** : Utilisation de tons **Ambre et Or**, évoquant l'univers des boissons, sur un fond sombre ou blanc épuré selon le mode.
- **Design en Cartes (Card-based)** : Remplacement des tableaux classiques par des cartes interactives et animées pour une meilleure lisibilité sur tablette et mobile.
- **Layout Edge-to-Edge** : Utilisation maximale de l'espace écran pour réduire le défilement et prioriser les données critiques.
- **Conseils Nexora (Sidebar AI)** : Un assistant intelligent intégré dans une barre latérale pour guider l'utilisateur dans ses tâches quotidiennes.

---

## 4. Nexora AI : Assistant de Guidance
L'intelligence artificielle est au cœur du module Drinks :
- **Analyse en temps réel** : Aide à l'identification des ruptures de stock imminentes.
- **Chat contextuel** : Assistance directe pour les opérations complexes (ex: "Comment enregistrer un retour de consignes complexe ?").
- **Tableau de bord intelligent** : Suggestions basées sur les tendances de ventes et les anomalies détectées.

---

## 5. Architecture Technique

### Sécurité et Isolation
- **Multi-tenant** : Isolation stricte des données par `team_id`. Un utilisateur d'une équipe "A" ne peut jamais voir les stocks ou les ventes de l'équipe "B".
- **Autorisations (Gates/Policies)** : Contrôle d'accès granulaire sur chaque action (création d'article, validation de vente, etc.).

### Technologies Utilisées
- **Backend** : PHP 8+ avec Laravel, structuré dans des espaces de noms dédiés (`App\Http\Controllers\Drinks`).
- **Frontend** : React.js avec TypeScript et Inertia.js pour une expérience fluide d'application monopage (SPA).
- **Branding de documents** : Injection dynamique des logos d'équipe et métadonnées dans les rapports PDF générés.

---

## 6. Prochaines Étapes / Évolutions
- Intégration d'une application mobile dédiée aux livreurs pour les tournées.
- Analyse prédictive des ventes pour optimiser les achats fournisseurs.
- Système de parrainage et de fidélité client avancé.

---
*Document généré le 11 Mai 2026 pour le projet Nexora.*
