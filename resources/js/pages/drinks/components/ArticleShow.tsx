import { Link, usePage, router } from '@inertiajs/react';
import { ArrowLeft, Package, Tag, Layers, DollarSign, History, Edit3, Trash2, CheckCircle2, XCircle, Info, Box } from 'lucide-react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import type { Team } from '@/types';

interface ArticleShowProps {
    article: {
        id: number;
        code: string;
        name: string;
        brand?: string;
        category?: { id: number; name: string };
        packaging?: { id: number; name: string };
        sale_price: number;
        retail_price: number;
        cost_price: number;
        stock_qty: number;
        packs_per_unit: number;
        is_active: boolean;
        is_consignable: boolean;
    };
}

export default function ArticleShow({ article }: ArticleShowProps) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;
    const slug = team.slug;

    const formatCurrency = (val: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
            {/* Header / Actions */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white border border-border p-8 rounded-[2.5rem] shadow-sm">
                <div className="flex items-center gap-5">
                    <Link 
                        href={route('drinks.articles.index', { current_team: slug })}
                        className="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-500 hover:bg-amber-500 hover:text-white transition-all shadow-sm"
                    >
                        <ArrowLeft className="h-6 w-6" />
                    </Link>
                    <div>
                        <div className="flex items-center gap-3 mb-1">
                            <h2 className="text-3xl font-black text-slate-900 tracking-tight">{article.name}</h2>
                            {article.is_active ? (
                                <Badge className="bg-emerald-500/10 text-emerald-600 border-emerald-500/20 text-[10px] font-black uppercase tracking-tighter">Actif</Badge>
                            ) : (
                                <Badge className="bg-rose-500/10 text-rose-600 border-rose-500/20 text-[10px] font-black uppercase tracking-tighter">Inactif</Badge>
                            )}
                        </div>
                        <p className="text-sm text-slate-500 font-medium">Référence : <span className="text-slate-900 font-bold">#{article.code}</span> • {article.brand || 'Marque non spécifiée'}</p>
                    </div>
                </div>
                
                <div className="flex items-center gap-3">
                    <Link
                        href={route('drinks.articles.edit', { current_team: slug, article: article.id })}
                        className="flex items-center gap-2 px-6 py-3 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 active:scale-95"
                    >
                        <Edit3 className="h-4 w-4" /> Modifier l'article
                    </Link>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Main Info */}
                <div className="lg:col-span-2 space-y-8">
                    {/* Stats Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="bg-white border border-border p-6 rounded-[2.5rem] shadow-sm">
                            <div className="w-10 h-10 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 mb-4">
                                <DollarSign className="h-5 w-5" />
                            </div>
                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Prix de Vente</p>
                            <p className="text-2xl font-black text-slate-900">{formatCurrency(article.sale_price)}</p>
                        </div>
                        <div className="bg-white border border-border p-6 rounded-[2.5rem] shadow-sm">
                            <div className="w-10 h-10 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 mb-4">
                                <Box className="h-5 w-5" />
                            </div>
                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Stock Actuel</p>
                            <p className={`text-2xl font-black ${article.stock_qty < 20 ? 'text-rose-500' : 'text-slate-900'}`}>{article.stock_qty} <span className="text-xs text-slate-400 font-bold uppercase ml-1">Unités</span></p>
                        </div>
                        <div className="bg-white border border-border p-6 rounded-[2.5rem] shadow-sm">
                            <div className="w-10 h-10 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-4">
                                <Layers className="h-5 w-5" />
                            </div>
                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Conditionnement</p>
                            <p className="text-2xl font-black text-slate-900">{article.packs_per_unit} <span className="text-xs text-slate-400 font-bold uppercase ml-1">Pcs/Crt</span></p>
                        </div>
                    </div>

                    {/* Details Sections */}
                    <div className="bg-white border border-border rounded-[2.5rem] overflow-hidden shadow-sm">
                        <div className="p-8 border-b border-border bg-slate-50/50 flex items-center justify-between">
                            <h3 className="font-black text-slate-900 flex items-center gap-3">
                                <Info className="h-5 w-5 text-amber-500" /> Informations Détaillées
                            </h3>
                        </div>
                        <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                            <div className="space-y-1">
                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Catégorie</p>
                                <p className="text-base font-bold text-slate-900">{article.category?.name || 'Non classé'}</p>
                            </div>
                            <div className="space-y-1">
                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Emballage / Consigne</p>
                                <p className="text-base font-bold text-slate-900">{article.packaging?.name || 'Sans emballage'}</p>
                            </div>
                            <div className="space-y-1">
                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Prix de Détail (Suggéré)</p>
                                <p className="text-base font-bold text-slate-900">{formatCurrency(article.retail_price)}</p>
                            </div>
                            <div className="space-y-1">
                                <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Prix de Revient (PR)</p>
                                <p className="text-base font-bold text-slate-900">{formatCurrency(article.cost_price)}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Sidebar Info */}
                <div className="lg:col-span-1 space-y-8">
                    <div className="bg-white border border-border rounded-[2.5rem] p-8 shadow-sm space-y-6">
                        <h3 className="font-black text-slate-900 flex items-center gap-3">
                            <Settings className="h-5 w-5 text-indigo-500" /> Paramètres
                        </h3>
                        
                        <div className="space-y-4">
                            <div className="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                                <div className="flex items-center gap-3">
                                    <div className={`w-2 h-2 rounded-full ${article.is_active ? 'bg-emerald-500' : 'bg-rose-500'} animate-pulse`} />
                                    <span className="text-sm font-bold text-slate-700">Visibilité</span>
                                </div>
                                <span className="text-xs font-black uppercase text-slate-400">{article.is_active ? 'Public' : 'Masqué'}</span>
                            </div>

                            <div className="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                                <div className="flex items-center gap-3">
                                    <div className={`w-2 h-2 rounded-full ${article.is_consignable ? 'bg-indigo-500' : 'bg-slate-300'}`} />
                                    <span className="text-sm font-bold text-slate-700">Consignable</span>
                                </div>
                                <span className="text-xs font-black uppercase text-slate-400">{article.is_consignable ? 'Oui' : 'Non'}</span>
                            </div>
                        </div>

                        <div className="pt-6 border-t border-border">
                            <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Actions de maintenance</p>
                            <button 
                                onClick={() => {
                                    if(confirm(`Supprimer définitivement l'article "${article.name}" ? Cette action est irréversible.`)) {
                                        router.delete(route('drinks.articles.destroy', { current_team: slug, article: article.id }));
                                    }
                                }}
                                className="w-full flex items-center justify-center gap-2 py-3 border-2 border-rose-100 text-rose-500 font-bold rounded-2xl hover:bg-rose-50 hover:border-rose-200 transition-all shadow-sm active:scale-95"
                            >
                                <Trash2 className="h-4 w-4" /> Supprimer l'article
                            </button>
                        </div>
                    </div>

                    <div className="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
                        <div className="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
                            <History className="w-24 h-24" />
                        </div>
                        <h4 className="text-lg font-black mb-2 relative z-10">Mouvements de Stock</h4>
                        <p className="text-slate-400 text-xs mb-6 relative z-10 leading-relaxed text-balance">
                            Consultez l'historique complet des entrées et sorties pour cet article.
                        </p>
                        <Button variant="outline" className="w-full border-slate-700 bg-transparent text-white hover:bg-white hover:text-slate-900 rounded-xl relative z-10">
                            Voir l'historique
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
}
