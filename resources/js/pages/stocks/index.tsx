import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Package, AlertTriangle, TrendingDown, Plus, Search,
    ArrowUpRight, ArrowDownRight, X, BarChart3
} from 'lucide-react';
import * as StockController from '@/actions/App/Http/Controllers/StockController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

interface Product {
    id: number;
    name: string;
    sku: string;
    stock_quantity: number;
    min_stock_level: number;
    unit: string;
    category?: string;
}

interface PaginatedProducts {
    data: Product[];
    links: unknown;
    meta?: {
        current_page: number;
        last_page: number;
        total: number;
    };
}

interface Stats {
    total: number;
    low_stock: number;
    out_of_stock: number;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    products: PaginatedProducts;
    stats: Stats;
}

function stockStatus(product: Product): 'ok' | 'low' | 'out' {
    if (product.stock_quantity <= 0) return 'out';
    if (product.stock_quantity <= product.min_stock_level) return 'low';
    return 'ok';
}

export default function StocksIndex({ team, products, stats }: Props) {
    const [search, setSearch] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        product_id: '',
        type: 'in' as 'in' | 'out' | 'adjustment',
        quantity: '',
        notes: '',
    });

    const filtered = products.data.filter(
        (p) =>
            p.name.toLowerCase().includes(search.toLowerCase()) ||
            p.sku.toLowerCase().includes(search.toLowerCase()),
    );

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(StockController.storeMovement({ current_team: team.slug }).url, {
            onSuccess: () => {
                setIsModalOpen(false);
                reset();
            },
        });
    };

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Stocks — NEXORA" />

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
                    <h1 className="text-2xl font-bold tracking-tight">Gestion des Stocks</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Vue d'ensemble des produits et niveaux de stock.
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <Link
                        href={StockController.mouvements({ current_team: team.slug }).url}
                        className="inline-flex items-center gap-2 rounded-lg border border-border bg-background px-4 py-2.5 text-sm font-medium hover:bg-secondary transition-colors"
                    >
                        <BarChart3 className="h-4 w-4" />
                        Historique
                    </Link>
                    <button
                        onClick={() => setIsModalOpen(true)}
                        className="inline-flex items-center gap-2 rounded-lg bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold hover:brightness-110 transition-all shadow-lg shadow-primary/20"
                    >
                        <Plus className="h-4 w-4" />
                        Nouveau mouvement
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
                    <div className="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Total produits</span>
                        <div className="p-2 rounded-xl bg-blue-500/10 text-blue-400">
                            <Package className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-foreground relative z-10 mt-3 block">
                        {stats.total}
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Stock bas</span>
                        <div className="p-2 rounded-xl bg-amber-500/10 text-amber-400">
                            <AlertTriangle className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-amber-500 relative z-10 mt-3 block">
                        {stats.low_stock}
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Rupture de stock</span>
                        <div className="p-2 rounded-xl bg-rose-500/10 text-rose-400">
                            <TrendingDown className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-rose-500 relative z-10 mt-3 block">
                        {stats.out_of_stock}
                    </span>
                </motion.div>
            </motion.div>

            {/* Tableau produits */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card overflow-hidden"
            >
                <div className="px-5 py-4 border-b border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <h2 className="text-base font-semibold text-foreground">Inventaire produits</h2>
                    <div className="relative w-full sm:w-64">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <input
                            type="text"
                            placeholder="Rechercher un produit..."
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
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Produit</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">SKU</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Catégorie</th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Stock actuel</th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Seuil min.</th>
                                <th className="px-5 py-3 text-center font-semibold text-muted-foreground">Statut</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {filtered.length > 0 ? (
                                filtered.map((product) => {
                                    const status = stockStatus(product);
                                    return (
                                        <tr key={product.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5 font-medium text-foreground">{product.name}</td>
                                            <td className="px-5 py-3.5 text-muted-foreground font-mono text-xs">{product.sku}</td>
                                            <td className="px-5 py-3.5 text-muted-foreground">{product.category ?? '—'}</td>
                                            <td className="px-5 py-3.5 text-right font-bold">
                                                {product.stock_quantity} {product.unit}
                                            </td>
                                            <td className="px-5 py-3.5 text-right text-muted-foreground">
                                                {product.min_stock_level} {product.unit}
                                            </td>
                                            <td className="px-5 py-3.5 text-center">
                                                {status === 'ok' && (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500">
                                                        OK
                                                    </span>
                                                )}
                                                {status === 'low' && (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-500">
                                                        Stock bas
                                                    </span>
                                                )}
                                                {status === 'out' && (
                                                    <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-rose-500/10 text-rose-500">
                                                        Rupture
                                                    </span>
                                                )}
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan={6} className="px-5 py-8 text-center text-muted-foreground">
                                        Aucun produit trouvé.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </motion.div>

            {/* Modal nouveau mouvement */}
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
                                <h2 className="text-lg font-bold text-foreground">Nouveau mouvement de stock</h2>
                                <button
                                    onClick={() => setIsModalOpen(false)}
                                    className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors"
                                >
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Type de mouvement</label>
                                    <div className="grid grid-cols-3 gap-2">
                                        {(['in', 'out', 'adjustment'] as const).map((type) => (
                                            <button
                                                key={type}
                                                type="button"
                                                onClick={() => setData('type', type)}
                                                className={`h-9 rounded-lg text-xs font-medium border flex items-center justify-center gap-1.5 transition-all ${
                                                    data.type === type
                                                        ? type === 'in'
                                                            ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500'
                                                            : type === 'out'
                                                              ? 'bg-amber-500/10 border-amber-500/30 text-amber-500'
                                                              : 'bg-blue-500/10 border-blue-500/30 text-blue-500'
                                                        : 'bg-background border-border text-muted-foreground hover:bg-secondary'
                                                }`}
                                            >
                                                {type === 'in' && <ArrowDownRight className="h-3 w-3" />}
                                                {type === 'out' && <ArrowUpRight className="h-3 w-3" />}
                                                {type === 'in' ? 'Entrée' : type === 'out' ? 'Sortie' : 'Ajustement'}
                                            </button>
                                        ))}
                                    </div>
                                    {errors.type && <span className="text-xs text-rose-500">{errors.type}</span>}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Produit *</label>
                                    <select
                                        required
                                        value={data.product_id}
                                        onChange={(e) => setData('product_id', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                    >
                                        <option value="">Sélectionner un produit...</option>
                                        {products.data.map((p) => (
                                            <option key={p.id} value={p.id}>
                                                {p.name} ({p.sku})
                                            </option>
                                        ))}
                                    </select>
                                    {errors.product_id && (
                                        <span className="text-xs text-rose-500">{errors.product_id}</span>
                                    )}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Quantité *</label>
                                    <input
                                        type="number"
                                        min="1"
                                        required
                                        value={data.quantity}
                                        onChange={(e) => setData('quantity', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ex: 50"
                                    />
                                    {errors.quantity && (
                                        <span className="text-xs text-rose-500">{errors.quantity}</span>
                                    )}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Note (optionnel)</label>
                                    <textarea
                                        rows={2}
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        className="w-full rounded-lg border border-input bg-background p-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary resize-none"
                                        placeholder="Raison du mouvement..."
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
                                        {processing ? 'Enregistrement...' : 'Valider'}
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

StocksIndex.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Stocks',
            href: props.currentTeam ? `/${props.currentTeam.slug}/stocks` : '/',
        },
    ],
});
