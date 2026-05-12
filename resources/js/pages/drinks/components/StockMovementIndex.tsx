import { Link, usePage } from '@inertiajs/react';
import { Eye, ArrowUpDown, TrendingUp, TrendingDown, RefreshCcw } from 'lucide-react';
import React from 'react';
import type { Team } from '@/types';
import { Badge } from '@/components/ui/badge';

export default function StockMovementIndex({ stockMovements }: { stockMovements: any }) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;
    
    // Safety check for items
    const items = Array.isArray(stockMovements) 
        ? stockMovements 
        : (stockMovements?.data || []);

    const getMovementBadge = (type: string) => {
        if (!type) return <Badge variant="outline">Inconnu</Badge>;
        
        switch (type.toLowerCase()) {
            case 'in':
            case 'entry':
            case 'addition':
                return <Badge className="bg-emerald-500/10 text-emerald-500 border-emerald-500/20 gap-1"><TrendingUp className="h-3 w-3" /> Entrée</Badge>;
            case 'out':
            case 'exit':
            case 'removal':
                return <Badge className="bg-rose-500/10 text-rose-500 border-rose-500/20 gap-1"><TrendingDown className="h-3 w-3" /> Sortie</Badge>;
            case 'adjustment':
                return <Badge className="bg-amber-500/10 text-amber-500 border-amber-500/20 gap-1"><RefreshCcw className="h-3 w-3" /> Ajustement</Badge>;
            default:
                return <Badge variant="outline">{type}</Badge>;
        }
    };

    if (!team) {
        return <div className="p-8 text-center text-muted-foreground">Erreur : Équipe non identifiée.</div>;
    }

    return (
        <div className="p-6 space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-xl font-bold text-foreground">Mouvements de Stock</h2>
                    <p className="text-sm text-muted-foreground">Historique complet des entrées et sorties d'articles.</p>
                </div>
            </div>
            
            <div className="rounded-xl border border-border bg-card overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/30 border-b border-border">
                            <tr>
                                <th className="h-12 px-4 text-left font-semibold text-muted-foreground uppercase tracking-wider text-[11px]">Date</th>
                                <th className="h-12 px-4 text-left font-semibold text-muted-foreground uppercase tracking-wider text-[11px]">Article</th>
                                <th className="h-12 px-4 text-left font-semibold text-muted-foreground uppercase tracking-wider text-[11px]">Type</th>
                                <th className="h-12 px-4 text-right font-semibold text-muted-foreground uppercase tracking-wider text-[11px]">Quantité</th>
                                <th className="h-12 px-4 text-left font-semibold text-muted-foreground uppercase tracking-wider text-[11px]">Source</th>
                                <th className="h-12 px-4 text-right font-semibold text-muted-foreground uppercase tracking-wider text-[11px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/50">
                            {items.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="p-12 text-center text-muted-foreground italic">Aucun mouvement trouvé.</td>
                                </tr>
                            ) : items.map((item: any) => (
                                <tr key={item.id} className="hover:bg-muted/20 transition-colors group">
                                    <td className="p-4 align-middle whitespace-nowrap text-muted-foreground">
                                        {item.created_at ? new Date(item.created_at).toLocaleString('fr-FR', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        }) : '-'}
                                    </td>
                                    <td className="p-4 align-middle font-medium text-foreground">{item.article?.name || 'Article inconnu'}</td>
                                    <td className="p-4 align-middle">{getMovementBadge(item.type)}</td>
                                    <td className={`p-4 align-middle text-right font-bold ${item.type === 'in' ? 'text-emerald-500' : 'text-rose-500'}`}>
                                        {item.type === 'in' ? '+' : '-'}{item.quantity}
                                    </td>
                                    <td className="p-4 align-middle text-xs text-muted-foreground capitalize">
                                        {item.movable_type ? item.movable_type.split('\\').pop() : 'Manuel'}
                                    </td>
                                    <td className="p-4 align-middle text-right">
                                        <Link 
                                            href={route('drinks.stock-movements.show', { current_team: team.slug, stock_movement: item.id })} 
                                            className="inline-flex items-center justify-center h-8 w-8 text-muted-foreground hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-all"
                                        >
                                            <Eye className="h-4 w-4" />
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Pagination */}
            {stockMovements?.links && stockMovements.links.length > 3 && (
                <div className="flex justify-center mt-6">
                    <div className="flex gap-1 bg-card p-1 rounded-lg border border-border shadow-sm">
                        {stockMovements.links.map((link: any, i: number) => (
                            <Link
                                key={i}
                                href={link.url || '#'}
                                className={`px-3 py-1.5 text-xs rounded-md transition-all ${
                                    link.active
                                        ? 'bg-amber-500 text-white font-bold shadow-md shadow-amber-500/20'
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
