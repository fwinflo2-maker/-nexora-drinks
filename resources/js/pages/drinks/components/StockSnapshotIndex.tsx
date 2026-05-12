import { Link, router, usePage } from '@inertiajs/react';
import { Camera, Calendar, ArrowRight, Package, Archive, Database } from 'lucide-react';
import React from 'react';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

type SnapshotDate = {
    snapshot_date: string;
    article_count: number;
    total_stock_qty: number;
    last_updated: string;
};

type StockSnapshotIndexProps = {
    dates: {
        data: SnapshotDate[];
        links: any[];
    };
};

export default function StockSnapshotIndex({ dates }: StockSnapshotIndexProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const slug = team.slug;

    const handleManualSnapshot = () => {
        router.post(route('drinks.stock-snapshots.store', { current_team: slug }));
    };

    return (
        <div className="space-y-8 animate-in fade-in duration-700">
            {/* Header section with Stats & Action */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl p-8 text-white shadow-xl shadow-indigo-500/20 relative overflow-hidden group">
                    <div className="relative z-10 flex flex-col h-full justify-between">
                        <div>
                            <div className="flex items-center gap-3 mb-6">
                                <div className="p-3 bg-white/10 backdrop-blur-md rounded-2xl">
                                    <Camera className="h-6 w-6 text-indigo-100" />
                                </div>
                                <h2 className="text-3xl font-black tracking-tight">Instantanés de Stock</h2>
                            </div>
                            <p className="text-indigo-100 text-sm max-w-md leading-relaxed">
                                Les snapshots capturent l'état exact de votre stock quotidiennement. 
                                Ils permettent un suivi historique précis pour vos inventaires et rapports financiers.
                            </p>
                        </div>
                        <div className="mt-8 flex items-center gap-4">
                            <Button 
                                onClick={handleManualSnapshot}
                                className="bg-white text-indigo-700 hover:bg-indigo-50 font-bold px-6 py-6 rounded-2xl shadow-lg border-0 transition-all hover:scale-105 active:scale-95"
                            >
                                <Database className="mr-2 h-5 w-5" />
                                Capturer l'état actuel
                            </Button>
                        </div>
                    </div>
                    <Database className="absolute -right-20 -bottom-20 h-80 w-80 text-white/5 rotate-12 transition-transform group-hover:scale-110 duration-1000" />
                </div>

                <div className="bg-card border border-border/60 rounded-3xl p-8 flex flex-col justify-center items-center text-center space-y-4">
                    <div className="w-16 h-16 bg-amber-500/10 rounded-full flex items-center justify-center">
                        <Archive className="h-8 w-8 text-amber-500" />
                    </div>
                    <div>
                        <p className="text-4xl font-black text-foreground">{dates.data.length}</p>
                        <p className="text-sm font-bold text-muted-foreground uppercase tracking-widest mt-1">Archives Disponibles</p>
                    </div>
                </div>
            </div>

            {/* List of snapshots */}
            <div className="bg-white border border-border rounded-3xl p-6 shadow-sm">
                <div className="flex items-center justify-between mb-8 px-2">
                    <h3 className="text-lg font-bold flex items-center gap-2">
                        <Calendar className="h-5 w-5 text-indigo-500" />
                        Historique des captures
                    </h3>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    {dates.data.map((row) => (
                        <Link
                            key={row.snapshot_date}
                            href={route('drinks.stock-snapshots.show', { current_team: slug, date: row.snapshot_date })}
                            className="group relative bg-card border border-border/60 p-5 rounded-2xl hover:border-indigo-500/50 hover:shadow-xl hover:shadow-indigo-500/5 transition-all"
                        >
                            <div className="flex items-center justify-between">
                                <div className="space-y-1">
                                    <p className="text-sm font-bold text-muted-foreground uppercase tracking-tighter">
                                        {new Date(row.snapshot_date).toLocaleDateString('fr-FR', { weekday: 'long' })}
                                    </p>
                                    <p className="text-xl font-black text-foreground">
                                        {new Date(row.snapshot_date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })}
                                    </p>
                                    <p className="text-[10px] text-indigo-500 font-bold">
                                        Mis à jour à {new Date(row.last_updated).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}
                                    </p>
                                </div>
                                <div className="p-2 bg-muted rounded-xl group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                    <ArrowRight className="h-5 w-5" />
                                </div>
                            </div>

                            <div className="mt-6 flex items-center gap-6 pt-6 border-t border-border/40">
                                <div className="flex flex-col">
                                    <span className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Articles</span>
                                    <span className="text-sm font-black text-foreground">{row.article_count}</span>
                                </div>
                                <div className="flex flex-col">
                                    <span className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Stock Total</span>
                                    <span className="text-sm font-black text-indigo-500">{row.total_stock_qty.toLocaleString()}</span>
                                </div>
                            </div>
                        </Link>
                    ))}
                </div>

                {dates.data.length === 0 && (
                    <div className="flex flex-col items-center justify-center py-20 text-muted-foreground space-y-4">
                        <div className="w-20 h-20 bg-muted rounded-full flex items-center justify-center">
                            <Camera className="h-10 w-10 opacity-20" />
                        </div>
                        <div className="text-center">
                            <p className="text-lg font-bold">Aucun snapshot trouvé</p>
                            <p className="text-sm opacity-60">Déclenchez votre premier snapshot manuel pour commencer l'historique.</p>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
