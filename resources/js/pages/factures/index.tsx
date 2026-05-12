import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    FileText, TrendingUp, Clock, AlertTriangle, CheckCircle2,
    Search, Plus, Eye
} from 'lucide-react';
import * as FactureController from '@/actions/App/Http/Controllers/FactureController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

type FactureStatus = 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled';

interface Facture {
    id: number;
    number: string;
    client_name: string;
    amount: number;
    status: FactureStatus;
    issued_at: string;
    due_at: string;
}

interface Stats {
    total_revenue: number;
    pending_amount: number;
    overdue_amount: number;
    paid_count: number;
    pending_count: number;
    overdue_count: number;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    factures: {
        data: Facture[];
        links: unknown;
    };
    stats: Stats;
}

const statusConfig: Record<FactureStatus, { label: string; className: string }> = {
    draft: { label: 'Brouillon', className: 'bg-gray-500/10 text-gray-500' },
    sent: { label: 'Envoyée', className: 'bg-blue-500/10 text-blue-500' },
    paid: { label: 'Payée', className: 'bg-emerald-500/10 text-emerald-500' },
    overdue: { label: 'En retard', className: 'bg-rose-500/10 text-rose-500' },
    cancelled: { label: 'Annulée', className: 'bg-gray-500/10 text-gray-400 line-through' },
};

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

export default function FacturesIndex({ team, factures, stats }: Props) {
    const [search, setSearch] = useState('');

    const filtered = factures.data.filter(
        (f) =>
            f.number.toLowerCase().includes(search.toLowerCase()) ||
            f.client_name.toLowerCase().includes(search.toLowerCase()),
    );

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Factures — NEXORA" />

            {/* Header */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <FileText className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                            Module Finance
                        </span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Factures</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Suivi des factures clients et encaissements.
                    </p>
                </div>
                <Link
                    href={FactureController.store({ current_team: team.slug }).url}
                    className="inline-flex items-center gap-2 rounded-lg bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold hover:brightness-110 transition-all shadow-lg shadow-primary/20"
                >
                    <Plus className="h-4 w-4" />
                    Nouvelle facture
                </Link>
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
                        <span className="text-sm font-medium text-muted-foreground">CA encaissé</span>
                        <div className="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">
                            <TrendingUp className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-2xl font-bold tracking-tight text-emerald-500 relative z-10 mt-3 block">
                        {formatCurrency(stats.total_revenue)}
                    </span>
                    <span className="text-xs text-muted-foreground relative z-10 mt-1 block">
                        {stats.paid_count} factures payées
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">En attente</span>
                        <div className="p-2 rounded-xl bg-amber-500/10 text-amber-400">
                            <Clock className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-2xl font-bold tracking-tight text-amber-500 relative z-10 mt-3 block">
                        {formatCurrency(stats.pending_amount)}
                    </span>
                    <span className="text-xs text-muted-foreground relative z-10 mt-1 block">
                        {stats.pending_count} factures en attente
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">En retard</span>
                        <div className="p-2 rounded-xl bg-rose-500/10 text-rose-400">
                            <AlertTriangle className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-2xl font-bold tracking-tight text-rose-500 relative z-10 mt-3 block">
                        {formatCurrency(stats.overdue_amount)}
                    </span>
                    <span className="text-xs text-muted-foreground relative z-10 mt-1 block">
                        {stats.overdue_count} factures en retard
                    </span>
                </motion.div>
            </motion.div>

            {/* Tableau factures */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card overflow-hidden"
            >
                <div className="px-5 py-4 border-b border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <h2 className="text-base font-semibold text-foreground">Liste des factures</h2>
                    <div className="relative w-full sm:w-64">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <input
                            type="text"
                            placeholder="Rechercher..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="h-9 w-full rounded-lg border border-input bg-background/50 pl-9 pr-4 text-sm focus:outline-none focus:ring-1 focus:ring-primary transition-all"
                        />
                    </div>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border bg-secondary/30">
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">N°</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Client</th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Montant</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Émise le</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Échéance</th>
                                <th className="px-5 py-3 text-center font-semibold text-muted-foreground">Statut</th>
                                <th className="px-5 py-3 text-center font-semibold text-muted-foreground">Action</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {filtered.length > 0 ? (
                                filtered.map((facture) => {
                                    const config = statusConfig[facture.status] ?? statusConfig.draft;
                                    return (
                                        <tr key={facture.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5 font-mono text-xs font-medium text-foreground">
                                                {facture.number}
                                            </td>
                                            <td className="px-5 py-3.5 font-medium text-foreground">
                                                {facture.client_name}
                                            </td>
                                            <td className="px-5 py-3.5 text-right font-bold text-foreground">
                                                {formatCurrency(facture.amount)}
                                            </td>
                                            <td className="px-5 py-3.5 text-muted-foreground">
                                                {formatDate(facture.issued_at)}
                                            </td>
                                            <td className="px-5 py-3.5 text-muted-foreground">
                                                {formatDate(facture.due_at)}
                                            </td>
                                            <td className="px-5 py-3.5 text-center">
                                                <span
                                                    className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${config.className}`}
                                                >
                                                    {config.label}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 text-center">
                                                <Link
                                                    href={FactureController.show({
                                                        current_team: team.slug,
                                                        invoice: facture.id,
                                                    }).url}
                                                    className="inline-flex items-center gap-1.5 text-xs text-primary hover:underline"
                                                >
                                                    <Eye className="h-3.5 w-3.5" />
                                                    Voir
                                                </Link>
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan={7} className="px-5 py-8 text-center text-muted-foreground">
                                        Aucune facture trouvée.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </motion.div>
        </div>
    );
}

FacturesIndex.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Factures',
            href: props.currentTeam ? `/${props.currentTeam.slug}/factures` : '/',
        },
    ],
});
