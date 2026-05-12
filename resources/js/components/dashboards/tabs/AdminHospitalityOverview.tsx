import { motion } from 'framer-motion';
import {
    TrendingUp, ArrowUpRight, ArrowDownRight,
    Users, Package, AlertTriangle,
    BedDouble, UtensilsCrossed, DollarSign, UserCog, ArrowRight,
} from 'lucide-react';
import { Link, usePage } from '@inertiajs/react';
import { LineChart, Line, PieChart, Pie, Cell, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.06 } } };
const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } } };

function KpiCard({ title, value, sub, icon: Icon, trend, positive, color }: {
    title: string; value: string; sub: string; icon: React.ElementType;
    trend: string; positive: boolean; color: string;
}) {
    return (
        <motion.div variants={item} className="relative rounded-2xl border border-border bg-card p-5 flex flex-col gap-3 overflow-hidden group hover:shadow-lg hover:border-primary/20 transition-all duration-300">
            <div className="flex items-center justify-between">
                <span className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{title}</span>
                <div className={`p-2 rounded-xl ${color}`}>
                    <Icon className="h-4 w-4" />
                </div>
            </div>
            <div>
                <div className="text-2xl font-bold tracking-tight text-foreground">{value}</div>
                <div className="text-xs text-muted-foreground mt-0.5">{sub}</div>
            </div>
            <div className={`flex items-center gap-1 text-xs font-semibold ${positive ? 'text-emerald-400' : 'text-rose-400'}`}>
                {positive ? <ArrowUpRight className="h-3.5 w-3.5" /> : <ArrowDownRight className="h-3.5 w-3.5" />}
                {trend}
            </div>
            <div className="absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-primary/[0.03] group-hover:bg-primary/[0.06] transition-colors" />
        </motion.div>
    );
}

function SectionTitle({ children }: { children: React.ReactNode }) {
    return <h2 className="text-sm font-semibold text-foreground mb-3">{children}</h2>;
}

export default function AdminHospitalityOverview({ teamKpis }: { teamKpis?: any }) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    const revenueData = [
        { mois: 'Janv', ca: 2_400_000 },
        { mois: 'Févr', ca: 2_800_000 },
        { mois: 'Mars', ca: 3_100_000 },
        { mois: 'Avr', ca: 3_600_000 },
        { mois: 'Mai', ca: 4_200_000 },
        { mois: 'Juin', ca: 4_750_000 },
    ];

    const occupationData = [
        { name: 'Chambres occupées', value: teamKpis?.taux_occupation ?? 72, color: '#6366F1' },
        { name: 'Chambres libres', value: 100 - (teamKpis?.taux_occupation ?? 72), color: '#E5E7EB' },
    ];

    const revenueByService = [
        { service: 'Hébergement', ca: 3_200_000 },
        { service: 'Bar', ca: 850_000 },
        { service: 'Restauration', ca: 620_000 },
        { service: 'Extras', ca: 180_000 },
    ];

    const hotelCategories = [
        { name: 'Réception', count: teamKpis?.sejours_actifs ?? 0, label: 'Séjours actifs', icon: '🏨' },
        { name: 'Bar', count: teamKpis?.total_products ?? 0, label: 'Produits en stock', icon: '🍹' },
        { name: 'Check-ins', count: teamKpis?.checkins_today ?? 0, label: "Aujourd'hui", icon: '🔑' },
        { name: 'Alertes', count: teamKpis?.stock_alerts ?? 0, label: 'Stock critique', icon: '⚠️' },
    ];

    const kpis = [
        {
            title: 'CA Mensuel',
            value: teamKpis?.ca_month_xaf != null
                ? `${(teamKpis.ca_month_xaf / 1_000_000).toFixed(1)}M XAF`
                : '—',
            sub: "Chiffre d'affaires du mois",
            icon: DollarSign,
            trend: '+11%',
            positive: true,
            color: 'bg-emerald-500/10 text-emerald-400',
        },
        {
            title: 'Séjours Actifs',
            value: (teamKpis?.sejours_actifs ?? 0).toString(),
            sub: 'Clients en séjour',
            icon: BedDouble,
            trend: '+3',
            positive: true,
            color: 'bg-indigo-500/10 text-indigo-400',
        },
        {
            title: 'Taux Occupation',
            value: `${teamKpis?.taux_occupation ?? 72}%`,
            sub: 'Chambres occupées',
            icon: TrendingUp,
            trend: '+5%',
            positive: true,
            color: 'bg-violet-500/10 text-violet-400',
        },
        {
            title: 'Alertes Stock Bar',
            value: (teamKpis?.stock_alerts ?? 0).toString(),
            sub: 'Produits en rupture ou critique',
            icon: AlertTriangle,
            trend: '-1',
            positive: true,
            color: 'bg-rose-500/10 text-rose-400',
        },
    ];

    const quickActions = [
        { label: 'Réception', href: `/${slug}/dashboard?section=hotel-reception`, icon: Users, color: 'bg-indigo-500/10 text-indigo-400' },
        { label: 'Bar & Stock', href: `/${slug}/dashboard?section=hotel-bar`, icon: Package, color: 'bg-orange-500/10 text-orange-400' },
        { label: 'Factures', href: `/${slug}/factures`, icon: UtensilsCrossed, color: 'bg-blue-500/10 text-blue-400' },
        { label: 'Équipe', href: `/${slug}/dashboard?section=profiles`, icon: UserCog, color: 'bg-violet-500/10 text-violet-400' },
    ];

    return (
        <motion.div variants={container} initial="hidden" animate="visible" className="space-y-6">

            {/* ═══ KPI Cards ═══ */}
            <motion.div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {kpis.map((kpi, i) => (
                    <KpiCard key={i} {...kpi} />
                ))}
            </motion.div>

            {/* ═══ Graphs Row ═══ */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* CA Trend */}
                <motion.div variants={item} className="lg:col-span-2 rounded-lg border border-border bg-card p-6">
                    <SectionTitle>Chiffre d'affaires - 6 derniers mois (XAF)</SectionTitle>
                    <ResponsiveContainer width="100%" height={250}>
                        <LineChart data={revenueData} margin={{ top: 5, right: 30, left: 0, bottom: 5 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                            <XAxis dataKey="mois" stroke="hsl(var(--muted-foreground))" />
                            <YAxis stroke="hsl(var(--muted-foreground))" />
                            <Tooltip contentStyle={{ backgroundColor: 'hsl(var(--card))', border: '1px solid hsl(var(--border))' }} />
                            <Line
                                type="monotone"
                                dataKey="ca"
                                stroke="hsl(var(--primary))"
                                strokeWidth={2}
                                dot={{ fill: 'hsl(var(--primary))', r: 4 }}
                            />
                        </LineChart>
                    </ResponsiveContainer>
                </motion.div>

                {/* Taux d'occupation */}
                <motion.div variants={item} className="rounded-lg border border-border bg-card p-6">
                    <SectionTitle>Taux d'Occupation</SectionTitle>
                    <ResponsiveContainer width="100%" height={200}>
                        <PieChart>
                            <Pie data={occupationData} cx="50%" cy="50%" innerRadius={60} outerRadius={90} dataKey="value" paddingAngle={2}>
                                {occupationData.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={entry.color} />
                                ))}
                            </Pie>
                            <Tooltip />
                        </PieChart>
                    </ResponsiveContainer>
                    <div className="mt-4 space-y-2">
                        {occupationData.map(entry => (
                            <div key={entry.name} className="flex items-center justify-between text-sm">
                                <div className="flex items-center gap-2">
                                    <div className="h-2 w-2 rounded-full" style={{ backgroundColor: entry.color }} />
                                    <span className="text-muted-foreground">{entry.name}</span>
                                </div>
                                <span className="font-semibold">{entry.value}%</span>
                            </div>
                        ))}
                    </div>
                </motion.div>
            </div>

            {/* ═══ Indicateurs Hôtel ═══ */}
            <motion.div variants={item}>
                <SectionTitle>Indicateurs par Service</SectionTitle>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {hotelCategories.map((cat, i) => (
                        <div key={i} className="rounded-lg border border-border bg-card p-4 hover:border-primary/20 hover:shadow-sm transition-all duration-200">
                            <div className="flex items-center gap-3 mb-3">
                                <span className="text-2xl">{cat.icon}</span>
                                <div className="font-semibold text-sm text-foreground">{cat.name}</div>
                            </div>
                            <div className="space-y-1">
                                <div className="flex justify-between text-xs">
                                    <span className="text-muted-foreground">{cat.label}</span>
                                    <span className="font-semibold text-foreground">{cat.count}</span>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </motion.div>

            {/* ═══ Revenus par Service ═══ */}
            <motion.div variants={item} className="rounded-lg border border-border bg-card p-6">
                <SectionTitle>Revenus par Service</SectionTitle>
                <ResponsiveContainer width="100%" height={250}>
                    <BarChart data={revenueByService} margin={{ top: 20, right: 30, left: 0, bottom: 5 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                        <XAxis dataKey="service" stroke="hsl(var(--muted-foreground))" />
                        <YAxis stroke="hsl(var(--muted-foreground))" />
                        <Tooltip contentStyle={{ backgroundColor: 'hsl(var(--card))', border: '1px solid hsl(var(--border))' }} />
                        <Bar dataKey="ca" fill="hsl(var(--primary))" radius={[8, 8, 0, 0]} />
                    </BarChart>
                </ResponsiveContainer>
                <div className="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                    {revenueByService.map(s => (
                        <div key={s.service} className="p-3 rounded-lg bg-secondary/50">
                            <div className="text-sm font-semibold text-foreground">{s.service}</div>
                            <div className="text-lg font-bold text-primary mt-2">{(s.ca / 1_000_000).toFixed(1)}M</div>
                        </div>
                    ))}
                </div>
            </motion.div>

            {/* ═══ Quick Actions ═══ */}
            <motion.div variants={item}>
                <SectionTitle>Actions Rapides</SectionTitle>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {quickActions.map((action, i) => (
                        <Link
                            key={i}
                            href={action.href}
                            className="rounded-xl border border-border bg-card p-4 flex items-center gap-3 hover:border-primary/30 transition-all duration-200 group"
                        >
                            <div className={`p-2.5 rounded-xl ${action.color} shrink-0`}>
                                <action.icon className="h-5 w-5" />
                            </div>
                            <span className="text-sm font-medium text-foreground flex-1">{action.label}</span>
                            <ArrowRight className="h-4 w-4 text-muted-foreground group-hover:text-foreground transition-colors shrink-0" />
                        </Link>
                    ))}
                </div>
            </motion.div>

        </motion.div>
    );
}
