import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Plus, Pencil, Trash2, ShoppingBag } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface FnBTable {
    id: number;
    name: string;
    capacity: number;
    status: string;
    active_orders_count?: number;
}

interface Props {
    tables: FnBTable[];
}

const STATUS_STYLES: Record<string, string> = {
    free: 'bg-emerald-100 text-emerald-700',
    occupied: 'bg-amber-100 text-amber-700',
    reserved: 'bg-blue-100 text-blue-700',
    closed: 'bg-slate-100 text-slate-500',
};

const STATUS_LABELS: Record<string, string> = {
    free: 'Libre', occupied: 'Occupée', reserved: 'Réservée', closed: 'Fermée',
};

interface TableFormData {
    name: string;
    capacity: string;
    status: string;
}

const DEFAULT_FORM: TableFormData = { name: '', capacity: '4', status: 'free' };

export default function TableIndex({ tables }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug;

    const [showForm, setShowForm] = useState(false);
    const [editTable, setEditTable] = useState<FnBTable | null>(null);
    const [form, setForm] = useState<TableFormData>(DEFAULT_FORM);
    const [processing, setProcessing] = useState(false);

    const openCreate = () => {
        setEditTable(null);
        setForm(DEFAULT_FORM);
        setShowForm(true);
    };

    const openEdit = (t: FnBTable) => {
        setEditTable(t);
        setForm({ name: t.name, capacity: String(t.capacity), status: t.status });
        setShowForm(true);
    };

    const close = () => {
        setShowForm(false);
        setEditTable(null);
        setForm(DEFAULT_FORM);
    };

    const submit = () => {
        setProcessing(true);
        const data = { name: form.name, capacity: parseInt(form.capacity) || 4, status: form.status };

        if (editTable) {
            router.put(route('fnb.tables.update', { current_team: slug, table: editTable.id }), data, {
                onSuccess: close,
                onFinish: () => setProcessing(false),
            });
        } else {
            router.post(route('fnb.tables.store', { current_team: slug }), data, {
                onSuccess: close,
                onFinish: () => setProcessing(false),
            });
        }
    };

    const destroy = (t: FnBTable) => {
        if (!confirm(`Supprimer la table "${t.name}" ?`)) return;
        router.delete(route('fnb.tables.destroy', { current_team: slug, table: t.id }), { preserveScroll: true });
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <p className="text-sm text-muted-foreground">{tables.length} table{tables.length !== 1 ? 's' : ''}</p>
                <Button size="sm" onClick={openCreate}>
                    <Plus className="h-3.5 w-3.5 mr-1.5" />
                    Nouvelle table
                </Button>
            </div>

            {showForm && (
                <motion.div
                    initial={{ opacity: 0, y: -8 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="bg-card border border-border rounded-xl p-5 space-y-4"
                >
                    <h3 className="text-sm font-semibold text-foreground">
                        {editTable ? 'Modifier la table' : 'Nouvelle table'}
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div className="space-y-1.5">
                            <Label htmlFor="tname">Nom / Numéro</Label>
                            <Input
                                id="tname"
                                value={form.name}
                                onChange={e => setForm(f => ({ ...f, name: e.target.value }))}
                                placeholder="Table 1"
                            />
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="tcap">Capacité</Label>
                            <Input
                                id="tcap"
                                type="number"
                                min={1}
                                value={form.capacity}
                                onChange={e => setForm(f => ({ ...f, capacity: e.target.value }))}
                            />
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="tstat">Statut</Label>
                            <select
                                id="tstat"
                                value={form.status}
                                onChange={e => setForm(f => ({ ...f, status: e.target.value }))}
                                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                            >
                                {Object.entries(STATUS_LABELS).map(([v, l]) => (
                                    <option key={v} value={v}>{l}</option>
                                ))}
                            </select>
                        </div>
                    </div>
                    <div className="flex gap-2 justify-end">
                        <Button variant="outline" size="sm" onClick={close}>Annuler</Button>
                        <Button size="sm" disabled={processing || !form.name} onClick={submit}>
                            {editTable ? 'Mettre à jour' : 'Créer'}
                        </Button>
                    </div>
                </motion.div>
            )}

            {tables.length === 0 ? (
                <div className="text-center py-12 text-muted-foreground text-sm">
                    Aucune table. Créez-en une pour commencer.
                </div>
            ) : (
                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    {tables.map((t) => (
                        <motion.div
                            key={t.id}
                            initial={{ opacity: 0, scale: 0.97 }}
                            animate={{ opacity: 1, scale: 1 }}
                            className="bg-card border border-border rounded-xl p-4 flex flex-col gap-2 relative group"
                        >
                            <div className="flex items-start justify-between">
                                <p className="font-semibold text-foreground text-sm">{t.name}</p>
                                <span className={`text-[10px] font-medium px-1.5 py-0.5 rounded-full ${STATUS_STYLES[t.status] ?? 'bg-slate-100 text-slate-500'}`}>
                                    {STATUS_LABELS[t.status] ?? t.status}
                                </span>
                            </div>
                            <p className="text-xs text-muted-foreground">{t.capacity} couverts</p>
                            {(t.active_orders_count ?? 0) > 0 && (
                                <div className="flex items-center gap-1 text-xs text-amber-600">
                                    <ShoppingBag className="h-3 w-3" />
                                    {t.active_orders_count} commande{(t.active_orders_count ?? 0) > 1 ? 's' : ''}
                                </div>
                            )}
                            <div className="absolute top-2 right-2 hidden group-hover:flex gap-1">
                                <button
                                    onClick={() => openEdit(t)}
                                    className="h-6 w-6 rounded-md bg-muted flex items-center justify-center hover:bg-muted/80 transition-colors"
                                >
                                    <Pencil className="h-3 w-3 text-muted-foreground" />
                                </button>
                                <button
                                    onClick={() => destroy(t)}
                                    className="h-6 w-6 rounded-md bg-red-50 flex items-center justify-center hover:bg-red-100 transition-colors"
                                >
                                    <Trash2 className="h-3 w-3 text-red-500" />
                                </button>
                            </div>
                        </motion.div>
                    ))}
                </div>
            )}
        </div>
    );
}
