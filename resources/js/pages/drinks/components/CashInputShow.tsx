import { Link, usePage, router } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, XCircle, ArrowDownCircle, Trash2 } from 'lucide-react';
import React from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

export default function CashInputShow({ cashInput }: { cashInput: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const handleAction = (actionRoute: string) => {
        if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
return;
}

        router.post(route(actionRoute, { current_team: team.slug, id: cashInput.id }));
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'validated': return <Badge className="bg-emerald-500/10 text-emerald-400 border-emerald-500/20 px-3 py-1 text-sm"><CheckCircle className="w-4 h-4 mr-1.5" /> Validé</Badge>;
            case 'draft': return <Badge variant="secondary" className="bg-muted text-muted-foreground border-border px-3 py-1 text-sm">Brouillon</Badge>;
            case 'cancelled': return <Badge className="bg-red-500/10 text-red-400 border-red-500/20 px-3 py-1 text-sm"><XCircle className="w-4 h-4 mr-1.5" /> Annulé</Badge>;
            default: return <Badge>{status}</Badge>;
        }
    };

    const formatCurrency = (val: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);
    };

    return (
        <div className="flex flex-col space-y-6 p-6 max-w-4xl mx-auto">
            {/* Header Actions */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-border/50 pb-4">
                <Button variant="ghost" className="-ml-4 text-muted-foreground hover:bg-muted" onClick={() => window.history.back()}>
                    <ArrowLeft className="mr-2 h-4 w-4" /> Retour aux apports
                </Button>

                <div className="flex items-center gap-2 flex-wrap">
                    {cashInput.status === 'draft' && (
                        <Button onClick={() => handleAction('drinks.cash-inputs.validate')} className="bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                            <CheckCircle className="mr-2 h-4 w-4" /> Valider l'apport
                        </Button>
                    )}
                    {cashInput.status === 'draft' && (
                        <Button 
                            variant="outline"
                            className="text-rose-600 border-rose-600/20 hover:bg-rose-50"
                            onClick={() => {
                                if (confirm('Supprimer ce brouillon ?')) {
                                    router.delete(route('drinks.cash-inputs.destroy', { current_team: team.slug, cashInput: cashInput.id }));
                                }
                            }}
                        >
                            <Trash2 className="mr-2 h-4 w-4" /> Supprimer
                        </Button>
                    )}
                    {cashInput.status === 'validated' && (
                        <Button onClick={() => handleAction('drinks.cash-inputs.cancel-validation')} variant="outline" className="text-amber-600 hover:text-amber-700 border-amber-500/30 hover:bg-amber-500/10">
                            <XCircle className="mr-2 h-4 w-4" /> Annuler validation
                        </Button>
                    )}
                </div>
            </div>

            {/* Document details */}
            <div className="bg-card border border-emerald-500/20 rounded-xl p-6 shadow-sm relative overflow-hidden">
                <div className="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                    <ArrowDownCircle className="w-32 h-32" />
                </div>
                
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 relative z-10">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground flex items-center gap-3">
                            Apport #{cashInput.code}
                            {getStatusBadge(cashInput.status)}
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Enregistré le {new Date(cashInput.document_date).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                        </p>
                    </div>
                    <div className="text-right">
                        <p className="text-sm text-muted-foreground uppercase tracking-wider mb-1">Montant apporté</p>
                        <p className="text-3xl font-bold text-emerald-500">+{formatCurrency(cashInput.amount)}</p>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 relative z-10 border-t border-border/50 pt-6">
                    <div>
                        <div className="flex items-center gap-2 text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-2">
                            Source des fonds
                        </div>
                        <p className="text-lg font-medium text-foreground">{cashInput.source || 'Non spécifiée'}</p>
                    </div>
                    <div>
                        <div className="flex items-center gap-2 text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-2">
                            Observation
                        </div>
                        <p className="text-sm text-foreground bg-muted/20 p-3 rounded-lg border border-border/50">
                            {cashInput.observation || <span className="text-muted-foreground italic">Aucune observation fournie.</span>}
                        </p>
                    </div>
                </div>
                
                <div className="grid grid-cols-2 gap-4 relative z-10 border-t border-border/50 pt-6 mt-6">
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Créé par</p>
                        <p className="text-sm font-medium">{cashInput.creator?.name || '—'}</p>
                    </div>
                    {cashInput.validated_by && (
                        <div>
                            <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Validé par</p>
                            <p className="text-sm font-medium">{cashInput.validator?.name || '—'}</p>
                        </div>
                    )}
                </div>

            </div>
        </div>
    );
}
