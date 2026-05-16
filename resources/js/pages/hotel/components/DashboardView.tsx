import { motion } from 'framer-motion';
import {
    BedDouble, LogIn, LogOut, TrendingUp, Wrench,
    CalendarCheck, Users, DollarSign,
} from 'lucide-react';
import {
    LineChart, Line, XAxis, YAxis, CartesianGrid,
    Tooltip, ResponsiveContainer,
} from 'recharts';
import React from 'react';

interface Stats {
    rooms_available: number;
    rooms_occupied: number;
    rooms_maintenance: number;
    reservations_today: number;
}

interface ReservationRow {
    id: number;
    reference: string;
    guest_name: string | null;
    room_number: string | null;
    status: string;
    total_price?: number;
    check_in?: string;
    check_out?: string;
}

interface RevenuePoint {
    date: string;
    revenue: number;
}

interface Props {
    stats: Stats;
    arrivalsToday: ReservationRow[];
    departuresToday: ReservationRow[];
    recentReservations: ReservationRow[];
    revenueChart: RevenuePoint[];
}

function StatCard({ label, value, icon, color }: { label: string; value: number | string; icon: React.ReactNode; color: string }) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-card rounded-2xl border border-border p-5 flex items-center gap-4 shadow-sm"
        >
            <div className={`h-11 w-11 rounded-xl flex items-center justify-center ${color}`}>
                {icon}
            </div>
            <div>
                <p className="text-2xl font-semibold text-foreground">{value}</p>
                <p className="text-xs text-muted-foreground mt-0.5">{label}</p>
            </div>
        </motion.div>
    );
}

export function StatusBadge({ status }: { status: string }) {
    const map: Record<string, string> = {
        pending: 'bg-amber-100 text-amber-700',
        confirmed: 'bg-blue-100 text-blue-700',
        checked_in: 'bg-emerald-100 text-emerald-700',
        checked_out: 'bg-slate-100 text-slate-600',
        cancelled: 'bg-red-100 text-red-600',
    };
    const labels: Record<string, string> = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        checked_in: 'En cours',
        checked_out: 'Départ',
        cancelled: 'Annulée',
    };

    return (
        <span className={`text-[11px] font-medium px-2 py-0.5 rounded-full ${map[status] ?? 'bg-slate-100 text-slate-500'}`}>
            {labels[status] ?? status}
        </span>
    );
}

export default function DashboardView({ stats, arrivalsToday, departuresToday, recentReservations, revenueChart }: Props) {
    const s: Stats = stats ?? { rooms_available: 0, rooms_occupied: 0, rooms_maintenance: 0, reservations_today: 0 };
    const arrivals = arrivalsToday ?? [];
    const departures = departuresToday ?? [];
    const recent = recentReservations ?? [];
    const chart = revenueChart ?? [];

    return (
        <div className="space-y-6">
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard label="Chambres disponibles" value={s.rooms_available}
                    icon={<BedDouble className="h-5 w-5 text-emerald-600" />} color="bg-emerald-50" />
                <StatCard label="Chambres occupées" value={s.rooms_occupied}
                    icon={<Users className="h-5 w-5 text-blue-600" />} color="bg-blue-50" />
                <StatCard label="En maintenance" value={s.rooms_maintenance}
                    icon={<Wrench className="h-5 w-5 text-amber-600" />} color="bg-amber-50" />
                <StatCard label="Réservations du jour" value={s.reservations_today}
                    icon={<CalendarCheck className="h-5 w-5 text-purple-600" />} color="bg-purple-50" />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div className="bg-card rounded-2xl border border-border p-5 shadow-sm">
                    <div className="flex items-center gap-2 mb-4">
                        <LogIn className="h-4 w-4 text-emerald-500" />
                        <h2 className="text-sm font-semibold text-foreground">Arrivées du jour</h2>
                        <span className="ml-auto text-xs text-muted-foreground">{arrivals.length}</span>
                    </div>
                    {arrivals.length === 0 ? (
                        <p className="text-xs text-muted-foreground text-center py-4">Aucune arrivée prévue</p>
                    ) : (
                        <div className="space-y-2">
                            {arrivals.map(r => (
                                <div key={r.id} className="flex items-center justify-between text-sm py-2 border-b border-border/50 last:border-0">
                                    <div>
                                        <p className="font-medium text-foreground">{r.guest_name ?? '—'}</p>
                                        <p className="text-xs text-muted-foreground">Chambre {r.room_number ?? '—'}</p>
                                    </div>
                                    <StatusBadge status={r.status} />
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <div className="bg-card rounded-2xl border border-border p-5 shadow-sm">
                    <div className="flex items-center gap-2 mb-4">
                        <LogOut className="h-4 w-4 text-red-400" />
                        <h2 className="text-sm font-semibold text-foreground">Départs du jour</h2>
                        <span className="ml-auto text-xs text-muted-foreground">{departures.length}</span>
                    </div>
                    {departures.length === 0 ? (
                        <p className="text-xs text-muted-foreground text-center py-4">Aucun départ prévu</p>
                    ) : (
                        <div className="space-y-2">
                            {departures.map(r => (
                                <div key={r.id} className="flex items-center justify-between text-sm py-2 border-b border-border/50 last:border-0">
                                    <div>
                                        <p className="font-medium text-foreground">{r.guest_name ?? '—'}</p>
                                        <p className="text-xs text-muted-foreground">Chambre {r.room_number ?? '—'}</p>
                                    </div>
                                    <StatusBadge status={r.status} />
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            <div className="bg-card rounded-2xl border border-border p-5 shadow-sm">
                <div className="flex items-center gap-2 mb-4">
                    <TrendingUp className="h-4 w-4 text-muted-foreground" />
                    <h2 className="text-sm font-semibold text-foreground">Revenus — 7 derniers jours</h2>
                </div>
                <ResponsiveContainer width="100%" height={200}>
                    <LineChart data={chart} margin={{ top: 5, right: 10, left: -10, bottom: 0 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                        <XAxis dataKey="date" tick={{ fontSize: 11 }} axisLine={false} tickLine={false} />
                        <YAxis tick={{ fontSize: 11 }} axisLine={false} tickLine={false} />
                        <Tooltip
                            contentStyle={{ borderRadius: 10, fontSize: 12 }}
                            formatter={(v: any) => [`${Number(v).toLocaleString()}`, 'Revenus']}
                        />
                        <Line type="monotone" dataKey="revenue" stroke="#2563eb" strokeWidth={2} dot={false} />
                    </LineChart>
                </ResponsiveContainer>
            </div>

            <div className="bg-card rounded-2xl border border-border p-5 shadow-sm">
                <div className="flex items-center gap-2 mb-4">
                    <DollarSign className="h-4 w-4 text-muted-foreground" />
                    <h2 className="text-sm font-semibold text-foreground">Dernières réservations</h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="text-left text-xs text-muted-foreground border-b border-border">
                                <th className="pb-2 font-medium">Référence</th>
                                <th className="pb-2 font-medium">Client</th>
                                <th className="pb-2 font-medium">Chambre</th>
                                <th className="pb-2 font-medium">Arrivée</th>
                                <th className="pb-2 font-medium">Départ</th>
                                <th className="pb-2 font-medium">Statut</th>
                                <th className="pb-2 font-medium text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            {recent.length === 0 ? (
                                <tr><td colSpan={7} className="py-6 text-center text-muted-foreground text-xs">Aucune réservation</td></tr>
                            ) : recent.map(r => (
                                <tr key={r.id} className="border-b border-border/50 last:border-0 hover:bg-muted/20">
                                    <td className="py-2.5 font-mono text-xs text-muted-foreground">{r.reference}</td>
                                    <td className="py-2.5 text-foreground">{r.guest_name ?? '—'}</td>
                                    <td className="py-2.5 text-muted-foreground">{r.room_number ?? '—'}</td>
                                    <td className="py-2.5 text-muted-foreground">{r.check_in ?? '—'}</td>
                                    <td className="py-2.5 text-muted-foreground">{r.check_out ?? '—'}</td>
                                    <td className="py-2.5"><StatusBadge status={r.status} /></td>
                                    <td className="py-2.5 text-right font-medium text-foreground">
                                        {r.total_price != null ? r.total_price.toLocaleString() : '—'}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
