import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowDownRight, ArrowUpRight, RefreshCw, Search, Package } from 'lucide-react';
import * as StockController from '@/actions/App/Http/Controllers/StockController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

type MovementType = 'in' | 'out' | 'adjustment';

interface Product {
    id: number;
    name: string;
    sku: string;
}

interface Warehouse {
    id: number;
    name: string;
}

interface Creator {
    id: number;
    name: string;
}

interface Mouvement {
    id: number;
    movement_type: MovementType;
    quantity: number;
    unit_cost: number | null;
    notes: string | null;
    created_at: string;
    product: Product | null;
    warehouse: Warehouse | null;
    creator: Creator | null;
}

interface PaginatedMouvements {
    data: Mouvement[];
    links: unknown;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    mouvements: PaginatedMouvements;
}

const typeConfig: Record<MovementType, { label: string; className: string; icon: React.ReactNode }> = {
    in: {
        label: 'Entrée',
        className: 'bg-emerald-500/10 text-emerald-500',
        icon: <ArrowDownRight className="h-3 w-3" />,
    },
    out: {
        label: 'Sortie',
        className: 'bg-amber-500/10 text-amber-500',
        icon: <ArrowUpRight className="h-3 w-3" />,
    },
    adjustment: {
        label: 'Ajustement',
        className: 'bg-blue-500/10 text-blue-500',
        icon: <RefreshCw className="h-3 w-3" />,
    },
};

function formatDate(dateStr: string): string {
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateStr));
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(val);
}

export default function StocksMouvements({ team, mouvements }: Props) {
    const [search, setSearch] = useState('');
    const [typeFilter, setTypeFilter] = useState<MovementType | 'all'>('all');

    const filtered = mouvements.data.filter((m) => {
        const matchesSearch =
            m.product?.name.toLowerCase().includes(search.toLowerCase()) ||
            m.product?.sku.toLowerCase().includes(search.toLowerCase()) ||
            m.warehouse?.name.toLowerCase().includes(search.toLowerCase());
        const matchesType = typeFilter === 'all' || m.movement_type === typeFilter;
        return matchesSearch && matchesType;
    });

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Mouvements de stock — NEXORA" />

            {/* Header */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Package className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                            Module Stock
                        </span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Mouvements de stock</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Historique complet des entrées, sorties et ajustements.
                    </p>
                </div>
                <Link
                    href={StockController.index({ current_team: team.slug }).url}
                    className="inline-flex items-center gap-2 rounded-lg border border-border bg-background px-4 py-2.5 text-sm font-medium hover:bg-secondary transition-colors"
                >
                    ← Retour aux stocks
                </Link>
            </motion.div>

            {/* Tableau mouvements */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card overflow-hidden"
            >
                <div className="px-5 py-4 border-b border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <h2 className="text-base font-semibold text-foreground">
                        Historique{' '}
                        <span className="text-muted-foreground font-normal text-sm">
                            ({mouvements.data.length} entrées)
                        </span>
                    </h2>
                    <div className="flex items-center gap-3 w-full sm:w-auto">
                        {/* Filtre type */}
                        <div className="flex items-center gap-1 rounded-lg border border-border bg-background p-1">
                            {(['all', 'in', 'out', 'adjustment'] as const).map((t) => (
                                <button
                                    key={t}
                                    onClick={() => setTypeFilter(t)}
                                    className={`px-2.5 py-1 rounded-md text-xs font-medium transition-colors ${
                                        typeFilter === t
                                            ? 'bg-primary text-primary-foreground'
                                            : 'text-muted-foreground hover:bg-secondary'
                                    }`}
                                >
                                    {t === 'all' ? 'Tous' : t === 'in' ? 'Entrées' : t === 'out' ? 'Sorties' : 'Ajustements'}
                                </button>
                            ))}
                        </div>
                        {/* Recherche */}
                        <div className="relative w-full sm:w-56">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input
                                type="text"
                                placeholder="Produit, entrepôt..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="h-9 w-full rounded-lg border border-input bg-background/50 pl-9 pr-4 text-sm focus:outline-none focus:ring-1 focus:ring-primary transition-all"
                            />
                        </div>
                    </div>
                </div>

                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border bg-secondary/30">
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Date</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Type</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Produit</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Entrepôt</th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Quantité</th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Coût unit.</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Notes</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Créé par</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {filtered.length > 0 ? (
                                filtered.map((m) => {
                                    const config = typeConfig[m.movement_type] ?? typeConfig.adjustment;
                                    return (
                                        <tr key={m.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5 text-muted-foreground whitespace-nowrap">
                                                {formatDate(m.created_at)}
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <span
                                                    className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${config.className}`}
                                                >
                                                    {config.icon}
                                                    {config.label}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <span className="font-medium text-foreground">
                                                    {m.product?.name ?? '—'}
                                                </span>
                                                {m.product?.sku && (
                                                    <span className="ml-1.5 text-xs text-muted-foreground font-mono">
                                                        {m.product.sku}
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-5 py-3.5 text-muted-foreground">
                                                {m.warehouse?.name ?? '—'}
                                            </td>
                                            <td className="px-5 py-3.5 text-right font-bold">
                                                <span
                                                    className={
                                                        m.movement_type === 'in'
                                                            ? 'text-emerald-500'
                                                            : m.movement_type === 'out'
                                                              ? 'text-amber-500'
                                                              : 'text-blue-500'
                                                    }
                                                >
                                                    {m.movement_type === 'in' ? '+' : m.movement_type === 'out' ? '-' : ''}
                                                    {m.quantity}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 text-right text-muted-foreground">
                                                {m.unit_cost != null ? formatCurrency(m.unit_cost) : '—'}
                                            </td>
                                            <td className="px-5 py-3.5 text-muted-foreground max-w-[200px] truncate">
                                                {m.notes ?? '—'}
                                            </td>
                                            <td className="px-5 py-3.5 text-muted-foreground">
                                                {m.creator?.name ?? '—'}
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan={8} className="px-5 py-12 text-center text-muted-foreground">
                                        Aucun mouvement trouvé.
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

StocksMouvements.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Stocks',
            href: props.currentTeam ? `/${props.currentTeam.slug}/stocks` : '/',
        },
        {
            title: 'Mouvements',
            href: props.currentTeam ? `/${props.currentTeam.slug}/stocks/mouvements` : '#',
        },
    ],
});
