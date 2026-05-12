import { Link, usePage, router } from '@inertiajs/react';
import { ArrowLeft, Pencil, FileText, CheckCircle, XCircle, Printer, Download, AlertTriangle, Trash2 } from 'lucide-react';
import React from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

export default function SaleShow({ sale }: { sale: any }) {
    const { currentTeam, errors } = usePage().props as any;
    const team = currentTeam as Team;

    const formatCurrency = (val: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);

    const handleAction = (actionRoute: string) => {
        if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
            return;
        }

        router.post(route(actionRoute, [team.slug, sale.id]));
    };

    const handlePrint = () => {
        // If the sale is still a draft, we validate it first
        if (sale.status === 'draft') {
            router.post(route('drinks.sales.validate', { current_team: team.slug, sale: sale.id }), {}, {
                onSuccess: () => {
                    // After successful validation, trigger the print
                    triggerPrint();
                }
            });
        } else {
            triggerPrint();
        }
    };

    const triggerPrint = () => {
        const url = route('drinks.sales.pdf', { current_team: team.slug, sale: sale.id });
        
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.src = url;
        
        document.body.appendChild(iframe);
        
        iframe.onload = () => {
            iframe.contentWindow?.focus();
            iframe.contentWindow?.print();
            
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
        };
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'validated': return <Badge className="bg-emerald-500/10 text-emerald-400 border-emerald-500/20 px-3 py-1 text-sm"><CheckCircle className="w-4 h-4 mr-1.5" /> Validée</Badge>;
            case 'draft': return <Badge variant="secondary" className="bg-muted text-muted-foreground border-border px-3 py-1 text-sm">Brouillon</Badge>;
            case 'cancelled': return <Badge className="bg-red-500/10 text-red-400 border-red-500/20 px-3 py-1 text-sm"><XCircle className="w-4 h-4 mr-1.5" /> Annulée</Badge>;
            default: return <Badge>{status}</Badge>;
        }
    };

    return (
        <div className="flex flex-col space-y-6 p-6 max-w-5xl mx-auto">
            {errors.sale && (
                <div className="bg-rose-500/10 border border-rose-500/20 text-rose-500 p-4 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                    <AlertTriangle className="h-5 w-5 shrink-0" />
                    <p className="text-sm font-medium">{errors.sale}</p>
                </div>
            )}
            
            {/* Header Actions */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-border/50 pb-4">
                <Button variant="ghost" className="-ml-4 text-muted-foreground hover:bg-muted" onClick={() => window.history.back()}>
                    <ArrowLeft className="mr-2 h-4 w-4" /> Retour aux ventes
                </Button>

                <div className="flex items-center gap-2 flex-wrap">
                    {sale.status === 'draft' && (
                        <Button onClick={() => handleAction('drinks.sales.validate')} className="bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                            <CheckCircle className="mr-2 h-4 w-4" /> Valider la Vente
                        </Button>
                    )}
                    {sale.status === 'validated' && (
                        <Button onClick={() => handleAction('drinks.sales.cancel-validation')} variant="outline" className="text-amber-600 hover:text-amber-700 border-amber-500/30 hover:bg-amber-500/10">
                            <XCircle className="mr-2 h-4 w-4" /> Annuler validation
                        </Button>
                    )}
                    
                    {sale.status === 'draft' && (
                        <Button 
                            variant="outline"
                            className="text-rose-600 border-rose-600/20 hover:bg-rose-50"
                            onClick={() => {
                                if (confirm('Supprimer ce brouillon ?')) {
                                    router.delete(route('drinks.sales.destroy', { current_team: team.slug, sale: sale.id }));
                                }
                            }}
                        >
                            <Trash2 className="mr-2 h-4 w-4" /> Supprimer
                        </Button>
                    )}
                    
                    <Button 
                        variant="outline" 
                        onClick={handlePrint}
                        className="border-border hover:bg-muted"
                    >
                        <Printer className="mr-2 h-4 w-4" /> Imprimer Reçu
                    </Button>
                    
                    {sale.status === 'draft' && (
                        <Button asChild className="bg-amber-500 hover:bg-amber-600 text-white shadow-sm">
                            <Link href={route('drinks.sales.edit', { current_team: team.slug, sale: sale.id })}>
                                <Pencil className="mr-2 h-4 w-4" /> Modifier
                            </Link>
                        </Button>
                    )}
                </div>
            </div>

            {/* Document Header (Invoice Style) */}
            <div className="bg-card border border-border rounded-xl p-6 shadow-sm">
                <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground flex items-center gap-3">
                            Vente #{sale.code}
                            {getStatusBadge(sale.status)}
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Enregistrée le {new Date(sale.document_date).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                        </p>
                    </div>
                    <div className="text-left md:text-right bg-muted/30 p-4 rounded-lg border border-border/50 min-w-[200px]">
                        <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Total TTC</p>
                        <p className="text-3xl font-bold text-emerald-500 tracking-tight">{formatCurrency(sale.total_ttc)}</p>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-2">
                    <div>
                        <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Client</p>
                        <p className="text-sm font-medium text-foreground">{sale.client?.name || 'Client divers (Comptoir)'}</p>
                        {sale.client?.phone && <p className="text-xs text-muted-foreground mt-0.5">{sale.client.phone}</p>}
                    </div>
                    <div>
                        <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Type de vente</p>
                        <p className="text-sm font-medium text-foreground capitalize">{sale.kind || 'Standard'}</p>
                    </div>
                    <div>
                        <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Créé par</p>
                        <p className="text-sm font-medium text-foreground">{sale.creator?.name || '—'}</p>
                    </div>
                    <div className="lg:col-span-1">
                        <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Observation</p>
                        <p className="text-sm text-muted-foreground italic">{sale.observation || 'Aucune observation.'}</p>
                    </div>
                </div>
            </div>

            {/* Lines Table */}
            <div className="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
                <div className="px-6 py-4 border-b border-border bg-muted/10">
                    <h2 className="text-lg font-semibold text-foreground flex items-center gap-2">
                        <FileText className="h-5 w-5 text-amber-500" /> Détail des articles
                    </h2>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-muted/30 text-muted-foreground uppercase text-xs tracking-wider">
                            <tr>
                                <th className="px-6 py-4 font-semibold">Article</th>
                                <th className="px-6 py-4 font-semibold text-center">Quantité</th>
                                <th className="px-6 py-4 font-semibold text-right">Prix Unitaire</th>
                                <th className="px-6 py-4 font-semibold text-right">Montant HT</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/50">
                            {sale.article_lines?.length === 0 ? (
                                <tr>
                                    <td colSpan={4} className="px-6 py-8 text-center text-muted-foreground">Aucune ligne de vente.</td>
                                </tr>
                            ) : (
                                sale.article_lines?.map((line: any) => (
                                    <tr key={line.id} className="hover:bg-muted/20 transition-colors">
                                        <td className="px-6 py-4">
                                            <p className="font-medium text-foreground">{line.article?.name}</p>
                                            <p className="text-xs text-muted-foreground">{line.article?.code}</p>
                                        </td>
                                        <td className="px-6 py-4 text-center font-medium">{line.quantity}</td>
                                        <td className="px-6 py-4 text-right text-muted-foreground">{formatCurrency(line.unit_price)}</td>
                                        <td className="px-6 py-4 text-right font-medium text-foreground">{formatCurrency(line.amount_ht)}</td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                        <tfoot className="bg-muted/10">
                            <tr>
                                <td colSpan={3} className="px-6 py-3 text-right text-sm font-medium text-muted-foreground">Total HT</td>
                                <td className="px-6 py-3 text-right text-sm font-semibold text-foreground">{formatCurrency(sale.total_ht)}</td>
                            </tr>
                            <tr>
                                <td colSpan={3} className="px-6 py-3 text-right text-sm font-medium text-muted-foreground">TVA (19.25%)</td>
                                <td className="px-6 py-3 text-right text-sm font-semibold text-muted-foreground">{formatCurrency(sale.total_ttc - sale.total_ht)}</td>
                            </tr>
                            <tr className="border-t border-border/50 bg-muted/30">
                                <td colSpan={3} className="px-6 py-4 text-right text-base font-bold text-foreground uppercase tracking-wider">Total à payer TTC</td>
                                <td className="px-6 py-4 text-right text-lg font-bold text-emerald-500">{formatCurrency(sale.total_ttc)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div className="grid grid-cols-2 gap-4 relative z-10 border-t border-border/50 pt-6 mt-6">
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Créée par</p>
                        <p className="text-sm font-medium">{sale.creator?.name || '—'}</p>
                    </div>
                    {sale.validated_by && (
                        <div>
                            <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Validée par</p>
                            <p className="text-sm font-medium">{sale.validator?.name || '—'}</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
