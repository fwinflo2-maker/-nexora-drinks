import { useForm, usePage } from '@inertiajs/react';
import { Save, ArrowLeft, Tag, Layers, DollarSign, Settings2, Package, CheckCircle2 } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Team } from '@/types';

interface Category { id: number; name: string }
interface Packaging { id: number; name: string }

interface ArticleFormProps {
    _action: 'create' | 'edit';
    categories: Category[];
    packagings: Packaging[];
    article?: {
        id?: number;
        code?: string;
        name?: string;
        brand?: string;
        category_id?: number | null;
        packaging_id?: number | null;
        sale_price?: number;
        retail_price?: number;
        cost_price?: number;
        packs_per_unit?: number;
        discount_rate?: number;
        rebate_rate?: number;
        is_active?: boolean;
        is_consignable?: boolean;
    };
}

export default function ArticleForm({ _action, categories, packagings, article }: ArticleFormProps) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam || {};
    const slug = team.slug || '';

    const safeCategories = Array.isArray(categories) ? categories : [];
    const safePackagings = Array.isArray(packagings) ? packagings : [];

    const { data, setData, post, put, processing, errors, transform } = useForm({
        code: article?.code ?? '',
        name: article?.name ?? '',
        brand: article?.brand ?? '',
        category_id: article?.category_id ? String(article.category_id) : '',
        packaging_id: article?.packaging_id ? String(article.packaging_id) : 'none',
        sale_price: article?.sale_price ?? 0,
        retail_price: article?.retail_price ?? 0,
        cost_price: article?.cost_price ?? 0,
        packs_per_unit: article?.packs_per_unit ?? 1,
        discount_rate: article?.discount_rate ?? 0,
        rebate_rate: article?.rebate_rate ?? 0,
        is_active: article?.is_active ?? true,
        is_consignable: article?.is_consignable ?? false,
    });

    transform((data) => ({
        ...data,
        packaging_id: data.packaging_id === 'none' ? null : data.packaging_id,
    }));

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();

        if (_action === 'edit' && article?.id) {
            put(route('drinks.articles.update', { current_team: slug, article: article.id }));
        } else {
            post(route('drinks.articles.store', { current_team: slug }));
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
                            {_action === 'edit' ? 'Modifier l\'article' : 'Nouvel Article'}
                        </h2>
                        <p className="text-sm text-muted-foreground">Définissez les caractéristiques et les tarifs de votre boisson.</p>
                    </div>
                </div>
                <div className="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shadow-inner">
                    <Package className="h-6 w-6" />
                </div>
            </div>

            <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Identification & Classification */}
                <div className="lg:col-span-2 space-y-8">
                    <div className="bg-white border border-border rounded-3xl p-8 shadow-sm space-y-8">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Nom de l'article <span className="text-rose-500">*</span></Label>
                                <Input 
                                    value={data.name} 
                                    onChange={e => setData('name', e.target.value)} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl focus:ring-amber-500/20 focus:border-amber-500 font-bold" 
                                    placeholder="Ex: Coca-Cola 33cl" 
                                />
                                {errors.name && <p className="text-[10px] text-rose-500 font-bold ml-1">{errors.name}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Marque / Brasserie</Label>
                                <Input 
                                    value={data.brand} 
                                    onChange={e => setData('brand', e.target.value)} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl focus:ring-amber-500/20 focus:border-amber-500" 
                                    placeholder="Ex: Brasseries du Cameroun" 
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Catégorie <span className="text-rose-500">*</span></Label>
                                <Select value={data.category_id} onValueChange={v => setData('category_id', v)}>
                                    <SelectTrigger className="h-12 bg-muted/30 border-transparent rounded-2xl focus:ring-amber-500/20">
                                        <SelectValue placeholder="Choisir une catégorie..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {safeCategories.map(c => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}
                                        {safeCategories.length === 0 && <SelectItem value="none" disabled>Aucune catégorie créée</SelectItem>}
                                    </SelectContent>
                                </Select>
                                {errors.category_id && <p className="text-[10px] text-rose-500 font-bold ml-1">{errors.category_id}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Emballage / Consigne</Label>
                                <Select value={data.packaging_id} onValueChange={v => setData('packaging_id', v)}>
                                    <SelectTrigger className="h-12 bg-muted/30 border-transparent rounded-2xl focus:ring-amber-500/20">
                                        <SelectValue placeholder="Aucun emballage..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="none">Sans emballage</SelectItem>
                                        {safePackagings.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </div>

                    {/* Tarification */}
                    <div className="bg-white border border-border rounded-3xl p-8 shadow-sm">
                        <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2 mb-8">
                            <DollarSign className="h-4 w-4" /> Tarification & Marges (XAF)
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Prix de Vente (Base)</Label>
                                <Input 
                                    type="number" 
                                    value={data.sale_price} 
                                    onChange={e => setData('sale_price', Number(e.target.value))} 
                                    className="h-12 bg-amber-500/5 border-amber-500/20 rounded-2xl text-amber-600 font-black text-lg focus:ring-amber-500/20" 
                                />
                            </div>
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Prix de Détail</Label>
                                <Input 
                                    type="number" 
                                    value={data.retail_price} 
                                    onChange={e => setData('retail_price', Number(e.target.value))} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl font-bold focus:ring-amber-500/20" 
                                />
                            </div>
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Prix d'Achat (PR)</Label>
                                <Input 
                                    type="number" 
                                    value={data.cost_price} 
                                    onChange={e => setData('cost_price', Number(e.target.value))} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl font-bold focus:ring-amber-500/20" 
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {/* Sidebar: Config & Actions */}
                <div className="lg:col-span-1 space-y-8">
                    <div className="bg-white border border-border rounded-3xl p-8 shadow-sm space-y-8">
                        <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2">
                            <Settings2 className="h-4 w-4" /> Configuration
                        </h3>

                        <div className="space-y-4">
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Unités par carton / Casier</Label>
                                <Input 
                                    type="number" 
                                    value={data.packs_per_unit} 
                                    onChange={e => setData('packs_per_unit', Number(e.target.value))} 
                                    className="h-12 bg-muted/30 border-transparent rounded-2xl font-bold" 
                                />
                            </div>
                            
                            <div className="flex flex-col gap-4 pt-4 border-t border-border">
                                <label className="flex items-center gap-3 cursor-pointer p-3 bg-muted/20 rounded-2xl hover:bg-muted/40 transition-colors">
                                    <Checkbox
                                        checked={data.is_active}
                                        onCheckedChange={v => setData('is_active', !!v)}
                                        className="rounded-md border-2 border-amber-500 data-[state=checked]:bg-amber-500"
                                    />
                                    <div className="flex flex-col">
                                        <span className="text-sm font-bold text-foreground">Article Actif</span>
                                        <span className="text-[10px] text-muted-foreground">Visible dans les ventes et stocks</span>
                                    </div>
                                </label>

                                <label className="flex items-center gap-3 cursor-pointer p-3 bg-muted/20 rounded-2xl hover:bg-muted/40 transition-colors">
                                    <Checkbox
                                        checked={data.is_consignable}
                                        onCheckedChange={v => setData('is_consignable', !!v)}
                                        className="rounded-md border-2 border-indigo-500 data-[state=checked]:bg-indigo-500"
                                    />
                                    <div className="flex flex-col">
                                        <span className="text-sm font-bold text-foreground">Consignable</span>
                                        <span className="text-[10px] text-muted-foreground">Gère les emballages vides</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <Button 
                        type="submit" 
                        disabled={processing} 
                        className="w-full h-14 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-3xl shadow-lg shadow-amber-500/20 gap-3 text-lg transition-all active:scale-95"
                    >
                        <Save className="h-6 w-6" />
                        {_action === 'edit' ? 'Mettre à jour' : 'Créer l\'article'}
                    </Button>
                </div>
            </form>
        </div>
    );
}
