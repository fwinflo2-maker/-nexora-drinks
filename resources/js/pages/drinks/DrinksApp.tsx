import { Head, Link, router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Box, Truck, ShoppingCart, TrendingDown, Wallet, DollarSign, Package,
    FileText, BarChart2, PackageCheck, Receipt, BarChart3, Settings, LogOut, UserCircle, Camera,
    Menu, X, History
} from 'lucide-react';
import { User } from 'lucide-react';
import { useState, useEffect } from 'react';
import React from 'react';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { logout } from '@/routes';
import type { Team } from '@/types';
import CashInputShow from './components/CashInputShow';
import CashDepositIndex from './components/CashDepositIndex';
import CashDepositShow from './components/CashDepositShow';
import PaymentIndex from './components/PaymentIndex';
import PaymentShow from './components/PaymentShow';
import ReportView from './components/ReportView';
import DashboardView from './components/DashboardView';
import SaleForm from './components/SaleForm';
import ProcurementForm from './components/ProcurementForm';
import InventoryForm from './components/InventoryForm';
import LossForm from './components/LossForm';
import LossShow from './components/LossShow';
import ExpenseForm from './components/ExpenseForm';
import PaymentForm from './components/PaymentForm';
import CashInputForm from './components/CashInputForm';
import CashDepositForm from './components/CashDepositForm';
import ArticleShow from './components/ArticleShow';
import ArticleForm from './components/ArticleForm';
import ArticleIndex from './components/ArticleIndex';
import CashInputIndex from './components/CashInputIndex';
import RolesAccessView from './components/RolesAccessView';
import CategoryForm from './components/CategoryForm';
import CategoryIndex from './components/CategoryIndex';
import PackagingForm from './components/PackagingForm';
import PackagingIndex from './components/PackagingIndex';
import ClientForm from './components/ClientForm';
import ClientIndex from './components/ClientIndex';
import SupplierForm from './components/SupplierForm';
import SupplierIndex from './components/SupplierIndex';
import PricingTierForm from './components/PricingTierForm';
import PricingTierIndex from './components/PricingTierIndex';
import ExpenseTypeForm from './components/ExpenseTypeForm';
import ExpenseTypeIndex from './components/ExpenseTypeIndex';
import LossIndex from './components/LossIndex';
import StockMovementIndex from './components/StockMovementIndex';
import StockMovementShow from './components/StockMovementShow';
import ClientShow from './components/ClientShow';
import StockSnapshotIndex from './components/StockSnapshotIndex';
import StockSnapshotShow from './components/StockSnapshotShow';

import DrinksChatWidget from './components/DrinksChatWidget';
import ExpenseIndex from './components/ExpenseIndex';
import ExpenseShow from './components/ExpenseShow';
import InventoryIndex from './components/InventoryIndex';
import InventoryShow from './components/InventoryShow';
import MembresView from './components/MembresView';
import ProcurementIndex from './components/ProcurementIndex';
import ProcurementShow from './components/ProcurementShow';
import ProfileSettingsView from './components/ProfileSettingsView';
import SaleIndex from './components/SaleIndex';
import SaleShow from './components/SaleShow';
import AuditLogView from './components/AuditLogView';

type DrinksAppProps = {
    _module: string;
    _action: string;
    [key: string]: any;
};

const MODULE_GROUPS = [
    {
        title: 'Tableau de bord',
        items: [
            { id: 'dashboard', label: 'Vue Générale', icon: <Box className="h-4 w-4" />, roles: ['admin'] },
        ]
    },
    {
        title: 'Catalogue & Tiers',
        items: [
            { id: 'articles', label: 'Articles & Tarifs', icon: <Package className="h-4 w-4" />, roles: ['admin'] },
            { id: 'categories', label: 'Catégories', icon: <Box className="h-4 w-4" />, roles: ['admin'] },
            { id: 'pricing-tiers', label: 'Grilles Tarifaires', icon: <DollarSign className="h-4 w-4" />, roles: ['admin'] },
            { id: 'packagings', label: 'Consignes & Emballages', icon: <Box className="h-4 w-4" />, roles: ['caissier', 'magasinier'] },
            { id: 'clients', label: 'Clients', icon: <FileText className="h-4 w-4" />, roles: ['caissier', 'gerant'] },
            { id: 'suppliers', label: 'Fournisseurs', icon: <Box className="h-4 w-4" />, roles: ['ops'] },
        ]
    },
    {
        title: 'Opérations',
        items: [
            { id: 'procurements', label: 'Approvisionnements', icon: <Truck className="h-4 w-4" />, roles: ['ops'] },
            { id: 'sales', label: 'Ventes', icon: <ShoppingCart className="h-4 w-4" />, roles: ['caissier'] },
            { id: 'roadmap', label: 'Feuille de Route', icon: <Truck className="h-4 w-4" />, roles: ['ops', 'caissier'] },
        ]
    },
    {
        title: 'Stock',
        items: [
            { id: 'inventories', label: 'Inventaires', icon: <PackageCheck className="h-4 w-4" />, roles: ['magasinier', 'ops'] },
            { id: 'losses', label: 'Pertes', icon: <TrendingDown className="h-4 w-4" />, roles: ['ops', 'magasinier'] },
            { id: 'stock-movements', label: 'Mouvements', icon: <BarChart3 className="h-4 w-4" />, roles: ['magasinier'] },
            { id: 'stock-snapshots', label: 'Snapshots Stock', icon: <Camera className="h-4 w-4" />, roles: ['magasinier', 'ops'] },
        ]
    },
    {
        title: 'Finance & Caisse',
        items: [
            { id: 'payments', label: 'Règlements Clients', icon: <Wallet className="h-4 w-4" />, roles: ['caissier', 'comptable'] },
            { id: 'expenses', label: 'Charges', icon: <DollarSign className="h-4 w-4" />, roles: ['comptable'] },
            { id: 'expense-types', label: 'Types de Charges', icon: <Settings className="h-4 w-4" />, roles: ['comptable'] },
            { id: 'cash-inputs', label: 'Apports de Fonds', icon: <Receipt className="h-4 w-4" />, roles: ['comptable'] },
            { id: 'cash-deposits', label: 'Versements Banque', icon: <BarChart2 className="h-4 w-4" />, roles: ['comptable'] },
        ]
    },
    {
        title: 'Analytique',
        items: [
            { id: 'reports', label: 'Rapports & Bilans', icon: <BarChart2 className="h-4 w-4" />, roles: ['gerant', 'comptable', 'admin'] },
        ]
    },
    {
        title: 'Administration & Audit',
        items: [
            { id: 'membres', label: 'Utilisateurs', icon: <UserCircle className="h-4 w-4" />, roles: ['admin'] },
            { id: 'settings', label: 'Paramètres', icon: <Settings className="h-4 w-4" />, roles: ['admin'] },
            { id: 'logs', label: 'Audit & Logs', icon: <History className="h-4 w-4" />, roles: ['admin'] },
        ]
    }
];

export default function DrinksApp({ _module, _action, ...props }: DrinksAppProps) {
    const { currentTeam, auth, role: pageRole } = usePage().props as any;
    const team = currentTeam as Team & { role?: string; roleLabel?: string };
    const slug = team?.slug;

    const rawRole = pageRole || team?.role || 'member';
    const roleAliases: Record<string, string> = {
        owner: 'admin',
        manager: 'gerant',
        gerant: 'gerant',
        receptionniste: 'reception',
        serveur: 'barman',
    };
    const roleKey = roleAliases[String(rawRole).toLowerCase()] ?? String(rawRole).toLowerCase();

    const [isInitializing, setIsInitializing] = useState(true);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    useEffect(() => {
        const timer = setTimeout(() => setIsInitializing(false), 1200);

        return () => clearTimeout(timer);
    }, []);

    const currentItem = MODULE_GROUPS.flatMap(g => g.items).find(i => i.id === _module) || { label: _module };

    const handleLogout = () => {
        router.post(logout().url, {}, {
            onFinish: () => {
                // Redirige vers la page de login après la déconnexion
                window.location.href = '/login';
            },
        });
    };

    const SidebarContent = () => (
        <>
            {/* Brand */}
            <div className="px-5 py-4 border-b border-border flex-shrink-0 flex items-center justify-between">
                <Link
                    href={route('drinks.dashboard', { current_team: slug })}
                    className="flex items-center gap-2.5 group"
                    onClick={() => setIsSidebarOpen(false)}
                >
                    <div className="w-7 h-7 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-amber-400 transition-colors">
                        <Box className="h-4 w-4 text-white" />
                    </div>
                    <span className="font-semibold text-foreground text-sm">Distribution</span>
                </Link>
                <Button variant="ghost" size="icon" className="lg:hidden" onClick={() => setIsSidebarOpen(false)}>
                    <X className="h-4 w-4" />
                </Button>
            </div>

            {/* Nav groups */}
            <div className="flex-1 overflow-y-auto py-3 px-2 space-y-4">
                {MODULE_GROUPS.map((group, i) => {
                    const filteredItems = group.items.filter(item => 
                        item.roles.includes(roleKey) || roleKey === 'admin'
                    );

                    if (filteredItems.length === 0) {
return null;
}

                    return (
                        <div key={i}>
                            <h4 className="px-3 mb-1 text-[10px] font-semibold uppercase tracking-widest text-muted-foreground">
                                {group.title}
                            </h4>
                            <nav className="space-y-0.5">
                                {filteredItems.map(item => {
                                    const isActive = _module === item.id;
                                    let href = '#';

                                    if (item.id === 'dashboard') {
href = route('drinks.dashboard', { current_team: slug });
} else if (item.id === 'reports') {
href = route('drinks.reports.brouillard', { current_team: slug });
} else if (item.id === 'roadmap') {
href = route('drinks.reports.roadmap', { current_team: slug });
} else if (item.id === 'membres') {
    href = route('drinks.membres.index', { current_team: slug });
} else if (item.id === 'logs') {
    href = route('drinks.logs', { current_team: slug });
} else {
href = route(`drinks.${item.id}.index`, { current_team: slug });
}

                                    return (
                                        <Link
                                            key={item.id}
                                            href={href}
                                            onClick={() => setIsSidebarOpen(false)}
                                            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors ${
                                                isActive
                                                    ? 'bg-amber-500/15 text-amber-400 font-medium'
                                                    : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                            }`}
                                        >
                                            {item.icon}
                                            {item.label}
                                        </Link>
                                    );
                                })}
                            </nav>
                        </div>
                    );
                })}
            </div>

            {/* User footer */}
            <div className="px-2 py-3 border-t border-border space-y-1 flex-shrink-0">
                <div className="px-3 py-2 rounded-lg bg-muted/50 flex items-center gap-2.5">
                    <Avatar className="h-8 w-8 rounded-full border border-border shadow-sm">
                        <AvatarImage src={auth?.user?.avatar_url} alt={auth?.user?.name} />
                        <AvatarFallback className="bg-amber-500/10 text-amber-600 text-[10px] font-bold">
                            {userInitials}
                        </AvatarFallback>
                    </Avatar>
                    <div className="min-w-0 flex-1">
                        <p className="text-xs font-medium text-foreground truncate">{auth?.user?.name}</p>
                        <p className="text-[10px] text-muted-foreground capitalize">{team?.roleLabel ?? rawRole}</p>
                    </div>
                </div>
                <button
                    onClick={handleLogout}
                    className="flex w-full items-center gap-2.5 px-3 py-2 rounded-lg text-muted-foreground text-sm hover:bg-red-500/10 hover:text-red-400 transition-all duration-200"
                >
                    <LogOut className="h-3.5 w-3.5" />
                    Se déconnecter
                </button>
            </div>
        </>
    );

    const renderContent = () => {
        if (_module === 'dashboard') {
            return <DashboardView stats={props.stats || {}} role={rawRole} />;
        }

        if (_module === 'reports') {
            return <ReportView _action={_action} {...props} />;
        }

        if (_module === 'settings') {
            return <ProfileSettingsView members={props.members ?? []} role={rawRole} />;
        }

        if (_module === 'membres') {
            return <MembresView members={props.members ?? []} roles={props.roles ?? []} />;
        }

        if (_module === 'logs') {
            return <AuditLogView logs={props.logs ?? []} />;
        }

        switch (_action) {
            case 'index':
                if (_module === 'sales') {
return <SaleIndex sales={props.sales} />;
}

                if (_module === 'articles') {
return <ArticleIndex articles={props.articles} />;
}

                if (_module === 'procurements') {
return <ProcurementIndex procurements={props.procurements} />;
}

                if (_module === 'inventories') {
return <InventoryIndex inventories={props.inventories} />;
}

                if (_module === 'expenses') {
return <ExpenseIndex expenses={props.expenses} />;
}

                if (_module === 'cash-inputs') {
return <CashInputIndex cashInputs={props.cashInputs} />;
}

                if (_module === 'cash-deposits') {
return <CashDepositIndex cashDeposits={props.cashDeposits} />;
}

                if (_module === 'payments') {
return <PaymentIndex payments={props.payments} />;
}

                if (_module === 'categories') {
return <CategoryIndex categories={props.categories} />;
}

                if (_module === 'packagings') {
return <PackagingIndex packagings={props.packagings} />;
}

                if (_module === 'clients') {
return <ClientIndex clients={props.clients} />;
}

                if (_module === 'suppliers') {
return <SupplierIndex suppliers={props.suppliers} />;
}

                if (_module === 'pricing-tiers') {
return <PricingTierIndex pricingTiers={props.pricingTiers} />;
}

                if (_module === 'expense-types') {
return <ExpenseTypeIndex expenseTypes={props.expenseTypes} />;
}

                if (_module === 'losses') {
return <LossIndex losses={props.losses} />;
}

                if (_module === 'stock-movements') {
return <StockMovementIndex stockMovements={props.stockMovements} />;
}

                if (_module === 'stock-snapshots') {
return <StockSnapshotIndex dates={props.dates} />;
}

                return <div>Module introuvable</div>;
            case 'create':
            case 'edit':
                return renderForm();
            case 'show':
                if (_module === 'sales') {
return <SaleShow sale={props.sale} />;
}

                if (_module === 'procurements') {
return <ProcurementShow procurement={props.procurement} />;
}

                if (_module === 'inventories') {
return <InventoryShow inventory={props.inventory} />;
}

                if (_module === 'expenses') {
return <ExpenseShow expense={props.expense} />;
}

                if (_module === 'cash-inputs') {
return <CashInputShow cashInput={props.cashInput} />;
}

                if (_module === 'cash-deposits') {
return <CashDepositShow cashDeposit={props.cashDeposit} />;
}

                if (_module === 'payments') {
return <PaymentShow payment={props.payment} />;
}

                if (_module === 'clients') {
return <ClientShow client={props.client} />;
}

                if (_module === 'losses') {
return <LossShow loss={props.loss} />;
}

                if (_module === 'stock-snapshots') {
return <StockSnapshotShow snapshots={props.snapshots} date={props.date} />;
}

                if (_module === 'stock-movements') {
                    return <StockMovementShow movement={props.movement} />;
                }

                if (_module === 'articles') {
                    return <ArticleShow article={props.article} />;
                }

                return <div>Action introuvable</div>;
            default:
                return <div>Action inconnue : {_action}</div>;
        }
    };

    /** Dispatch create/edit to module-specific forms when available */
    const renderForm = () => {
        switch (_module) {
            case 'articles':
                return (
                    <ArticleForm
                        _action={_action as 'create' | 'edit'}
                        categories={props.categories ?? []}
                        packagings={props.packagings ?? []}
                        article={props.article}
                    />
                );
            case 'sales':
                return <SaleForm _action={_action} clients={props.clients} articles={props.articles} kinds={props.kinds} sale={props.sale} />;
            case 'procurements':
                return <ProcurementForm _action={_action} suppliers={props.suppliers} articles={props.articles} kinds={props.kinds} procurement={props.procurement} />;
            case 'inventories':
                return <InventoryForm _action={_action} articles={props.articles} inventory={props.inventory} />;
            case 'losses':
                return <LossForm _action={_action} articles={props.articles} loss={props.loss} />;
            case 'expenses':
                return <ExpenseForm _action={_action} expenseTypes={props.expenseTypes} expense={props.expense} />;
            case 'payments':
                return <PaymentForm _action={_action} clients={props.clients} sales={props.sales} payment={props.payment} />;
            case 'cash-inputs':
                return <CashInputForm _action={_action} cashInput={props.cashInput} />;
            case 'cash-deposits':
                return <CashDepositForm _action={_action} cashDeposit={props.cashDeposit} />;
            case 'categories':
                return <CategoryForm _action={_action} category={props.category} />;
            case 'packagings':
                return <PackagingForm _action={_action} packaging={props.packaging} />;
            case 'clients':
                return <ClientForm _action={_action} client={props.client} />;
            case 'suppliers':
                return <SupplierForm _action={_action} supplier={props.supplier} />;
            case 'pricing-tiers':
                return <PricingTierForm _action={_action} pricingTier={props.pricingTier} />;
            case 'expense-types':
                return <ExpenseTypeForm _action={_action} expenseType={props.expenseType} />;
            default:
                return <div>Formulaire introuvable</div>;
        }
    };

    const userInitials = auth?.user?.name
        ? auth.user.name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
        : '??';

    const actionLabel = _action === 'create' ? 'Nouveau' : _action === 'edit' ? 'Édition' : 'Détails';

    return (
        <div className="h-full w-full bg-[#FDFCFB] text-foreground font-sans selection:bg-amber-100 selection:text-amber-900 overflow-hidden flex flex-col">
            <AnimatePresence>
                {isInitializing && (
                    <motion.div
                        initial={{ opacity: 1 }}
                        exit={{ opacity: 0, transition: { duration: 0.8 } }}
                        className="fixed inset-0 z-[200] bg-white flex flex-col items-center justify-center"
                    >
                        <motion.div
                            initial={{ y: 20, opacity: 0 }}
                            animate={{ y: 0, opacity: 1 }}
                            transition={{ duration: 0.5 }}
                            className="flex flex-col items-center gap-8"
                        >
                            <div className="relative">
                                <motion.div
                                    animate={{ 
                                        rotate: 360,
                                        scale: [1, 1.1, 1]
                                    }}
                                    transition={{ 
                                        rotate: { repeat: Infinity, duration: 3, ease: "linear" },
                                        scale: { repeat: Infinity, duration: 2, ease: "easeInOut" }
                                    }}
                                    className="w-24 h-24 rounded-3xl bg-gradient-to-tr from-amber-500 to-orange-600 shadow-2xl shadow-amber-500/20"
                                />
                                <div className="absolute inset-0 flex items-center justify-center">
                                    <ShoppingCart className="h-10 w-10 text-white" />
                                </div>
                            </div>
                            
                            <div className="flex flex-col items-center">
                                <h2 className="text-2xl font-black tracking-tighter text-foreground">DRINKS OS</h2>
                                <p className="text-[10px] uppercase tracking-[0.3em] font-bold text-amber-500 mt-1">NEXORA ECOSYSTEM</p>
                            </div>

                            <div className="w-48 h-1 bg-muted rounded-full overflow-hidden mt-4">
                                <motion.div 
                                    initial={{ x: "-100%" }}
                                    animate={{ x: "0%" }}
                                    transition={{ duration: 1.2, ease: "easeInOut" }}
                                    className="w-full h-full bg-amber-500"
                                />
                            </div>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>
            <Head title={`Distribution Boissons - ${currentItem.label}`} />
            <ParticlesBackground />

            <div className="flex flex-1 w-full overflow-hidden bg-background">
                {/* Sidebar Navigation - Desktop */}
                <aside className="hidden lg:flex w-60 flex-shrink-0 bg-card border-r border-border flex-col">
                    <SidebarContent />
                </aside>

                {/* Sidebar Navigation - Mobile */}
                <AnimatePresence>
                    {isSidebarOpen && (
                        <>
                            <motion.div
                                initial={{ opacity: 0 }}
                                animate={{ opacity: 1 }}
                                exit={{ opacity: 0 }}
                                onClick={() => setIsSidebarOpen(false)}
                                className="fixed inset-0 bg-black/50 backdrop-blur-sm z-[150] lg:hidden"
                            />
                            <motion.aside
                                initial={{ x: '-100%' }}
                                animate={{ x: 0 }}
                                exit={{ x: '-100%' }}
                                transition={{ type: 'spring', damping: 25, stiffness: 200 }}
                                className="fixed inset-y-0 left-0 w-72 bg-card border-r border-border flex flex-col z-[151] lg:hidden"
                            >
                                <SidebarContent />
                            </motion.aside>
                        </>
                    )}
                </AnimatePresence>

                {/* Main Content Area */}
                <main className="flex-1 overflow-y-auto bg-background relative flex flex-col">
                    <div className="p-4 sm:p-6 flex-1 flex flex-col">
                        <header className="mb-4 flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <Button variant="ghost" size="icon" className="lg:hidden" onClick={() => setIsSidebarOpen(true)}>
                                    <Menu className="h-5 w-5" />
                                </Button>
                                <div>
                                    <div className="flex items-center gap-2 mb-0.5">
                                        <span className="text-[10px] font-bold uppercase tracking-widest text-muted-foreground/60">Drinks</span>
                                        <span className="text-muted-foreground/30">›</span>
                                        <span className="text-[10px] font-bold uppercase tracking-widest text-amber-500">{currentItem.label}</span>
                                    </div>
                                    <h1 className="text-base sm:text-xl font-bold text-foreground flex items-center gap-2">
                                        {currentItem.label}
                                        {_action !== 'index' && (
                                            <span className="text-muted-foreground font-normal text-sm sm:text-base">
                                                / {actionLabel}
                                            </span>
                                        )}
                                    </h1>
                                </div>
                            </div>
                        </header>

                        <div className="flex-1">
                            <AnimatePresence mode="wait">
                                <motion.div
                                    key={`${_module}-${_action}`}
                                    initial={{ opacity: 0, y: 10 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -10 }}
                                    transition={{ duration: 0.2 }}
                                    className="h-full"
                                >
                                    {renderContent()}
                                </motion.div>
                            </AnimatePresence>
                        </div>
                    </div>
                </main>
            </div>

            {/* AI Chat Widget — admin & gérant only */}
            {(roleKey === 'admin' || roleKey === 'gerant') && <DrinksChatWidget />}
        </div>
    );
}
