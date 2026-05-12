import { Link, usePage } from '@inertiajs/react';
import { Camera, Calendar, ArrowLeft, Package, Search, Download, TrendingUp, TrendingDown } from 'lucide-react';
import React, { useState } from 'react';
import type { Team } from '@/types';

type SnapshotDetail = {
    id: number;
    snapshot_date: string;
    stock_qty: number;
    cost_price: number;
    article: {
        id: number;
        name: string;
        code: string;
    } | null;
};

type StockSnapshotShowProps = {
    snapshots: SnapshotDetail[];
    date: string;
};

export default function StockSnapshotShow({ snapshots, date }: StockSnapshotShowProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const slug = team.slug;

    const [search, setSearch] = useState('');

    const filtered = snapshots.filter(s => 
        s.article?.name.toLowerCase().includes(search.toLowerCase()) ||
        s.article?.code.toLowerCase().includes(search.toLowerCase())
    );

    const totalValue = snapshots.reduce((acc, s) => acc + (s.stock_qty * s.cost_price), 0);

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
            {/* Header section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div className="flex items-center gap-4">
                    <Link 
                        href={route('drinks.stock-snapshots.index', { current_team: slug })}
                        className="w-12 h-12 bg-card border border-border rounded-2xl flex items-center justify-center text-muted-foreground hover:text-foreground hover:border-indigo-500/50 transition-all shadow-sm"
                    >
                        <ArrowLeft className="h-5 w-5" />
                    </Link>
                    <div>
                        <div className="flex items-center gap-2 text-xs font-bold text-indigo-500 uppercase tracking-widest mb-1">
                            <Camera className="h-3 w-3" />
                            Instantané du
                        </div>
                        <h2 className="text-3xl font-black text-foreground tracking-tight">
                            {new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })}
                        </h2>
                    </div>
                </div>

                <div className="flex items-center gap-3">
                    <div className="bg-card border border-border px-5 py-3 rounded-2xl flex items-center gap-4">
                        <div className="text-right">
                            <p className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest leading-none mb-1">Valeur Totale</p>
                            <p className="text-lg font-black text-emerald-500">
                                {new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(totalValue)}
                            </p>
                        </div>
                        <div className="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center">
                            <TrendingUp className="h-5 w-5 text-emerald-500" />
                        </div>
                    </div>
                </div>
            </div>

            {/* Filters & Content */}
            <div className="bg-white border border-border rounded-3xl p-6 shadow-sm">
                <div className="flex items-center justify-between mb-8 gap-4">
                    <div className="relative flex-1 max-w-md">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <input
                            type="text"
                            placeholder="Rechercher un article..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full pl-11 pr-4 py-3 bg-card border border-border/60 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm outline-none"
                        />
                    </div>
                    <div className="hidden md:flex items-center gap-2 text-sm text-muted-foreground font-medium">
                        <Package className="h-4 w-4" />
                        {filtered.length} articles archivés
                    </div>
                </div>

                <div className="overflow-hidden rounded-2xl border border-border shadow-sm">
                    <table className="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr className="bg-muted/50 border-b border-border">
                                <th className="px-6 py-4 font-bold text-muted-foreground uppercase tracking-wider text-[10px]">Article</th>
                                <th className="px-6 py-4 font-bold text-muted-foreground uppercase tracking-wider text-[10px] text-right">Quantité Archivée</th>
                                <th className="px-6 py-4 font-bold text-muted-foreground uppercase tracking-wider text-[10px] text-right">C.M.P.</th>
                                <th className="px-6 py-4 font-bold text-muted-foreground uppercase tracking-wider text-[10px] text-right">Valeur</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/40">
                            {filtered.map((s) => (
                                <tr key={s.id} className="hover:bg-muted/30 transition-colors group">
                                    <td className="px-6 py-4">
                                        <div className="flex flex-col">
                                            <span className="font-bold text-foreground group-hover:text-indigo-600 transition-colors">
                                                {s.article?.name || 'Article inconnu'}
                                            </span>
                                            <span className="text-[10px] text-muted-foreground font-mono">{s.article?.code}</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-black ${s.stock_qty > 0 ? 'bg-indigo-500/10 text-indigo-500' : 'bg-rose-500/10 text-rose-500'}`}>
                                            {s.stock_qty.toLocaleString()}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right font-medium text-muted-foreground">
                                        {new Intl.NumberFormat('fr-FR').format(s.cost_price)}
                                    </td>
                                    <td className="px-6 py-4 text-right font-black text-foreground">
                                        {new Intl.NumberFormat('fr-FR').format(s.stock_qty * s.cost_price)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {filtered.length === 0 && (
                    <div className="flex flex-col items-center justify-center py-20 text-muted-foreground space-y-4">
                        <Search className="h-12 w-12 opacity-20" />
                        <p className="text-sm italic">Aucun article ne correspond à votre recherche.</p>
                    </div>
                )}
            </div>
        </div>
    );
}
