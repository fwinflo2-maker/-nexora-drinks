import { Link, usePage, router } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, XCircle, Printer, Calendar, FileText, Package, AlertTriangle, Trash2 } from 'lucide-react';
import React from 'react';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

type LossLine = {
    id: number;
    article: { name: string };
    quantity: number;
    cost_price: number;
};

type Loss = {
    id: number;
    code: string;
    document_date: string;
    reason: string;
    observation: string;
    status: string;
    total_amount: number;
    lines: LossLine[];
};

export default function LossShow({ loss }: { loss: Loss }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const slug = team.slug;

    const isDraft = loss.status.toLowerCase() === 'draft';
    const isValidated = loss.status.toLowerCase() === 'validated';

    const handleValidate = () => {
        if (confirm('Voulez-vous vraiment valider cette perte ? Cela réduira les stocks.')) {
            router.post(route('drinks.losses.validate', { current_team: team.slug, loss: loss.id }));
        }
    };

    const handleCancel = () => {
        if (confirm('Annuler la validation ? Cela réintégrera les stocks.')) {
            router.post(route('drinks.losses.cancel-validation', { current_team: team.slug, loss: loss.id }));
        }
    };

    const formatCurrency = (val: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);
    };

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
            {/* Header / Actions */}
            <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white border border-border p-8 rounded-3xl shadow-sm">
                <div className="flex items-center gap-5">
                    <Link
                        href={route('drinks.losses.index', { current_team: team.slug })}
                        className="w-12 h-12 bg-muted rounded-2xl flex items-center justify-center text-muted-foreground hover:bg-rose-500 hover:text-white transition-all shadow-sm"
                    >
                        <ArrowLeft className="h-6 w-6" />
                    </Link>
                    <div>
                        <div className="flex items-center gap-3 mb-1">
                            <h2 className="text-2xl font-black text-foreground tracking-tight">Perte #{loss.code}</h2>
                            <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border ${
                                isDraft ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'
                            }`}>
                                {isDraft ? 'Brouillon' : 'Validé'}
                            </span>
                        </div>
                        <p className="text-sm text-muted-foreground flex items-center gap-2">
                            <Calendar className="h-4 w-4" />
                            Déclarée le {new Date(loss.document_date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })}
                        </p>
                    </div>
                </div>

                <div className="flex flex-wrap items-center gap-3">
                    {isDraft && (
                        <Button
                            onClick={handleValidate}
                            className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold h-11 px-6 rounded-2xl shadow-lg shadow-emerald-500/20 gap-2 transition-all active:scale-95"
                        >
                            <CheckCircle className="h-5 w-5" />
                            Valider la perte
                        </Button>
                    )}
                    {isValidated && (
                        <Button
                            onClick={handleCancel}
                            variant="ghost"
                            className="text-rose-500 hover:bg-rose-500/10 font-bold h-11 px-6 rounded-2xl gap-2"
                        >
                            <XCircle className="h-5 w-5" />
                            Annuler validation
                        </Button>
                    )}
                    {isDraft && (
                        <Button
                            variant="ghost"
                            className="text-rose-500 hover:bg-rose-500/10 font-bold h-11 px-6 rounded-2xl gap-2"
                            onClick={() => {
                                if (confirm('Voulez-vous supprimer ce brouillon ?')) {
                                    router.delete(route('drinks.losses.destroy', { current_team: team.slug, loss: loss.id }));
                                }
                            }}
                        >
                            <Trash2 className="h-5 w-5" />
                            Supprimer
                        </Button>
                    )}
                    <Button variant="outline" className="h-11 px-6 rounded-2xl gap-2 font-bold border-2" onClick={() => window.print()}>
                        <Printer className="h-5 w-5" />
                        Imprimer
                    </Button>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Details Card */}
                <div className="lg:col-span-1 space-y-8">
                    <div className="bg-white border border-border rounded-3xl p-8 shadow-sm space-y-6">
                        <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2">
                            <FileText className="h-4 w-4" /> Informations
                        </h3>
                        
                        <div className="space-y-4">
                            <div>
                                <label className="text-[10px] font-bold text-muted-foreground uppercase tracking-wider block mb-1">Motif</label>
                                <p className="text-sm font-bold text-foreground bg-muted/30 p-3 rounded-xl border border-border/50">
                                    {loss.reason || 'Non spécifié'}
                                </p>
                            </div>
                            <div>
                                <label className="text-[10px] font-bold text-muted-foreground uppercase tracking-wider block mb-1">Observation</label>
                                <div className="text-sm text-muted-foreground bg-muted/30 p-4 rounded-xl border border-border/50 italic leading-relaxed">
                                    {loss.observation || 'Aucune observation particulière.'}
                                </div>
                            </div>
                        </div>

                        <div className="pt-6 border-t border-border flex flex-col items-center justify-center py-4 bg-rose-500/5 rounded-2xl border-dashed border-rose-500/20">
                            <span className="text-[10px] font-bold text-rose-500 uppercase tracking-[0.2em] mb-1">Valeur Totale Perdue</span>
                            <span className="text-3xl font-black text-rose-600 tracking-tighter">
                                {formatCurrency(loss.total_amount)}
                            </span>
                        </div>
                    </div>

                    {isDraft && (
                        <div className="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 flex gap-4">
                            <AlertTriangle className="h-6 w-6 text-amber-500 shrink-0" />
                            <p className="text-xs text-amber-700 leading-relaxed font-medium">
                                Cette perte est encore en mode <strong>Brouillon</strong>. 
                                Les stocks ne seront impactés qu'après la validation finale.
                            </p>
                        </div>
                    )}
                </div>

                {/* Lines Card */}
                <div className="lg:col-span-2">
                    <div className="bg-white border border-border rounded-3xl shadow-sm overflow-hidden">
                        <div className="p-8 border-b border-border flex items-center justify-between">
                            <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2">
                                <Package className="h-4 w-4" /> Articles Impactés
                            </h3>
                            <span className="px-3 py-1 bg-muted rounded-full text-[10px] font-bold text-muted-foreground">
                                {loss.lines.length} ligne(s)
                            </span>
                        </div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm text-left">
                                <thead className="bg-muted/30 border-b border-border">
                                    <tr>
                                        <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Désignation</th>
                                        <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-center">Quantité</th>
                                        <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right">Valeur Unitaire</th>
                                        <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right">Total Ligne</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border/40">
                                    {loss.lines.map((line) => (
                                        <tr key={line.id} className="hover:bg-muted/10 transition-colors">
                                            <td className="px-8 py-5 font-bold text-foreground">
                                                {line.article?.name}
                                            </td>
                                            <td className="px-8 py-5 text-center tabular-nums font-black text-rose-500">
                                                {line.quantity}
                                            </td>
                                            <td className="px-8 py-5 text-right tabular-nums text-muted-foreground">
                                                {formatCurrency(line.cost_price)}
                                            </td>
                                            <td className="px-8 py-5 text-right tabular-nums font-bold text-foreground">
                                                {formatCurrency(line.quantity * line.cost_price)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div className="grid grid-cols-2 gap-4 relative z-10 border-t border-border/50 pt-6 mt-6">
                    <div>
                        <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Créée par</p>
                        <p className="text-sm font-medium">{loss.creator?.name || '—'}</p>
                    </div>
                    {loss.validated_by && (
                        <div>
                            <p className="text-xs text-muted-foreground uppercase tracking-wider mb-1">Validée par</p>
                            <p className="text-sm font-medium">{loss.validator?.name || '—'}</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
