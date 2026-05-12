import { Link, usePage, router } from '@inertiajs/react';
import { ArrowLeft, Wallet, Printer, FileText, User, CheckCircle2, XCircle, Trash2 } from 'lucide-react';
import React from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

export default function PaymentShow({ payment }: { payment: any }) {
    const { currentTeam, auth } = usePage().props;
    const team = currentTeam as Team;
    const user = (auth as any).user;

    const formatCurrency = (val: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);
    };

    const getPaymentMethodLabel = (method: string) => {
        switch (method) {
            case 'cash': return 'Espèces';
            case 'card': return 'Carte bancaire';
            case 'transfer': return 'Virement bancaire';
            case 'mobile_money': return 'Mobile Money';
            case 'cheque': return 'Chèque';
            default: return method;
        }
    };

    const handleValidate = () => {
        if (confirm('Voulez-vous vraiment valider ce règlement ?')) {
            router.post(route('drinks.payments.validate', { current_team: team.slug, payment: payment.id }));
        }
    };

    const handleCancelValidation = () => {
        if (confirm('Voulez-vous annuler la validation de ce règlement ?')) {
            router.post(route('drinks.payments.cancel-validation', { current_team: team.slug, payment: payment.id }));
        }
    };

    const isDraft = payment.status === 'draft';
    const isValidated = payment.status === 'validated';

    return (
        <div className="flex flex-col space-y-6 p-6 max-w-4xl mx-auto">
            {/* Header Actions */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-border/50 pb-4">
                <Button variant="ghost" className="-ml-4 text-muted-foreground hover:bg-muted" onClick={() => window.history.back()}>
                    <ArrowLeft className="mr-2 h-4 w-4" /> Retour aux règlements
                </Button>

                <div className="flex items-center gap-2 flex-wrap">
                    {isDraft && (
                        <Button 
                            variant="default" 
                            className="bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-600/20"
                            onClick={handleValidate}
                        >
                            <CheckCircle2 className="mr-2 h-4 w-4" /> Valider le règlement
                        </Button>
                    )}

                    {isDraft && (
                        <Button 
                            variant="outline"
                            className="text-rose-600 border-rose-600/20 hover:bg-rose-50"
                            onClick={() => {
                                if (confirm('Supprimer ce brouillon ?')) {
                                    router.delete(route('drinks.payments.destroy', { current_team: team.slug, payment: payment.id }));
                                }
                            }}
                        >
                            <Trash2 className="mr-2 h-4 w-4" /> Supprimer
                        </Button>
                    )}

                    {isValidated && (
                        <Button 
                            variant="outline" 
                            className="text-amber-600 border-amber-600/20 hover:bg-amber-50 hover:border-amber-600/50"
                            onClick={handleCancelValidation}
                        >
                            <XCircle className="mr-2 h-4 w-4" /> Annuler validation
                        </Button>
                    )}

                    <Button variant="outline" asChild className="hover:bg-muted text-cyan-600 border-cyan-600/20 hover:border-cyan-600/50">
                        <a href={route('drinks.payments.pdf', { current_team: team.slug, payment: payment.id })} target="_blank" rel="noopener noreferrer">
                            <Printer className="mr-2 h-4 w-4" /> Imprimer le reçu
                        </a>
                    </Button>
                </div>
            </div>

            {/* Document details */}
            <div className="bg-card border border-cyan-500/20 rounded-xl p-6 shadow-sm relative overflow-hidden">
                <div className="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                    <Wallet className="w-32 h-32" />
                </div>
                
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 relative z-10">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground flex items-center gap-3">
                            Règlement #{payment.code}
                            {isDraft ? (
                                <Badge variant="outline" className="text-amber-500 border-amber-500/20 bg-amber-500/5 px-3 py-1">Brouillon</Badge>
                            ) : (
                                <Badge className="bg-cyan-500/10 text-cyan-500 border-cyan-500/20 px-3 py-1 text-sm">Validé</Badge>
                            )}
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Enregistré le {new Date(payment.document_date).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                        </p>
                    </div>
                    <div className="text-right">
                        <p className="text-sm text-muted-foreground uppercase tracking-wider mb-1">Montant Encaissé</p>
                        <p className="text-3xl font-bold text-cyan-500">+{formatCurrency(payment.amount)}</p>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 relative z-10 border-t border-border/50 pt-6">
                    <div className="space-y-4">
                        <div>
                            <div className="flex items-center gap-2 text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-2">
                                <User className="w-4 h-4" /> Client
                            </div>
                            <p className="text-lg font-medium text-foreground">{payment.client?.name || 'Client de passage'}</p>
                            {payment.client?.phone && <p className="text-sm text-muted-foreground">{payment.client.phone}</p>}
                        </div>
                        
                        <div>
                            <div className="flex items-center gap-2 text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-2 mt-6">
                                <FileText className="w-4 h-4" /> Vente associée
                            </div>
                            {payment.sale ? (
                                <div className="p-3 bg-muted/20 border border-border/50 rounded-lg">
                                    <div className="flex items-center justify-between">
                                        <span className="font-medium">{payment.sale.code}</span>
                                        <span className="text-sm text-muted-foreground">TTC: {formatCurrency(payment.sale.total_ttc)}</span>
                                    </div>
                                    <Link href={route('drinks.sales.show', { current_team: team.slug, sale: payment.sale.id })} className="text-xs text-cyan-500 hover:underline mt-2 inline-block">
                                        Voir la facture →
                                    </Link>
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground italic">Aucune vente spécifique liée (Acompte / Avance).</p>
                            )}
                        </div>
                    </div>

                    <div>
                        <div className="flex items-center gap-2 text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-2">
                            Méthode de paiement
                        </div>
                        <p className="text-lg font-medium text-foreground">{getPaymentMethodLabel(payment.mode)}</p>
                        
                        <div className="flex items-center gap-2 text-sm font-semibold text-muted-foreground uppercase tracking-wider mb-2 mt-6">
                            Observation
                        </div>
                        <p className="text-sm text-foreground bg-muted/20 p-3 rounded-lg border border-border/50">
                            {payment.observation || <span className="text-muted-foreground italic">Aucune observation fournie.</span>}
                        </p>
                    </div>
                </div>
                
                <div className="grid grid-cols-2 gap-4 relative z-10 border-t border-border/50 pt-6 mt-6">
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Encaissé par</p>
                        <p className="text-sm font-medium">{payment.creator?.name || '—'}</p>
                    </div>
                    {payment.validated_by && (
                        <div>
                            <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Validé par</p>
                            <p className="text-sm font-medium">{payment.validator?.name || '—'}</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
