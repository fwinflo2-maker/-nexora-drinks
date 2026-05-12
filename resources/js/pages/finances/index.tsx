import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Wallet, TrendingUp, TrendingDown, Plus, X, Trash2,
    BarChart3, Receipt
} from 'lucide-react';
import * as FinanceController from '@/actions/App/Http/Controllers/FinanceController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

interface Depense {
    id: number;
    label: string;
    amount: number;
    category: string;
    date: string;
    note?: string;
}

interface KPIs {
    total_revenue: number;
    total_expenses: number;
    net_balance: number;
    revenue_this_month: number;
    expenses_this_month: number;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    kpis: KPIs;
    depenses: Depense[];
    categories: string[];
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(val);
}

function formatDate(dateStr: string): string {
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(dateStr));
}

export default function FinancesIndex({ team, kpis, depenses, categories }: Props) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [deletingId, setDeletingId] = useState<number | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        label: '',
        amount: '',
        category: '',
        date: '',
        note: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(FinanceController.storeDepense({ current_team: team.slug }).url, {
            onSuccess: () => {
                setIsModalOpen(false);
                reset();
            },
        });
    };

    const handleDelete = (id: number) => {
        setDeletingId(id);
        router.delete(
            FinanceController.destroyDepense({ current_team: team.slug, expense: id }).url,
            { onFinish: () => setDeletingId(null) },
        );
    };

    const isPositive = kpis.net_balance >= 0;

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Finances — NEXORA" />

            {/* Header */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Wallet className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                            Module Finance
                        </span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Gestion Financiere</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Revenus, depenses et solde de tresorerie.
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <Link
                        href={FinanceController.rapports({ current_team: team.slug }).url}
                        className="inline-flex items-center gap-2 rounded-lg border border-border bg-background px-4 py-2.5 text-sm font-medium hover:bg-secondary transition-colors"
                    >
                        <BarChart3 className="h-4 w-4" />
                        Rapports
                    </Link>
                    <button
                        onClick={() => setIsModalOpen(true)}
                        className="inline-flex items-center gap-2 rounded-lg bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold hover:brightness-110 transition-all shadow-lg shadow-primary/20"
                    >
                        <Plus className="h-4 w-4" />
                        Nouvelle depense
                    </button>
                </div>
            </motion.div>

            {/* KPI Grid */}
            <motion.div
                variants={container}
                initial="hidden"
                animate="visible"
                className="grid grid-cols-1 md:grid-cols-3 gap-4"
            >
                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Revenus totaux</span>
                        <div className="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">
                            <TrendingUp className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-2xl font-bold tracking-tight text-emerald-500 relative z-10 mt-3 block">
                        {formatCurrency(kpis.total_revenue)}
                    </span>
                    <span className="text-xs text-muted-foreground relative z-10 mt-1 block">
                        Ce mois : {formatCurrency(kpis.revenue_this_month)}
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Depenses totales</span>
                        <div className="p-2 rounded-xl bg-rose-500/10 text-rose-400">
                            <TrendingDown className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-2xl font-bold tracking-tight text-rose-500 relative z-10 mt-3 block">
                        {formatCurrency(kpis.total_expenses)}
                    </span>
                    <span className="text-xs text-muted-foreground relative z-10 mt-1 block">
                        Ce mois : {formatCurrency(kpis.expenses_this_month)}
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div
                        className={`absolute inset-0 bg-gradient-to-br ${isPositive ? 'from-blue-500/5' : 'from-rose-500/5'} to-transparent opacity-0 group-hover:opacity-100 transition-opacity`}
                    />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Solde net</span>
                        <div
                            className={`p-2 rounded-xl ${isPositive ? 'bg-blue-500/10 text-blue-400' : 'bg-rose-500/10 text-rose-400'}`}
                        >
                            <Wallet className="h-4 w-4" />
                        </div>
                    </div>
                    <span
                        className={`text-2xl font-bold tracking-tight relative z-10 mt-3 block ${isPositive ? 'text-blue-500' : 'text-rose-500'}`}
                    >
                        {formatCurrency(kpis.net_balance)}
                    </span>
                    <span className="text-xs text-muted-foreground relative z-10 mt-1 block">
                        {isPositive ? 'Benefice' : 'Deficit'}
                    </span>
                </motion.div>
            </motion.div>

            {/* Liste depenses */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card overflow-hidden"
            >
                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                    <h2 className="text-base font-semibold text-foreground">Depenses recentes</h2>
                    <span className="text-sm text-muted-foreground">{depenses.length} entrees</span>
                </div>

                {depenses.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-16 text-muted-foreground">
                        <Receipt className="h-12 w-12 mb-4 opacity-20" />
                        <p className="text-sm">Aucune depense enregistree.</p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border bg-secondary/30">
                                    <th className="px-5 py-3 text-left font-semibold text-muted-foreground">
                                        Libelle
                                    </th>
                                    <th className="px-5 py-3 text-left font-semibold text-muted-foreground">
                                        Categorie
                                    </th>
                                    <th className="px-5 py-3 text-right font-semibold text-muted-foreground">
                                        Montant
                                    </th>
                                    <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Date</th>
                                    <th className="px-5 py-3 text-center font-semibold text-muted-foreground">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {depenses.map((depense) => (
                                    <tr key={depense.id} className="hover:bg-secondary/20 transition-colors">
                                        <td className="px-5 py-3.5 font-medium text-foreground">
                                            {depense.label}
                                            {depense.note && (
                                                <p className="text-xs text-muted-foreground mt-0.5">{depense.note}</p>
                                            )}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-secondary text-foreground">
                                                {depense.category}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3.5 text-right font-bold text-rose-500">
                                            -{formatCurrency(depense.amount)}
                                        </td>
                                        <td className="px-5 py-3.5 text-muted-foreground">
                                            {formatDate(depense.date)}
                                        </td>
                                        <td className="px-5 py-3.5 text-center">
                                            <button
                                                onClick={() => handleDelete(depense.id)}
                                                disabled={deletingId === depense.id}
                                                className="p-1.5 rounded-lg text-muted-foreground hover:bg-rose-500/10 hover:text-rose-500 transition-colors disabled:opacity-50"
                                                title="Supprimer"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </motion.div>

            {/* Modal nouvelle depense */}
            <AnimatePresence>
                {isModalOpen && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm"
                            onClick={() => setIsModalOpen(false)}
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95, y: 20 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: 20 }}
                            className="fixed z-50 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-6 rounded-2xl border border-border bg-card shadow-2xl"
                        >
                            <div className="flex items-center justify-between mb-5">
                                <h2 className="text-lg font-bold text-foreground">Nouvelle depense</h2>
                                <button
                                    onClick={() => setIsModalOpen(false)}
                                    className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors"
                                >
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Libelle *</label>
                                    <input
                                        type="text"
                                        required
                                        value={data.label}
                                        onChange={(e) => setData('label', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ex: Carburant livraison"
                                    />
                                    {errors.label && <span className="text-xs text-rose-500">{errors.label}</span>}
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div className="grid gap-1.5">
                                        <label className="text-sm font-medium">Montant (XAF) *</label>
                                        <input
                                            type="number"
                                            min="0"
                                            required
                                            value={data.amount}
                                            onChange={(e) => setData('amount', e.target.value)}
                                            className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                            placeholder="Ex: 25000"
                                        />
                                        {errors.amount && (
                                            <span className="text-xs text-rose-500">{errors.amount}</span>
                                        )}
                                    </div>
                                    <div className="grid gap-1.5">
                                        <label className="text-sm font-medium">Date *</label>
                                        <input
                                            type="date"
                                            required
                                            value={data.date}
                                            onChange={(e) => setData('date', e.target.value)}
                                            className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        />
                                        {errors.date && (
                                            <span className="text-xs text-rose-500">{errors.date}</span>
                                        )}
                                    </div>
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Categorie *</label>
                                    {categories.length > 0 ? (
                                        <select
                                            required
                                            value={data.category}
                                            onChange={(e) => setData('category', e.target.value)}
                                            className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        >
                                            <option value="">Choisir une categorie...</option>
                                            {categories.map((cat) => (
                                                <option key={cat} value={cat}>
                                                    {cat}
                                                </option>
                                            ))}
                                        </select>
                                    ) : (
                                        <input
                                            type="text"
                                            required
                                            value={data.category}
                                            onChange={(e) => setData('category', e.target.value)}
                                            className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                            placeholder="Ex: Transport, Salaires..."
                                        />
                                    )}
                                    {errors.category && (
                                        <span className="text-xs text-rose-500">{errors.category}</span>
                                    )}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Note (optionnel)</label>
                                    <textarea
                                        rows={2}
                                        value={data.note}
                                        onChange={(e) => setData('note', e.target.value)}
                                        className="w-full rounded-lg border border-input bg-background p-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary resize-none"
                                        placeholder="Details supplementaires..."
                                    />
                                </div>

                                <div className="mt-2 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        onClick={() => setIsModalOpen(false)}
                                        className="h-10 px-4 rounded-lg border border-border text-sm font-medium hover:bg-secondary transition-colors"
                                    >
                                        Annuler
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="h-10 px-6 rounded-lg bg-primary text-primary-foreground text-sm font-bold hover:brightness-110 transition-all disabled:opacity-50"
                                    >
                                        {processing ? 'Enregistrement...' : 'Enregistrer'}
                                    </button>
                                </div>
                            </form>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>
        </div>
    );
}

FinancesIndex.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Finances',
            href: props.currentTeam ? `/${props.currentTeam.slug}/finances` : '/',
        },
    ],
});
