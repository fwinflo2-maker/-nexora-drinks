import { Head, Link, router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    UtensilsCrossed, Settings, LogOut, UserCircle, Menu, X,
    LayoutDashboard, TableProperties, ChefHat, ShoppingBag,
    BookOpen, History, ListOrdered, BarChart3,
} from 'lucide-react';
import { useState, useEffect } from 'react';
import React from 'react';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { logout } from '@/routes';
import type { Team } from '@/types';

import DashboardView from './components/DashboardView';
import TableIndex from './components/TableIndex';
import CategoryIndex from './components/CategoryIndex';
import MenuItemIndex from './components/MenuItemIndex';
import MenuItemForm from './components/MenuItemForm';
import OrderIndex from './components/OrderIndex';
import OrderCreate from './components/OrderCreate';
import OrderShow from './components/OrderShow';
import ReportView from './components/ReportView';

type FnBAppProps = {
    _module: string;
    _action: string;
    [key: string]: any;
};

const MODULE_GROUPS = [
    {
        title: 'Tableau de bord',
        items: [
            { id: 'dashboard', label: 'Vue Générale', icon: <LayoutDashboard className="h-4 w-4" />, roles: ['admin', 'manager', 'cashier'] },
        ],
    },
    {
        title: 'Salle',
        items: [
            { id: 'tables', label: 'Tables', icon: <TableProperties className="h-4 w-4" />, roles: ['admin', 'manager', 'waiter'] },
        ],
    },
    {
        title: 'Menu',
        items: [
            { id: 'categories', label: 'Catégories', icon: <ListOrdered className="h-4 w-4" />, roles: ['admin', 'manager'] },
            { id: 'menu-items', label: 'Articles', icon: <BookOpen className="h-4 w-4" />, roles: ['admin', 'manager'] },
        ],
    },
    {
        title: 'Service',
        items: [
            { id: 'orders', label: 'Commandes', icon: <ShoppingBag className="h-4 w-4" />, roles: ['admin', 'manager', 'waiter', 'cashier'] },
            { id: 'kitchen', label: 'Écran Cuisine', icon: <ChefHat className="h-4 w-4" />, roles: ['admin', 'manager', 'kitchen'] },
        ],
    },
    {
        title: 'Rapports',
        items: [
            { id: 'reports', label: 'Rapports', icon: <BarChart3 className="h-4 w-4" />, roles: ['admin', 'manager'] },
        ],
    },
    {
        title: 'Administration',
        items: [
            { id: 'membres', label: 'Utilisateurs', icon: <UserCircle className="h-4 w-4" />, roles: ['admin', 'manager'] },
            { id: 'settings', label: 'Paramètres', icon: <Settings className="h-4 w-4" />, roles: ['admin'] },
            { id: 'logs', label: 'Audit & Logs', icon: <History className="h-4 w-4" />, roles: ['admin'] },
        ],
    },
];

export default function FnBApp({ _module, _action, ...props }: FnBAppProps) {
    const { currentTeam, auth, role: pageRole } = usePage().props as any;
    const team = currentTeam as Team & { role?: string; roleLabel?: string };
    const slug = team?.slug;

    const rawRole = pageRole || team?.role || 'member';
    const roleAliases: Record<string, string> = {
        owner: 'admin',
        admin: 'admin',
        manager: 'manager',
        fnb_manager: 'manager',
        fnb_waiter: 'waiter',
        fnb_kitchen: 'kitchen',
        fnb_cashier: 'cashier',
    };
    const roleKey = roleAliases[String(rawRole).toLowerCase()] ?? String(rawRole).toLowerCase();

    const currentItem = MODULE_GROUPS.flatMap(g => g.items).find(i => i.id === _module) || { label: _module };

    const [isInitializing, setIsInitializing] = useState(true);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    useEffect(() => {
        const timer = setTimeout(() => setIsInitializing(false), 1200);
        return () => clearTimeout(timer);
    }, []);

    const handleLogout = () => {
        router.post(logout().url, {}, {
            onFinish: () => { window.location.href = '/login'; },
        });
    };

    const SidebarContent = () => (
        <>
            <div className="px-5 py-4 border-b border-border flex-shrink-0 flex items-center justify-between">
                <Link
                    href={route('fnb.dashboard', { current_team: slug })}
                    className="flex items-center gap-2.5 group"
                    onClick={() => setIsSidebarOpen(false)}
                >
                    <div className="w-7 h-7 bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-500 transition-colors">
                        <UtensilsCrossed className="h-4 w-4 text-white" />
                    </div>
                    <span className="font-semibold text-foreground text-sm">Restauration</span>
                </Link>
                <Button variant="ghost" size="icon" className="lg:hidden" onClick={() => setIsSidebarOpen(false)}>
                    <X className="h-4 w-4" />
                </Button>
            </div>

            <div className="flex-1 overflow-y-auto py-3 px-2 space-y-4">
                {MODULE_GROUPS.map((group, i) => {
                    const filteredItems = group.items.filter(item =>
                        item.roles.includes(roleKey) || roleKey === 'admin'
                    );

                    if (filteredItems.length === 0) return null;

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
                                        href = route('fnb.dashboard', { current_team: slug });
                                    } else if (item.id === 'kitchen') {
                                        href = route('fnb.kitchen', { current_team: slug });
                                    } else if (item.id === 'reports') {
                                        href = route('fnb.reports.orders', { current_team: slug });
                                    } else if (item.id === 'membres' || item.id === 'settings' || item.id === 'logs') {
                                        href = '#';
                                    } else {
                                        href = route(`fnb.${item.id}.index`, { current_team: slug });
                                    }

                                    return (
                                        <Link
                                            key={item.id}
                                            href={href}
                                            onClick={() => setIsSidebarOpen(false)}
                                            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors ${
                                                isActive
                                                    ? 'bg-emerald-600/15 text-emerald-600 font-medium'
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

            <div className="px-2 py-3 border-t border-border space-y-1 flex-shrink-0">
                <div className="px-3 py-2 rounded-lg bg-muted/50 flex items-center gap-2.5">
                    <Avatar className="h-8 w-8 rounded-full border border-border shadow-sm">
                        <AvatarImage src={auth?.user?.avatar_url} alt={auth?.user?.name} />
                        <AvatarFallback className="bg-emerald-600/10 text-emerald-600 text-[10px] font-bold">
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
        if (_module === 'dashboard') return <DashboardView {...(props as any)} />;

        if (_module === 'reports') {
            return <ReportView _action={_action} dateFrom={props.dateFrom} dateTo={props.dateTo} rows={props.rows} />;
        }

        switch (_action) {
            case 'index':
                if (_module === 'tables') return <TableIndex tables={props.tables ?? []} />;
                if (_module === 'categories') return <CategoryIndex categories={props.categories ?? []} />;
                if (_module === 'menu-items') return <MenuItemIndex items={props.items ?? []} categories={props.categories ?? []} />;
                if (_module === 'orders') return <OrderIndex orders={props.orders} filters={props.filters} statuses={props.statuses ?? []} />;
                return <div className="p-6 text-muted-foreground">Module introuvable</div>;

            case 'create':
                if (_module === 'menu-items') return <MenuItemForm _action="create" categories={props.categories ?? []} />;
                if (_module === 'orders') return <OrderCreate tables={props.tables ?? []} categories={props.categories ?? []} />;
                return <div className="p-6 text-muted-foreground">Formulaire introuvable</div>;

            case 'edit':
                if (_module === 'menu-items') return <MenuItemForm _action="edit" item={props.item} categories={props.categories ?? []} />;
                return <div className="p-6 text-muted-foreground">Formulaire introuvable</div>;

            case 'show':
                if (_module === 'orders') return <OrderShow order={props.order} />;
                return <div className="p-6 text-muted-foreground">Détails introuvables</div>;

            default:
                return <div className="p-6 text-muted-foreground">Action inconnue : {_action}</div>;
        }
    };

    const userInitials = auth?.user?.name
        ? auth.user.name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
        : '??';

    const actionLabel = _action === 'create' ? 'Nouveau' : _action === 'edit' ? 'Édition' : 'Détails';

    return (
        <div className="h-full w-full bg-[#FDFCFB] text-foreground font-sans selection:bg-emerald-100 selection:text-emerald-900 overflow-hidden flex flex-col">
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
                                        scale: [1, 1.1, 1],
                                    }}
                                    transition={{
                                        rotate: { repeat: Infinity, duration: 3, ease: 'linear' },
                                        scale: { repeat: Infinity, duration: 2, ease: 'easeInOut' },
                                    }}
                                    className="w-24 h-24 rounded-3xl bg-gradient-to-tr from-emerald-600 to-green-400 shadow-2xl shadow-emerald-500/20"
                                />
                                <div className="absolute inset-0 flex items-center justify-center">
                                    <UtensilsCrossed className="h-10 w-10 text-white" />
                                </div>
                            </div>

                            <div className="flex flex-col items-center">
                                <h2 className="text-2xl font-black tracking-tighter text-foreground">F&B OS</h2>
                                <p className="text-[10px] uppercase tracking-[0.3em] font-bold text-emerald-600 mt-1">NEXORA ECOSYSTEM</p>
                            </div>

                            <div className="w-48 h-1 bg-muted rounded-full overflow-hidden mt-4">
                                <motion.div
                                    initial={{ x: '-100%' }}
                                    animate={{ x: '0%' }}
                                    transition={{ duration: 1.2, ease: 'easeInOut' }}
                                    className="w-full h-full bg-emerald-600"
                                />
                            </div>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>
            <Head title={`Restauration F&B - ${currentItem.label}`} />
            <ParticlesBackground />

            <div className="flex flex-1 w-full overflow-hidden bg-background">
                <aside className="hidden lg:flex w-60 flex-shrink-0 bg-card border-r border-border flex-col">
                    <SidebarContent />
                </aside>

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

                <main className="flex-1 overflow-y-auto bg-background relative flex flex-col">
                    <div className="p-4 sm:p-6 flex-1 flex flex-col">
                        <header className="mb-4 flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <Button variant="ghost" size="icon" className="lg:hidden" onClick={() => setIsSidebarOpen(true)}>
                                    <Menu className="h-5 w-5" />
                                </Button>
                                <div>
                                    <div className="flex items-center gap-2 mb-0.5">
                                        <span className="text-[10px] font-bold uppercase tracking-widest text-muted-foreground/60">F&B</span>
                                        <span className="text-muted-foreground/30">›</span>
                                        <span className="text-[10px] font-bold uppercase tracking-widest text-emerald-600">{currentItem.label}</span>
                                    </div>
                                    <h1 className="text-base sm:text-xl font-bold text-foreground flex items-center gap-2">
                                        {currentItem.label}
                                        {_action !== 'index' && _action !== 'dashboard' && (
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
        </div>
    );
}
