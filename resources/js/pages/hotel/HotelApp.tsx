import { Head, Link, router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    BedDouble, Users, CalendarCheck, Settings, LogOut,
    UserCircle, Menu, X, LayoutDashboard, Layers, Home,
    History, ClipboardList, BarChart3,
} from 'lucide-react';
import { useState, useEffect } from 'react';
import React from 'react';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { ParticlesBackground } from '@/components/ui/particles-background';
import { logout } from '@/routes';
import type { Team } from '@/types';

import DashboardView from './components/DashboardView';
import RoomTypeIndex from './components/RoomTypeIndex';
import RoomTypeForm from './components/RoomTypeForm';
import RoomIndex from './components/RoomIndex';
import RoomForm from './components/RoomForm';
import GuestIndex from './components/GuestIndex';
import GuestForm from './components/GuestForm';
import GuestShow from './components/GuestShow';
import ReservationIndex from './components/ReservationIndex';
import ReservationForm from './components/ReservationForm';
import ReservationShow from './components/ReservationShow';
import FolioSummary from './components/FolioSummary';
import CheckoutWizard from './components/CheckoutWizard';
import ReservationFnBTab from './components/ReservationFnBTab';
import ReportView from './components/ReportView';

type HotelAppProps = {
    _module: string;
    _action: string;
    [key: string]: any;
};

const MODULE_GROUPS = [
    {
        title: 'Tableau de bord',
        items: [
            { id: 'dashboard', label: 'Vue Générale', icon: <LayoutDashboard className="h-4 w-4" />, roles: ['admin', 'manager', 'receptionist', 'housekeeper'] },
        ],
    },
    {
        title: 'Paramétrage',
        items: [
            { id: 'room-types', label: 'Types de chambres', icon: <Layers className="h-4 w-4" />, roles: ['admin', 'manager'] },
            { id: 'rooms', label: 'Chambres', icon: <Home className="h-4 w-4" />, roles: ['admin', 'manager', 'receptionist'] },
        ],
    },
    {
        title: 'Clients',
        items: [
            { id: 'guests', label: 'Clients', icon: <Users className="h-4 w-4" />, roles: ['admin', 'manager', 'receptionist'] },
        ],
    },
    {
        title: 'Réservations',
        items: [
            { id: 'reservations', label: 'Réservations', icon: <CalendarCheck className="h-4 w-4" />, roles: ['admin', 'manager', 'receptionist'] },
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

export default function HotelApp({ _module, _action, ...props }: HotelAppProps) {
    const { currentTeam, auth, role: pageRole } = usePage().props as any;
    const team = currentTeam as Team & { role?: string; roleLabel?: string };
    const slug = team?.slug;

    const rawRole = pageRole || team?.role || 'member';
    const roleAliases: Record<string, string> = {
        owner: 'admin',
        admin: 'admin',
        manager: 'manager',
        hotel_manager: 'manager',
        hotel_receptionist: 'receptionist',
        hotel_housekeeper: 'housekeeper',
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
                    href={route('hotel.dashboard', { current_team: slug })}
                    className="flex items-center gap-2.5 group"
                    onClick={() => setIsSidebarOpen(false)}
                >
                    <div className="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-500 transition-colors">
                        <BedDouble className="h-4 w-4 text-white" />
                    </div>
                    <span className="font-semibold text-foreground text-sm">Hôtellerie</span>
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
                                        href = route('hotel.dashboard', { current_team: slug });
                                    } else if (item.id === 'reports') {
                                        href = route('hotel.reports.reservations', { current_team: slug });
                                    } else if (item.id === 'membres') {
                                        href = '#';
                                    } else if (item.id === 'settings') {
                                        href = '#';
                                    } else if (item.id === 'logs') {
                                        href = '#';
                                    } else {
                                        href = route(`hotel.${item.id}.index`, { current_team: slug });
                                    }

                                    return (
                                        <Link
                                            key={item.id}
                                            href={href}
                                            onClick={() => setIsSidebarOpen(false)}
                                            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors ${
                                                isActive
                                                    ? 'bg-blue-600/15 text-blue-500 font-medium'
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
                        <AvatarFallback className="bg-blue-600/10 text-blue-600 text-[10px] font-bold">
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
            return <DashboardView {...(props as any)} />;
        }

        if (_module === 'reports') {
            return <ReportView _action={_action} dateFrom={props.dateFrom} dateTo={props.dateTo} rows={props.rows} />;
        }

        switch (_action) {
            case 'index':
                if (_module === 'room-types') return <RoomTypeIndex roomTypes={props.roomTypes ?? []} />;
                if (_module === 'rooms') return <RoomIndex rooms={props.rooms ?? []} />;
                if (_module === 'guests') return <GuestIndex guests={props.guests} filters={props.filters} />;
                if (_module === 'reservations') return <ReservationIndex reservations={props.reservations} filters={props.filters} statuses={props.statuses ?? []} />;
                return <div className="p-6 text-muted-foreground">Module introuvable</div>;

            case 'create':
                if (_module === 'room-types') return <RoomTypeForm _action="create" />;
                if (_module === 'rooms') return <RoomForm _action="create" roomTypes={props.roomTypes ?? []} statuses={props.statuses ?? []} />;
                if (_module === 'guests') return <GuestForm _action="create" />;
                if (_module === 'reservations') return <ReservationForm _action="create" rooms={props.rooms ?? []} guests={props.guests ?? []} />;
                return <div className="p-6 text-muted-foreground">Formulaire introuvable</div>;

            case 'edit':
                if (_module === 'room-types') return <RoomTypeForm _action="edit" roomType={props.roomType} />;
                if (_module === 'rooms') return <RoomForm _action="edit" room={props.room} roomTypes={props.roomTypes ?? []} statuses={props.statuses ?? []} />;
                if (_module === 'guests') return <GuestForm _action="edit" guest={props.guest} />;
                return <div className="p-6 text-muted-foreground">Formulaire introuvable</div>;

            case 'show':
                if (_module === 'guests') return <GuestShow guest={props.guest} />;
                if (_module === 'reservations') return <ReservationShow reservation={props.reservation} fnb_menu_items={props.fnb_menu_items ?? []} />;
                return <div className="p-6 text-muted-foreground">Détails introuvables</div>;

            case 'folio-summary':
                if (_module === 'reservations') return <FolioSummary reservation={props.reservation} balance={props.balance} folios={props.folios} />;
                return <div className="p-6 text-muted-foreground">Folio introuvable</div>;

            case 'fnb-orders':
                if (_module === 'reservations') return <ReservationFnBTab reservationId={props.reservation?.id} fnbOrders={props.fnb_orders ?? []} />;
                return <div className="p-6 text-muted-foreground">Commandes introuvables</div>;

            case 'checkout':
                if (_module === 'reservations') return <CheckoutWizard reservation={props.reservation} balance={props.balance} folios={props.folios ?? []} />;
                return <div className="p-6 text-muted-foreground">Check-out introuvable</div>;

            default:
                return <div className="p-6 text-muted-foreground">Action inconnue : {_action}</div>;
        }
    };

    const userInitials = auth?.user?.name
        ? auth.user.name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
        : '??';

    const actionLabel = _action === 'create' ? 'Nouveau' : _action === 'edit' ? 'Édition' : 'Détails';

    return (
        <div className="h-full w-full bg-[#FDFCFB] text-foreground font-sans selection:bg-blue-100 selection:text-blue-900 overflow-hidden flex flex-col">
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
                                    className="w-24 h-24 rounded-3xl bg-gradient-to-tr from-blue-600 to-blue-400 shadow-2xl shadow-blue-500/20"
                                />
                                <div className="absolute inset-0 flex items-center justify-center">
                                    <BedDouble className="h-10 w-10 text-white" />
                                </div>
                            </div>

                            <div className="flex flex-col items-center">
                                <h2 className="text-2xl font-black tracking-tighter text-foreground">HOTEL OS</h2>
                                <p className="text-[10px] uppercase tracking-[0.3em] font-bold text-blue-500 mt-1">NEXORA ECOSYSTEM</p>
                            </div>

                            <div className="w-48 h-1 bg-muted rounded-full overflow-hidden mt-4">
                                <motion.div
                                    initial={{ x: '-100%' }}
                                    animate={{ x: '0%' }}
                                    transition={{ duration: 1.2, ease: 'easeInOut' }}
                                    className="w-full h-full bg-blue-600"
                                />
                            </div>
                        </motion.div>
                    </motion.div>
                )}
            </AnimatePresence>
            <Head title={`Hôtellerie - ${currentItem.label}`} />
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
                                        <span className="text-[10px] font-bold uppercase tracking-widest text-muted-foreground/60">Hôtel</span>
                                        <span className="text-muted-foreground/30">›</span>
                                        <span className="text-[10px] font-bold uppercase tracking-widest text-blue-500">{currentItem.label}</span>
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
