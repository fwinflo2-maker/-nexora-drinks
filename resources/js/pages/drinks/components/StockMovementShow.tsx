import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, TrendingUp, TrendingDown, RefreshCcw, Calendar, Box, Info } from 'lucide-react';
import React from 'react';
import type { Team } from '@/types';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

export default function StockMovementShow({ movement }: { movement: any }) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;

    if (!movement) {
        return (
            <div className="p-8 text-center space-y-4">
                <p className="text-muted-foreground italic">Chargement du mouvement...</p>
                <Button variant="outline" onClick={() => window.history.back()}>
                    <ArrowLeft className="mr-2 h-4 w-4" /> Retour
                </Button>
            </div>
        );
    }

    const getMovementBadge = (type: string) => {
        if (!type) return <Badge variant="outline">Inconnu</Badge>;
        
        switch (type.toLowerCase()) {
            case 'in':
            case 'entry':
                return <Badge className="bg-emerald-500/10 text-emerald-500 border-emerald-500/20 gap-1"><TrendingUp className="h-3 w-3" /> Entrée de Stock</Badge>;
            case 'out':
            case 'exit':
                return <Badge className="bg-rose-500/10 text-rose-500 border-rose-500/20 gap-1"><TrendingDown className="h-3 w-3" /> Sortie de Stock</Badge>;
            default:
                return <Badge className="bg-amber-500/10 text-amber-500 border-amber-500/20 gap-1"><RefreshCcw className="h-3 w-3" /> Ajustement</Badge>;
        }
    };

    return (
        <div className="p-6 max-w-3xl mx-auto space-y-6">
            <Button variant="ghost" className="mb-4 -ml-4 text-muted-foreground hover:text-foreground transition-colors" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour à l'historique
            </Button>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Main Card */}
                <div className="md:col-span-2 space-y-6">
                    <div className="bg-card border border-border rounded-2xl p-6 shadow-sm space-y-6">
                        <div className="flex items-start justify-between">
                            <div className="space-y-1">
                                <p className="text-[10px] font-bold uppercase tracking-[0.2em] text-muted-foreground">Détails du mouvement</p>
                                <h2 className="text-2xl font-black tracking-tight flex items-center gap-3">
                                    Mouvement #{movement.id}
                                </h2>
                            </div>
                            {getMovementBadge(movement.type)}
                        </div>

                        <div className="grid grid-cols-2 gap-8 py-6 border-y border-border/50">
                            <div className="space-y-1">
                                <p className="text-xs text-muted-foreground flex items-center gap-2"><Calendar className="h-3 w-3" /> Date & Heure</p>
                                <p className="font-semibold text-foreground">
                                    {movement.created_at ? new Date(movement.created_at).toLocaleString('fr-FR') : '-'}
                                </p>
                            </div>
                            <div className="space-y-1">
                                <p className="text-xs text-muted-foreground flex items-center gap-2"><Box className="h-3 w-3" /> Quantité impactée</p>
                                <p className={`text-xl font-black ${movement.type === 'in' ? 'text-emerald-500' : 'text-rose-500'}`}>
                                    {movement.type === 'in' ? '+' : '-'}{movement.quantity}
                                </p>
                            </div>
                        </div>

                        <div className="space-y-4 pt-4">
                            <div className="flex items-center gap-3 p-4 bg-muted/30 rounded-xl">
                                <div className="h-10 w-10 bg-background border border-border rounded-lg flex items-center justify-center shadow-sm">
                                    <Box className="h-5 w-5 text-amber-500" />
                                </div>
                                <div>
                                    <p className="text-xs text-muted-foreground font-medium">Article concerné</p>
                                    <p className="font-bold text-foreground text-lg">{movement.article?.name || 'Article inconnu'}</p>
                                </div>
                            </div>

                            {movement.observation && (
                                <div className="space-y-2 p-4 border border-border/50 rounded-xl bg-background/50">
                                    <p className="text-xs text-muted-foreground flex items-center gap-2 font-semibold uppercase tracking-wider">
                                        <Info className="h-3 w-3" /> Note d'observation
                                    </p>
                                    <p className="text-sm leading-relaxed text-foreground/80">{movement.observation}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Info Sidebar */}
                <div className="space-y-6">
                    <div className="bg-card border border-border rounded-2xl p-5 shadow-sm space-y-4">
                        <h3 className="text-sm font-bold flex items-center gap-2">
                            <RefreshCcw className="h-4 w-4 text-amber-500" /> Origine
                        </h3>
                        <div className="space-y-3">
                            <div className="p-3 bg-muted/50 rounded-lg space-y-1 border border-border/50">
                                <p className="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">Type de source</p>
                                <p className="text-sm font-medium">{movement.movable_type ? movement.movable_type.split('\\').pop() : 'Manuel'}</p>
                            </div>
                            {movement.movable_id && (
                                <div className="p-3 bg-muted/50 rounded-lg space-y-1 border border-border/50">
                                    <p className="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">ID Document</p>
                                    <p className="text-sm font-mono font-bold">#{movement.movable_id}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
