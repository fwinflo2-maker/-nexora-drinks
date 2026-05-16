import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Plus, Pencil, Trash2, Tag } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface Category {
    id: number;
    name: string;
    color: string | null;
    icon: string | null;
    sort_order: number;
    is_active: boolean;
    menu_items_count?: number;
}

interface Props {
    categories: Category[];
}

interface CategoryFormData {
    name: string;
    color: string;
    icon: string;
    sort_order: string;
    is_active: boolean;
}

const DEFAULT_FORM: CategoryFormData = {
    name: '', color: '#10b981', icon: '', sort_order: '0', is_active: true,
};

export default function CategoryIndex({ categories }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug;

    const [showForm, setShowForm] = useState(false);
    const [editCat, setEditCat] = useState<Category | null>(null);
    const [form, setForm] = useState<CategoryFormData>(DEFAULT_FORM);
    const [processing, setProcessing] = useState(false);

    const openCreate = () => {
        setEditCat(null);
        setForm(DEFAULT_FORM);
        setShowForm(true);
    };

    const openEdit = (c: Category) => {
        setEditCat(c);
        setForm({
            name: c.name,
            color: c.color ?? '#10b981',
            icon: c.icon ?? '',
            sort_order: String(c.sort_order),
            is_active: c.is_active,
        });
        setShowForm(true);
    };

    const close = () => {
        setShowForm(false);
        setEditCat(null);
        setForm(DEFAULT_FORM);
    };

    const submit = () => {
        setProcessing(true);
        const data = {
            name: form.name,
            color: form.color || null,
            icon: form.icon || null,
            sort_order: parseInt(form.sort_order) || 0,
            is_active: form.is_active,
        };

        if (editCat) {
            router.put(route('fnb.categories.update', { current_team: slug, category: editCat.id }), data, {
                onSuccess: close,
                onFinish: () => setProcessing(false),
            });
        } else {
            router.post(route('fnb.categories.store', { current_team: slug }), data, {
                onSuccess: close,
                onFinish: () => setProcessing(false),
            });
        }
    };

    const destroy = (c: Category) => {
        if (!confirm(`Supprimer la catégorie "${c.name}" ?`)) return;
        router.delete(route('fnb.categories.destroy', { current_team: slug, category: c.id }), { preserveScroll: true });
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <p className="text-sm text-muted-foreground">{categories.length} catégorie{categories.length !== 1 ? 's' : ''}</p>
                <Button size="sm" onClick={openCreate}>
                    <Plus className="h-3.5 w-3.5 mr-1.5" />
                    Nouvelle catégorie
                </Button>
            </div>

            {showForm && (
                <motion.div
                    initial={{ opacity: 0, y: -8 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="bg-card border border-border rounded-xl p-5 space-y-4"
                >
                    <h3 className="text-sm font-semibold text-foreground">
                        {editCat ? 'Modifier la catégorie' : 'Nouvelle catégorie'}
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div className="space-y-1.5 sm:col-span-2 lg:col-span-1">
                            <Label htmlFor="cname">Nom</Label>
                            <Input
                                id="cname"
                                value={form.name}
                                onChange={e => setForm(f => ({ ...f, name: e.target.value }))}
                                placeholder="Boissons"
                            />
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="ccolor">Couleur</Label>
                            <div className="flex gap-2 items-center">
                                <input
                                    id="ccolor"
                                    type="color"
                                    value={form.color}
                                    onChange={e => setForm(f => ({ ...f, color: e.target.value }))}
                                    className="h-9 w-9 rounded-md border border-input cursor-pointer"
                                />
                                <Input
                                    value={form.color}
                                    onChange={e => setForm(f => ({ ...f, color: e.target.value }))}
                                    className="flex-1"
                                />
                            </div>
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="cicon">Icône (texte)</Label>
                            <Input
                                id="cicon"
                                value={form.icon}
                                onChange={e => setForm(f => ({ ...f, icon: e.target.value }))}
                                placeholder="🍹"
                            />
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="csort">Ordre</Label>
                            <Input
                                id="csort"
                                type="number"
                                min={0}
                                value={form.sort_order}
                                onChange={e => setForm(f => ({ ...f, sort_order: e.target.value }))}
                            />
                        </div>
                    </div>
                    <label className="flex items-center gap-2 text-sm cursor-pointer">
                        <input
                            type="checkbox"
                            checked={form.is_active}
                            onChange={e => setForm(f => ({ ...f, is_active: e.target.checked }))}
                            className="rounded"
                        />
                        Active
                    </label>
                    <div className="flex gap-2 justify-end">
                        <Button variant="outline" size="sm" onClick={close}>Annuler</Button>
                        <Button size="sm" disabled={processing || !form.name} onClick={submit}>
                            {editCat ? 'Mettre à jour' : 'Créer'}
                        </Button>
                    </div>
                </motion.div>
            )}

            {categories.length === 0 ? (
                <div className="text-center py-12 text-muted-foreground text-sm">
                    Aucune catégorie. Créez-en une pour commencer.
                </div>
            ) : (
                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    {categories.map((c) => (
                        <motion.div
                            key={c.id}
                            initial={{ opacity: 0, scale: 0.97 }}
                            animate={{ opacity: 1, scale: 1 }}
                            className="bg-card border border-border rounded-xl p-4 flex flex-col gap-2 relative group"
                        >
                            <div className="flex items-center gap-2">
                                {c.icon ? (
                                    <span className="text-xl">{c.icon}</span>
                                ) : (
                                    <div
                                        className="h-8 w-8 rounded-lg flex items-center justify-center"
                                        style={{ backgroundColor: `${c.color}20`, color: c.color ?? undefined }}
                                    >
                                        <Tag className="h-4 w-4" />
                                    </div>
                                )}
                                <p className="font-semibold text-foreground text-sm truncate">{c.name}</p>
                            </div>
                            <div className="flex items-center justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {c.menu_items_count ?? 0} article{(c.menu_items_count ?? 0) !== 1 ? 's' : ''}
                                </span>
                                {!c.is_active && (
                                    <span className="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full">Inactive</span>
                                )}
                            </div>
                            <div className="absolute top-2 right-2 hidden group-hover:flex gap-1">
                                <button
                                    onClick={() => openEdit(c)}
                                    className="h-6 w-6 rounded-md bg-muted flex items-center justify-center hover:bg-muted/80 transition-colors"
                                >
                                    <Pencil className="h-3 w-3 text-muted-foreground" />
                                </button>
                                <button
                                    onClick={() => destroy(c)}
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
