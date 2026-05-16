import { Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight, Search } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface Category {
    id: number;
    name: string;
    color: string | null;
}

interface MenuItem {
    id: number;
    name: string;
    description: string | null;
    price: number;
    cost_price: number | null;
    sku: string | null;
    is_available: boolean;
    category: Category | null;
}

interface Props {
    items: MenuItem[];
    categories: Category[];
}

export default function MenuItemIndex({ items, categories }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug;

    const [search, setSearch] = useState('');
    const [filterCat, setFilterCat] = useState('');

    const filtered = items.filter(item => {
        const matchSearch = !search || item.name.toLowerCase().includes(search.toLowerCase());
        const matchCat = !filterCat || String(item.category?.id) === filterCat;
        return matchSearch && matchCat;
    });

    const toggle = (item: MenuItem) => {
        router.post(route('fnb.menu-items.toggle', { current_team: slug, menuItem: item.id }), {}, { preserveScroll: true });
    };

    const destroy = (item: MenuItem) => {
        if (!confirm(`Supprimer "${item.name}" ?`)) return;
        router.delete(route('fnb.menu-items.destroy', { current_team: slug, menuItem: item.id }), { preserveScroll: true });
    };

    return (
        <div className="space-y-4">
            <div className="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                <div className="flex gap-2 flex-1 max-w-sm">
                    <div className="relative flex-1">
                        <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                        <Input
                            className="pl-8"
                            placeholder="Rechercher..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                        />
                    </div>
                    <select
                        value={filterCat}
                        onChange={e => setFilterCat(e.target.value)}
                        className="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                    >
                        <option value="">Toutes catégories</option>
                        {categories.map(c => (
                            <option key={c.id} value={String(c.id)}>{c.name}</option>
                        ))}
                    </select>
                </div>
                <Link href={route('fnb.menu-items.create', { current_team: slug })}>
                    <Button size="sm">
                        <Plus className="h-3.5 w-3.5 mr-1.5" />
                        Nouvel article
                    </Button>
                </Link>
            </div>

            {filtered.length === 0 ? (
                <div className="text-center py-12 text-muted-foreground text-sm">
                    Aucun article trouvé.
                </div>
            ) : (
                <div className="bg-card border border-border rounded-xl overflow-hidden">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border bg-muted/30">
                                <th className="text-left px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider">Article</th>
                                <th className="text-left px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider hidden sm:table-cell">Catégorie</th>
                                <th className="text-right px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider">Prix</th>
                                <th className="text-center px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider">Dispo</th>
                                <th className="px-4 py-3" />
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {filtered.map((item) => (
                                <motion.tr
                                    key={item.id}
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    className={`hover:bg-muted/20 transition-colors ${!item.is_available ? 'opacity-50' : ''}`}
                                >
                                    <td className="px-4 py-3">
                                        <p className="font-medium text-foreground">{item.name}</p>
                                        {item.description && (
                                            <p className="text-xs text-muted-foreground truncate max-w-xs">{item.description}</p>
                                        )}
                                        {item.sku && <p className="text-[10px] font-mono text-muted-foreground/60">{item.sku}</p>}
                                    </td>
                                    <td className="px-4 py-3 hidden sm:table-cell">
                                        {item.category && (
                                            <span
                                                className="text-xs px-2 py-0.5 rounded-full"
                                                style={{
                                                    backgroundColor: `${item.category.color ?? '#10b981'}20`,
                                                    color: item.category.color ?? '#10b981',
                                                }}
                                            >
                                                {item.category.name}
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-4 py-3 text-right font-mono text-foreground">
                                        {Number(item.price).toLocaleString()}
                                    </td>
                                    <td className="px-4 py-3 text-center">
                                        <button onClick={() => toggle(item)} className="text-muted-foreground hover:text-foreground transition-colors">
                                            {item.is_available
                                                ? <ToggleRight className="h-5 w-5 text-emerald-600" />
                                                : <ToggleLeft className="h-5 w-5" />
                                            }
                                        </button>
                                    </td>
                                    <td className="px-4 py-3">
                                        <div className="flex items-center gap-1 justify-end">
                                            <Link href={route('fnb.menu-items.edit', { current_team: slug, menuItem: item.id })}>
                                                <button className="h-7 w-7 rounded-md bg-muted flex items-center justify-center hover:bg-muted/80 transition-colors">
                                                    <Pencil className="h-3.5 w-3.5 text-muted-foreground" />
                                                </button>
                                            </Link>
                                            <button
                                                onClick={() => destroy(item)}
                                                className="h-7 w-7 rounded-md bg-red-50 flex items-center justify-center hover:bg-red-100 transition-colors"
                                            >
                                                <Trash2 className="h-3.5 w-3.5 text-red-500" />
                                            </button>
                                        </div>
                                    </td>
                                </motion.tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}
