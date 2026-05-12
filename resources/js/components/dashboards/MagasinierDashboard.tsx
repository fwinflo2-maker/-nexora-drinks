import { Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Package, AlertTriangle, ArrowLeftRight, Archive, ArrowUp, ArrowDown, CheckSquare, Layers } from 'lucide-react';

const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };
const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.07 } } };

/* ─── Horizontal stock level bar ─── */
function StockBar({ product, current, min, optimal, unit }: {
    product: string; current: number; min: number; optimal: number; unit: string;
}) {
    const pct = Math.min((current / optimal) * 100, 100);
    const minPct = (min / optimal) * 100;
    const isCritical = current <= min;
    const isLow = current <= min * 1.5 && !isCritical;
    const color = isCritical ? "bg-rose-500" : isLow ? "bg-amber-500" : "bg-emerald-500";
    const textColor = isCritical ? "text-rose-400" : isLow ? "text-amber-400" : "text-emerald-400";

    return (
        <div className="flex items-center gap-3">
            <div className="flex-1 min-w-0">
                <div className="flex items-center justify-between mb-1">
                    <span className="text-xs font-medium text-foreground truncate">{product}</span>
                    <span className={`text-xs font-bold ${textColor} ml-2 shrink-0`}>{current} {unit}</span>
                </div>
                <div className="relative h-2 rounded-full bg-secondary overflow-visible">
                    {/* Min threshold marker */}
                    <div className="absolute top-0 bottom-0 w-0.5 bg-rose-400/60 rounded-full z-10"
                        style={{ left: `${minPct}%` }} />
                    {/* Fill bar */}
                    <div className={`h-full rounded-full ${color} transition-all duration-700`}
                        style={{ width: `${pct}%` }} />
                </div>
                <div className="flex justify-between mt-0.5">
                    <span className="text-[9px] text-muted-foreground">0</span>
                    <span className="text-[9px] text-rose-400/70">seuil: {min}</span>
                    <span className="text-[9px] text-muted-foreground">{optimal}</span>
                </div>
            </div>
            {isCritical && (
                <AlertTriangle className="h-4 w-4 text-rose-400 shrink-0 animate-pulse" />
            )}
        </div>
    );
}

/* ─── Mock data (fallback) ─── */
const mockStocks = [
    { product: "Castel Beer 65cl",    current: 8,   min: 30,  optimal: 200, unit: "cs" },
    { product: "Beaufort 65cl",       current: 12,  min: 20,  optimal: 150, unit: "cs" },
    { product: "Mutzig 65cl",         current: 45,  min: 50,  optimal: 180, unit: "cs" },
    { product: "Guinness 33cl",       current: 18,  min: 25,  optimal: 120, unit: "cs" },
    { product: "Coca-Cola 50cl",      current: 230, min: 100, optimal: 400, unit: "cs" },
    { product: "Malta Guinness 33cl", current: 156, min: 80,  optimal: 300, unit: "cs" },
    { product: "Top Ananas 33cl",     current: 87,  min: 60,  optimal: 250, unit: "cs" },
    { product: "Whisky JW Black",     current: 5,   min: 10,  optimal: 50,  unit: "bt" },
];

/* ─── Movement type config ─── */
const movementConfig: Record<string, { label: string; color: string; iconColor: string }> = {
    in:         { label: 'Entrée',      color: 'bg-emerald-500/10 text-emerald-400', iconColor: 'text-emerald-400' },
    out:        { label: 'Sortie',      color: 'bg-rose-500/10 text-rose-400',       iconColor: 'text-rose-400'    },
    adjustment: { label: 'Ajustement',  color: 'bg-blue-500/10 text-blue-400',       iconColor: 'text-blue-400'    },
    transfer:   { label: 'Transfert',   color: 'bg-violet-500/10 text-violet-400',   iconColor: 'text-violet-400'  },
};

export default function MagasinierDashboard({ userName }: { userName: string }) {
    const { stockKpis, stockItems: serverStockItems, recentMovements: serverMovements, stockAlerts: serverAlerts, teamName, currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });

    const alertsCount = stockKpis?.stock_alerts ?? 0;

    const kpis = [
        {
            title: "Références en stock",
            value: (stockKpis?.total_references ?? 0).toString(),
            sub: "références actives",
            icon: Package,
            color: "bg-blue-500/10 text-blue-400",
            highlight: false,
        },
        {
            title: "Alertes critiques",
            value: alertsCount.toString(),
            sub: "stock ≤ seuil minimal",
            icon: AlertTriangle,
            color: alertsCount > 0 ? "bg-rose-500/10 text-rose-400" : "bg-muted/10 text-muted-foreground",
            highlight: alertsCount > 0,
        },
        {
            title: "Mouvements du jour",
            value: (stockKpis?.movements_today ?? 0).toString(),
            sub: "entrées & sorties",
            icon: ArrowLeftRight,
            color: "bg-amber-500/10 text-amber-400",
            highlight: false,
        },
        {
            title: "Consignations",
            value: (stockKpis?.consignations_en_cours ?? 0).toString(),
            sub: "en cours",
            icon: Archive,
            color: "bg-violet-500/10 text-violet-400",
            highlight: false,
        },
    ];

    const stocks = (serverStockItems?.length > 0 ? serverStockItems : mockStocks).slice(0, 10).map((item: any) => ({
        product: item.name ?? item.product,
        current: item.total_quantity ?? item.current,
        min: item.min_threshold ?? item.min,
        optimal: (item.min_threshold ?? item.min ?? 10) * 4,
        unit: 'cs',
    }));

    const criticalCount = stocks.filter((s: { current: number; min: number }) => s.current <= s.min).length;

    const movements: Array<{ id: number; product_name: string; movement_type: string; quantity: number; created_at: string }> = serverMovements ?? [];
    const alerts: Array<{ id: number; name: string; total_quantity: number; min_threshold: number }> = serverAlerts ?? [];

    return (
        <div className="relative z-50 flex flex-1 flex-col gap-6 p-6 lg:p-8 w-full">

            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>
                <div className="flex items-start justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground">Gestion du Stock</h1>
                        <p className="text-sm text-muted-foreground mt-1 capitalize">{today} · {teamName ?? userName}</p>
                    </div>
                    {criticalCount > 0 && (
                        <div className="flex items-center gap-2 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-2 text-sm">
                            <AlertTriangle className="h-4 w-4 text-rose-400 animate-pulse" />
                            <span className="text-rose-400 font-semibold">{criticalCount} produits en rupture critique</span>
                        </div>
                    )}
                </div>
            </motion.div>

            {/* KPIs */}
            <motion.div variants={container} initial="hidden" animate="visible"
                className="grid grid-cols-2 xl:grid-cols-4 gap-4">
                {kpis.map((k, i) => {
                    const Icon = k.icon;

                    return (
                        <motion.div key={i} variants={item} className={`rounded-2xl border bg-card p-5 flex flex-col gap-3 hover:shadow-lg transition-all duration-300 ${k.highlight ? 'border-rose-500/30 hover:border-rose-500/50' : 'border-border hover:border-primary/20'}`}>
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{k.title}</span>
                                <div className={`p-2 rounded-xl ${k.color}`}><Icon className="h-4 w-4" /></div>
                            </div>
                            <div>
                                <div className={`text-3xl font-bold tracking-tight ${k.highlight ? 'text-rose-400' : 'text-foreground'}`}>{k.value}</div>
                                <div className="text-xs text-muted-foreground mt-0.5">{k.sub}</div>
                            </div>
                        </motion.div>
                    );
                })}
            </motion.div>

            {/* Stock levels */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.3 }}
                className="rounded-2xl border border-border bg-card p-5">
                <div className="flex items-center justify-between mb-5">
                    <div>
                        <h2 className="text-sm font-semibold text-foreground">Niveaux de stock — Principales références</h2>
                        <p className="text-xs text-muted-foreground mt-0.5">La barre rouge indique le seuil minimal</p>
                    </div>
                    <div className="flex items-center gap-4 text-[10px] text-muted-foreground">
                        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-rose-500" />Critique</span>
                        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-amber-500" />Faible</span>
                        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-emerald-500" />Normal</span>
                    </div>
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    {stocks.map((s: { product: string; current: number; min: number; optimal: number; unit: string }, i: number) => <StockBar key={i} {...s} />)}
                </div>
            </motion.div>

            {/* Mouvements + Alertes */}
            <div className="grid grid-cols-1 xl:grid-cols-2 gap-4">

                {/* Mouvements */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.4 }}
                    className="rounded-2xl border border-border bg-card overflow-hidden">
                    <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                        <h2 className="text-sm font-semibold text-foreground">Derniers mouvements</h2>
                        <Link href={`/${slug}/stocks/mouvements`} className="text-xs text-primary hover:underline font-medium">
                            Enregistrer +
                        </Link>
                    </div>
                    {movements.length === 0 ? (
                        <p className="px-5 py-6 text-sm text-muted-foreground">Aucun mouvement aujourd'hui</p>
                    ) : (
                        <div className="divide-y divide-border">
                            {movements.map((m) => {
                                const cfg = movementConfig[m.movement_type] ?? { label: m.movement_type, color: 'bg-muted/10 text-muted-foreground', iconColor: 'text-muted-foreground' };
                                const time = new Date(m.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                                const isIn = m.movement_type === 'in';

                                return (
                                    <div key={m.id} className="px-5 py-3.5 flex items-center gap-3 hover:bg-secondary/20 transition-colors">
                                        <div className={`p-2 rounded-xl shrink-0 ${cfg.color}`}>
                                            {isIn ? <ArrowDown className="h-3.5 w-3.5" /> : <ArrowUp className="h-3.5 w-3.5" />}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium text-foreground truncate">{m.product_name}</p>
                                            <p className="text-xs text-muted-foreground">{cfg.label}</p>
                                        </div>
                                        <div className="text-right shrink-0">
                                            <p className={`text-sm font-bold ${cfg.iconColor}`}>{isIn ? '+' : '-'}{m.quantity}</p>
                                            <p className="text-[10px] text-muted-foreground">{time}</p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </motion.div>

                {/* Alertes stock */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.5 }}
                    className="rounded-2xl border border-rose-500/20 bg-rose-500/[0.03] overflow-hidden">
                    <div className="px-5 py-4 border-b border-rose-500/20 flex items-center gap-2">
                        <div className="h-2 w-2 rounded-full bg-rose-400 animate-pulse" />
                        <h2 className="text-sm font-semibold text-foreground">Alertes de stock</h2>
                    </div>
                    {alerts.length === 0 ? (
                        <p className="px-5 py-6 text-sm text-muted-foreground">Aucune alerte</p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-rose-500/20 bg-rose-500/[0.03]">
                                        {["Produit", "Stock actuel", "Seuil min"].map(h => (
                                            <th key={h} className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-rose-500/10">
                                    {alerts.map((a) => (
                                        <tr key={a.id} className="hover:bg-rose-500/5 transition-colors">
                                            <td className="px-5 py-3.5 font-medium text-foreground">{a.name}</td>
                                            <td className="px-5 py-3.5 font-bold text-rose-400">{(a.total_quantity ?? 0).toLocaleString('fr-FR')}</td>
                                            <td className="px-5 py-3.5 text-muted-foreground">{(a.min_threshold ?? 0).toLocaleString('fr-FR')}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </motion.div>
            </div>

            {/* Accès rapides */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.6 }}
                className="rounded-2xl border border-border bg-card p-5">
                <h2 className="text-sm font-semibold text-foreground mb-4">Accès rapides</h2>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <Link href={`/${slug}/stocks`}
                        className="flex items-center gap-3 rounded-xl border border-border bg-secondary/20 p-4 hover:bg-secondary/40 hover:border-primary/20 transition-all duration-200">
                        <div className="p-2 rounded-xl bg-blue-500/10 text-blue-400"><Package className="h-4 w-4" /></div>
                        <span className="text-sm font-medium text-foreground">Stock & Inventaire</span>
                    </Link>
                    <Link href={`/${slug}/stocks/mouvements`}
                        className="flex items-center gap-3 rounded-xl border border-border bg-secondary/20 p-4 hover:bg-secondary/40 hover:border-primary/20 transition-all duration-200">
                        <div className="p-2 rounded-xl bg-amber-500/10 text-amber-400"><ArrowLeftRight className="h-4 w-4" /></div>
                        <span className="text-sm font-medium text-foreground">Mouvements</span>
                    </Link>
                    <Link href={`/${slug}/stocks/rangement`}
                        className="flex items-center gap-3 rounded-xl border border-border bg-secondary/20 p-4 hover:bg-secondary/40 hover:border-primary/20 transition-all duration-200">
                        <div className="p-2 rounded-xl bg-violet-500/10 text-violet-400"><Layers className="h-4 w-4" /></div>
                        <span className="text-sm font-medium text-foreground">Rangement</span>
                    </Link>
                    <Link href={`/${slug}/stocks/picking`}
                        className="flex items-center gap-3 rounded-xl border border-border bg-secondary/20 p-4 hover:bg-secondary/40 hover:border-primary/20 transition-all duration-200">
                        <div className="p-2 rounded-xl bg-emerald-500/10 text-emerald-400"><CheckSquare className="h-4 w-4" /></div>
                        <span className="text-sm font-medium text-foreground">Picking</span>
                    </Link>
                </div>
            </motion.div>

        </div>
    );
}
