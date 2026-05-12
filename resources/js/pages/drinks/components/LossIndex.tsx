import { Link, usePage } from '@inertiajs/react';
import { Plus, Eye, TrendingDown, Calendar, AlertCircle } from 'lucide-react';
import React from 'react';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

type Loss = {
    id: number;
    code: string;
    document_date: string;
    status: string;
    total_amount: number;
};

type LossIndexProps = {
    losses: {
        data: Loss[];
        links: any[];
    };
};

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    draft: { label: 'Brouillon', color: 'bg-amber-500/10 text-amber-500 border-amber-500/20' },
    validated: { label: 'Validé', color: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' },
};

export default function LossIndex({ losses }: LossIndexProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const slug = team.slug;
    const items = losses.data || [];

    const formatCurrency = (val: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);
    };

    return (
        <div className="space-y-8 animate-in fade-in duration-700">
            {/* Header section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white border border-border rounded-3xl p-8 shadow-sm">
                <div className="flex items-center gap-5">
                    <div className="w-14 h-14 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 shadow-inner">
                        <TrendingDown className="h-8 w-8" />
                    </div>
                    <div>
                        <h2 className="text-2xl font-black text-foreground tracking-tight">Déclaration de Pertes</h2>
                        <p className="text-sm text-muted-foreground">Suivez et documentez les casses, périmés ou vols de stock.</p>
                    </div>
                </div>
                <Link
                    href={route('drinks.losses.create', { current_team: slug })}
                    className="inline-flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-bold h-12 px-6 rounded-2xl shadow-lg shadow-rose-500/20 transition-all active:scale-95"
                >
                    <Plus className="h-5 w-5" /> Déclarer une perte
                </Link>
            </div>

            {/* List */}
            <div className="bg-white border border-border rounded-3xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-muted/30 border-b border-border">
                            <tr>
                                <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Code & Date</th>
                                <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right">Valeur Estimée</th>
                                <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-center">Statut</th>
                                <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/40">
                            {items.length === 0 ? (
                                <tr>
                                    <td colSpan={4} className="px-6 py-20 text-center">
                                        <div className="flex flex-col items-center justify-center space-y-4">
                                            <div className="w-16 h-16 bg-muted rounded-full flex items-center justify-center text-muted-foreground opacity-20">
                                                <AlertCircle className="h-8 w-8" />
                                            </div>
                                            <p className="text-muted-foreground font-medium italic">Aucune perte enregistrée.</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : items.map((item) => {
                                const status = STATUS_MAP[item.status.toLowerCase()] || { label: item.status, color: 'bg-muted' };

                                return (
                                    <tr key={item.id} className="hover:bg-muted/20 transition-colors group">
                                        <td className="px-6 py-5">
                                            <div className="flex flex-col">
                                                <span className="font-bold text-foreground">#{item.code}</span>
                                                <div className="flex items-center gap-1.5 text-xs text-muted-foreground">
                                                    <Calendar className="h-3 w-3" />
                                                    {new Date(item.document_date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-5 text-right font-black text-foreground">
                                            {formatCurrency(item.total_amount)}
                                        </td>
                                        <td className="px-6 py-5 text-center">
                                            <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border ${status.color}`}>
                                                {status.label}
                                            </span>
                                        </td>
                                        <td className="px-6 py-5 text-right">
                                            <Link 
                                                href={route('drinks.losses.show', { current_team: slug, loss: item.id })}
                                                className="inline-flex items-center justify-center h-10 w-10 bg-muted hover:bg-rose-500 hover:text-white rounded-xl transition-all shadow-sm"
                                            >
                                                <Eye className="h-4 w-4" />
                                            </Link>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
