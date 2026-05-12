import { Link, usePage, router } from '@inertiajs/react';
import { Plus, Search, Eye, Pencil, Trash2 } from 'lucide-react';
import React, { useState, useEffect } from 'react';
import { Badge } from '@/components/ui/badge';
import type { Team } from '@/types';

export default function InventoryIndex({ inventories }: { inventories: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    
    const [searchQuery, setSearchQuery] = useState(new URLSearchParams(window.location.search).get('search') || '');

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (searchQuery !== (new URLSearchParams(window.location.search).get('search') || '')) {
                router.get(window.location.pathname, { search: searchQuery }, { preserveState: true, replace: true });
            }
        }, 500);

        return () => clearTimeout(timeout);
    }, [searchQuery]);

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'validated': return <Badge className="bg-emerald-500/10 text-emerald-400 border-emerald-500/20">Validé</Badge>;
            case 'draft': return <Badge variant="secondary" className="bg-muted text-muted-foreground border-border">Brouillon</Badge>;
            case 'cancelled': return <Badge className="bg-red-500/10 text-red-400 border-red-500/20">Annulé</Badge>;
            default: return <Badge>{status}</Badge>;
        }
    };

    return (
        <div className="flex flex-col space-y-6 p-6">
            <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div className="relative w-full sm:max-w-xs">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        placeholder="Rechercher (réf)..."
                        className="w-full pl-9 pr-3 py-2 bg-card border border-border rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500/50 transition-shadow"
                    />
                </div>
                <Link
                    href={route('drinks.inventories.create', { current_team: team.slug })}
                    className="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                >
                    <Plus className="h-4 w-4" /> Nouvel Inventaire
                </Link>
            </div>

            <div className="bg-card border border-border rounded-xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/30">
                            <tr className="border-b border-border">
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Référence</th>
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Date</th>
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Statut</th>
                                <th className="px-5 py-3.5 text-right text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/50">
                            {inventories?.data?.length === 0 ? (
                                <tr><td colSpan={4} className="py-12 text-center text-muted-foreground">Aucun inventaire trouvé.</td></tr>
                            ) : (
                                inventories?.data?.map((inv: any) => (
                                    <tr 
                                        key={inv.id} 
                                        onClick={(e) => {
                                            if ((e.target as HTMLElement).closest('.actions-cell')) {
return;
}

                                            router.visit(route('drinks.inventories.show', { current_team: team.slug, inventory: inv.id }));
                                        }}
                                        className="hover:bg-muted/40 transition-colors group cursor-pointer"
                                    >
                                        <td className="px-5 py-3.5 font-medium text-foreground whitespace-nowrap">{inv.code}</td>
                                        <td className="px-5 py-3.5 text-muted-foreground whitespace-nowrap">{inv.document_date}</td>
                                        <td className="px-5 py-3.5 whitespace-nowrap">{getStatusBadge(inv.status)}</td>
                                        <td className="px-5 py-3.5 actions-cell">
                                            <div className="flex items-center justify-end gap-1 opacity-100 sm:opacity-40 group-hover:opacity-100 transition-opacity">
                                                <Link
                                                    href={route('drinks.inventories.show', { current_team: team.slug, inventory: inv.id })}
                                                    className="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                    title="Détails"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>
                                                {inv.status === 'draft' && (
                                                    <>
                                                        <Link
                                                            href={route('drinks.inventories.edit', { current_team: team.slug, inventory: inv.id })}
                                                            className="p-2 text-muted-foreground hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors"
                                                            title="Modifier"
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Link>
                                                        <button
                                                            onClick={(e) => {
                                                                e.stopPropagation();

                                                                if (confirm('Supprimer cet inventaire ?')) {
                                                                    router.delete(route('drinks.inventories.destroy', { current_team: team.slug, inventory: inv.id }));
                                                                }
                                                            }}
                                                            className="p-2 text-muted-foreground hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
                                                            title="Supprimer"
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </button>
                                                    </>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
            
            {/* Pagination */}
            {inventories?.links && inventories.links.length > 3 && (
                <div className="flex justify-center mt-2">
                    <div className="flex gap-1 bg-card p-1 rounded-lg border border-border">
                        {inventories.links.map((link: any, i: number) => (
                            <Link
                                key={i}
                                href={link.url || '#'}
                                className={`px-3 py-1.5 text-sm rounded-md transition-colors ${
                                    link.active
                                        ? 'bg-amber-500 text-white font-medium'
                                        : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                } ${!link.url ? 'opacity-30 cursor-not-allowed pointer-events-none' : ''}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
