import { Link, usePage, router } from '@inertiajs/react';
import { Plus, Search, Eye, Pencil, Trash2, CheckCircle2, XCircle, Package, TrendingUp } from 'lucide-react';
import React, { useState, useEffect } from 'react';
import type { Team } from '@/types';

export default function ArticleIndex({ articles }: { articles: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const slug = team.slug;
    
    const [searchQuery, setSearchQuery] = useState(new URLSearchParams(window.location.search).get('search') || '');

    useEffect(() => {
        const timeout = setTimeout(() => {
            const currentSearch = new URLSearchParams(window.location.search).get('search') || '';

            if (searchQuery !== currentSearch) {
                router.get(route('drinks.articles.index', { current_team: slug }), { search: searchQuery }, { preserveState: true, replace: true });
            }
        }, 500);

        return () => clearTimeout(timeout);
    }, [searchQuery, slug]);

    const formatCurrency = (val: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);

    return (
        <div className="space-y-8 animate-in fade-in duration-700">
            {/* Header section with white background */}
            <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white border border-border rounded-3xl p-8 shadow-sm">
                <div className="flex items-center gap-5">
                    <div className="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shadow-inner">
                        <Package className="h-8 w-8" />
                    </div>
                    <div>
                        <h2 className="text-2xl font-black text-foreground tracking-tight">Catalogue Articles</h2>
                        <p className="text-sm text-muted-foreground">Gérez vos boissons, formats et tarifs de vente.</p>
                    </div>
                </div>
                
                <div className="flex flex-col sm:flex-row items-center gap-4">
                    <div className="relative w-full sm:w-64">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <input
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Rechercher..."
                            className="w-full pl-10 pr-4 py-3 bg-muted/30 border-transparent rounded-2xl text-sm focus:ring-2 focus:ring-amber-500/20 transition-all outline-none"
                        />
                    </div>
                    <Link
                        href={route('drinks.articles.create', { current_team: slug })}
                        className="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl transition-all shadow-lg shadow-amber-500/20 active:scale-95"
                    >
                        <Plus className="h-5 w-5" /> Nouvel Article
                    </Link>
                </div>
            </div>

            {/* List with white background */}
            <div className="bg-white border border-border rounded-3xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-muted/30 border-b border-border">
                            <tr>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Code</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Désignation</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Catégorie</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right">Prix de Vente</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-center">Statut</th>
                                <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/40">
                            {articles?.data?.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-8 py-20 text-center">
                                        <div className="flex flex-col items-center justify-center space-y-3">
                                            <div className="w-16 h-16 bg-muted rounded-full flex items-center justify-center text-muted-foreground opacity-20">
                                                <Package className="h-8 w-8" />
                                            </div>
                                            <p className="text-muted-foreground font-medium italic">Aucun article dans votre catalogue.</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : (
                                articles?.data?.map((article: any) => (
                                    <tr 
                                        key={article.id} 
                                        onClick={(e) => {
                                            if ((e.target as HTMLElement).closest('button') || (e.target as HTMLElement).closest('a')) {
                                                return;
                                            }

                                            router.visit(route('drinks.articles.show', { current_team: slug, article: article.id }));
                                        }}
                                        className="hover:bg-muted/10 transition-colors group cursor-pointer"
                                    >
                                        <td className="px-8 py-5 font-bold text-muted-foreground">#{article.code}</td>
                                        <td className="px-8 py-5">
                                            <div className="flex flex-col">
                                                <span className="font-bold text-foreground text-base">{article.name}</span>
                                                <span className="text-[10px] text-muted-foreground uppercase font-medium">{article.brand || 'Pas de marque'}</span>
                                            </div>
                                        </td>
                                        <td className="px-8 py-5">
                                            <span className="px-2.5 py-1 bg-muted rounded-lg text-[10px] font-bold text-muted-foreground uppercase">
                                                {article.category?.name || 'Général'}
                                            </span>
                                        </td>
                                        <td className="px-8 py-5 text-right font-black text-amber-500 text-base">
                                            {formatCurrency(article.sale_price)}
                                        </td>
                                        <td className="px-8 py-5 text-center">
                                            {article.is_active ? (
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 text-[10px] font-black uppercase tracking-tighter border border-emerald-500/20">
                                                    <CheckCircle2 className="w-3 h-3"/> Actif
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 text-rose-600 text-[10px] font-black uppercase tracking-tighter border border-rose-500/20">
                                                    <XCircle className="w-3 h-3"/> Inactif
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-8 py-5 actions-cell">
                                            <div className="flex items-center justify-end gap-2">
                                                <Link
                                                    href={route('drinks.articles.show', { current_team: slug, article: article.id })}
                                                    className="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-amber-500 hover:text-white rounded-xl transition-all shadow-sm"
                                                    title="Voir les détails"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>
                                                <Link
                                                    href={route('drinks.articles.edit', { current_team: slug, article: article.id })}
                                                    className="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-900 hover:text-white rounded-xl transition-all shadow-sm"
                                                    title="Modifier"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Link>
                                                <button
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        if (confirm(`Supprimer l'article "${article.name}" ?`)) {
                                                            router.delete(route('drinks.articles.destroy', { current_team: slug, article: article.id }));
                                                        }
                                                    }}
                                                    className="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-rose-500 hover:text-white rounded-xl transition-all shadow-sm"
                                                    title="Supprimer"
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

                {/* Pagination with white style */}
                {articles?.links && articles.links.length > 3 && (
                    <div className="p-8 border-t border-border flex justify-center">
                        <div className="flex gap-2">
                            {articles.links.map((link: any, i: number) => (
                                <Link
                                    key={i}
                                    href={link.url || '#'}
                                    className={`h-10 min-w-[40px] flex items-center justify-center px-4 text-xs font-black rounded-xl transition-all ${
                                        link.active
                                            ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20'
                                            : 'bg-muted text-muted-foreground hover:bg-muted-foreground hover:text-white'
                                    } ${!link.url ? 'opacity-30 pointer-events-none' : ''}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
