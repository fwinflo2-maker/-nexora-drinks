import { motion } from 'framer-motion';
import {
    Activity, Package, Truck, AlertTriangle, ArrowUpRight, ArrowDownRight,
    TrendingUp, Users, ShoppingCart, Clock, CheckCircle, XCircle, MoreHorizontal, Wallet,
} from 'lucide-react';

/* ─── Animations ─── */
const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.06 } } };
const item      = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };

/* ─── Shared micro-components ─── */

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
                {trend} <span className="font-normal text-muted-foreground ml-1">vs hier</span>
            </div>
            <div className="absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-primary/[0.03] group-hover:bg-primary/[0.06] transition-colors" />
        </motion.div>
    );
}

function SectionTitle({ children }: { children: React.ReactNode }) {
    return <h2 className="text-sm font-semibold text-foreground mb-3">{children}</h2>;
}

/* ─── SVG Line Chart ─── */
function RevenueChart() {
    const points = [3.2, 2.8, 4.1, 3.7, 5.2, 4.8, 6.1, 5.4, 7.2, 6.8, 8.4, 7.9];
    const max = Math.max(...points);
    const w = 560; const h = 120; const pad = 10;
    const xs = points.map((_, i) => pad + (i / (points.length - 1)) * (w - 2 * pad));
    const ys = points.map(v => h - pad - ((v / max) * (h - 2 * pad)));
    const linePath = xs.map((x, i) => `${i === 0 ? 'M' : 'L'}${x},${ys[i]}`).join(' ');
    const areaPath = `${linePath} L${xs[xs.length - 1]},${h} L${xs[0]},${h} Z`;

    return (
        <svg viewBox={`0 0 ${w} ${h}`} className="w-full" style={{ height: 120 }}>
            <defs>
                <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="hsl(var(--primary))" stopOpacity="0.15" />
                    <stop offset="100%" stopColor="hsl(var(--primary))" stopOpacity="0" />
                </linearGradient>
            </defs>
            {[0.25, 0.5, 0.75, 1].map(t => (
                <line key={t} x1={pad} y1={h - pad - t * (h - 2 * pad)} x2={w - pad} y2={h - pad - t * (h - 2 * pad)}
                    stroke="hsl(var(--border))" strokeWidth="1" strokeDasharray="4 4" />
            ))}
            <path d={areaPath} fill="url(#revGrad)" />
            <path d={linePath} fill="none" stroke="hsl(var(--primary))" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"
                style={{ strokeDasharray: 800, strokeDashoffset: 800, animation: 'drawDash 1.5s ease-out forwards 0.3s' }} />
            {xs.map((x, i) => <circle key={i} cx={x} cy={ys[i]} r="3" fill="hsl(var(--primary))" opacity="0.8" />)}
            <style>{`@keyframes drawDash { to { stroke-dashoffset: 0; } }`}</style>
        </svg>
    );
}

/* ─── Bar Chart ─── */
function ZoneBar({ label, value, max, color }: { label: string; value: number; max: number; color: string }) {
    const pct = (value / max) * 100;

    return (
        <div className="flex items-center gap-3">
            <span className="text-xs text-muted-foreground w-20 shrink-0 truncate">{label}</span>
            <div className="flex-1 h-2 rounded-full bg-secondary overflow-hidden">
                <div className={`h-full rounded-full ${color}`}
                    style={{ width: 0, animation: `growBar 0.8s cubic-bezier(0.16,1,0.3,1) forwards 0.4s` }}
                    data-width={`${pct}%`}
                    ref={el => {
 if (el) {
setTimeout(() => {
 el.style.width = `${pct}%`; 
}, 400);
} 
}}
                />
            </div>
            <span className="text-xs font-semibold text-foreground w-14 text-right shrink-0">{value.toLocaleString()} XAF</span>
        </div>
    );
}

export default function AdminOverview({ userName, teamName, teamKpis, categories, warehouses }: { userName: string, teamName?: string, teamKpis?: any, categories?: any[], warehouses?: any[] }) {
    const kpis = [
        {
            title: "Chiffre d'Affaires",
            value: "0 XAF",
            sub: "En attente de ventes",
            icon: Wallet,
            trend: "0%",
            positive: true,
            color: "bg-emerald-500/10 text-emerald-400"
        },
        {
            title: "Entrepôts",
            value: (warehouses?.length || 0).toString(),
            sub: "Configuration active",
            icon: Package,
            trend: "Initial",
            positive: true,
            color: "bg-blue-500/10 text-blue-400"
        },
        {
            title: "Catégories",
            value: (categories?.length || 0).toString(),
            sub: "Produits configurés",
            icon: Activity,
            trend: "Initial",
            positive: true,
            color: "bg-rose-500/10 text-rose-400"
        },
        {
            title: "Utilisateurs",
            value: teamKpis?.users_count?.toString() || "1",
            sub: "Sur votre licence",
            icon: Users,
            trend: "+1",
            positive: true,
            color: "bg-violet-500/10 text-violet-400"
        }
    ];

    const deliveries = [
        { id: "LIV-4029", client: "Super U Akwa",        zone: "Akwa",   driver: "Njoya M.",   status: "Livré",      amount: "125 000", time: "10 min" },
        { id: "LIV-4030", client: "Cave Roi Pelé",        zone: "Bonanjo",driver: "Kamga P.",   status: "En route",   amount: "45 000",  time: "25 min" },
        { id: "LIV-4031", client: "Boulangerie Saker",    zone: "Deido",  driver: "Oumarou",    status: "Préparation",amount: "89 000",  time: "1 h"    },
        { id: "LIV-4032", client: "Cave Akwa Palace",     zone: "Akwa",   driver: "Eboa L.",    status: "Livré",      amount: "350 000", time: "2 h"    },
        { id: "LIV-4033", client: "Bar Le Wouri",         zone: "Bassa",  driver: "Nguema",     status: "Retard",     amount: "62 000",  time: "3 h"    },
    ];

    const stockAlerts = [
        { product: "Castel Beer 65cl (Casier)", current: 8,  min: 30, status: "Critique" },
        { product: "Beaufort 65cl (Casier)",    current: 12, min: 20, status: "Critique" },
        { product: "Mutzig Bouteille 33cl",     current: 45, min: 50, status: "Attention" },
        { product: "Guinness 33cl",             current: 18, min: 25, status: "Attention" },
    ];

    const zones = [
        { label: "Akwa",    value: 1_850_000, color: "bg-emerald-500" },
        { label: "Bonanjo", value: 1_120_000, color: "bg-blue-500"    },
        { label: "Deido",   value: 870_000,   color: "bg-violet-500"  },
        { label: "Bali",    value: 640_000,   color: "bg-amber-500"   },
        { label: "Bassa",   value: 248_500,   color: "bg-rose-500"    },
    ];

    const statusStyle: Record<string, string> = {
        "Livré":       "bg-emerald-500/10 text-emerald-400 border-emerald-500/20",
        "En route":    "bg-blue-500/10 text-blue-400 border-blue-500/20",
        "Préparation": "bg-amber-500/10 text-amber-400 border-amber-500/20",
        "Retard":      "bg-rose-500/10 text-rose-400 border-rose-500/20",
    };
    const statusIcon: Record<string, React.ReactNode> = {
        "Livré":       <CheckCircle className="h-3 w-3" />,
        "En route":    <Truck className="h-3 w-3" />,
        "Préparation": <Clock className="h-3 w-3" />,
        "Retard":      <XCircle className="h-3 w-3" />,
    };

    const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });

    return (
        <div className="flex flex-col gap-6">

            {/* KPI Cards */}
            <motion.div variants={container} initial="hidden" animate="visible"
                className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {kpis.map((k, i) => <KpiCard key={i} {...k} />)}
            </motion.div>

            {/* Row 2: Chart + Zone bars */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-4">

                {/* Revenue Chart */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.3 }}
                    className="xl:col-span-2 rounded-2xl border border-border bg-card p-5">
                    <div className="flex items-center justify-between mb-4">
                        <div>
                            <SectionTitle>Évolution du chiffre d'affaires</SectionTitle>
                            <p className="text-xs text-muted-foreground -mt-2">12 dernières semaines</p>
                        </div>
                        <div className="flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                            <TrendingUp className="h-3.5 w-3.5" />
                            +18.4% ce mois
                        </div>
                    </div>
                    <RevenueChart />
                    <div className="flex justify-between mt-2 px-1">
                        {['S1','S2','S3','S4','S5','S6','S7','S8','S9','S10','S11','S12'].map(s => (
                            <span key={s} className="text-[10px] text-muted-foreground">{s}</span>
                        ))}
                    </div>
                </motion.div>

                {/* Zone Performance */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.4 }}
                    className="rounded-2xl border border-border bg-card p-5">
                    <SectionTitle>CA par zone de distribution</SectionTitle>
                    <div className="flex flex-col gap-4 mt-4">
                        {zones.map((z, i) => (
                            <ZoneBar key={i} label={z.label} value={z.value} max={zones[0].value} color={z.color} />
                        ))}
                    </div>
                    <div className="mt-5 pt-4 border-t border-border flex items-center justify-between text-xs">
                        <span className="text-muted-foreground">Total journalier</span>
                        <span className="font-bold text-foreground">4 728 500 XAF</span>
                    </div>
                </motion.div>
            </div>

            {/* Row 3: Deliveries + Stock Alerts */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-4">

                {/* Deliveries Table */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.5 }}
                    className="xl:col-span-2 rounded-2xl border border-border bg-card overflow-hidden">
                    <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                        <SectionTitle>Livraisons récentes</SectionTitle>
                        <button className="text-xs text-primary hover:underline font-medium">Voir tout →</button>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border bg-secondary/30">
                                    <th className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">ID</th>
                                    <th className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">Client</th>
                                    <th className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">Zone</th>
                                    <th className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">Chauffeur</th>
                                    <th className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">Statut</th>
                                    <th className="text-right px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">Montant</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {deliveries.map((d, i) => (
                                    <tr key={i} className="hover:bg-secondary/20 transition-colors group">
                                        <td className="px-5 py-3.5 font-mono text-xs text-muted-foreground">{d.id}</td>
                                        <td className="px-5 py-3.5 font-medium text-foreground">{d.client}</td>
                                        <td className="px-5 py-3.5 text-muted-foreground">{d.zone}</td>
                                        <td className="px-5 py-3.5 text-muted-foreground">{d.driver}</td>
                                        <td className="px-5 py-3.5">
                                            <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border ${statusStyle[d.status]}`}>
                                                {statusIcon[d.status]}{d.status}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3.5 text-right font-semibold text-foreground">{d.amount} XAF</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </motion.div>

                {/* Stock Alerts */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.6 }}
                    className="rounded-2xl border border-border bg-card overflow-hidden">
                    <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                        <SectionTitle>Alertes de stock</SectionTitle>
                        <span className="flex h-5 w-5 items-center justify-center rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold">
                            {stockAlerts.length}
                        </span>
                    </div>
                    <div className="divide-y divide-border">
                        {stockAlerts.map((a, i) => (
                            <div key={i} className="px-5 py-3.5 flex items-center justify-between hover:bg-secondary/20 transition-colors">
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-medium text-foreground truncate">{a.product}</p>
                                    <p className="text-xs text-muted-foreground mt-0.5">Min requis: {a.min} unités</p>
                                </div>
                                <div className="ml-3 text-right shrink-0">
                                    <p className={`text-lg font-bold ${a.status === 'Critique' ? 'text-rose-400' : 'text-amber-400'}`}>
                                        {a.current}
                                    </p>
                                    <span className={`text-[10px] font-semibold uppercase tracking-wider ${a.status === 'Critique' ? 'text-rose-400' : 'text-amber-400'}`}>
                                        {a.status}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="px-5 py-3 border-t border-border">
                        <button className="w-full text-xs text-primary hover:underline font-medium text-center">
                            Gérer les stocks →
                        </button>
                    </div>
                </motion.div>
            </div>
        </div>
    );
}
