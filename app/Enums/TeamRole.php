<?php

namespace App\Enums;

enum TeamRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Manager = 'manager';
    case Gerant = 'gerant';
    case Ops = 'ops';
    case Magasinier = 'magasinier';
    case Commercial = 'commercial';
    case Livreur = 'livreur';
    case Logisticien = 'logisticien';
    case Caissier = 'caissier';
    case Comptable = 'comptable';
    case Member = 'member';

    /**
     * Get the display label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Propriétaire',
            self::Admin => 'Administrateur',
            self::Manager => 'Manager',
            self::Gerant => 'Gérant',
            self::Ops => 'Opérations (OPS)',
            self::Magasinier => 'Magasinier',
            self::Commercial => 'Commercial',
            self::Livreur => 'Livreur',
            self::Logisticien => 'Logisticien',
            self::Caissier => 'Caissier',
            self::Comptable => 'Comptable',
            self::Member => 'Membre',
        };
    }

    /**
     * Get all the permissions for this role.
     *
     * @return array<TeamPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Owner => TeamPermission::cases(),

            self::Admin => [
                // Team management
                TeamPermission::UpdateTeam,
                TeamPermission::CreateInvitation,
                TeamPermission::CancelInvitation,
                TeamPermission::AddMember,
                TeamPermission::UpdateMember,
                TeamPermission::RemoveMember,
                // All cross-sector modules
                TeamPermission::StockView, TeamPermission::StockCreate, TeamPermission::StockUpdate, TeamPermission::StockDelete,
                TeamPermission::OrdersView, TeamPermission::OrdersCreate, TeamPermission::OrdersUpdate, TeamPermission::OrdersDelete,
                TeamPermission::ClientsView, TeamPermission::ClientsCreate, TeamPermission::ClientsUpdate, TeamPermission::ClientsDelete,
                TeamPermission::DeliveriesView, TeamPermission::DeliveriesCreate, TeamPermission::DeliveriesUpdate,
                TeamPermission::ConsignmentView, TeamPermission::ConsignmentCreate, TeamPermission::ConsignmentUpdate,
                TeamPermission::PosView, TeamPermission::PosCreate,
                TeamPermission::InvoicesView, TeamPermission::InvoicesCreate, TeamPermission::InvoicesUpdate,
                TeamPermission::FinanceView, TeamPermission::FinanceCreate, TeamPermission::FinanceUpdate,
                TeamPermission::ReportsView,
                TeamPermission::UsersView, TeamPermission::UsersCreate, TeamPermission::UsersUpdate, TeamPermission::UsersDelete,
                TeamPermission::SettingsView, TeamPermission::SettingsUpdate,
                // Drinks — full access
                ...self::drinksFullAccess(),
            ],

            self::Manager => [
                TeamPermission::CreateInvitation,
                TeamPermission::CancelInvitation,
                TeamPermission::StockView, TeamPermission::StockCreate, TeamPermission::StockUpdate, TeamPermission::StockDelete,
                TeamPermission::OrdersView, TeamPermission::OrdersCreate, TeamPermission::OrdersUpdate, TeamPermission::OrdersDelete,
                TeamPermission::ClientsView, TeamPermission::ClientsCreate, TeamPermission::ClientsUpdate, TeamPermission::ClientsDelete,
                TeamPermission::DeliveriesView, TeamPermission::DeliveriesCreate, TeamPermission::DeliveriesUpdate,
                TeamPermission::ConsignmentView, TeamPermission::ConsignmentCreate, TeamPermission::ConsignmentUpdate,
                TeamPermission::PosView,
                TeamPermission::InvoicesView, TeamPermission::InvoicesCreate, TeamPermission::InvoicesUpdate,
                TeamPermission::FinanceView,
                TeamPermission::ReportsView,
            ],

            // Gérant Boissons : équivalent Admin Drinks sans Settings & sans gestion d'utilisateurs.
            self::Gerant => [
                TeamPermission::CreateInvitation,
                TeamPermission::CancelInvitation,
                TeamPermission::UsersView,
                TeamPermission::UsersCreate,
                TeamPermission::UsersUpdate,
                TeamPermission::UsersDelete,
                TeamPermission::DrinksDashboardView,
                TeamPermission::DrinksArticlesView, TeamPermission::DrinksArticlesCreate,
                TeamPermission::DrinksArticlesUpdate, TeamPermission::DrinksArticlesDelete,
                TeamPermission::DrinksCategoriesView,
                TeamPermission::DrinksSuppliersView, TeamPermission::DrinksSuppliersCreate,
                TeamPermission::DrinksSuppliersUpdate, TeamPermission::DrinksSuppliersDelete,
                TeamPermission::DrinksPackagingsView,
                TeamPermission::DrinksSalesView,
                TeamPermission::DrinksProcurementsView, TeamPermission::DrinksProcurementsCreate,
                TeamPermission::DrinksProcurementsUpdate, TeamPermission::DrinksProcurementsDelete,
                TeamPermission::DrinksProcurementsValidate,
                TeamPermission::DrinksInventoriesView, TeamPermission::DrinksInventoriesCreate,
                TeamPermission::DrinksInventoriesUpdate, TeamPermission::DrinksInventoriesDelete,
                TeamPermission::DrinksInventoriesValidate,
                TeamPermission::DrinksLossesView, TeamPermission::DrinksLossesCreate,
                TeamPermission::DrinksLossesUpdate, TeamPermission::DrinksLossesDelete,
                TeamPermission::DrinksLossesValidate,
                TeamPermission::DrinksStockMovementsView,
                TeamPermission::DrinksStockSnapshotsView,
                TeamPermission::DrinksReportsView,
            ],

            // OPS : approvisionnements, fournisseurs, inventaires, pertes, mouvements, rapports stock.
            self::Ops => [
                TeamPermission::DrinksDashboardView,
                TeamPermission::DrinksArticlesView, TeamPermission::DrinksArticlesCreate,
                TeamPermission::DrinksArticlesUpdate, TeamPermission::DrinksArticlesDelete,
                TeamPermission::DrinksCategoriesView,
                TeamPermission::DrinksSuppliersView, TeamPermission::DrinksSuppliersCreate,
                TeamPermission::DrinksSuppliersUpdate, TeamPermission::DrinksSuppliersDelete,
                TeamPermission::DrinksPackagingsView,
                TeamPermission::DrinksSalesView,
                TeamPermission::DrinksProcurementsView, TeamPermission::DrinksProcurementsCreate,
                TeamPermission::DrinksProcurementsUpdate, TeamPermission::DrinksProcurementsDelete,
                TeamPermission::DrinksProcurementsValidate,
                TeamPermission::DrinksInventoriesView, TeamPermission::DrinksInventoriesCreate,
                TeamPermission::DrinksInventoriesUpdate, TeamPermission::DrinksInventoriesDelete,
                TeamPermission::DrinksInventoriesValidate,
                TeamPermission::DrinksLossesView, TeamPermission::DrinksLossesCreate,
                TeamPermission::DrinksLossesUpdate, TeamPermission::DrinksLossesDelete,
                TeamPermission::DrinksLossesValidate,
                TeamPermission::DrinksStockMovementsView,
                TeamPermission::DrinksStockSnapshotsView,
                TeamPermission::DrinksReportsView,
            ],

            self::Magasinier => [
                TeamPermission::StockView, TeamPermission::StockCreate, TeamPermission::StockUpdate, TeamPermission::StockDelete,
                TeamPermission::OrdersView,
                TeamPermission::DeliveriesView, TeamPermission::DeliveriesCreate,
                TeamPermission::ConsignmentView, TeamPermission::ConsignmentCreate, TeamPermission::ConsignmentUpdate,
                // Drinks : stocks, emballages, inventaires, pertes
                TeamPermission::DrinksDashboardView,
                TeamPermission::DrinksArticlesView,
                TeamPermission::DrinksPackagingsView, TeamPermission::DrinksPackagingsCreate,
                TeamPermission::DrinksPackagingsUpdate, TeamPermission::DrinksPackagingsDelete,
                TeamPermission::DrinksInventoriesView, TeamPermission::DrinksInventoriesCreate,
                TeamPermission::DrinksInventoriesUpdate, TeamPermission::DrinksInventoriesDelete,
                TeamPermission::DrinksInventoriesValidate,
                TeamPermission::DrinksLossesView, TeamPermission::DrinksLossesCreate,
                TeamPermission::DrinksLossesUpdate, TeamPermission::DrinksLossesDelete,
                TeamPermission::DrinksLossesValidate,
                TeamPermission::DrinksStockMovementsView,
                TeamPermission::DrinksStockSnapshotsView,
                TeamPermission::DrinksReportsView,
            ],

            self::Commercial => [
                TeamPermission::StockView,
                TeamPermission::OrdersView, TeamPermission::OrdersCreate,
                TeamPermission::ClientsView, TeamPermission::ClientsCreate, TeamPermission::ClientsUpdate, TeamPermission::ClientsDelete,
                TeamPermission::ConsignmentView,
                TeamPermission::InvoicesView,
                // Drinks : ventes et clients
                TeamPermission::DrinksDashboardView,
                TeamPermission::DrinksArticlesView,
                TeamPermission::DrinksClientsView, TeamPermission::DrinksClientsCreate,
                TeamPermission::DrinksClientsUpdate, TeamPermission::DrinksClientsDelete,
                TeamPermission::DrinksSalesView,
            ],

            self::Livreur => [
                TeamPermission::StockView,
                TeamPermission::OrdersView,
                TeamPermission::DeliveriesView, TeamPermission::DeliveriesUpdate,
                TeamPermission::ConsignmentView, TeamPermission::ConsignmentCreate, TeamPermission::ConsignmentUpdate,
                TeamPermission::ClientsView,
            ],

            self::Logisticien => [
                TeamPermission::StockView,
                TeamPermission::OrdersView, TeamPermission::OrdersUpdate,
                TeamPermission::DeliveriesView, TeamPermission::DeliveriesCreate, TeamPermission::DeliveriesUpdate,
                TeamPermission::ConsignmentView, TeamPermission::ConsignmentCreate, TeamPermission::ConsignmentUpdate,
                TeamPermission::ClientsView,
                TeamPermission::ReportsView,
            ],

            self::Caissier => [
                TeamPermission::PosView, TeamPermission::PosCreate,
                TeamPermission::InvoicesView, TeamPermission::InvoicesCreate,
                TeamPermission::ClientsView,
                TeamPermission::FinanceView,
                // Drinks : ventes, paiements, clients (R), tarifs (R)
                TeamPermission::DrinksDashboardView,
                TeamPermission::DrinksArticlesView,
                TeamPermission::DrinksPricingTiersView,
                TeamPermission::DrinksClientsView,
                TeamPermission::DrinksPackagingsView,
                TeamPermission::DrinksSalesView, TeamPermission::DrinksSalesCreate,
                TeamPermission::DrinksSalesUpdate, TeamPermission::DrinksSalesDelete,
                TeamPermission::DrinksSalesValidate,
                TeamPermission::DrinksPaymentsView, TeamPermission::DrinksPaymentsCreate,
                TeamPermission::DrinksPaymentsUpdate, TeamPermission::DrinksPaymentsDelete,
            ],

            self::Comptable => [
                TeamPermission::StockView,
                TeamPermission::OrdersView,
                TeamPermission::ClientsView,
                TeamPermission::DeliveriesView,
                TeamPermission::ConsignmentView,
                TeamPermission::PosView,
                TeamPermission::InvoicesView, TeamPermission::InvoicesCreate, TeamPermission::InvoicesUpdate,
                TeamPermission::FinanceView, TeamPermission::FinanceCreate, TeamPermission::FinanceUpdate,
                TeamPermission::ReportsView,
                // Drinks : finance, charges, apports, versements, règlements, rapports
                TeamPermission::DrinksDashboardView,
                TeamPermission::DrinksClientsView,
                TeamPermission::DrinksExpenseTypesView, TeamPermission::DrinksExpenseTypesCreate,
                TeamPermission::DrinksExpenseTypesUpdate, TeamPermission::DrinksExpenseTypesDelete,
                TeamPermission::DrinksExpensesView, TeamPermission::DrinksExpensesCreate,
                TeamPermission::DrinksExpensesUpdate, TeamPermission::DrinksExpensesDelete,
                TeamPermission::DrinksExpensesValidate,
                TeamPermission::DrinksCashInputsView, TeamPermission::DrinksCashInputsCreate,
                TeamPermission::DrinksCashInputsUpdate, TeamPermission::DrinksCashInputsValidate,
                TeamPermission::DrinksCashDepositsView, TeamPermission::DrinksCashDepositsCreate,
                TeamPermission::DrinksCashDepositsUpdate, TeamPermission::DrinksCashDepositsValidate,
                TeamPermission::DrinksPaymentsView, TeamPermission::DrinksPaymentsCreate,
                TeamPermission::DrinksPaymentsUpdate, TeamPermission::DrinksPaymentsDelete,
                TeamPermission::DrinksReportsView,
            ],

            self::Member => [],
        };
    }

    /**
     * Determine if the role has the given permission.
     */
    public function hasPermission(TeamPermission $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    /**
     * Get the hierarchy level for this role.
     * Higher numbers indicate higher privileges.
     */
    public function level(): int
    {
        return match ($this) {
            self::Owner => 10,
            self::Admin => 9,
            self::Manager => 8,
            self::Gerant => 8,
            self::Ops => 6,
            self::Magasinier => 5,
            self::Commercial => 5,
            self::Livreur => 5,
            self::Logisticien => 5,
            self::Caissier => 4,
            self::Comptable => 4,
            self::Member => 1,
        };
    }

    /**
     * Check if this role is at least as privileged as another role.
     */
    public function isAtLeast(TeamRole $role): bool
    {
        return $this->level() >= $role->level();
    }

    /**
     * Get the roles that can be assigned to team members (excludes Owner).
     *
     * @return array<array{value: string, label: string}>
     */
    public static function assignable(): array
    {
        return collect(self::cases())
            ->filter(fn (self $role) => $role !== self::Owner)
            ->map(fn (self $role) => ['value' => $role->value, 'label' => $role->label()])
            ->values()
            ->toArray();
    }

    /**
     * All drinks-sector permissions, used by Admin and Gerant role mappings.
     *
     * @return array<TeamPermission>
     */
    private static function drinksFullAccess(): array
    {
        return collect(TeamPermission::cases())
            ->filter(fn (TeamPermission $p) => str_starts_with($p->value, 'drinks.'))
            ->values()
            ->all();
    }
}
