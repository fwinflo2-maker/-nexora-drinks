import { Head, Link, usePage } from '@inertiajs/react';
import { BedDouble, Coffee, ReceiptText, TrendingUp } from 'lucide-react';
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
            <Head title="Hôtel & Restaurant — Vue liée" />
            <div className="min-h-screen bg-background p-6 space-y-6">
                <div>
                    <h1 className="text-xl font-bold text-foreground flex items-center gap-2">
                        <BedDouble className="h-5 w-5 text-blue-500" />
                        Hôtel & Restaurant
                    </h1>
                    <p className="text-xs text-muted-foreground mt-0.5">Vue consolidée — Chambres & commandes liées</p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="rounded-xl border border-border bg-card p-4">
                        <p className="text-xs text-muted-foreground">Chambres occupées</p>
                        <p className="text-2xl font-bold text-foreground mt-1">{stats.occupied_rooms}</p>
                    </div>
                    <div className="rounded-xl border border-border bg-card p-4">
                        <p className="text-xs text-muted-foreground">Commandes room service ouvertes</p>
                        <p className="text-2xl font-bold text-orange-500 mt-1">{stats.open_room_orders}</p>
                    </div>
                    <div className="rounded-xl border border-border bg-card p-4">
                        <p className="text-xs text-muted-foreground">CA Restaurant / chambres (aujourd'hui)</p>
                        <p className="text-2xl font-bold text-emerald-500 mt-1">{fmt(stats.restaurant_revenue_today)}</p>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="rounded-xl border border-border bg-card overflow-hidden">
                        <div className="px-5 py-3 border-b border-border flex items-center gap-2">
                            <BedDouble className="h-4 w-4 text-blue-500" />
                            <h2 className="text-sm font-semibold">Chambres en cours ({checked_in_reservations.length})</h2>
                        </div>
                        {checked_in_reservations.length === 0 ? (
                            <p className="py-8 text-center text-sm text-muted-foreground">Aucune chambre occupée.</p>
                        ) : (
                            <div className="divide-y divide-border/50">
                                {checked_in_reservations.map(res => (
                                    <div key={res.id} className="px-5 py-3 flex items-center justify-between hover:bg-muted/20 transition-colors">
                                        <div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-sm font-semibold text-foreground">Ch. {res.room_number}</span>
                                                {res.open_orders > 0 && (
                                                    <span className="text-[10px] bg-orange-100 text-orange-600 rounded-full px-2 py-0.5 font-medium">
                                                        {res.open_orders} cmd ouverte{res.open_orders > 1 ? 's' : ''}
                                                    </span>
                                                )}
                                            </div>
                                            <p className="text-xs text-muted-foreground">{res.guest_name} · Départ : {res.check_out}</p>
                                        </div>
                                        <div className="text-right ml-4">
                                            <p className="text-xs text-foreground font-medium">{fmt(res.total_price)}</p>
                                            {res.restaurant_total > 0 && (
                                                <p className="text-[10px] text-orange-500">+{fmt(res.restaurant_total)} resto</p>
                                            )}
                                        </div>
                                        <Link
                                            href={route('hotel.reservations.show', { current_team: slug, reservation: res.id })}
                                            className="ml-3 text-[10px] text-blue-500 hover:text-blue-400 shrink-0"
                                        >
                                            Voir →
                                        </Link>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="rounded-xl border border-border bg-card overflow-hidden">
                        <div className="px-5 py-3 border-b border-border flex items-center gap-2">
                            <Coffee className="h-4 w-4 text-orange-500" />
                            <h2 className="text-sm font-semibold">Room service ouvert ({open_room_orders.length})</h2>
                        </div>
                        {open_room_orders.length === 0 ? (
                            <p className="py-8 text-center text-sm text-muted-foreground">Aucune commande room service ouverte.</p>
                        ) : (
                            <div className="divide-y divide-border/50">
                                {open_room_orders.map(order => (
                                    <div key={order.id} className="px-5 py-3 flex items-center justify-between">
                                        <div>
                                            <p className="text-sm font-medium text-foreground">{order.reference}</p>
                                            <p className="text-xs text-muted-foreground">
                                                {order.room_number ? `Chambre ${order.room_number}` : '—'} · {new Date(order.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}
                                            </p>
                                        </div>
                                        <span className="text-sm font-semibold text-foreground">{fmt(order.total)}</span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>

                <div className="flex gap-3">
                    <Link href={route('hotel.dashboard', { current_team: slug })}>
                        <button className="text-xs text-muted-foreground hover:text-foreground transition-colors flex items-center gap-1">
                            ← Dashboard Hôtel
                        </button>
                    </Link>
                    <Link href={route('fnb.dashboard', { current_team: slug })}>
                        <button className="text-xs text-muted-foreground hover:text-foreground transition-colors flex items-center gap-1">
                            ← Dashboard Restaurant
                        </button>
                    </Link>
                </div>
            </div>
        </>
    );
}
