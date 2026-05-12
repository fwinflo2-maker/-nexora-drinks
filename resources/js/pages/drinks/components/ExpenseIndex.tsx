import { Link, usePage, router } from '@inertiajs/react';
import { Plus, Search, Eye, Pencil, Trash2 } from 'lucide-react';
import React, { useState, useEffect } from 'react';
import { Badge } from '@/components/ui/badge';
import type { Team } from '@/types';

export default function ExpenseIndex({ expenses }: { expenses: any }) {
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
            case 'validated': return <Badge className="bg-emerald-500/10 text-emerald-400 border-emerald-500/20">Validée</Badge>;
            case 'draft': return <Badge variant="secondary" className="bg-muted text-muted-foreground border-border">Brouillon</Badge>;
            case 'cancelled': return <Badge className="bg-red-500/10 text-red-400 border-red-500/20">Annulée</Badge>;
            default: return <Badge>{status}</Badge>;
        }
    };

    const formatCurrency = (val: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);
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
                        placeholder="Rechercher (réf, type)..."
                        className="w-full pl-9 pr-3 py-2 bg-card border border-border rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-violet-500/50 transition-shadow"
                    />
                </div>
                <Link
                    href={route('drinks.expenses.create', { current_team: team.slug })}
                    className="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                >
                    <Plus className="h-4 w-4" /> Nouvelle Charge
                </Link>
            </div>

            <div className="bg-card border border-border rounded-xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/30">
                            <tr className="border-b border-border">
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Référence</th>
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Date</th>
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Type de Charge</th>
                                <th className="px-5 py-3.5 text-right text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Montant</th>
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Statut</th>
                                <th className="px-5 py-3.5 text-right text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/50">
                            {expenses?.data?.length === 0 ? (
                                <tr><td colSpan={6} className="py-12 text-center text-muted-foreground">Aucune charge trouvée.</td></tr>
                            ) : (
                                expenses?.data?.map((expense: any) => (
                                    <tr 
                                        key={expense.id} 
                                        onClick={(e) => {
                                            if ((e.target as HTMLElement).closest('.actions-cell')) {
return;
}

                                            router.visit(route('drinks.expenses.show', { current_team: team.slug, expense: expense.id }));
                                        }}
                                        className="hover:bg-muted/40 transition-colors group cursor-pointer"
                                    >
                                        <td className="px-5 py-3.5 font-medium text-foreground whitespace-nowrap">{expense.code}</td>
                                        <td className="px-5 py-3.5 text-muted-foreground whitespace-nowrap">{expense.document_date}</td>
                                        <td className="px-5 py-3.5 text-muted-foreground">{expense.type?.name || '—'}</td>
                                        <td className="px-5 py-3.5 text-right font-medium text-foreground whitespace-nowrap">{formatCurrency(expense.amount)}</td>
                                        <td className="px-5 py-3.5 whitespace-nowrap">{getStatusBadge(expense.status)}</td>
                                        <td className="px-5 py-3.5 actions-cell">
                                            <div className="flex items-center justify-end gap-1 opacity-100 sm:opacity-40 group-hover:opacity-100 transition-opacity">
                                                <Link
                                                    href={route('drinks.expenses.show', { current_team: team.slug, expense: expense.id })}
                                                    className="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                    title="Détails"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>
                                                {expense.status === 'draft' && (
                                                    <>
                                                        <Link
                                                            href={route('drinks.expenses.edit', { current_team: team.slug, expense: expense.id })}
                                                            className="p-2 text-muted-foreground hover:text-violet-500 hover:bg-violet-500/10 rounded-lg transition-colors"
                                                            title="Modifier"
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Link>
                                                        <button
                                                            onClick={(e) => {
                                                                e.stopPropagation();

                                                                if (confirm('Supprimer cette charge ?')) {
                                                                    router.delete(route('drinks.expenses.destroy', { current_team: team.slug, expense: expense.id }));
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
            {expenses?.links && expenses.links.length > 3 && (
                <div className="flex justify-center mt-2">
                    <div className="flex gap-1 bg-card p-1 rounded-lg border border-border">
                        {expenses.links.map((link: any, i: number) => (
                            <Link
                                key={i}
                                href={link.url || '#'}
                                className={`px-3 py-1.5 text-sm rounded-md transition-colors ${
                                    link.active
                                        ? 'bg-violet-600 text-white font-medium'
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
