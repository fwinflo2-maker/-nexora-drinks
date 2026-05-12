import { usePage, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowUpRight, ArrowDownRight, TrendingUp, TrendingDown, FileText, Clock, CheckCircle, AlertCircle, CreditCard, Plus } from 'lucide-react';

const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };
const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.07 } } };

/* ─── Donut Chart ─── */
function DonutChart({ segments }: { segments: { label: string; value: number; color: string }[] }) {
    const total = segments.reduce((s, x) => s + x.value, 0);
    const r = 60; const cx = 80; const cy = 80;
    let cumAngle = -Math.PI / 2;
    const arcs = segments.map(seg => {
        const angle = (seg.value / total) * 2 * Math.PI;
        const x1 = cx + r * Math.cos(cumAngle);
        const y1 = cy + r * Math.sin(cumAngle);
        cumAngle += angle;
        const x2 = cx + r * Math.cos(cumAngle);
        const y2 = cy + r * Math.sin(cumAngle);
        const largeArc = angle > Math.PI ? 1 : 0;

        return { ...seg, d: `M ${cx} ${cy} L ${x1} ${y1} A ${r} ${r} 0 ${largeArc} 1 ${x2} ${y2} Z`, pct: Math.round((seg.value / total) * 100) };
    });

    return (
        <svg viewBox="0 0 160 160" className="w-full max-w-[160px]">
            <circle cx={cx} cy={cy} r={r} fill="none" stroke="hsl(var(--border))" strokeWidth="1" />
            {arcs.map((a, i) => (
                <path key={i} d={a.d} fill={a.color} opacity="0.85"
                    className="hover:opacity-100 transition-opacity cursor-pointer" />
            ))}
            <circle cx={cx} cy={cy} r={r * 0.55} fill="hsl(var(--card))" />
            <text x={cx} y={cy - 6} textAnchor="middle" className="text-xs" fill="hsl(var(--foreground))" fontSize="16" fontWeight="700">{Math.round(total / 1_000_000 * 10) / 10}M</text>
            <text x={cx} y={cy + 12} textAnchor="middle" fill="hsl(var(--muted-foreground))" fontSize="9">XAF</text>
        </svg>
    );
}

/* ─── Bar Chart (monthly) ─── */
function MonthlyBars({ data }: { data: { month: string; value: number; target: number }[] }) {
    const max = Math.max(...data.flatMap(d => [d.value, d.target]));
    const h = 100;

    return (
        <div className="flex items-end gap-2 h-28">
            {data.map((d, i) => (
                <div key={i} className="flex-1 flex flex-col items-center gap-1">
                    <div className="w-full flex gap-0.5 items-end" style={{ height: h }}>
                        <div className="flex-1 rounded-t-sm bg-primary/20 transition-all"
                            ref={el => {
 if (el) {
setTimeout(() => {
 el.style.height = `${(d.target / max) * h}px`; 
}, 500 + i * 80);
} 
}}
                            style={{ height: 0, transition: 'height 0.6s cubic-bezier(0.16,1,0.3,1)' }} />
                        <div className="flex-1 rounded-t-sm bg-primary transition-all"
                            ref={el => {
 if (el) {
setTimeout(() => {
 el.style.height = `${(d.value / max) * h}px`; 
}, 500 + i * 80);
} 
}}
                            style={{ height: 0, transition: 'height 0.6s cubic-bezier(0.16,1,0.3,1)' }} />
                    </div>
                    <span className="text-[9px] text-muted-foreground">{d.month}</span>
                </div>
            ))}
        </div>
    );
}

/* ─── Static monthly data (no backend equivalent) ─── */
const monthly = [
    { month: "Fév", value: 19_800_000, target: 22_000_000 },
    { month: "Mar", value: 23_400_000, target: 24_000_000 },
    { month: "Avr", value: 28_450_000, target: 30_000_000 },
];

/* ─── Status style map ─── */
const statusStyle: Record<string, string> = {
    draft:    "bg-secondary/50 text-muted-foreground border-border",
    sent:     "bg-blue-500/10 text-blue-400 border-blue-500/20",
    paid:     "bg-emerald-500/10 text-emerald-400 border-emerald-500/20",
    partial:  "bg-violet-500/10 text-violet-400 border-violet-500/20",
    overdue:  "bg-rose-500/10 text-rose-400 border-rose-500/20",
};

const statusLabel: Record<string, string> = {
    draft:   "Brouillon",
    sent:    "Envoyée",
    paid:    "Payée",
    partial: "Partielle",
    overdue: "En retard",
};

const methodLabels: Record<string, string> = {
    especes:      'Espèces',
    mobile_money: 'Mobile Money',
    virement:     'Virement',
    cheque:       'Chèque',
};

const donutColors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'];

const demoPaymentModes = [
    { label: "Espèces",      value: 12_400_000, color: donutColors[0] },
    { label: "Mobile Money", value: 4_850_000,  color: donutColors[1] },
    { label: "Virement",     value: 9_200_000,  color: donutColors[2] },
    { label: "Chèque",       value: 2_000_000,  color: donutColors[3] },
];

export default function ComptableDashboard({ userName }: { userName: string }) {
    const { financeKpis, recentInvoices, paymentMethods, teamName, currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });

    /* ─── KPIs ─── */
    const kpis = [
        {
            title: "CA Mois",
            value: `${(financeKpis?.ca_month ?? 0).toLocaleString('fr-FR')} XAF`,
            icon: TrendingUp,
            color: "bg-emerald-500/10 text-emerald-400",
        },
        {
            title: "Factures Impayées",
            value: String(financeKpis?.factures_impayees ?? 0),
            icon: CreditCard,
            color: "bg-rose-500/10 text-rose-400",
        },
        {
            title: "Montant Impayé",
            value: `${(financeKpis?.montant_impaye_xaf ?? 0).toLocaleString('fr-FR')} XAF`,
            icon: TrendingDown,
            color: "bg-amber-500/10 text-amber-400",
        },
        {
            title: "Taux Recouvrement",
            value: `${financeKpis?.taux_recouvrement ?? 0}%`,
            icon: TrendingUp,
            color: "bg-blue-500/10 text-blue-400",
        },
    ];

    /* ─── Payment mode segments ─── */
    const rawMethods: { method: string; total: number }[] = paymentMethods ?? [];
    const paymentSegments = rawMethods.length > 0
        ? rawMethods.map((m, i) => ({
            label: methodLabels[m.method] ?? m.method,
            value: m.total,
            color: donutColors[i % donutColors.length],
        }))
        : demoPaymentModes;

    /* ─── Invoices ─── */
    const invoices: {
        id: number;
        invoice_number: string;
        client_name: string;
        total: number;
        paid_amount: number;
        status: string;
        due_date: string;
    }[] = recentInvoices ?? [];

    const overdueCount = invoices.filter(inv => inv.status === 'overdue').length;

    return (
        <div className="relative z-50 flex flex-1 flex-col gap-6 p-6 lg:p-8 w-full">

            {/* Header */}
            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}
                className="flex items-start justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-foreground">Finances & Comptabilité</h1>
                    <p className="text-sm text-muted-foreground mt-1 capitalize">{today} · {teamName ?? userName}</p>
                </div>
                {slug && (
                    <Link
                        href={`/${slug}/factures`}
                        className="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-primary-foreground text-sm font-semibold hover:bg-primary/90 transition-colors shrink-0"
                    >
                        <Plus className="h-4 w-4" />
                        Voir Factures
                    </Link>
                )}
            </motion.div>

            {/* KPIs */}
            <motion.div variants={container} initial="hidden" animate="visible"
                className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {kpis.map((k, i) => {
                    const Icon = k.icon;

                    return (
                        <motion.div key={i} variants={item} className="rounded-2xl border border-border bg-card p-5 flex flex-col gap-3 hover:shadow-lg hover:border-primary/20 transition-all duration-300">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{k.title}</span>
                                <div className={`p-2 rounded-xl ${k.color}`}><Icon className="h-4 w-4" /></div>
                            </div>
                            <div className="text-xl font-bold tracking-tight text-foreground">{k.value}</div>
                        </motion.div>
                    );
                })}
            </motion.div>

            {/* Charts row */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-4">

                {/* Monthly bars */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.3 }}
                    className="xl:col-span-2 rounded-2xl border border-border bg-card p-5">
                    <div className="flex items-center justify-between mb-2">
                        <div>
                            <h2 className="text-sm font-semibold text-foreground">CA Mensuel — Réalisé vs Objectif</h2>
                            <p className="text-xs text-muted-foreground mt-0.5">3 derniers mois</p>
                        </div>
                        <div className="flex items-center gap-3 text-[10px] text-muted-foreground">
                            <span className="flex items-center gap-1"><span className="inline-block w-2.5 h-2.5 rounded-sm bg-primary/20" />Objectif</span>
                            <span className="flex items-center gap-1"><span className="inline-block w-2.5 h-2.5 rounded-sm bg-primary" />Réalisé</span>
                        </div>
                    </div>
                    <MonthlyBars data={monthly} />
                    <div className="mt-4 grid grid-cols-3 gap-3">
                        {monthly.map((m, i) => (
                            <div key={i} className="rounded-xl bg-secondary/30 p-3 text-center">
                                <div className="text-sm font-bold text-foreground">{(m.value / 1_000_000).toFixed(1)}M</div>
                                <div className="text-[10px] text-muted-foreground">{m.month}</div>
                                <div className={`text-[10px] font-semibold mt-1 ${m.value >= m.target ? 'text-emerald-400' : 'text-rose-400'}`}>
                                    {m.value >= m.target ? '✓ Objectif atteint' : `${Math.round((m.value / m.target) * 100)}% de l'objectif`}
                                </div>
                            </div>
                        ))}
                    </div>
                </motion.div>

                {/* Donut chart */}
                <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.4 }}
                    className="rounded-2xl border border-border bg-card p-5">
                    <h2 className="text-sm font-semibold text-foreground mb-4">Recettes par mode de paiement</h2>
                    <div className="flex flex-col items-center">
                        <DonutChart segments={paymentSegments} />
                        <div className="mt-4 w-full space-y-2">
                            {paymentSegments.map((p, i) => (
                                <div key={i} className="flex items-center justify-between text-xs">
                                    <div className="flex items-center gap-2">
                                        <span className="h-2.5 w-2.5 rounded-full shrink-0" style={{ background: p.color }} />
                                        <span className="text-muted-foreground">{p.label}</span>
                                    </div>
                                    <span className="font-semibold text-foreground">{(p.value / 1_000_000).toFixed(1)}M XAF</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </motion.div>
            </div>

            {/* Invoices table */}
            <motion.div variants={item} initial="hidden" animate="visible" transition={{ delay: 0.5 }}
                className="rounded-2xl border border-border bg-card overflow-hidden">
                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 className="text-sm font-semibold text-foreground">Factures & paiements récents</h2>
                    <div className="flex items-center gap-2">
                        {overdueCount > 0 && (
                            <span className="text-xs text-rose-400 font-semibold">{overdueCount} en retard</span>
                        )}
                        {slug && (
                            <Link href={`/${slug}/factures`} className="text-xs text-primary hover:underline font-medium ml-2">
                                Tout voir →
                            </Link>
                        )}
                    </div>
                </div>
                <div className="overflow-x-auto">
                    {invoices.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-12 gap-3 text-muted-foreground">
                            <FileText className="h-8 w-8 opacity-40" />
                            <span className="text-sm">Aucune facture récente</span>
                        </div>
                    ) : (
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border bg-secondary/30">
                                    {["N° Facture", "Client", "Total", "Payé", "Échéance", "Statut", "Actions"].map(h => (
                                        <th key={h} className="text-left px-5 py-3 text-xs font-semibold text-muted-foreground tracking-wide last:text-right">{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {invoices.map((inv) => {
                                    const style = statusStyle[inv.status] ?? "bg-secondary/50 text-muted-foreground border-border";
                                    const label = statusLabel[inv.status] ?? inv.status;

                                    return (
                                        <tr key={inv.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5 font-mono text-xs text-muted-foreground">{inv.invoice_number}</td>
                                            <td className="px-5 py-3.5 font-medium text-foreground">{inv.client_name}</td>
                                            <td className="px-5 py-3.5 font-semibold text-foreground">{(inv.total ?? 0).toLocaleString('fr-FR')} XAF</td>
                                            <td className="px-5 py-3.5 text-muted-foreground">{(inv.paid_amount ?? 0).toLocaleString('fr-FR')} XAF</td>
                                            <td className="px-5 py-3.5 text-muted-foreground">{inv.due_date}</td>
                                            <td className="px-5 py-3.5">
                                                <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border ${style}`}>
                                                    {inv.status === 'paid' ? <CheckCircle className="h-3 w-3" /> : inv.status === 'overdue' ? <AlertCircle className="h-3 w-3" /> : <Clock className="h-3 w-3" />}
                                                    {label}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 text-right">
                                                {slug ? (
                                                    <Link href={`/${slug}/factures/${inv.id}`} className="text-xs text-primary hover:underline">
                                                        Détails
                                                    </Link>
                                                ) : (
                                                    <button className="text-xs text-primary hover:underline">Détails</button>
                                                )}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    )}
                </div>
            </motion.div>

        </div>
    );
}
