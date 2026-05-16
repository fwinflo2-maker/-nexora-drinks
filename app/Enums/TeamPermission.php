<?php

namespace App\Enums;

enum TeamPermission: string
{
    // Team management
    case UpdateTeam = 'team:update';
    case DeleteTeam = 'team:delete';

    // Member management
    case AddMember = 'member:add';
    case UpdateMember = 'member:update';
    case RemoveMember = 'member:remove';

    // Invitations
    case CreateInvitation = 'invitation:create';
    case CancelInvitation = 'invitation:cancel';

    // Stock & Entrepôt
    case StockView = 'stock:view';
    case StockCreate = 'stock:create';
    case StockUpdate = 'stock:update';
    case StockDelete = 'stock:delete';

    // Commandes
    case OrdersView = 'orders:view';
    case OrdersCreate = 'orders:create';
    case OrdersUpdate = 'orders:update';
    case OrdersDelete = 'orders:delete';

    // Clients & CRM
    case ClientsView = 'clients:view';
    case ClientsCreate = 'clients:create';
    case ClientsUpdate = 'clients:update';
    case ClientsDelete = 'clients:delete';

    // Livraisons & Tournées
    case DeliveriesView = 'deliveries:view';
    case DeliveriesCreate = 'deliveries:create';
    case DeliveriesUpdate = 'deliveries:update';

    // Consignation
    case ConsignmentView = 'consignment:view';
    case ConsignmentCreate = 'consignment:create';
    case ConsignmentUpdate = 'consignment:update';

    // Caisse / POS
    case PosView = 'pos:view';
    case PosCreate = 'pos:create';

    // Facturation
    case InvoicesView = 'invoices:view';
    case InvoicesCreate = 'invoices:create';
    case InvoicesUpdate = 'invoices:update';

    // Finance & Rapports
    case FinanceView = 'finance:view';
    case FinanceCreate = 'finance:create';
    case FinanceUpdate = 'finance:update';

    // Rapports
    case ReportsView = 'reports:view';

    // Utilisateurs / Config
    case UsersView = 'users:view';
    case UsersCreate = 'users:create';
    case UsersUpdate = 'users:update';
    case UsersDelete = 'users:delete';

    // Settings
    case SettingsView = 'settings:view';
    case SettingsUpdate = 'settings:update';

    // ───────────────────────────────────────────────────────────────────────
    // Secteur DRINKS (distribution boissons) — issu de SYGES
    // ───────────────────────────────────────────────────────────────────────

    // Master data
    case DrinksArticlesView = 'drinks.articles:view';
    case DrinksArticlesCreate = 'drinks.articles:create';
    case DrinksArticlesUpdate = 'drinks.articles:update';
    case DrinksArticlesDelete = 'drinks.articles:delete';

    case DrinksCategoriesView = 'drinks.categories:view';
    case DrinksCategoriesCreate = 'drinks.categories:create';
    case DrinksCategoriesUpdate = 'drinks.categories:update';
    case DrinksCategoriesDelete = 'drinks.categories:delete';

    case DrinksPricingTiersView = 'drinks.pricing_tiers:view';
    case DrinksPricingTiersCreate = 'drinks.pricing_tiers:create';
    case DrinksPricingTiersUpdate = 'drinks.pricing_tiers:update';
    case DrinksPricingTiersDelete = 'drinks.pricing_tiers:delete';

    case DrinksClientsView = 'drinks.clients:view';
    case DrinksClientsCreate = 'drinks.clients:create';
    case DrinksClientsUpdate = 'drinks.clients:update';
    case DrinksClientsDelete = 'drinks.clients:delete';

    case DrinksSuppliersView = 'drinks.suppliers:view';
    case DrinksSuppliersCreate = 'drinks.suppliers:create';
    case DrinksSuppliersUpdate = 'drinks.suppliers:update';
    case DrinksSuppliersDelete = 'drinks.suppliers:delete';

    case DrinksPackagingsView = 'drinks.packagings:view';
    case DrinksPackagingsCreate = 'drinks.packagings:create';
    case DrinksPackagingsUpdate = 'drinks.packagings:update';
    case DrinksPackagingsDelete = 'drinks.packagings:delete';

    // Transactions opérationnelles
    case DrinksProcurementsView = 'drinks.procurements:view';
    case DrinksProcurementsCreate = 'drinks.procurements:create';
    case DrinksProcurementsUpdate = 'drinks.procurements:update';
    case DrinksProcurementsDelete = 'drinks.procurements:delete';
    case DrinksProcurementsValidate = 'drinks.procurements:validate';

    case DrinksSalesView = 'drinks.sales:view';
    case DrinksSalesCreate = 'drinks.sales:create';
    case DrinksSalesUpdate = 'drinks.sales:update';
    case DrinksSalesDelete = 'drinks.sales:delete';
    case DrinksSalesValidate = 'drinks.sales:validate';

    case DrinksInventoriesView = 'drinks.inventories:view';
    case DrinksInventoriesCreate = 'drinks.inventories:create';
    case DrinksInventoriesUpdate = 'drinks.inventories:update';
    case DrinksInventoriesDelete = 'drinks.inventories:delete';
    case DrinksInventoriesValidate = 'drinks.inventories:validate';

    case DrinksLossesView = 'drinks.losses:view';
    case DrinksLossesCreate = 'drinks.losses:create';
    case DrinksLossesUpdate = 'drinks.losses:update';
    case DrinksLossesDelete = 'drinks.losses:delete';
    case DrinksLossesValidate = 'drinks.losses:validate';

    // Finance
    case DrinksExpenseTypesView = 'drinks.expense_types:view';
    case DrinksExpenseTypesCreate = 'drinks.expense_types:create';
    case DrinksExpenseTypesUpdate = 'drinks.expense_types:update';
    case DrinksExpenseTypesDelete = 'drinks.expense_types:delete';

    case DrinksExpensesView = 'drinks.expenses:view';
    case DrinksExpensesCreate = 'drinks.expenses:create';
    case DrinksExpensesUpdate = 'drinks.expenses:update';
    case DrinksExpensesDelete = 'drinks.expenses:delete';
    case DrinksExpensesValidate = 'drinks.expenses:validate';

    case DrinksCashInputsView = 'drinks.cash_inputs:view';
    case DrinksCashInputsCreate = 'drinks.cash_inputs:create';
    case DrinksCashInputsUpdate = 'drinks.cash_inputs:update';
    case DrinksCashInputsValidate = 'drinks.cash_inputs:validate';

    case DrinksCashDepositsView = 'drinks.cash_deposits:view';
    case DrinksCashDepositsCreate = 'drinks.cash_deposits:create';
    case DrinksCashDepositsUpdate = 'drinks.cash_deposits:update';
    case DrinksCashDepositsDelete = 'drinks.cash_deposits:delete';
    case DrinksCashDepositsValidate = 'drinks.cash_deposits:validate';

    case DrinksPaymentsView = 'drinks.payments:view';
    case DrinksPaymentsCreate = 'drinks.payments:create';
    case DrinksPaymentsUpdate = 'drinks.payments:update';
    case DrinksPaymentsDelete = 'drinks.payments:delete';

    // Lecture / dashboards / settings
    case DrinksStockMovementsView = 'drinks.stock_movements:view';
    case DrinksStockSnapshotsView = 'drinks.stock_snapshots:view';
    case DrinksReportsView = 'drinks.reports:view';
    case DrinksSettingsManage = 'drinks.settings:manage';
    case DrinksDashboardView = 'drinks.dashboard:view';

    // ───────────────────────────────────────────────────────────────────────
    // Secteur HOTEL
    // ───────────────────────────────────────────────────────────────────────

    case HotelDashboardView = 'hotel.dashboard:view';

    case HotelRoomsView = 'hotel.rooms:view';
    case HotelRoomsCreate = 'hotel.rooms:create';
    case HotelRoomsEdit = 'hotel.rooms:edit';
    case HotelRoomsDelete = 'hotel.rooms:delete';

    case HotelRoomTypesView = 'hotel.room_types:view';
    case HotelRoomTypesCreate = 'hotel.room_types:create';
    case HotelRoomTypesEdit = 'hotel.room_types:edit';
    case HotelRoomTypesDelete = 'hotel.room_types:delete';

    case HotelReservationsView = 'hotel.reservations:view';
    case HotelReservationsCreate = 'hotel.reservations:create';
    case HotelReservationsEdit = 'hotel.reservations:edit';
    case HotelReservationsDelete = 'hotel.reservations:delete';
    case HotelReservationsCheckin = 'hotel.reservations:checkin';
    case HotelReservationsCheckout = 'hotel.reservations:checkout';
    case HotelReservationsCancel = 'hotel.reservations:cancel';

    case HotelGuestsView = 'hotel.guests:view';
    case HotelGuestsCreate = 'hotel.guests:create';
    case HotelGuestsEdit = 'hotel.guests:edit';

    case HotelFoliosView = 'hotel.folios:view';
    case HotelFoliosManage = 'hotel.folios:manage';

    case HotelReportsView = 'hotel.reports:view';
    case HotelReportsExport = 'hotel.reports:export';

    // ───────────────────────────────────────────────────────────────────────
    // Secteur F&B (Restauration)
    // ───────────────────────────────────────────────────────────────────────

    case FnBDashboardView = 'fnb.dashboard:view';

    case FnBMenuView = 'fnb.menu:view';
    case FnBMenuCreate = 'fnb.menu:create';
    case FnBMenuEdit = 'fnb.menu:edit';
    case FnBMenuDelete = 'fnb.menu:delete';

    case FnBCategoriesView = 'fnb.categories:view';
    case FnBCategoriesManage = 'fnb.categories:manage';

    case FnBTablesView = 'fnb.tables:view';
    case FnBTablesManage = 'fnb.tables:manage';

    case FnBOrdersView = 'fnb.orders:view';
    case FnBOrdersCreate = 'fnb.orders:create';
    case FnBOrdersEdit = 'fnb.orders:edit';
    case FnBOrdersClose = 'fnb.orders:close';
    case FnBOrdersCancel = 'fnb.orders:cancel';

    case FnBKitchenView = 'fnb.kitchen:view';
    case FnBKitchenUpdateStatus = 'fnb.kitchen:update_status';

    case FnBReportsView = 'fnb.reports:view';
    case FnBReportsExport = 'fnb.reports:export';
}
