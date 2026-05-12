import { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
    Box, RefreshCw, AlertTriangle, ArrowUpRight, ArrowDownRight, 
    Search, Plus, X, Package, CheckCircle2, TrendingUp, History 
} from 'lucide-react';

const fadeUp = {
    hidden: { opacity: 0, y: 16 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.4, ease: [0.22, 1, 0.36, 1] } }
};

const container = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { staggerChildren: 0.05 } }
};

interface ConsignmentIndexProps {
    balances: any[];
    packagingTypes: any[];
    clients: any[];
    recentMovements: any[];
    stats: {
        total_owed: number;
        total_value_xaf: number;
    };
    flash?: {
        success?: string;
    };
}

export default function ConsignmentIndex({ balances, packagingTypes, clients, recentMovements, stats, flash }: ConsignmentIndexProps) {
    const [search, setSearch] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        client_id: '',
        packaging_type_id: '',
        movement_type: 'in', // Par défaut un retour de consigne
        quantity: '',
        notes: ''
    });

    const filteredBalances = balances.filter(b => 
        b.client_name.toLowerCase().includes(search.toLowerCase()) ||
        b.packaging_name.toLowerCase().includes(search.toLowerCase())
    );

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('consignments.movement'), {
            onSuccess: () => {
                setIsModalOpen(false);
                reset();
            }
        });
    };

    const formatCurrency = (val: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);
    };

    return (
        <div className="flex flex-col gap-6 p-6 lg:p-8 w-full min-h-screen bg-background text-foreground">
            <Head title="Dette Emballages (Consignes) — NEXORA" />

            {/* ── Header ── */}
            <motion.div initial={{ opacity: 0, y: -8 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }} className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Box className="h-5 w-5 text-primary" />
                        <span className="text-xs font-semibold text-primary uppercase tracking-wider">Module Logistique</span>
                    </div>
                    <h1 className="text-2xl font-bold tracking-tight">Dette Emballages (Consignes)</h1>
                    <p className="text-sm text-muted-foreground mt-1">Suivi en temps réel des casiers et bouteilles chez les clients.</p>
                </div>
                <div className="flex items-center gap-3">
                    <button 
                        onClick={() => setIsModalOpen(true)}
                        className="inline-flex items-center gap-2 rounded-lg bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold hover:brightness-110 transition-all shadow-lg shadow-primary/20"
                    >
                        <RefreshCw className="h-4 w-4" />
                        Saisir un retour
                    </button>
                </div>
            </motion.div>

            {/* ── KPI Grid ── */}
            <motion.div variants={container} initial="hidden" animate="visible" className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <motion.div variants={fadeUp} className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group">
                    <div className="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Emballages dehors</span>
                        <div className="p-2 rounded-xl bg-blue-500/10 text-blue-400"><Package className="h-4 w-4" /></div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-foreground relative z-10 mt-3 block">
                        {new Intl.NumberFormat('fr-FR').format(stats.total_owed)} <span className="text-sm font-normal text-muted-foreground">unités</span>
                    </span>
                </motion.div>

                <motion.div variants={fadeUp} className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group">
                    <div className="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Valeur immobilisée</span>
                        <div className="p-2 rounded-xl bg-amber-500/10 text-amber-400"><AlertTriangle className="h-4 w-4" /></div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-foreground relative z-10 mt-3 block">
                        {formatCurrency(stats.total_value_xaf)}
                    </span>
                </motion.div>

                <motion.div variants={fadeUp} className="rounded-2xl border border-border bg-card p-5 relative overflow-hidden group">
                    <div className="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                    <div className="flex items-center justify-between relative z-10">
                        <span className="text-sm font-medium text-muted-foreground">Taux de rotation</span>
                        <div className="p-2 rounded-xl bg-emerald-500/10 text-emerald-400"><TrendingUp className="h-4 w-4" /></div>
                    </div>
                    <span className="text-3xl font-bold tracking-tight text-foreground relative z-10 mt-3 block text-emerald-400">
                        84.2% <span className="text-sm font-normal text-muted-foreground">ce mois</span>
                    </span>
                </motion.div>
            </motion.div>

            {/* ── Contenu Principal ── */}
            <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
                
                {/* Tableau des dettes */}
                <motion.div variants={fadeUp} initial="hidden" animate="visible" className="xl:col-span-2 rounded-2xl border border-border bg-card overflow-hidden flex flex-col">
                    <div className="px-5 py-4 border-b border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <h2 className="text-base font-semibold text-foreground">Soldes par client</h2>
                        <div className="relative w-full sm:w-64">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input 
                                type="text"
                                placeholder="Chercher un client..."
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
                                    <th className="px-5 py-3 text-left font-semibold text-muted-foreground">Type d'emballage</th>
                                    <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Qté Due</th>
                                    <th className="px-5 py-3 text-right font-semibold text-muted-foreground">Valeur (XAF)</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {filteredBalances.length > 0 ? (
                                    filteredBalances.map((b) => (
                                        <tr key={b.id} className="hover:bg-secondary/20 transition-colors">
                                            <td className="px-5 py-3.5 font-medium text-foreground">{b.client_name}</td>
                                            <td className="px-5 py-3.5 text-muted-foreground">{b.packaging_name}</td>
                                            <td className="px-5 py-3.5 text-right font-bold text-amber-500">{b.quantity_owed}</td>
                                            <td className="px-5 py-3.5 text-right font-mono text-muted-foreground">{formatCurrency(b.value_xaf)}</td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={4} className="px-5 py-8 text-center text-muted-foreground">
                                            Aucun solde trouvé.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </motion.div>

                {/* Mouvements récents */}
                <motion.div variants={fadeUp} initial="hidden" animate="visible" className="rounded-2xl border border-border bg-card p-5">
                    <div className="flex items-center gap-2 mb-5">
                        <History className="h-4 w-4 text-muted-foreground" />
                        <h2 className="text-base font-semibold text-foreground">Activité récente</h2>
                    </div>
                    <div className="space-y-4">
                        {recentMovements.length > 0 ? recentMovements.map((m) => (
                            <div key={m.id} className="flex items-start gap-3 p-3 rounded-xl border border-border/50 bg-secondary/10">
                                <div className={`p-2 rounded-lg shrink-0 ${m.type.includes('Retour') ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400'}`}>
                                    {m.type.includes('Retour') ? <ArrowDownRight className="h-4 w-4" /> : <ArrowUpRight className="h-4 w-4" />}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <div className="flex items-center justify-between">
                                        <p className="text-sm font-semibold text-foreground truncate">{m.client}</p>
                                        <span className={`text-xs font-bold ${m.type.includes('Retour') ? 'text-emerald-400' : 'text-amber-400'}`}>
                                            {m.type.includes('Retour') ? '+' : '-'}{m.quantity}
                                        </span>
                                    </div>
                                    <p className="text-xs text-muted-foreground mt-0.5">{m.packaging}</p>
                                    <div className="flex items-center justify-between mt-2 text-[10px] text-muted-foreground">
                                        <span>{m.date}</span>
                                        <span>par {m.user}</span>
                                    </div>
                                </div>
                            </div>
                        )) : (
                            <div className="text-center py-8 text-sm text-muted-foreground">
                                Aucun mouvement récent.
                            </div>
                        )}
                    </div>
                </motion.div>
            </div>

            {/* ── Modal Nouveau Mouvement ── */}
            <AnimatePresence>
                {isModalOpen && (
                    <>
                        <motion.div 
                            initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
                            className="fixed inset-0 z-50 bg-background/80 backdrop-blur-sm"
                            onClick={() => setIsModalOpen(false)}
                        />
                        <motion.div 
                            initial={{ opacity: 0, scale: 0.95, y: 20 }} animate={{ opacity: 1, scale: 1, y: 0 }} exit={{ opacity: 0, scale: 0.95, y: 20 }}
                            className="fixed z-50 left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md p-6 rounded-2xl border border-border bg-card shadow-2xl"
                        >
                            <div className="flex items-center justify-between mb-5">
                                <h2 className="text-lg font-bold text-foreground">Saisir un mouvement d'emballage</h2>
                                <button onClick={() => setIsModalOpen(false)} className="p-1 rounded-md text-muted-foreground hover:bg-secondary transition-colors">
                                    <X className="h-5 w-5" />
                                </button>
                            </div>

                            <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Type d'opération</label>
                                    <div className="grid grid-cols-2 gap-2">
                                        <button 
                                            type="button"
                                            onClick={() => setData('movement_type', 'in')}
                                            className={`h-10 rounded-lg text-sm font-medium border flex items-center justify-center gap-2 transition-all ${
                                                data.movement_type === 'in' 
                                                ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500' 
                                                : 'bg-background border-border text-muted-foreground hover:bg-secondary'
                                            }`}
                                        >
                                            <ArrowDownRight className="h-4 w-4" /> Retour (Entrée)
                                        </button>
                                        <button 
                                            type="button"
                                            onClick={() => setData('movement_type', 'out')}
                                            className={`h-10 rounded-lg text-sm font-medium border flex items-center justify-center gap-2 transition-all ${
                                                data.movement_type === 'out' 
                                                ? 'bg-amber-500/10 border-amber-500/30 text-amber-500' 
                                                : 'bg-background border-border text-muted-foreground hover:bg-secondary'
                                            }`}
                                        >
                                            <ArrowUpRight className="h-4 w-4" /> Prêt (Sortie)
                                        </button>
                                    </div>
                                    {errors.movement_type && <span className="text-xs text-rose-500">{errors.movement_type}</span>}
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Client concerné *</label>
                                    <select 
                                        required 
                                        value={data.client_id} 
                                        onChange={e => setData('client_id', e.target.value)}
                                        className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                    >
                                        <option value="">Sélectionner un client...</option>
                                        {clients.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                                    </select>
                                    {errors.client_id && <span className="text-xs text-rose-500">{errors.client_id}</span>}
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <div className="grid gap-1.5">
                                        <label className="text-sm font-medium">Type d'emballage *</label>
                                        <select 
                                            required 
                                            value={data.packaging_type_id} 
                                            onChange={e => setData('packaging_type_id', e.target.value)}
                                            className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        >
                                            <option value="">Choisir...</option>
                                            {packagingTypes.map(p => <option key={p.id} value={p.id}>{p.name}</option>)}
                                        </select>
                                        {errors.packaging_type_id && <span className="text-xs text-rose-500">{errors.packaging_type_id}</span>}
                                    </div>
                                    <div className="grid gap-1.5">
                                        <label className="text-sm font-medium">Quantité *</label>
                                        <input 
                                            type="number" min="1" required
                                            value={data.quantity}
                                            onChange={e => setData('quantity', e.target.value)}
                                            className="h-10 w-full rounded-lg border border-input bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                            placeholder="Ex: 10"
                                        />
                                        {errors.quantity && <span className="text-xs text-rose-500">{errors.quantity}</span>}
                                    </div>
                                </div>

                                <div className="grid gap-1.5">
                                    <label className="text-sm font-medium">Note (Optionnel)</label>
                                    <textarea 
                                        rows={2}
                                        value={data.notes}
                                        onChange={e => setData('notes', e.target.value)}
                                        className="w-full rounded-lg border border-input bg-background p-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary resize-none"
                                        placeholder="Commentaire sur l'état..."
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

// Optionnel: On peut utiliser un layout si nécessaire
// ConsignmentIndex.layout = page => <DashboardLayout children={page} />;
