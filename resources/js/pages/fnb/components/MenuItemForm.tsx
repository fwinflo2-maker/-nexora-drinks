import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface Category {
    id: number;
    name: string;
}

interface MenuItem {
    id: number;
    name: string;
    description: string | null;
    price: number;
    cost_price: number | null;
    sku: string | null;
    is_available: boolean;
    category_id: number;
}

interface Props {
    _action: 'create' | 'edit';
    item?: MenuItem;
    categories: Category[];
}

export default function MenuItemForm({ _action, item, categories }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug;

    const [form, setForm] = useState({
        category_id: String(item?.category_id ?? categories[0]?.id ?? ''),
        name: item?.name ?? '',
        description: item?.description ?? '',
        price: String(item?.price ?? ''),
        cost_price: String(item?.cost_price ?? ''),
        sku: item?.sku ?? '',
        is_available: item?.is_available ?? true,
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);

    const field = (key: keyof typeof form) => (
        (value: string | boolean) => setForm(f => ({ ...f, [key]: value }))
    );

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);
        setErrors({});

        const data = {
            category_id: parseInt(form.category_id),
            name: form.name,
            description: form.description || null,
            price: parseFloat(form.price) || 0,
            cost_price: form.cost_price ? parseFloat(form.cost_price) : null,
            sku: form.sku || null,
            is_available: form.is_available,
        };

        if (_action === 'edit' && item) {
            router.put(
                route('fnb.menu-items.update', { current_team: slug, menuItem: item.id }),
                data,
                {
                    onError: setErrors,
                    onFinish: () => setProcessing(false),
                }
            );
        } else {
            router.post(
                route('fnb.menu-items.store', { current_team: slug }),
                data,
                {
                    onError: setErrors,
                    onFinish: () => setProcessing(false),
                }
            );
        }
    };

    return (
        <form onSubmit={submit} className="max-w-2xl space-y-6">
            <div className="bg-card border border-border rounded-xl p-6 space-y-5">
                <div className="space-y-1.5">
                    <Label htmlFor="cat">Catégorie *</Label>
                    <select
                        id="cat"
                        value={form.category_id}
                        onChange={e => field('category_id')(e.target.value)}
                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                    >
                        {categories.map(c => (
                            <option key={c.id} value={String(c.id)}>{c.name}</option>
                        ))}
                    </select>
                    {errors.category_id && <p className="text-xs text-red-500">{errors.category_id}</p>}
                </div>

                <div className="space-y-1.5">
                    <Label htmlFor="iname">Nom *</Label>
                    <Input
                        id="iname"
                        value={form.name}
                        onChange={e => field('name')(e.target.value)}
                        placeholder="Coca-Cola"
                    />
                    {errors.name && <p className="text-xs text-red-500">{errors.name}</p>}
                </div>

                <div className="space-y-1.5">
                    <Label htmlFor="desc">Description</Label>
                    <Textarea
                        id="desc"
                        value={form.description}
                        onChange={e => field('description')(e.target.value)}
                        rows={3}
                        placeholder="Description optionnelle..."
                    />
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1.5">
                        <Label htmlFor="price">Prix de vente *</Label>
                        <Input
                            id="price"
                            type="number"
                            step="0.01"
                            min="0"
                            value={form.price}
                            onChange={e => field('price')(e.target.value)}
                            placeholder="0.00"
                        />
                        {errors.price && <p className="text-xs text-red-500">{errors.price}</p>}
                    </div>
                    <div className="space-y-1.5">
                        <Label htmlFor="cost">Prix de revient</Label>
                        <Input
                            id="cost"
                            type="number"
                            step="0.01"
                            min="0"
                            value={form.cost_price}
                            onChange={e => field('cost_price')(e.target.value)}
                            placeholder="0.00"
                        />
                    </div>
                </div>

                <div className="space-y-1.5">
                    <Label htmlFor="sku">SKU / Référence</Label>
                    <Input
                        id="sku"
                        value={form.sku}
                        onChange={e => field('sku')(e.target.value)}
                        placeholder="COKE-33CL"
                    />
                </div>

                <label className="flex items-center gap-2 text-sm cursor-pointer">
                    <input
                        type="checkbox"
                        checked={form.is_available}
                        onChange={e => field('is_available')(e.target.checked)}
                        className="rounded"
                    />
                    Disponible à la vente
                </label>
            </div>

            <div className="flex gap-3">
                <Button type="button" variant="outline" onClick={() => router.visit(route('fnb.menu-items.index', { current_team: slug }))}>
                    Annuler
                </Button>
                <Button type="submit" disabled={processing || !form.name || !form.price}>
                    {_action === 'edit' ? 'Mettre à jour' : 'Créer l\'article'}
                </Button>
            </div>
        </form>
    );
}
