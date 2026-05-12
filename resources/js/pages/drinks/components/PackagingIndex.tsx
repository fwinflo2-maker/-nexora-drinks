import { Link, usePage, router } from '@inertiajs/react';
import { Plus, Search, Edit, Package, Boxes, CheckCircle2, XCircle, MoreVertical, Trash2 } from 'lucide-react';
import React, { useState } from 'react';
import type { Team } from '@/types';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

export default function PackagingIndex({ packagings }: { packagings: any[] }) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;
    const slug = team.slug;
    
    const items = Array.isArray(packagings) ? packagings : (packagings as any).data || [];
    const [searchQuery, setSearchQuery] = useState('');

    const filteredItems = items.filter((item: any) => 
        item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.code.toLowerCase().includes(searchQuery.toLowerCase())
    );

    const formatCurrency = (val: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);

    const handleDelete = (id: number) => {
        if (confirm('Supprimer cet emballage ? Cela pourrait affecter les articles liés.')) {
            router.delete(route('drinks.packagings.destroy', { current_team: slug, packaging: id }));
        }
    };

    return (
        <div className="space-y-8 animate-in fade-in duration-700">
            {/* Header section */}
            <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white border border-border rounded-3xl p-8 shadow-sm">
                <div className="flex items-center gap-5">
                    <div className="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shadow-inner">
                        <Boxes className="h-8 w-8" />
                    </div>
                    <div>
                        <h2 className="text-2xl font-black text-foreground tracking-tight">Consignes & Emballages</h2>
                        <p className="text-sm text-muted-foreground">Gérez vos bouteilles, casiers et types de conditionnement.</p>
                    </div>
                </div>
                
                <div className="flex flex-col sm:flex-row items-center gap-4">
                    <div className="relative w-full sm:w-64">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <input
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Rechercher un emballage..."
                            className="w-full pl-10 pr-4 py-3 bg-muted/30 border-transparent rounded-2xl text-sm focus:ring-2 focus:ring-amber-500/20 transition-all outline-none"
                        />
                    </div>
                    <Link
                        href={route('drinks.packagings.create', { current_team: slug })}
                        className="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl transition-all shadow-lg shadow-amber-500/20 active:scale-95"
                    >
                        <Plus className="h-5 w-5" /> Nouveau
                    </Link>
                </div>
            </div>

            {/* List */}
            <div className="bg-white border border-border rounded-3xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-muted/30 border-b border-border">
                            <tr>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Code</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Désignation</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Prix Consigne</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Articles Liés</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-center">Statut</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/40">
                            {filteredItems.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-8 py-20 text-center">
                                        <div className="flex flex-col items-center justify-center space-y-3">
                                            <div className="w-16 h-16 bg-muted rounded-full flex items-center justify-center text-muted-foreground opacity-20">
                                                <Boxes className="h-8 w-8" />
                                            </div>
                                            <p className="text-muted-foreground font-medium italic">Aucun emballage trouvé.</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : (
                                filteredItems.map((item: any) => (
                                    <tr key={item.id} className="hover:bg-muted/10 transition-colors group">
                                        <td className="px-8 py-5 font-bold text-muted-foreground">#{item.code}</td>
                                        <td className="px-8 py-5">
                                            <div className="flex flex-col">
                                                <span className="font-bold text-foreground text-base">{item.name}</span>
                                                <span className="text-[10px] text-muted-foreground uppercase font-medium">
                                                    {item.packs_per_unit} unités par casier
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-8 py-5 font-black text-amber-500 text-base">
                                            {formatCurrency(item.deposit_price)}
                                        </td>
                                        <td className="px-8 py-5">
                                            <div className="flex items-center gap-2">
                                                <div className="px-2.5 py-1 bg-indigo-500/10 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-tighter border border-indigo-500/20">
                                                    {item.articles_count || 0} Articles
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-8 py-5 text-center">
                                            {item.is_active ? (
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase tracking-tighter border border-emerald-500/20">
                                                    <CheckCircle2 className="w-3 h-3"/> Actif
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 text-rose-600 text-[10px] font-black uppercase tracking-tighter border border-rose-500/20">
                                                    <XCircle className="w-3 h-3"/> Inactif
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-8 py-5 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <Link
                                                    href={route('drinks.packagings.edit', { current_team: slug, packaging: item.id })}
                                                    className="w-9 h-9 flex items-center justify-center bg-muted hover:bg-amber-500 hover:text-white rounded-xl transition-all shadow-sm"
                                                >
                                                    <Edit className="h-4 w-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(item.id)}
                                                    className="w-9 h-9 flex items-center justify-center bg-muted hover:bg-rose-500 hover:text-white rounded-xl transition-all shadow-sm"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
