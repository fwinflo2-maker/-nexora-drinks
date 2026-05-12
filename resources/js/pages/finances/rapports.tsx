import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { BarChart3, ArrowLeft, TrendingUp, TrendingDown, Calendar } from 'lucide-react';
import * as FinanceController from '@/actions/App/Http/Controllers/FinanceController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

interface MonthlyData {
    month: string;
    revenue: number;
    expenses: number;
    net: number;
}

interface CategoryData {
    category: string;
    total: number;
    percentage: number;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    monthly: MonthlyData[];
    by_category: CategoryData[];
    year: number;
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(val);
}

export default function FinancesRapports({ team, monthly, by_category, year }: Props) {
    const maxRevenue = Math.max(...monthly.map((m) => m.revenue), 1);
    const maxExpenses = Math.max(...monthly.map((m) => m.expenses), 1);
    const maxBar = Math.max(maxRevenue, maxExpenses);

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Rapports Financiers — NEXORA" />

            {/* Header */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div>
                    <Link
                        href={FinanceController.index({ current_team: team.slug }).url}
                        className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors mb-3"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Retour aux finances
                    </Link>
                    <div className="flex items-center gap-2 mb-1">
                        <BarChart3 className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                            Module Finance
                        </span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Rapports financiers {year}</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Analyse mensuelle des revenus et depenses.
                    </p>
                </div>
            </motion.div>

            {/* Graphique mensuel (barres CSS) */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card p-5"
            >
                <div className="flex items-center justify-between mb-6">
                    <h2 className="text-base font-semibold text-foreground">Revenus vs Depenses par mois</h2>
                    <div className="flex items-center gap-4 text-xs">
                        <span className="flex items-center gap-1.5">
                            <span className="h-2.5 w-2.5 rounded-sm bg-emerald-500" />
                            Revenus
                        </span>
                        <span className="flex items-center gap-1.5">
                            <span className="h-2.5 w-2.5 rounded-sm bg-rose-500" />
                            Depenses
                        </span>
                    </div>
                </div>

                {monthly.length === 0 ? (
                    <div className="flex items-center justify-center py-16 text-muted-foreground">
                        <p className="text-sm">Aucune donnee disponible pour cette periode.</p>
                    </div>
                ) : (
                    <div className="flex items-end gap-2 h-48 overflow-x-auto pb-2">
                        {monthly.map((m) => (
                            <div key={m.month} className="flex flex-col items-center gap-1 flex-1 min-w-[40px]">
                                <div className="flex items-end gap-0.5 h-36 w-full">
                                    <div
                                        className="flex-1 bg-emerald-500/80 rounded-t-sm transition-all hover:bg-emerald-500"
                                        style={{ height: `${(m.revenue / maxBar) * 100}%` }}
                                        title={formatCurrency(m.revenue)}
                                    />
                                    <div
                                        className="flex-1 bg-rose-500/80 rounded-t-sm transition-all hover:bg-rose-500"
                                        style={{ height: `${(m.expenses / maxBar) * 100}%` }}
                                        title={formatCurrency(m.expenses)}
                                    />
                                </div>
                                <span className="text-[10px] text-muted-foreground truncate w-full text-center">
                                    {m.month}
                                </span>
                            </div>
                        ))}
                    </div>
                )}
            </motion.div>

            {/* Tableau mensuel + Repartition categories */}
            <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
                {/* Tableau mensuel */}
                <motion.div
                    variants={fadeUp}
                    initial="hidden"
                    animate="visible"
                    className="rounded-2xl border border-border bg-card overflow-hidden"
                >
                    <div className="px-5 py-4 border-b border-border">
                        <h2 className="text-base font-semibold text-foreground">Detail mensuel</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border bg-secondary/30">
                                    <th className="px-4 py-3 text-left font-semibold text-muted-foreground">Mois</th>
                                    <th className="px-4 py-3 text-right font-semibold text-muted-foreground">
                                        Revenus
                                    </th>
                                    <th className="px-4 py-3 text-right font-semibold text-muted-foreground">
                                        Depenses
                                    </th>
                                    <th className="px-4 py-3 text-right font-semibold text-muted-foreground">Net</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {monthly.map((m) => (
                                    <tr key={m.month} className="hover:bg-secondary/20 transition-colors">
                                        <td className="px-4 py-3 font-medium text-foreground">{m.month}</td>
                                        <td className="px-4 py-3 text-right text-emerald-500 font-medium">
                                            {formatCurrency(m.revenue)}
                                        </td>
                                        <td className="px-4 py-3 text-right text-rose-500 font-medium">
                                            {formatCurrency(m.expenses)}
                                        </td>
                                        <td
                                            className={`px-4 py-3 text-right font-bold ${m.net >= 0 ? 'text-foreground' : 'text-rose-500'}`}
                                        >
                                            {formatCurrency(m.net)}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </motion.div>

                {/* Repartition par categorie */}
                <motion.div
                    variants={fadeUp}
                    initial="hidden"
                    animate="visible"
                    className="rounded-2xl border border-border bg-card p-5"
                >
                    <h2 className="text-base font-semibold text-foreground mb-5">Depenses par categorie</h2>
                    {by_category.length === 0 ? (
                        <div className="flex items-center justify-center py-12 text-muted-foreground">
                            <p className="text-sm">Aucune depense categorisee.</p>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {by_category.map((cat) => (
                                <div key={cat.category}>
                                    <div className="flex items-center justify-between mb-1.5">
                                        <span className="text-sm font-medium text-foreground">{cat.category}</span>
                                        <div className="flex items-center gap-2">
                                            <span className="text-sm font-bold text-rose-500">
                                                {formatCurrency(cat.total)}
                                            </span>
                                            <span className="text-xs text-muted-foreground">
                                                {cat.percentage.toFixed(1)}%
                                            </span>
                                        </div>
                                    </div>
                                    <div className="h-2 w-full bg-secondary rounded-full overflow-hidden">
                                        <div
                                            className="h-full bg-rose-500 rounded-full transition-all"
                                            style={{ width: `${cat.percentage}%` }}
                                        />
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </motion.div>
            </div>
        </div>
    );
}

FinancesRapports.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Finances',
            href: props.currentTeam ? `/${props.currentTeam.slug}/finances` : '/',
        },
        {
            title: 'Rapports',
            href: props.currentTeam ? `/${props.currentTeam.slug}/finances/rapports` : '#',
        },
    ],
});
