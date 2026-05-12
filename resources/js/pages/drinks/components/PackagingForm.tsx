import { useForm, usePage } from '@inertiajs/react';
import { Save, ArrowLeft, Boxes, DollarSign, Settings2, CheckCircle2, Info } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

export default function PackagingForm({ _action, packaging }: { _action: string, packaging?: any }) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam || {};
    const slug = team.slug || '';
    const item = packaging;

    const { data, setData, post, put, processing, errors } = useForm({
        code: item?.code ?? '',
        name: item?.name ?? '',
        deposit_price: item?.deposit_price ?? 0,
        packs_per_unit: item?.packs_per_unit ?? 1,
        is_active: item?.is_active ?? true
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (_action === 'edit' && item?.id) {
            put(route('drinks.packagings.update', { current_team: slug, packaging: item.id }));
        } else {
            post(route('drinks.packagings.store', { current_team: slug }));
        }
    };

    return (
        <div className="space-y-8 animate-in fade-in duration-700 pb-20">
            {/* Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white border border-border p-8 rounded-3xl shadow-sm">
                <div className="flex items-center gap-5">
                    <Button 
                        variant="ghost" 
                        onClick={() => window.history.back()} 
                        className="w-12 h-12 bg-muted rounded-2xl flex items-center justify-center text-muted-foreground hover:bg-amber-500 hover:text-white transition-all shadow-sm p-0"
                    >
                        <ArrowLeft className="h-6 w-6" />
                    </Button>
                    <div>
                        <h2 className="text-2xl font-black text-foreground tracking-tight">
                            {_action === 'edit' ? 'Modifier l\'emballage' : 'Nouvel Emballage'}
                        </h2>
                        <p className="text-sm text-muted-foreground">Configurez les caractéristiques de consigne et de conditionnement.</p>
                    </div>
                </div>
                <div className="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shadow-inner">
                    <Boxes className="h-6 w-6" />
                </div>
            </div>

            <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Identification & Specs */}
                <div className="lg:col-span-2 space-y-8">
                    <div className="bg-white border border-border rounded-3xl p-8 shadow-sm space-y-8">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Nom de l'emballage <span className="text-rose-500">*</span></Label>
                                <Input 
                                    value={data.name} 
                                    onChange={e => setData('name', e.target.value)} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl focus:ring-amber-500/20 focus:border-amber-500 font-bold" 
                                    placeholder="Ex: Bouteille 65cl (Casier 12)" 
                                />
                                {errors.name && <p className="text-[10px] text-rose-500 font-bold ml-1">{errors.name}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Code Interne</Label>
                                <Input 
                                    value={data.code} 
                                    onChange={e => setData('code', e.target.value)} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl focus:ring-amber-500/20 focus:border-amber-500" 
                                    placeholder="Auto-généré si vide" 
                                />
                                {errors.code && <p className="text-[10px] text-rose-500 font-bold ml-1">{errors.code}</p>}
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border/50">
                            <div className="space-y-2">
                                <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2 mb-2">
                                    <DollarSign className="h-3.5 w-3.5" /> Valeur de Consigne (XAF)
                                </h3>
                                <Input 
                                    type="number" 
                                    value={data.deposit_price} 
                                    onChange={e => setData('deposit_price', Number(e.target.value))} 
                                    className="h-12 bg-amber-500/5 border-amber-500/20 rounded-2xl text-amber-600 font-black text-lg" 
                                />
                                {errors.deposit_price && <p className="text-[10px] text-rose-500 font-bold ml-1">{errors.deposit_price}</p>}
                            </div>
                            <div className="space-y-2">
                                <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2 mb-2">
                                    <Settings2 className="h-3.5 w-3.5" /> Unités par Casier / Carton
                                </h3>
                                <Input 
                                    type="number" 
                                    value={data.packs_per_unit} 
                                    onChange={e => setData('packs_per_unit', Number(e.target.value))} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl font-bold" 
                                />
                                {errors.packs_per_unit && <p className="text-[10px] text-rose-500 font-bold ml-1">{errors.packs_per_unit}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Associated Articles Info (Edit Mode) */}
                    {_action === 'edit' && item?.articles && item.articles.length > 0 && (
                        <div className="space-y-4">
                            <div className="bg-amber-500/5 border border-amber-500/20 rounded-3xl p-6 flex items-start gap-4">
                                <div className="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                                    <Info className="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 className="font-bold text-amber-900">Articles liés à cet emballage</h4>
                                    <p className="text-sm text-amber-800/70 mt-1">
                                        Cet emballage est utilisé par {item.articles.length} article(s). 
                                        Toute modification du prix de consigne affectera automatiquement le calcul du prix total lors des ventes.
                                    </p>
                                </div>
                            </div>

                            <div className="bg-white border border-border rounded-3xl overflow-hidden shadow-sm">
                                <table className="w-full text-sm text-left">
                                    <thead className="bg-muted/30 border-b border-border">
                                        <tr>
                                            <th className="px-6 py-3 text-[10px] font-bold text-muted-foreground uppercase">Article</th>
                                            <th className="px-6 py-3 text-[10px] font-bold text-muted-foreground uppercase text-right">Prix Vente</th>
                                            <th className="px-6 py-3 text-[10px] font-bold text-muted-foreground uppercase text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border/40">
                                        {item.articles.map((article: any) => (
                                            <tr key={article.id} className="hover:bg-muted/5 transition-colors group">
                                                <td className="px-6 py-4">
                                                    <div className="flex flex-col">
                                                        <span className="font-bold text-foreground">{article.name}</span>
                                                        <span className="text-[10px] text-muted-foreground uppercase font-medium">#{article.code}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-right font-black text-amber-500">
                                                    {new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(article.sale_price)}
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    <Link
                                                        href={route('drinks.articles.edit', { current_team: slug, article: article.id })}
                                                        className="inline-flex h-8 px-3 items-center justify-center bg-muted hover:bg-indigo-500 hover:text-white rounded-lg transition-all text-[10px] font-black uppercase"
                                                    >
                                                        Modifier l'article
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                    {_action === 'edit' && (!item?.articles || item.articles.length === 0) && (
                        <div className="bg-muted/30 border border-dashed border-border rounded-3xl p-8 text-center">
                            <p className="text-sm text-muted-foreground italic">Aucun article n'utilise actuellement cet emballage.</p>
                        </div>
                    )}
                </div>

                {/* Sidebar: Config & Actions */}
                <div className="lg:col-span-1 space-y-8">
                    <div className="bg-white border border-border rounded-3xl p-8 shadow-sm space-y-8">
                        <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2">
                            <Settings2 className="h-4 w-4" /> Statut
                        </h3>

                        <div className="space-y-4">
                            <label className="flex items-center gap-3 cursor-pointer p-4 bg-muted/20 rounded-2xl hover:bg-muted/40 transition-colors">
                                <Checkbox
                                    checked={data.is_active}
                                    onCheckedChange={v => setData('is_active', !!v)}
                                    className="rounded-md border-2 border-amber-500 data-[state=checked]:bg-amber-500"
                                />
                                <div className="flex flex-col">
                                    <span className="text-sm font-bold text-foreground">Emballage Actif</span>
                                    <span className="text-[10px] text-muted-foreground">Disponible pour les nouveaux articles</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <Button 
                        type="submit" 
                        disabled={processing} 
                        className="w-full h-14 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-3xl shadow-lg shadow-amber-500/20 gap-3 text-lg transition-all active:scale-95"
                    >
                        <Save className="h-6 w-6" />
                        {_action === 'edit' ? 'Mettre à jour' : 'Créer l\'emballage'}
                    </Button>
                </div>
            </form>
        </div>
    );
}
