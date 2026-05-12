import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { Box, ArrowDownRight, ArrowUpRight, Plus, X, User } from 'lucide-react';
import * as ConsignationController from '@/actions/App/Http/Controllers/ConsignationController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

interface PackagingType {
    id: number;
    name: string;
    unit_value_xaf: number;
}

interface Balance {
    id: number;
    quantity_owed: number;
    last_updated_at: string | null;
    packaging_type: PackagingType;
}

interface Mouvement {
    id: number;
    movement_type: 'in' | 'out';
    quantity: number;
    notes: string | null;
    created_at: string;
    packaging_type: PackagingType | null;
    creator: { id: number; name: string } | null;
}

interface PaginatedHistorique {
    data: Mouvement[];
    links: unknown;
}

interface Client {
    id: number;
    name: string;
    phone?: string;
    address?: string;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    client: Client;
    historique: PaginatedHistorique;
    balances: Balance[];
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
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(dateStr));
}

export default function ConsignationsShow({ team, client, historique, balances }: Props) {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        packaging_type_id: '',
        movement_type: 'in' as 'in' | 'out',
        quantity: '',
        notes: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(ConsignationController.storeMovement({ current_team: team.slug, client: client.id }).url, {
            onSuccess: () => {
                setIsModalOpen(false);
                reset();
            },
        });
    };

    const totalOwed = balances.reduce((s, b) => s + b.quantity_owed, 0);
    const totalValue = balances.reduce(
        (s, b) => s + b.quantity_owed * (b.packaging_type?.unit_value_xaf ?? 0),
        0,
    );

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title={`Consignations — ${client.name}`} />

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
                    <h1 className="text-2xl font-bold tracking-tight">{client.name}</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Historique des consignations et soldes actuels.
                    </p>
                </div>
                <div className="flex items-center gap-3">
                    <Link
                        href={ConsignationController.index({ current_team: team.slug }).url}
                        className="inline-flex items-center gap-2 rounded-lg border border-border bg-background px-4 py-2.5 text-sm font-medium hover:bg-secondary transition-colors"
                    >
                        ← Retour
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

            {/* Info client + soldes */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Fiche client */}
                <motion.div
                    variants={fadeUp}
                    initial="hidden"
                    animate="visible"
                    className="rounded-2xl border border-border bg-card p-5"
                >
                    <div className="flex items-center gap-3 mb-4">
                        <div className="p-3 rounded-xl bg-primary/10 text-primary">
                            <User className="h-5 w-5" />
                        </div>
                        <h2 className="text-base font-semibold">Informations client</h2>
                    </div>
                    <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                            <span className="text-muted-foreground">Nom</span>
                            <span className="font-medium">{client.name}</span>
                        </div>
                        {client.phone && (
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Téléphone</span>
                                <span>{client.phone}</span>
                            </div>
                        )}
                        {client.address && (
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Adresse</span>
                                <span className="text-right max-w-[60%]">{client.address}</span>
                            </div>
                        )}
                        <div className="pt-3 mt-3 border-t border-border">
                            <div className="flex justify-between">
                                <span className="text-muted-foreground">Total dû</span>
                                <span className="font-bold text-amber-500">{totalOwed} unités</span>
                            </div>
                            <div className="flex justify-between mt-1">
                                <span className="text-muted-foreground">Valeur</span>
                                <span className="font-bold text-rose-500">{formatCurrency(totalValue)}</span>
                            </div>
                        </div>
                    </div>
                </motion.div>

                {/* Soldes par type */}
                <motion.div
                    variants={fadeUp}
                    initial="hidden"
                    animate="visible"
                    className="lg:col-span-2 rounded-2xl border border-border bg-card p-5"
                >
                    <h2 className="text-base font-semibold mb-4">Soldes par type d'emballage</h2>
                    {balances.length > 0 ? (
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {balances.map((b) => (
                                <div
                                    key={b.id}
                                    className="flex items-center justify-between p-4 rounded-xl border border-border bg-secondary/20"
                                >
                                    <div>
                                        <p className="font-medium text-sm">{b.packaging_type?.name ?? '—'}</p>
                                        <p className="text-xs text-muted-foreground mt-0.5">
                                            {formatCurrency(b.packaging_type?.unit_value_xaf ?? 0)} / unité
                                        </p>
                                    </div>
                                    <div className="text-right">
                                        <p
                                            className={`text-2xl font-bold ${b.quantity_owed > 0 ? 'text-amber-500' : 'text-emerald-500'}`}
                                        >
                                            {b.quantity_owed}
                                        </p>
                                        <p className="text-xs text-muted-foreground">unités dues</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-sm text-muted-foreground text-center py-8">
                            Aucun solde enregistré pour ce client.
                        </p>
                    )}
                </motion.div>
            </div>

            {/* Historique des mouvements */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card overflow-hidden"
            >
                <div className="px-5 py-4 border-b border-border">
                    <h2 className="text-base font-semibold">
                        Historique des mouvements{' '}
                        <span className="text-muted-foreground font-normal text-sm">
                            ({historique.data.length})
                        </span>
                    </h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border bg-secondary/30">
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Date</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Type</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Emballage</th>
                                <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Quantité</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Notes</th>
                                <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Par</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {historique.data.length > 0 ? (
                                historique.data.map((m) => (
                                    <tr key={m.id} className="hover:bg-secondary/20 transition-colors">
                                        <td className="px-5 py-3.5 text-muted-foreground whitespace-nowrap">
                                            {formatDate(m.created_at)}
                                        </td>
                                        <td className="px-5 py-3.5">
                                            <span
                                                className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${
                                                    m.movement_type === 'in'
                                                        ? 'bg-emerald-500/10 text-emerald-500'
                                                        : 'bg-amber-500/10 text-amber-500'
                                                }`}
                                            >
                                                {m.movement_type === 'in' ? (
                                                    <ArrowDownRight className="h-3 w-3" />
                                                ) : (
                                                    <ArrowUpRight className="h-3 w-3" />
                                                )}
                                                {m.movement_type === 'in' ? 'Retour' : 'Prêt'}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3.5 text-foreground">
                                            {m.packaging_type?.name ?? '—'}
                                        </td>
                                        <td className="px-5 py-3.5 text-right font-bold">
                                            <span
                                                className={
                                                    m.movement_type === 'in' ? 'text-emerald-500' : 'text-amber-500'
                                                }
                                            >
                                                {m.movement_type === 'in' ? '-' : '+'}
                                                {m.quantity}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3.5 text-muted-foreground max-w-[200px] truncate">
                                            {m.notes ?? '—'}
                                        </td>
                                        <td className="px-5 py-3.5 text-muted-foreground">
                                            {m.creator?.name ?? '—'}
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan={6} className="px-5 py-12 text-center text-muted-foreground">
                                        Aucun mouvement enregistré.
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
                                <h2 className="text-lg font-bold text-foreground">Mouvement — {client.name}</h2>
                                <button
                                    onClick={() => setIsModalOpen(false)}
                                    className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors"
                                >
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Type d'opération</label>
                                    <div className="grid grid-cols-2 gap-2">
                                        {(['in', 'out'] as const).map((type) => (
                                            <button
                                                key={type}
                                                type="button"
                                                onClick={() => setData('movement_type', type)}
                                                className={`h-10 rounded-lg text-sm font-medium border flex items-center justify-center gap-2 transition-all ${
                                                    data.movement_type === type
                                                        ? type === 'in'
                                                            ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500'
                                                            : 'bg-amber-500/10 border-amber-500/30 text-amber-500'
                                                        : 'bg-background border-border text-muted-foreground hover:bg-secondary'
                                                }`}
                                            >
                                                {type === 'in' ? (
                                                    <ArrowDownRight className="h-4 w-4" />
                                                ) : (
                                                    <ArrowUpRight className="h-4 w-4" />
                                                )}
                                                {type === 'in' ? 'Retour (entrée)' : 'Prêt (sortie)'}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Type d'emballage *</label>
                                    <select
                                        required
                                        value={data.packaging_type_id}
                                        onChange={(e) => setData('packaging_type_id', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                    >
                                        <option value="">Choisir un type...</option>
                                        {balances.map((b) => (
                                            <option key={b.packaging_type.id} value={b.packaging_type.id}>
                                                {b.packaging_type.name} (solde : {b.quantity_owed})
                                            </option>
                                        ))}
                                    </select>
                                    {errors.packaging_type_id && (
                                        <span className="text-xs text-rose-500">{errors.packaging_type_id}</span>
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
                                        placeholder="Ex: 5"
                                    />
                                    {errors.quantity && (
                                        <span className="text-xs text-rose-500">{errors.quantity}</span>
                                    )}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Notes (optionnel)</label>
                                    <textarea
                                        rows={2}
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        className="w-full rounded-lg border border-input bg-background p-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary resize-none"
                                        placeholder="État de l'emballage, remarques..."
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

ConsignationsShow.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Consignations',
            href: props.currentTeam ? `/${props.currentTeam.slug}/consignations` : '/',
        },
        {
            title: 'Détail client',
            href: '#',
        },
    ],
});
