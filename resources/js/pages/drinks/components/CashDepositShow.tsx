import { Link, usePage, router } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, XCircle, ArrowUpCircle, Building, Printer, Trash2 } from 'lucide-react';
import React from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

export default function CashDepositShow({ cashDeposit }: { cashDeposit: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const handleAction = (actionRoute: string) => {
        if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
            return;
        }

        router.post(route(actionRoute, { current_team: team.slug, id: cashDeposit.id }));
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
                    <ArrowLeft className="mr-2 h-4 w-4" /> Retour aux versements
                </Button>

                <div className="flex items-center gap-2 flex-wrap">
                    <Button variant="outline" asChild className="hover:bg-muted">
                        <a href={route('drinks.cash-deposits.pdf', { current_team: team.slug, id: cashDeposit.id })} target="_blank" rel="noopener noreferrer">
                            <Printer className="mr-2 h-4 w-4" /> Imprimer le reçu
                        </a>
                    </Button>

                    {cashDeposit.status === 'draft' && (
                        <Button onClick={() => handleAction('drinks.cash-deposits.validate')} className="bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                            <CheckCircle className="mr-2 h-4 w-4" /> Valider le versement
                        </Button>
                    )}
                    {cashDeposit.status === 'draft' && (
                        <Button 
                            variant="outline"
                            className="text-rose-600 border-rose-600/20 hover:bg-rose-50"
                            onClick={() => {
                                if (confirm('Supprimer ce brouillon ?')) {
                                    router.delete(route('drinks.cash-deposits.destroy', { current_team: team.slug, cashDeposit: cashDeposit.id }));
                                }
                            }}
                        >
                            <Trash2 className="mr-2 h-4 w-4" /> Supprimer
                        </Button>
                    )}
                    {cashDeposit.status === 'validated' && (
                        <Button onClick={() => handleAction('drinks.cash-deposits.cancel-validation')} variant="outline" className="text-amber-600 hover:text-amber-700 border-amber-500/30 hover:bg-amber-500/10">
                            <XCircle className="mr-2 h-4 w-4" /> Annuler validation
                        </Button>
                    )}
                </div>
            </div>

            {/* Document details */}
            <div className="bg-card border border-blue-500/20 rounded-xl p-6 shadow-sm relative overflow-hidden">
                <div className="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                    <Building className="w-32 h-32" />
                </div>
                
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 relative z-10">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground flex items-center gap-3">
                            Versement #{cashDeposit.code}
                            {getStatusBadge(cashDeposit.status)}
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Enregistré le {new Date(cashDeposit.document_date).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                        </p>
                    </div>
                    <div className="text-right">
                        <p className="text-sm text-muted-foreground uppercase tracking-wider mb-1">Montant Total Versé</p>
                        <p className="text-3xl font-bold text-blue-500">{formatCurrency(cashDeposit.total_amount)}</p>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10 border-t border-border/50 pt-6">
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Banque</p>
                        <p className="text-sm font-medium">{cashDeposit.bank_name || '—'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Référence Bancaire</p>
                        <p className="text-sm font-medium">{cashDeposit.reference || '—'}</p>
                    </div>
                    <div className="lg:col-span-2">
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Observation</p>
                        <p className="text-sm text-muted-foreground italic">{cashDeposit.observation || 'Aucune observation.'}</p>
                    </div>
                </div>

                <div className="bg-muted/10 border border-border/50 rounded-lg p-4 mt-6 relative z-10">
                    <h3 className="text-sm font-semibold uppercase tracking-wider mb-4 text-muted-foreground">Détail des montants</h3>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div className="p-3 bg-card border border-border/50 rounded-md">
                            <p className="text-xs text-muted-foreground uppercase mb-1">Espèces</p>
                            <p className="text-lg font-medium">{formatCurrency(cashDeposit.amount_cash || 0)}</p>
                        </div>
                        <div className="p-3 bg-card border border-border/50 rounded-md">
                            <p className="text-xs text-muted-foreground uppercase mb-1">Chèques</p>
                            <p className="text-lg font-medium">{formatCurrency(cashDeposit.amount_cheque || 0)}</p>
                        </div>
                        <div className="p-3 bg-card border border-border/50 rounded-md">
                            <p className="text-xs text-muted-foreground uppercase mb-1">Autres (Virement, etc.)</p>
                            <p className="text-lg font-medium">{formatCurrency(cashDeposit.amount_other || 0)}</p>
                        </div>
                    </div>
                </div>
                
                <div className="grid grid-cols-2 gap-4 relative z-10 border-t border-border/50 pt-6 mt-6">
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Créé par</p>
                        <p className="text-sm font-medium">{cashDeposit.creator?.name || '—'}</p>
                    </div>
                    {cashDeposit.validated_by && (
                        <div>
                            <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Validé par</p>
                            <p className="text-sm font-medium">{cashDeposit.validator?.name || '—'}</p>
                        </div>
                    )}
                </div>

            </div>
        </div>
    );
}
