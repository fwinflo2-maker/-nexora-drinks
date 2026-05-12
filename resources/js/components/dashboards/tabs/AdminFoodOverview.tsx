import { motion } from 'framer-motion';
import {
    TrendingUp, ArrowUpRight, ArrowDownRight,
    Users, Boxes, Package, AlertTriangle,
    ShoppingCart, DollarSign, UserCog, Truck, ArrowRight,
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

export default function AdminFoodOverview({ teamKpis }: { teamKpis?: any }) {
    const { currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    const revenueData = [
        { mois: 'Janv', ca: 1_800_000 },
        { mois: 'Févr', ca: 2_100_000 },
        { mois: 'Mars', ca: 2_450_000 },
        { mois: 'Avr', ca: 2_980_000 },
        { mois: 'Mai', ca: 3_200_000 },
        { mois: 'Juin', ca: 3_750_000 },
    ];

    const productMixData = [
        { name: 'Produits frais', value: 32, color: '#10B981' },
        { name: 'Épicerie', value: 38, color: '#F59E0B' },
        { name: 'Boulangerie', value: 14, color: '#F97316' },
        { name: 'Boissons', value: 16, color: '#3B82F6' },
    ];

    const zonePerformance = [
        { zone: 'Akwa', ca: 1_850_000, clients: 24 },
        { zone: 'Deido', ca: 1_420_000, clients: 18 },
        { zone: 'Bonanjo', ca: 1_180_000, clients: 15 },
        { zone: 'Bali', ca: 640_000, clients: 10 },
        { zone: 'Bassa', ca: 280_000, clients: 5 },
    ];

    const foodCategories = [
        { name: 'Produits frais', products: 12, stock: 280, icon: '🥩' },
        { name: 'Épicerie', products: 45, stock: 1200, icon: '🛒' },
        { name: 'Boulangerie', products: 8, stock: 150, icon: '🍞' },
        { name: 'Boissons', products: 20, stock: 480, icon: '🥤' },
    ];

    const kpis = [
        {
            title: 'CA Alimentaire',
            value: (teamKpis?.ca_month_xaf ?? 0).toLocaleString('fr-FR') + ' XAF',
            sub: 'Chiffre d\'affaires du mois',
            icon: Boxes,
            trend: '+14.2%',
            positive: true,
            color: 'bg-emerald-500/10 text-emerald-400',
        },
        {
            title: 'Clients Actifs',
            value: (teamKpis?.clients_actifs ?? 0).toString(),
            sub: 'Points de vente actifs',
            icon: Users,
            trend: '+8',
            positive: true,
            color: 'bg-blue-500/10 text-blue-400',
        },
        {
            title: 'Produits',
            value: (teamKpis?.total_products ?? 85).toString(),
            sub: 'En catalogue',
            icon: Package,
            trend: '+5',
            positive: true,
            color: 'bg-amber-500/10 text-amber-400',
        },
        {
            title: 'Alertes Stock',
            value: (teamKpis?.stock_alerts ?? 0).toString(),
            sub: 'Produits en rupture ou critique',
            icon: AlertTriangle,
            trend: '-2',
            positive: true,
            color: 'bg-rose-500/10 text-rose-400',
        },
    ];

    const quickActions = [
        { label: 'Gérer les stocks', href: `/${slug}/stocks`, icon: Package, color: 'bg-emerald-500/10 text-emerald-400' },
        { label: 'Factures', href: `/${slug}/factures`, icon: ShoppingCart, color: 'bg-blue-500/10 text-blue-400' },
        { label: 'Équipe', href: `/${slug}/dashboard?section=profiles`, icon: UserCog, color: 'bg-violet-500/10 text-violet-400' },
        { label: 'Approvisionnements', href: `/${slug}/finances`, icon: Truck, color: 'bg-amber-500/10 text-amber-400' },
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

                {/* Product Mix */}
                <motion.div variants={item} className="rounded-lg border border-border bg-card p-6">
                    <SectionTitle>Mix Catégories</SectionTitle>
                    <ResponsiveContainer width="100%" height={250}>
                        <PieChart>
                            <Pie data={productMixData} cx="50%" cy="50%" innerRadius={60} outerRadius={90} dataKey="value" paddingAngle={2}>
                                {productMixData.map((entry, index) => (
                                    <Cell key={`cell-${index}`} fill={entry.color} />
                                ))}
                            </Pie>
                            <Tooltip />
                        </PieChart>
                    </ResponsiveContainer>
                    <div className="mt-4 space-y-2">
                        {productMixData.map(entry => (
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

            {/* ═══ Catégories Alimentaires ═══ */}
            <motion.div variants={item}>
                <SectionTitle>Catégories Alimentaires</SectionTitle>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {foodCategories.map((cat, i) => (
                        <div key={i} className="rounded-lg border border-border bg-card p-4 hover:border-primary/20 hover:shadow-sm transition-all duration-200">
                            <div className="flex items-center gap-3 mb-3">
                                <span className="text-2xl">{cat.icon}</span>
                                <div className="font-semibold text-sm text-foreground">{cat.name}</div>
                            </div>
                            <div className="space-y-1">
                                <div className="flex justify-between text-xs">
                                    <span className="text-muted-foreground">Produits</span>
                                    <span className="font-semibold text-foreground">{cat.products}</span>
                                </div>
                                <div className="flex justify-between text-xs">
                                    <span className="text-muted-foreground">Stock unités</span>
                                    <span className="font-semibold text-foreground">{cat.stock.toLocaleString('fr-FR')}</span>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </motion.div>

            {/* ═══ Zone Performance ═══ */}
            <motion.div variants={item} className="rounded-lg border border-border bg-card p-6">
                <SectionTitle>Performance par Zone</SectionTitle>
                <ResponsiveContainer width="100%" height={300}>
                    <BarChart data={zonePerformance} margin={{ top: 20, right: 30, left: 0, bottom: 5 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" />
                        <XAxis dataKey="zone" stroke="hsl(var(--muted-foreground))" />
                        <YAxis stroke="hsl(var(--muted-foreground))" />
                        <Tooltip contentStyle={{ backgroundColor: 'hsl(var(--card))', border: '1px solid hsl(var(--border))' }} />
                        <Bar dataKey="ca" fill="hsl(var(--primary))" radius={[8, 8, 0, 0]} />
                    </BarChart>
                </ResponsiveContainer>
                <div className="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                    {zonePerformance.map(zone => (
                        <div key={zone.zone} className="p-3 rounded-lg bg-secondary/50">
                            <div className="text-sm font-semibold text-foreground">{zone.zone}</div>
                            <div className="text-xs text-muted-foreground mt-1">{zone.clients} clients</div>
                            <div className="text-lg font-bold text-primary mt-2">{(zone.ca / 1_000_000).toFixed(1)}M</div>
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
