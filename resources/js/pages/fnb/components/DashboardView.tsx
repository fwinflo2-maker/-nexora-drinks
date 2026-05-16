import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    UtensilsCrossed, ShoppingBag, DollarSign,
    TableProperties, ChefHat, TrendingUp,
} from 'lucide-react';
import {
    BarChart, Bar, LineChart, Line,
    XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
} from 'recharts';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface Stats {
    tables_free: number;
    tables_occupied: number;
    orders_open: number;
    revenue_today: number;
}

interface OrderSummary {
    id: number;
    reference: string;
    table_name: string | null;
    status: string;
    total: number;
    items_count: number;
}

interface TopItem {
    name: string | null;
    total_qty: number;
}

interface HourRevenue {
    hour: string;
    revenue: number;
}

interface Props {
    stats: Stats;
    activeOrders: OrderSummary[];
    topItems: TopItem[];
    revenueByHour: HourRevenue[];
}

function StatCard({
    label, value, icon, color, sub,
}: {
    label: string; value: string | number; icon: React.ReactNode; color: string; sub?: string;
}) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-card rounded-2xl border border-border p-5 flex items-center gap-4 shadow-sm"
        >
            <div className={`h-11 w-11 rounded-xl flex items-center justify-center shrink-0 ${color}`}>
                {icon}
            </div>
            <div>
                <p className="text-2xl font-semibold text-foreground">{value}</p>
                <p className="text-xs text-muted-foreground mt-0.5">{label}</p>
                {sub && <p className="text-[10px] text-muted-foreground/60">{sub}</p>}
            </div>
        </motion.div>
    );
}

export function OrderStatusBadge({ status }: { status: string }) {
    const map: Record<string, string> = {
        open: 'bg-slate-100 text-slate-600',
        sent: 'bg-blue-100 text-blue-700',
        preparing: 'bg-amber-100 text-amber-700',
        ready: 'bg-emerald-100 text-emerald-700',
        closed: 'bg-slate-100 text-slate-400',
        cancelled: 'bg-red-100 text-red-500',
    };
    const labels: Record<string, string> = {
        open: 'Ouverte', sent: 'Envoyée', preparing: 'En prép.',
        ready: 'Prête', closed: 'Clôturée', cancelled: 'Annulée',
    };

    return (
        <span className={`text-[11px] font-medium px-2 py-0.5 rounded-full ${map[status] ?? 'bg-slate-100 text-slate-500'}`}>
            {labels[status] ?? status}
        </span>
    );
}

export default function DashboardView({ stats, activeOrders, topItems, revenueByHour }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm text-muted-foreground">Suivi temps réel de votre salle</p>
                </div>
                <Link
                    href={route('fnb.kitchen', { current_team: team?.slug })}
                    className="flex items-center gap-2 px-4 py-2 rounded-xl bg-foreground text-background text-sm font-medium hover:opacity-90 transition-opacity"
                >
                    <ChefHat className="h-4 w-4" />
                    Écran cuisine
                </Link>
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard label="Tables libres" value={stats.tables_free}
                    icon={<TableProperties className="h-5 w-5 text-emerald-600" />}
                    color="bg-emerald-50" />
                <StatCard label="Tables occupées" value={stats.tables_occupied}
                    icon={<UtensilsCrossed className="h-5 w-5 text-blue-600" />}
                    color="bg-blue-50" />
                <StatCard label="Commandes ouvertes" value={stats.orders_open}
                    icon={<ShoppingBag className="h-5 w-5 text-amber-600" />}
                    color="bg-amber-50" />
                <StatCard label="CA du jour" value={(stats.revenue_today ?? 0).toLocaleString()}
                    icon={<DollarSign className="h-5 w-5 text-purple-600" />}
                    color="bg-purple-50" />
            </div>

            <div className="bg-card rounded-2xl border border-border p-5 shadow-sm">
                <div className="flex items-center gap-2 mb-4">
                    <ShoppingBag className="h-4 w-4 text-muted-foreground" />
                    <h2 className="text-sm font-semibold text-foreground">Commandes en cours</h2>
                    <span className="ml-auto text-xs text-muted-foreground">{activeOrders.length}</span>
                </div>
                {activeOrders.length === 0 ? (
                    <p className="text-xs text-muted-foreground text-center py-4">Aucune commande active</p>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        {activeOrders.map((o) => (
                            <motion.div
                                key={o.id}
                                initial={{ opacity: 0, scale: 0.97 }}
                                animate={{ opacity: 1, scale: 1 }}
                                className="rounded-xl border border-border bg-muted/30 p-3"
                            >
                                <div className="flex items-center justify-between mb-2">
                                    <span className="font-mono text-xs text-muted-foreground">{o.reference}</span>
                                    <OrderStatusBadge status={o.status} />
                                </div>
                                <p className="text-sm font-medium text-foreground">{o.table_name ?? 'Sans table'}</p>
                                <div className="flex justify-between items-center mt-2 text-xs text-muted-foreground">
                                    <span>{o.items_count} article{o.items_count > 1 ? 's' : ''}</span>
                                    <span className="font-medium text-foreground">{o.total.toLocaleString()}</span>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                )}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div className="bg-card rounded-2xl border border-border p-5 shadow-sm">
                    <div className="flex items-center gap-2 mb-4">
                        <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        <h2 className="text-sm font-semibold text-foreground">Top articles du jour</h2>
                    </div>
                    <ResponsiveContainer width="100%" height={180}>
                        <BarChart data={topItems} layout="vertical" margin={{ left: 0, right: 10 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" horizontal={false} />
                            <XAxis type="number" tick={{ fontSize: 11 }} axisLine={false} tickLine={false} />
                            <YAxis type="category" dataKey="name" tick={{ fontSize: 11 }} axisLine={false} tickLine={false} width={90} />
                            <Tooltip
                                contentStyle={{ borderRadius: 10, fontSize: 12 }}
                                formatter={(v: any) => [`${Number(v)}`, 'Qté']}
                            />
                            <Bar dataKey="total_qty" fill="#6366f1" radius={[0, 6, 6, 0]} />
                        </BarChart>
                    </ResponsiveContainer>
                </div>

                <div className="bg-card rounded-2xl border border-border p-5 shadow-sm">
                    <div className="flex items-center gap-2 mb-4">
                        <DollarSign className="h-4 w-4 text-muted-foreground" />
                        <h2 className="text-sm font-semibold text-foreground">CA par heure</h2>
                    </div>
                    <ResponsiveContainer width="100%" height={180}>
                        <LineChart data={revenueByHour} margin={{ top: 5, right: 10, left: -15, bottom: 0 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                            <XAxis dataKey="hour" tick={{ fontSize: 10 }} axisLine={false} tickLine={false} />
                            <YAxis tick={{ fontSize: 10 }} axisLine={false} tickLine={false} />
                            <Tooltip
                                contentStyle={{ borderRadius: 10, fontSize: 12 }}
                                formatter={(v: any) => [`${Number(v).toLocaleString()}`, 'CA']}
                            />
                            <Line type="monotone" dataKey="revenue" stroke="#10b981" strokeWidth={2} dot={false} />
                        </LineChart>
                    </ResponsiveContainer>
                </div>
            </div>
        </div>
    );
}
