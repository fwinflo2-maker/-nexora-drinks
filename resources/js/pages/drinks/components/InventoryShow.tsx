import { Link, usePage, router } from '@inertiajs/react';
import { ArrowLeft, Pencil, FileText, CheckCircle, XCircle, Trash2 } from 'lucide-react';
import React from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

export default function InventoryShow({ inventory }: { inventory: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const handleAction = (actionRoute: string) => {
        if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
            return;
        }

        router.post(route(actionRoute, { current_team: team.slug, inventory: inventory.id }));
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'validated': return <Badge className="bg-emerald-500/10 text-emerald-400 border-emerald-500/20 px-3 py-1 text-sm"><CheckCircle className="w-4 h-4 mr-1.5" /> Validé</Badge>;
            case 'draft': return <Badge variant="secondary" className="bg-muted text-muted-foreground border-border px-3 py-1 text-sm">Brouillon</Badge>;
            case 'cancelled': return <Badge className="bg-red-500/10 text-red-400 border-red-500/20 px-3 py-1 text-sm"><XCircle className="w-4 h-4 mr-1.5" /> Annulé</Badge>;
            default: return <Badge>{status}</Badge>;
        }
    };

    return (
        <div className="flex flex-col space-y-6 p-6 max-w-5xl mx-auto">
            {/* Header Actions */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-border/50 pb-4">
                <Button variant="ghost" className="-ml-4 text-muted-foreground hover:bg-muted" onClick={() => window.history.back()}>
                    <ArrowLeft className="mr-2 h-4 w-4" /> Retour aux inventaires
                </Button>

                <div className="flex items-center gap-2 flex-wrap">
                    {inventory.status === 'draft' && (
                        <Button onClick={() => handleAction('drinks.inventories.validate')} className="bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                            <CheckCircle className="mr-2 h-4 w-4" /> Valider l'inventaire
                        </Button>
                    )}
                    {inventory.status === 'validated' && (
                        <Button onClick={() => handleAction('drinks.inventories.cancel-validation')} variant="outline" className="text-amber-600 hover:text-amber-700 border-amber-500/30 hover:bg-amber-500/10">
                            <XCircle className="mr-2 h-4 w-4" /> Annuler validation
                        </Button>
                    )}
                    {inventory.status === 'draft' && (
                        <Button 
                            variant="outline"
                            className="text-rose-600 border-rose-600/20 hover:bg-rose-50"
                            onClick={() => {
                                if (confirm('Supprimer ce brouillon ?')) {
                                    router.delete(route('drinks.inventories.destroy', { current_team: team.slug, inventory: inventory.id }));
                                }
                            }}
                        >
                            <Trash2 className="mr-2 h-4 w-4" /> Supprimer
                        </Button>
                    )}
                    {inventory.status === 'draft' && (
                        <Button asChild className="bg-amber-500 hover:bg-amber-600 text-white shadow-sm">
                            <Link href={route('drinks.inventories.edit', { current_team: team.slug, inventory: inventory.id })}>
                                <Pencil className="mr-2 h-4 w-4" /> Modifier
                            </Link>
                        </Button>
                    )}
                </div>
            </div>

            {/* Document Header */}
            <div className="bg-card border border-border rounded-xl p-6 shadow-sm">
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground flex items-center gap-3">
                            Inventaire #{inventory.code}
                            {getStatusBadge(inventory.status)}
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Enregistré le {new Date(inventory.document_date).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                        </p>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Créé par</p>
                        <p className="text-sm font-medium text-foreground">{inventory.creator?.name || '—'}</p>
                    </div>
                    <div className="lg:col-span-2">
                        <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Observation</p>
                        <p className="text-sm text-muted-foreground italic">{inventory.observation || 'Aucune observation.'}</p>
                    </div>
                </div>
            </div>

            {/* Lines Table */}
            <div className="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-border bg-muted/10">
                    <h2 className="text-lg font-semibold text-foreground flex items-center gap-2">
                        <FileText className="h-5 w-5 text-amber-500" /> Détail du comptage
                    </h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-muted/30 text-muted-foreground uppercase text-xs tracking-wider">
                            <tr>
                                <th className="px-6 py-4 font-semibold">Article</th>
                                <th className="px-6 py-4 font-semibold text-right">Stock (Système)</th>
                                <th className="px-6 py-4 font-semibold text-right">Qté Comptée (Physique)</th>
                                <th className="px-6 py-4 font-semibold text-right">Écart</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/50">
                            {inventory.lines?.length === 0 ? (
                                <tr>
                                    <td colSpan={4} className="px-6 py-8 text-center text-muted-foreground">Aucune ligne de comptage.</td>
                                </tr>
                            ) : (
                                inventory.lines?.map((line: any) => {
                                    // Normally the backend should return theoretical stock at the time of inventory.
                                    // If it's missing, we try to use the current stock from the relationship.
                                    const theorical = line.expected_qty ?? line.article?.stock_qty ?? 0;
                                    const ecart = line.counted_qty - theorical;

                                    return (
                                        <tr key={line.id} className="hover:bg-muted/20 transition-colors">
                                            <td className="px-6 py-4">
                                                <p className="font-medium text-foreground">{line.article?.name}</p>
                                                <p className="text-xs text-muted-foreground">{line.article?.code}</p>
                                            </td>
                                            <td className="px-6 py-4 text-right text-muted-foreground">{theorical}</td>
                                            <td className="px-6 py-4 text-right font-medium">{line.counted_qty}</td>
                                            <td className={`px-6 py-4 text-right font-bold ${ecart < 0 ? 'text-destructive' : ecart > 0 ? 'text-emerald-500' : 'text-muted-foreground'}`}>
                                                {ecart > 0 ? `+${ecart}` : ecart}
                                            </td>
                                        </tr>
                                    );
                                })
                            )}
                        </tbody>
                    </table>
                </div>
                <div className="grid grid-cols-2 gap-4 relative z-10 border-t border-border/50 pt-6 mt-6">
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Créé par</p>
                        <p className="text-sm font-medium">{inventory.creator?.name || '—'}</p>
                    </div>
                    {inventory.validated_by && (
                        <div>
                            <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Validé par</p>
                            <p className="text-sm font-medium">{inventory.validator?.name || '—'}</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
