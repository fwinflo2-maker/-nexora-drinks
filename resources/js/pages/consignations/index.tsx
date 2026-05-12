import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { Box, Package, AlertTriangle, Search, Plus, X, Eye, TrendingUp } from 'lucide-react';
import * as ConsignationController from '@/actions/App/Http/Controllers/ConsignationController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

interface PackagingType {
    id: number;
    name: string;
    unit_value_xaf: number;
}

interface PackagingBalance {
    id: number;
    packaging_type_id: number;
    quantity_owed: number;
    last_updated_at: string | null;
    packaging_type: PackagingType;
}

interface Client {
    id: number;
    name: string;
    phone?: string;
    packaging_balances: PackagingBalance[];
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    clients: Client[];
    packaging_types: PackagingType[];
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(val);
}

export default function ConsignationsIndex({ team, clients, packaging_types }: Props) {
    const [search, setSearch] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        unit_value_xaf: '',
        description: '',
    });

    const clientsWithDebt = clients.filter((c) => c.packaging_balances.some((b) => b.quantity_owed > 0));

    const totalOwed = clients.reduce(
        (sum, c) => sum + c.packaging_balances.reduce((s, b) => s + b.quantity_owed, 0),
        0,
    );

    const totalValue = clients.reduce(
        (sum, c) =>
            sum +
            c.packaging_balances.reduce((s, b) => s + b.quantity_owed * (b.packaging_type?.unit_value_xaf ?? 0), 0),
        0,
    );

    const filtered = clients.filter((c) => c.name.toLowerCase().includes(search.toLowerCase()));

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(ConsignationController.store({ current_team: team.slug }).url, {
            onSuccess: () => {
                setIsModalOpen(false);
                reset();
            },
        });
    };

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Consignations — NEXORA" />

            {/* Header */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Box className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                            Module Logistique
                        </span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Consignations</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Suivi des emballages (casiers, bouteilles) chez les clients.
                    </p>
                </div>
                <button
                    onClick={() => setIsModalOpen(true)}
                    className="inline-flex items-center gap-2 rounded-lg bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold hover:brightness-110 transition-all shadow-lg shadow-primary/20"
                >
                    <Plus className="h-4 w-4" />
                    Nouveau type d'emballage
                </button>
            </motion.div>

            {/* KPIs */}
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
                        <span className="text-sm font-medium text-muted-foreground">Clients avec dette</span>
                        <div className="p-2 rounded-xl bg-blue-500/10 text-blue-400">
                            <Package className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-foreground relative z-10 mt-3 block">
                        {clientsWithDebt.length}
                        <span className="text-sm font-normal text-muted-foreground ml-1">/ {clients.length}</span>
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Emballages dehors</span>
                        <div className="p-2 rounded-xl bg-amber-500/10 text-amber-400">
                            <AlertTriangle className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-amber-500 relative z-10 mt-3 block">
                        {new Intl.NumberFormat('fr-FR').format(totalOwed)}
                        <span className="text-sm font-normal text-muted-foreground ml-1">unités</span>
                    </span>
                </motion.div>

                <motion.div
                    variants={fadeUp}
                    className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                >
                    <div className="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Valeur immobilisée</span>
                        <div className="p-2 rounded-xl bg-rose-500/10 text-rose-400">
                            <TrendingUp className="h-4 w-4" />
                        </div>
                    </div>
                    <span className="text-2xl font-bold tracking-tight text-rose-500 relative z-10 mt-3 block">
                        {formatCurrency(totalValue)}
                    </span>
                </motion.div>
            </motion.div>

            {/* Tableau clients */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card overflow-hidden"
            >
                <div className="px-5 py-4 border-b border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <h2 className="text-base font-semibold text-foreground">
                        Clients{' '}
                        <span className="text-muted-foreground font-normal text-sm">({clients.length})</span>
                    </h2>
                    <div className="relative w-full sm:w-64">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <input
                            type="text"
                            placeholder="Rechercher un client..."
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
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Client</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">
                                    Types d'emballage
                                </th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">
                                    Total dû (unités)
                                </th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">
                                    Valeur (XAF)
                                </th>
                                <th className="px-5 py-3 text-center font-semibold text-muted-foreground">Action</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {filtered.length > 0 ? (
                                filtered.map((client) => {
                                    const totalClientOwed = client.packaging_balances.reduce(
                                        (s, b) => s + b.quantity_owed,
                                        0,
                                    );
                                    const totalClientValue = client.packaging_balances.reduce(
                                        (s, b) => s + b.quantity_owed * (b.packaging_type?.unit_value_xaf ?? 0),
                                        0,
                                    );
                                    return (
                                        <tr key={client.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5 font-medium text-foreground">
                                                {client.name}
                                                {client.phone && (
                                                    <p className="text-xs text-muted-foreground mt-0.5">
                                                        {client.phone}
                                                    </p>
                                                )}
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <div className="flex flex-wrap gap-1">
                                                    {client.packaging_balances.length > 0 ? (
                                                        client.packaging_balances.map((b) => (
                                                            <span
                                                                key={b.id}
                                                                className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${
                                                                    b.quantity_owed > 0
                                                                        ? 'bg-amber-500/10 text-amber-500'
                                                                        : 'bg-secondary text-muted-foreground'
                                                                }`}
                                                            >
                                                                {b.packaging_type?.name}: {b.quantity_owed}
                                                            </span>
                                                        ))
                                                    ) : (
                                                        <span className="text-muted-foreground text-xs">—</span>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="px-5 py-3.5 text-right font-bold">
                                                <span
                                                    className={
                                                        totalClientOwed > 0 ? 'text-amber-500' : 'text-muted-foreground'
                                                    }
                                                >
                                                    {totalClientOwed}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 text-right text-muted-foreground font-mono text-xs">
                                                {totalClientValue > 0 ? formatCurrency(totalClientValue) : '—'}
                                            </td>
                                            <td className="px-5 py-3.5 text-center">
                                                <Link
                                                    href={ConsignationController.show({
                                                        current_team: team.slug,
                                                        client: client.id,
                                                    }).url}
                                                    className="inline-flex items-center gap-1.5 text-xs text-primary hover:underline"
                                                >
                                                    <Eye className="h-3.5 w-3.5" />
                                                    Détail
                                                </Link>
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan={5} className="px-5 py-12 text-center text-muted-foreground">
                                        Aucun client trouvé.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </motion.div>

            {/* Types d'emballage existants */}
            {packaging_types.length > 0 && (
                <motion.div
                    variants={fadeUp}
                    initial="hidden"
                    animate="visible"
                    className="rounded-2xl border border-border bg-card p-5"
                >
                    <h2 className="text-base font-semibold text-foreground mb-4">Types d'emballage actifs</h2>
                    <div className="flex flex-wrap gap-2">
                        {packaging_types.map((pt) => (
                            <div
                                key={pt.id}
                                className="flex items-center gap-2 px-3 py-2 rounded-xl border border-border bg-secondary/30 text-sm"
                            >
                                <Box className="h-3.5 w-3.5 text-muted-foreground" />
                                <span className="font-medium">{pt.name}</span>
                                <span className="text-muted-foreground text-xs">
                                    {formatCurrency(pt.unit_value_xaf)} / unité
                                </span>
                            </div>
                        ))}
                    </div>
                </motion.div>
            )}

            {/* Modal nouveau type d'emballage */}
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
                                <h2 className="text-lg font-bold text-foreground">Nouveau type d'emballage</h2>
                                <button
                                    onClick={() => setIsModalOpen(false)}
                                    className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors"
                                >
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Nom *</label>
                                    <input
                                        type="text"
                                        required
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ex: Casier 12 bouteilles"
                                    />
                                    {errors.name && <span className="text-xs text-rose-500">{errors.name}</span>}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Valeur unitaire (XAF) *</label>
                                    <input
                                        type="number"
                                        min="0"
                                        required
                                        value={data.unit_value_xaf}
                                        onChange={(e) => setData('unit_value_xaf', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ex: 2500"
                                    />
                                    {errors.unit_value_xaf && (
                                        <span className="text-xs text-rose-500">{errors.unit_value_xaf}</span>
                                    )}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Description (optionnel)</label>
                                    <textarea
                                        rows={2}
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        className="w-full rounded-lg border border-input bg-background p-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary resize-none"
                                        placeholder="Description de l'emballage..."
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
                                        {processing ? 'Enregistrement...' : 'Créer'}
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

ConsignationsIndex.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Consignations',
            href: props.currentTeam ? `/${props.currentTeam.slug}/consignations` : '/',
        },
    ],
});
