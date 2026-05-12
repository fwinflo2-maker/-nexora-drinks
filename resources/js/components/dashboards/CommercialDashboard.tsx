import { Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ShoppingCart, Users, TrendingUp, Star, ArrowUpRight, Clock, CheckCircle, Package, FileText, Archive } from 'lucide-react';

const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };
const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.07 } } };

/* ─── Radial Gauge ─── */
function ObjectiveGauge({ achieved, target }: { achieved: number; target: number }) {
    const pct = Math.min(achieved / target, 1);
    const r = 70; const cx = 90; const cy = 90;
    const angle = pct * Math.PI * 1.5;
    const startAngle = Math.PI * 0.75;
    const endAngle = startAngle + angle;
    const x1 = cx + r * Math.cos(startAngle);
    const y1 = cy + r * Math.sin(startAngle);
    const x2 = cx + r * Math.cos(endAngle);
    const y2 = cy + r * Math.sin(endAngle);
    const largeArc = angle > Math.PI ? 1 : 0;
    const bgEnd = cx + r * Math.cos(startAngle + Math.PI * 1.5);
    const bgEndY = cy + r * Math.sin(startAngle + Math.PI * 1.5);

    return (
        <div className="flex flex-col items-center">
            <svg width="180" height="140" viewBox="0 0 180 140">
                {/* Background arc */}
                <path d={`M ${x1} ${y1} A ${r} ${r} 0 1 1 ${bgEnd} ${bgEndY}`}
                    fill="none" stroke="hsl(var(--border))" strokeWidth="10" strokeLinecap="round" />
                {/* Progress arc */}
                <path d={`M ${x1} ${y1} A ${r} ${r} 0 ${largeArc} 1 ${x2} ${y2}`}
                    fill="none" stroke="hsl(var(--primary))" strokeWidth="10" strokeLinecap="round"
                    style={{ strokeDasharray: 1000, strokeDashoffset: 1000 - pct * 330, transition: 'stroke-dashoffset 1.2s cubic-bezier(0.16,1,0.3,1) 0.3s' }}
                />
                {/* Label */}
                <text x="90" y="88" textAnchor="middle" fill="hsl(var(--foreground))" fontSize="28" fontWeight="800">{Math.round(pct * 100)}%</text>
                <text x="90" y="108" textAnchor="middle" fill="hsl(var(--muted-foreground))" fontSize="11">de l'objectif</text>
            </svg>
            <div className="text-center -mt-2">
                <div className="text-sm font-bold text-foreground">{(achieved / 1_000_000).toFixed(1)}M XAF</div>
                <div className="text-xs text-muted-foreground">sur {(target / 1_000_000).toFixed(0)}M XAF</div>
            </div>
        </div>
    );
}

/* ─── Status config ─── */
const statusConfig: Record<string, { label: string; color: string }> = {
    pending:    { label: 'En attente',   color: 'bg-amber-500/10 text-amber-400' },
    confirmed:  { label: 'Confirmée',    color: 'bg-blue-500/10 text-blue-400' },
    preparing:  { label: 'Préparation',  color: 'bg-violet-500/10 text-violet-400' },
    ready:      { label: 'Prête',        color: 'bg-emerald-500/10 text-emerald-400' },
    delivering: { label: 'En livraison', color: 'bg-cyan-500/10 text-cyan-400' },
    delivered:  { label: 'Livrée',       color: 'bg-emerald-500/10 text-emerald-400' },
    cancelled:  { label: 'Annulée',      color: 'bg-rose-500/10 text-rose-400' },
};

const badgeColors: Record<string, string> = {
    gold: "text-amber-400", silver: "text-slate-400", bronze: "text-orange-700"
};

export default function CommercialDashboard({ userName }: { userName: string }) {
    const { commercialKpis, ordersToday: serverOrders, topClients: serverTopClients, clientsToRelance: serverRelance, teamName, currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });

    const kpis = [
        {
            title: "Commandes du Jour",
            value: (commercialKpis?.orders_today ?? 0).toString(),
            sub: "aujourd'hui",
            icon: ShoppingCart,
            color: "bg-blue-500/10 text-blue-400",
        },
        {
            title: "CA du Mois",
            value: (commercialKpis?.ca_month ?? 0).toLocaleString('fr-FR'),
            sub: "XAF",
            icon: TrendingUp,
            color: "bg-emerald-500/10 text-emerald-400",
        },
        {
            title: "Nouveaux Clients",
            value: (commercialKpis?.new_clients_month ?? 0).toString(),
            sub: "ce mois",
            icon: Users,
            color: "bg-violet-500/10 text-violet-400",
        },
        {
            title: "Panier Moyen",
            value: (commercialKpis?.pan_moyen ?? 0).toLocaleString('fr-FR'),
            sub: "XAF / commande",
            icon: Package,
            color: "bg-amber-500/10 text-amber-400",
        },
    ];

    const orders: Array<{ id: number; order_number: string; client_name: string; status: string; total: number; delivery_date: string }> = serverOrders ?? [];
    const topClients: Array<{ id: number; name: string; zone: string; orders_count: number }> = serverTopClients ?? [];
    const relance: Array<{ id: number; name: string; zone: string; last_order_at: string; days_since: number }> = serverRelance ?? [];

    return (
        <div className="relative z-50 flex flex-1 flex-col gap-6 p-6 lg:p-8 w-full">

            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>
                <h1 className="text-2xl font-bold tracking-tight text-foreground">Activité Commerciale</h1>
                <p className="text-sm text-muted-foreground mt-1 capitalize">{today} · {teamName ?? userName}</p>
            </motion.div>

            {/* KPIs */}
            <motion.div variants={container} initial="hidden" animate="visible"
                className="grid grid-cols-2 xl:grid-cols-4 gap-4">
                {kpis.map((k, i) => {
                    const Icon = k.icon;

                    return (
                        <motion.div key={i} variants={item} className="rounded-2xl border border-border bg-card p-5 flex flex-col gap-3 hover:shadow-lg hover:border-primary/20 transition-all duration-300">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{k.title}</span>
                                <div className={`p-2 rounded-xl ${k.color}`}><Icon className="h-4 w-4" /></div>
                            </div>
                            <div>
                                <div className="text-2xl font-bold tracking-tight text-foreground">{k.value}</div>
                                <div className="text-xs text-muted-foreground mt-0.5">{k.sub}</div>
                            </div>
                            <div className="flex items-center gap-1 text-xs font-semibold text-emerald-400">
                                <ArrowUpRight className="h-3.5 w-3.5" />
                                <span className="font-normal text-muted-foreground ml-1">ce mois</span>
                            </div>
                        </motion.div>
                    );
                })}
            </motion.div>

            {/* Gauge + Top clients */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.3 }}
                    className="rounded-2xl border border-border bg-card p-5 flex flex-col items-center">
                    <h2 className="text-sm font-semibold text-foreground mb-2 self-start">Objectif mensuel</h2>
                    <ObjectiveGauge achieved={commercialKpis?.ca_month ?? 0} target={40_000_000} />
                    <div className="w-full mt-4 grid grid-cols-3 gap-2 text-center">
                        {[
                            { label: "Semaine", v: "7.2M", ok: true },
                            { label: "Mois",    v: `${((commercialKpis?.ca_month ?? 0) / 1_000_000).toFixed(1)}M`, ok: true },
                            { label: "Reste",   v: `${((40_000_000 - (commercialKpis?.ca_month ?? 0)) / 1_000_000).toFixed(1)}M`, ok: false },
                        ].map((s, i) => (
                            <div key={i} className="rounded-xl bg-secondary/30 px-2 py-2">
                                <div className={`text-sm font-bold ${s.ok ? 'text-foreground' : 'text-muted-foreground'}`}>{s.v}</div>
                                <div className="text-[10px] text-muted-foreground">{s.label}</div>
                            </div>
                        ))}
                    </div>
                </motion.div>

                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.4 }}
                    className="xl:col-span-2 rounded-2xl border border-border bg-card p-5">
                    <h2 className="text-sm font-semibold text-foreground mb-4">Top clients par commandes</h2>
                    {topClients.length === 0 ? (
                        <p className="text-sm text-muted-foreground">Aucun client</p>
                    ) : (
                        <div className="space-y-3">
                            {topClients.map((c, i) => (
                                <div key={c.id} className="flex items-center gap-3">
                                    <span className="text-sm font-bold text-muted-foreground w-4">{i + 1}</span>
                                    {i < 3 ? (
                                        <Star className={`h-3.5 w-3.5 shrink-0 ${i === 0 ? badgeColors.gold : i === 1 ? badgeColors.silver : badgeColors.bronze}`} fill="currentColor" />
                                    ) : (
                                        <span className="h-3.5 w-3.5 shrink-0" />
                                    )}
                                    <span className="text-sm text-foreground font-medium flex-1 truncate">{c.name}</span>
                                    <span className="text-xs text-muted-foreground">{c.zone}</span>
                                    <span className="text-xs font-semibold text-foreground w-20 text-right">{c.orders_count} cmd</span>
                                </div>
                            ))}
                        </div>
                    )}
                </motion.div>
            </div>

            {/* Orders table */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.5 }}
                className="rounded-2xl border border-border bg-card overflow-hidden">
                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 className="text-sm font-semibold text-foreground">Commandes du jour</h2>
                    <Link href={`/${slug}/dashboard/commercial`} className="text-xs text-primary hover:underline font-medium">
                        Voir tout →
                    </Link>
                </div>
                <div className="overflow-x-auto">
                    {orders.length === 0 ? (
                        <p className="px-5 py-6 text-sm text-muted-foreground">Aucune commande aujourd'hui</p>
                    ) : (
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border bg-secondary/30">
                                    {["N°", "Client", "Statut", "Montant", "Livraison"].map(h => (
                                        <th key={h} className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide">{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {orders.map((o) => {
                                    const st = statusConfig[o.status] ?? { label: o.status, color: 'bg-muted/30 text-muted-foreground' };

                                    return (
                                        <tr key={o.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5 font-mono text-xs text-muted-foreground">{o.order_number}</td>
                                            <td className="px-5 py-3.5 font-medium text-foreground">{o.client_name}</td>
                                            <td className="px-5 py-3.5">
                                                <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold ${st.color}`}>
                                                    {o.status === 'delivered' ? <CheckCircle className="h-3 w-3" /> : <Clock className="h-3 w-3" />}
                                                    {st.label}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 font-semibold text-foreground">{(o.total ?? 0).toLocaleString('fr-FR')} XAF</td>
                                            <td className="px-5 py-3.5 font-mono text-xs text-muted-foreground">{o.delivery_date}</td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    )}
                </div>
            </motion.div>

            {/* À relancer */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.6 }}
                className="rounded-2xl border border-rose-500/20 bg-rose-500/[0.03] p-5">
                <div className="flex items-center gap-2 mb-4">
                    <div className="h-2 w-2 rounded-full bg-rose-400 animate-pulse" />
                    <h2 className="text-sm font-semibold text-foreground">Clients à relancer</h2>
                </div>
                {relance.length === 0 ? (
                    <p className="text-sm text-muted-foreground">Aucun client à relancer</p>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {relance.map((r) => (
                            <div key={r.id} className="rounded-xl border border-border bg-card p-4 flex flex-col gap-2">
                                <div className="flex items-start justify-between gap-2">
                                    <p className="text-sm font-semibold text-foreground">{r.name}</p>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400">{r.zone}</span>
                                </div>
                                <p className="text-xs text-muted-foreground">{r.days_since} jours sans commande</p>
                                <Link href={`/${slug}/dashboard/commercial`} className="mt-1 text-xs text-primary hover:underline font-medium self-start">
                                    Contacter →
                                </Link>
                            </div>
                        ))}
                    </div>
                )}
            </motion.div>

            {/* Accès rapides */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.7 }}
                className="rounded-2xl border border-border bg-card p-5">
                <h2 className="text-sm font-semibold text-foreground mb-4">Accès rapides</h2>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <Link href={`/${slug}/dashboard`}
                        className="flex items-center gap-3 rounded-xl border border-border bg-secondary/20 p-4 hover:bg-secondary/40 hover:border-primary/20 transition-all duration-200">
                        <div className="p-2 rounded-xl bg-blue-500/10 text-blue-400"><Users className="h-4 w-4" /></div>
                        <span className="text-sm font-medium text-foreground">Vue générale</span>
                    </Link>
                    <Link href={`/${slug}/dashboard/commercial`}
                        className="flex items-center gap-3 rounded-xl border border-border bg-secondary/20 p-4 hover:bg-secondary/40 hover:border-primary/20 transition-all duration-200">
                        <div className="p-2 rounded-xl bg-emerald-500/10 text-emerald-400"><FileText className="h-4 w-4" /></div>
                        <span className="text-sm font-medium text-foreground">Commercial</span>
                    </Link>
                    <Link href={`/${slug}/dashboard/commercial`}
                        className="flex items-center gap-3 rounded-xl border border-border bg-secondary/20 p-4 hover:bg-secondary/40 hover:border-primary/20 transition-all duration-200">
                        <div className="p-2 rounded-xl bg-violet-500/10 text-violet-400"><Archive className="h-4 w-4" /></div>
                        <span className="text-sm font-medium text-foreground">Commandes</span>
                    </Link>
                </div>
            </motion.div>
        </div>
    );
}
