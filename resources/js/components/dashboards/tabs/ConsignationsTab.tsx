import { Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Box, AlertTriangle, TrendingDown, TrendingUp, Package, AlertCircle,
    ArrowRight, CheckCircle, Clock, MoreHorizontal,
} from 'lucide-react';

const container = { hidden: { opacity: 0 }, visible: { opacity: 1, transition: { staggerChildren: 0.06 } } };
const item = { hidden: { opacity: 0, y: 16 }, visible: { opacity: 1, y: 0, transition: { duration: 0.4 } } };

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
                {positive ? <TrendingUp className="h-3.5 w-3.5" /> : <TrendingDown className="h-3.5 w-3.5" />}
                {trend}
            </div>
            <div className="absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-primary/[0.03] group-hover:bg-primary/[0.06] transition-colors" />
        </motion.div>
    );
}

function SectionTitle({ children }: { children: React.ReactNode }) {
    return <h2 className="text-sm font-semibold text-foreground mb-3">{children}</h2>;
}

function StatusBadge({ status }: { status: string }) {
    const configs = {
        'À jour': { bg: 'bg-emerald-500/10', text: 'text-emerald-400', icon: CheckCircle },
        'Retard': { bg: 'bg-rose-500/10', text: 'text-rose-400', icon: AlertTriangle },
        'En attente': { bg: 'bg-amber-500/10', text: 'text-amber-400', icon: Clock },
        'Critique': { bg: 'bg-red-500/10', text: 'text-red-400', icon: AlertCircle },
    };
    const config = configs[status as keyof typeof configs] || configs['À jour'];
    const Icon = config.icon;

    return (
        <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold ${config.bg} ${config.text}`}>
            <Icon className="h-3 w-3" />
            {status}
        </span>
    );
}

export default function ConsignationsTab() {
    const { consignationKpis, clientConsignations: serverConsignations, packagingTypes: serverPackagingTypes, currentTeam } = usePage().props as any;
    const slug = currentTeam?.slug ?? '';

    const kpis = [
        {
            title: 'Casiers Chez Clients',
            value: (consignationKpis?.casiers_en_circulation ?? 0).toString(),
            sub: 'Casiers en circulation',
            icon: Box,
            trend: '+45',
            positive: true,
            color: 'bg-blue-500/10 text-blue-400',
        },
        {
            title: 'Valeur Consignée',
            value: (consignationKpis?.valeur_totale_xaf ?? 0).toLocaleString('fr-FR') + ' XAF',
            sub: 'Montant total (XAF)',
            icon: TrendingUp,
            trend: '+2.1M',
            positive: true,
            color: 'bg-emerald-500/10 text-emerald-400',
        },
        {
            title: 'Retards > 30j',
            value: (consignationKpis?.retards_count ?? 0).toString(),
            sub: 'Clients en retard',
            icon: AlertTriangle,
            trend: '-5',
            positive: true,
            color: 'bg-rose-500/10 text-rose-400',
        },
        {
            title: 'Taux de Retour',
            value: (consignationKpis?.taux_retour_pct ?? 0) + '%',
            sub: 'Taux de retour',
            icon: Package,
            trend: '+3%',
            positive: true,
            color: 'bg-violet-500/10 text-violet-400',
        },
    ];

    const displayConsignations: Array<{
        client_name: string;
        zone: string;
        casiers_count: number;
        valeur_xaf: number;
        days_held: number;
        status: string;
    }> = serverConsignations ?? [];

    const displayPackagingTypes: Array<{
        name: string;
        unit_price: number;
        in_circulation: number;
    }> = serverPackagingTypes ?? [];

    return (
        <motion.div variants={container} initial="hidden" animate="visible" className="space-y-6">

            {/* ═══ KPI Cards ═══ */}
            <motion.div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {kpis.map((kpi, i) => (
                    <KpiCard key={i} {...kpi} />
                ))}
            </motion.div>

            {/* ═══ Client Consignations Table ═══ */}
            <motion.div variants={item} className="rounded-lg border border-border bg-card overflow-hidden">
                <div className="p-4 border-b border-border">
                    <SectionTitle>Soldes Consignation par Client</SectionTitle>
                </div>
                {displayConsignations.length === 0 ? (
                    <div className="p-8 text-center text-sm text-muted-foreground">
                        Aucune consignation en cours
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border bg-secondary/50">
                                    <th className="text-left px-4 py-3 font-semibold text-xs text-muted-foreground uppercase">Client</th>
                                    <th className="text-right px-4 py-3 font-semibold text-xs text-muted-foreground uppercase">Casiers</th>
                                    <th className="text-right px-4 py-3 font-semibold text-xs text-muted-foreground uppercase">Valeur</th>
                                    <th className="text-center px-4 py-3 font-semibold text-xs text-muted-foreground uppercase">Jours</th>
                                    <th className="text-center px-4 py-3 font-semibold text-xs text-muted-foreground uppercase">Statut</th>
                                    <th className="text-center px-4 py-3 font-semibold text-xs text-muted-foreground uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {displayConsignations.map((row, i) => (
                                    <tr key={i} className="border-b border-border hover:bg-secondary/30 transition-colors">
                                        <td className="px-4 py-3">
                                            <div className="font-medium text-foreground">{row.client_name}</div>
                                            <div className="text-xs text-muted-foreground">{row.zone}</div>
                                        </td>
                                        <td className="text-right px-4 py-3 font-semibold text-foreground">{row.casiers_count}</td>
                                        <td className="text-right px-4 py-3 font-semibold text-foreground">
                                            {(row.valeur_xaf ?? 0).toLocaleString('fr-FR') + ' XAF'}
                                        </td>
                                        <td className="text-center px-4 py-3">
                                            <span className="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary/10 text-primary text-xs font-semibold">
                                                {row.days_held}
                                            </span>
                                        </td>
                                        <td className="text-center px-4 py-3">
                                            <StatusBadge status={row.status} />
                                        </td>
                                        <td className="text-center px-4 py-3">
                                            <button className="p-1 rounded-lg hover:bg-secondary transition-colors text-muted-foreground hover:text-foreground">
                                                <MoreHorizontal className="h-4 w-4" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </motion.div>

            {/* ═══ Packaging Types ═══ */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <motion.div variants={item} className="rounded-lg border border-border bg-card p-4">
                    <SectionTitle>Types d'Emballages</SectionTitle>
                    {displayPackagingTypes.length === 0 ? (
                        <div className="py-6 text-center text-sm text-muted-foreground">
                            Aucun type d'emballage configuré
                        </div>
                    ) : (
                        <div className="space-y-3">
                            {displayPackagingTypes.map((pkg, i) => (
                                <div key={i} className="flex items-center justify-between p-3 rounded-lg bg-secondary/50 hover:bg-secondary transition-colors">
                                    <div className="flex-1">
                                        <div className="font-medium text-sm text-foreground">{pkg.name}</div>
                                        <div className="text-xs text-muted-foreground mt-0.5">{pkg.in_circulation} casiers en circulation</div>
                                    </div>
                                    <div className="text-right">
                                        <div className="font-semibold text-foreground text-sm">
                                            {(pkg.unit_price ?? 0).toLocaleString('fr-FR') + ' XAF'}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </motion.div>

                {/* Quick Actions */}
                <motion.div variants={item} className="rounded-lg border border-border bg-card p-4">
                    <SectionTitle>Actions Rapides</SectionTitle>
                    <div className="space-y-2">
                        <Link
                            href={`/${slug}/consignations`}
                            className="w-full flex items-center justify-between p-3 rounded-lg bg-primary/10 hover:bg-primary/20 transition-colors text-primary font-medium text-sm"
                        >
                            <span>Enregistrer Retour Casiers</span>
                            <ArrowRight className="h-4 w-4" />
                        </Link>
                        <Link
                            href={`/${slug}/consignations`}
                            className="w-full flex items-center justify-between p-3 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 transition-colors text-amber-400 font-medium text-sm"
                        >
                            <span>Relancer Clients en Retard</span>
                            <AlertTriangle className="h-4 w-4" />
                        </Link>
                        <Link
                            href={`/${slug}/factures`}
                            className="w-full flex items-center justify-between p-3 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 transition-colors text-emerald-400 font-medium text-sm"
                        >
                            <span>Générer Relevé Consignation</span>
                            <CheckCircle className="h-4 w-4" />
                        </Link>
                        <Link
                            href={`/${slug}/consignations`}
                            className="w-full flex items-center justify-between p-3 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 transition-colors text-blue-400 font-medium text-sm"
                        >
                            <span>Voir Page Complète</span>
                            <ArrowRight className="h-4 w-4" />
                        </Link>
                    </div>
                </motion.div>
            </div>
        </motion.div>
    );
}
