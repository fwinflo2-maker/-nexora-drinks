import { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Truck, Plus, X, Calendar, CheckCircle2, Clock, MapPin,
    TrendingUp, Package, AlertCircle
} from 'lucide-react';
import * as TourneeController from '@/actions/App/Http/Controllers/TourneeController';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } },
};

type TourneeStatus = 'planned' | 'in_progress' | 'completed' | 'cancelled';

interface Tournee {
    id: number;
    name: string;
    date: string;
    status: TourneeStatus;
    driver_name?: string;
    deliveries_count: number;
    completed_count: number;
    total_amount?: number;
}

interface Stats {
    total: number;
    planned: number;
    in_progress: number;
    completed: number;
}

interface Team {
    id: number;
    name: string;
    slug: string;
}

interface Props {
    team: Team;
    tournees: {
        data: Tournee[];
        links: unknown;
    };
    stats: Stats;
}

const statusConfig: Record<TourneeStatus, { label: string; className: string }> = {
    planned: {
        label: 'Planifiée',
        className: 'bg-blue-500/10 text-blue-500',
    },
    in_progress: {
        label: 'En cours',
        className: 'bg-amber-500/10 text-amber-500',
    },
    completed: {
        label: 'Terminée',
        className: 'bg-emerald-500/10 text-emerald-500',
    },
    cancelled: {
        label: 'Annulée',
        className: 'bg-rose-500/10 text-rose-500',
    },
};

function formatDate(dateStr: string): string {
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(dateStr));
}

function formatCurrency(val: number): string {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(val);
}

export default function TourneesIndex({ team, tournees, stats }: Props) {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        date: '',
        driver_name: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(TourneeController.store({ current_team: team.slug }).url, {
            onSuccess: () => {
                setIsModalOpen(false);
                reset();
            },
        });
    };

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Tournées — NEXORA" />

            {/* Header */}
            <motion.div
                initial={{ opacity: 0, y: -8 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4 }}
                className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Truck className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">
                            Module Logistique
                        </span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Tournées de livraison</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Gestion et suivi des tournées de livraison.
                    </p>
                </div>
                <button
                    onClick={() => setIsModalOpen(true)}
                    className="inline-flex items-center gap-2 rounded-lg bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold hover:brightness-110 transition-all shadow-lg shadow-primary/20"
                >
                    <Plus className="h-4 w-4" />
                    Nouvelle tournée
                </button>
            </motion.div>

            {/* KPI Grid */}
            <motion.div
                variants={container}
                initial="hidden"
                animate="visible"
                className="grid grid-cols-2 md:grid-cols-4 gap-4"
            >
                {[
                    { label: 'Total', value: stats.total, icon: Truck, color: 'blue' },
                    { label: 'Planifiées', value: stats.planned, icon: Calendar, color: 'indigo' },
                    { label: 'En cours', value: stats.in_progress, icon: Clock, color: 'amber' },
                    { label: 'Terminées', value: stats.completed, icon: CheckCircle2, color: 'emerald' },
                ].map(({ label, value, icon: Icon, color }) => (
                    <motion.div
                        key={label}
                        variants={fadeUp}
                        className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group"
                    >
                        <div
                            className={`absolute inset-0 bg-gradient-to-br from-${color}-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity`}
                        />
                        <div className="flex items-center justify-between relative z-10">
                            <span className="text-sm font-medium text-muted-foreground">{label}</span>
                            <div className={`p-2 rounded-xl bg-${color}-500/10 text-${color}-400`}>
                                <Icon className="h-4 w-4" />
                            </div>
                        </div>
                        <span className="text-3xl font-bold tracking-tight text-foreground relative z-10 mt-3 block">
                            {value}
                        </span>
                    </motion.div>
                ))}
            </motion.div>

            {/* Liste des tournées */}
            <motion.div
                variants={fadeUp}
                initial="hidden"
                animate="visible"
                className="rounded-2xl border border-border bg-card overflow-hidden"
            >
                <div className="px-5 py-4 border-b border-border">
                    <h2 className="text-base font-semibold text-foreground">Liste des tournées</h2>
                </div>

                {tournees.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-16 text-muted-foreground">
                        <Truck className="h-12 w-12 mb-4 opacity-20" />
                        <p className="text-sm">Aucune tournée pour le moment.</p>
                        <button
                            onClick={() => setIsModalOpen(true)}
                            className="mt-4 text-sm text-primary hover:underline"
                        >
                            Créer la première tournée
                        </button>
                    </div>
                ) : (
                    <div className="divide-y divide-border">
                        {tournees.data.map((tournee) => {
                            const config = statusConfig[tournee.status] ?? statusConfig.planned;
                            const progress =
                                tournee.deliveries_count > 0
                                    ? Math.round((tournee.completed_count / tournee.deliveries_count) * 100)
                                    : 0;

                            return (
                                <div
                                    key={tournee.id}
                                    className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-5 py-4 hover:bg-secondary/20 transition-colors"
                                >
                                    <div className="flex items-start gap-4 flex-1 min-w-0">
                                        <div className="p-2.5 rounded-xl bg-primary/10 text-primary shrink-0">
                                            <Truck className="h-5 w-5" />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2 flex-wrap">
                                                <span className="font-semibold text-foreground truncate">
                                                    {tournee.name}
                                                </span>
                                                <span
                                                    className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${config.className}`}
                                                >
                                                    {config.label}
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-4 mt-1 text-xs text-muted-foreground flex-wrap">
                                                <span className="flex items-center gap-1">
                                                    <Calendar className="h-3 w-3" />
                                                    {formatDate(tournee.date)}
                                                </span>
                                                {tournee.driver_name && (
                                                    <span className="flex items-center gap-1">
                                                        <MapPin className="h-3 w-3" />
                                                        {tournee.driver_name}
                                                    </span>
                                                )}
                                                <span className="flex items-center gap-1">
                                                    <Package className="h-3 w-3" />
                                                    {tournee.completed_count}/{tournee.deliveries_count} livraisons
                                                </span>
                                            </div>
                                            {tournee.deliveries_count > 0 && (
                                                <div className="mt-2 h-1.5 w-full max-w-xs bg-secondary rounded-full overflow-hidden">
                                                    <div
                                                        className="h-full bg-primary rounded-full transition-all"
                                                        style={{ width: `${progress}%` }}
                                                    />
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3 shrink-0">
                                        {tournee.total_amount !== undefined && (
                                            <span className="text-sm font-bold text-foreground">
                                                {formatCurrency(tournee.total_amount)}
                                            </span>
                                        )}
                                        <Link
                                            href={TourneeController.show({
                                                current_team: team.slug,
                                                deliveryRoute: tournee.id,
                                            }).url}
                                            className="h-9 px-4 rounded-lg border border-border text-sm font-medium hover:bg-secondary transition-colors inline-flex items-center"
                                        >
                                            Voir
                                        </Link>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </motion.div>

            {/* Modal nouvelle tournée */}
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
                                <h2 className="text-lg font-bold text-foreground">Nouvelle tournée</h2>
                                <button
                                    onClick={() => setIsModalOpen(false)}
                                    className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors"
                                >
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Nom de la tournée *</label>
                                    <input
                                        type="text"
                                        required
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Ex: Tournée Nord — Lundi"
                                    />
                                    {errors.name && <span className="text-xs text-rose-500">{errors.name}</span>}
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
                                    {errors.date && <span className="text-xs text-rose-500">{errors.date}</span>}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Chauffeur (optionnel)</label>
                                    <input
                                        type="text"
                                        value={data.driver_name}
                                        onChange={(e) => setData('driver_name', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Nom du chauffeur"
                                    />
                                    {errors.driver_name && (
                                        <span className="text-xs text-rose-500">{errors.driver_name}</span>
                                    )}
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
                                        {processing ? 'Création...' : 'Créer la tournée'}
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

TourneesIndex.layout = (props: { currentTeam?: { slug: string } | null }) => ({
    breadcrumbs: [
        {
            title: 'Tournées',
            href: props.currentTeam ? `/${props.currentTeam.slug}/tournees` : '/',
        },
    ],
});
