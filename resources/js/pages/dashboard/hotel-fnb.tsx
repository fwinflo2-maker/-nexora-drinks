import { Head, Link, usePage } from '@inertiajs/react';
import { BedDouble, UtensilsCrossed, Sparkles, TrendingUp, Clock, ArrowRight } from 'lucide-react';
import type { Team } from '@/types';

interface CheckedInReservation {
    id: number;
    reference: string;
    room_number: string;
    guest_name: string;
    nights: number;
    check_out: string;
    total_price: number;
    restaurant_total: number;
    open_orders: number;
}

interface OpenOrder {
    id: number;
    reference: string;
    room_number: string | null;
    total: number;
    order_type: string;
    created_at: string;
}

interface Stats {
    occupied_rooms: number;
    open_room_orders: number;
    restaurant_revenue_today: number;
}

interface Props {
    checked_in_reservations: CheckedInReservation[];
    open_room_orders: OpenOrder[];
    stats: Stats;
}

const fmt = (n: number) => n.toLocaleString('fr-FR') + ' FCFA';

export default function HotelFnBDashboard({ checked_in_reservations, open_room_orders, stats }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;
    const slug = team?.slug;

    return (
        <>
            <Head title="Mode 3 — Hôtel & Restaurant" />
            <div className="min-h-screen bg-[#0f0b1a] text-white">

                {/* Header bande violette */}
                <div className="border-b border-violet-900/40 bg-[#130e22]/80 backdrop-blur-sm sticky top-0 z-10">
                    <div className="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 rounded-lg bg-violet-600 flex items-center justify-center shadow-lg shadow-violet-900/50">
                                <Sparkles className="h-4 w-4 text-white" />
                            </div>
                            <div>
                                <span className="text-sm font-bold text-white tracking-wide">{team?.name}</span>
                                <span className="ml-2 text-[10px] font-semibold uppercase tracking-widest text-violet-400 bg-violet-500/10 border border-violet-500/20 rounded-full px-2 py-0.5">Mode 3</span>
                            </div>
                        </div>
                        <div className="flex items-center gap-2 text-xs text-violet-300/60">
                            <Link
                                href={route('hotel.dashboard', { current_team: slug })}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-500/20 bg-blue-500/5 text-blue-400 hover:bg-blue-500/10 transition-colors"
                            >
                                <BedDouble className="h-3 w-3" />
                                Hôtel
                            </Link>
                            <Link
                                href={route('fnb.dashboard', { current_team: slug })}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-emerald-500/20 bg-emerald-500/5 text-emerald-400 hover:bg-emerald-500/10 transition-colors"
                            >
                                <UtensilsCrossed className="h-3 w-3" />
                                Restaurant
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="max-w-6xl mx-auto px-6 py-8 space-y-8">

                    {/* Hero title */}
                    <div>
                        <h1 className="text-2xl font-bold text-white flex items-center gap-3">
                            Tableau de bord intégré
                        </h1>
                        <p className="text-sm text-violet-300/60 mt-1">Vue consolidée — Chambres, room service & restaurant liés</p>
                    </div>

                    {/* Stats */}
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div className="rounded-2xl border border-violet-800/30 bg-violet-900/10 p-5 relative overflow-hidden group hover:border-violet-600/40 transition-colors">
                            <div className="absolute top-0 right-0 w-20 h-20 bg-blue-500/5 rounded-full -translate-y-4 translate-x-4" />
                            <div className="flex items-center gap-2 mb-3">
                                <BedDouble className="h-4 w-4 text-blue-400" />
                                <span className="text-xs text-violet-300/60 uppercase tracking-widest font-medium">Chambres occupées</span>
                            </div>
                            <p className="text-3xl font-bold text-white">{stats.occupied_rooms}</p>
                        </div>

                        <div className="rounded-2xl border border-violet-800/30 bg-violet-900/10 p-5 relative overflow-hidden group hover:border-violet-600/40 transition-colors">
                            <div className="absolute top-0 right-0 w-20 h-20 bg-amber-500/5 rounded-full -translate-y-4 translate-x-4" />
                            <div className="flex items-center gap-2 mb-3">
                                <Clock className="h-4 w-4 text-amber-400" />
                                <span className="text-xs text-violet-300/60 uppercase tracking-widest font-medium">Room service ouvert</span>
                            </div>
                            <p className="text-3xl font-bold text-amber-400">{stats.open_room_orders}</p>
                        </div>

                        <div className="rounded-2xl border border-violet-800/30 bg-violet-900/10 p-5 relative overflow-hidden group hover:border-violet-600/40 transition-colors">
                            <div className="absolute top-0 right-0 w-20 h-20 bg-emerald-500/5 rounded-full -translate-y-4 translate-x-4" />
                            <div className="flex items-center gap-2 mb-3">
                                <TrendingUp className="h-4 w-4 text-emerald-400" />
                                <span className="text-xs text-violet-300/60 uppercase tracking-widest font-medium">CA resto/chambres (auj.)</span>
                            </div>
                            <p className="text-3xl font-bold text-emerald-400">{fmt(stats.restaurant_revenue_today)}</p>
                        </div>
                    </div>

                    {/* Tables */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {/* Chambres en cours */}
                        <div className="rounded-2xl border border-violet-800/30 bg-[#130e22]/60 overflow-hidden">
                            <div className="px-5 py-3.5 border-b border-violet-800/20 flex items-center gap-2">
                                <BedDouble className="h-4 w-4 text-blue-400" />
                                <h2 className="text-sm font-semibold text-white">
                                    Chambres en cours
                                    <span className="ml-2 text-xs text-violet-400/60">({checked_in_reservations.length})</span>
                                </h2>
                            </div>
                            {checked_in_reservations.length === 0 ? (
                                <p className="py-10 text-center text-sm text-violet-300/40">Aucune chambre occupée.</p>
                            ) : (
                                <div className="divide-y divide-violet-900/30">
                                    {checked_in_reservations.map(res => (
                                        <div key={res.id} className="px-5 py-3.5 flex items-center justify-between hover:bg-violet-900/10 transition-colors group">
                                            <div className="min-w-0">
                                                <div className="flex items-center gap-2">
                                                    <span className="text-sm font-semibold text-white">Ch. {res.room_number}</span>
                                                    {res.open_orders > 0 && (
                                                        <span className="text-[10px] bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full px-2 py-0.5 font-medium">
                                                            {res.open_orders} cmd
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="text-xs text-violet-300/50 mt-0.5 truncate">{res.guest_name} · Départ : {res.check_out}</p>
                                            </div>
                                            <div className="text-right ml-4 shrink-0">
                                                <p className="text-xs text-white font-medium">{fmt(res.total_price)}</p>
                                                {res.restaurant_total > 0 && (
                                                    <p className="text-[10px] text-amber-400/80">+{fmt(res.restaurant_total)} resto</p>
                                                )}
                                            </div>
                                            <Link
                                                href={route('hotel.reservations.show', { current_team: slug, reservation: res.id })}
                                                className="ml-4 text-violet-400/40 group-hover:text-violet-300 transition-colors shrink-0"
                                            >
                                                <ArrowRight className="h-3.5 w-3.5" />
                                            </Link>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Room service ouvert */}
                        <div className="rounded-2xl border border-violet-800/30 bg-[#130e22]/60 overflow-hidden">
                            <div className="px-5 py-3.5 border-b border-violet-800/20 flex items-center gap-2">
                                <UtensilsCrossed className="h-4 w-4 text-emerald-400" />
                                <h2 className="text-sm font-semibold text-white">
                                    Room service ouvert
                                    <span className="ml-2 text-xs text-violet-400/60">({open_room_orders.length})</span>
                                </h2>
                            </div>
                            {open_room_orders.length === 0 ? (
                                <p className="py-10 text-center text-sm text-violet-300/40">Aucune commande room service ouverte.</p>
                            ) : (
                                <div className="divide-y divide-violet-900/30">
                                    {open_room_orders.map(order => (
                                        <div key={order.id} className="px-5 py-3.5 flex items-center justify-between hover:bg-violet-900/10 transition-colors">
                                            <div className="min-w-0">
                                                <p className="text-sm font-medium text-white">{order.reference}</p>
                                                <p className="text-xs text-violet-300/50 mt-0.5">
                                                    {order.room_number ? `Chambre ${order.room_number}` : '—'} · {new Date(order.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}
                                                </p>
                                            </div>
                                            <span className="text-sm font-semibold text-white shrink-0 ml-4">{fmt(order.total)}</span>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
