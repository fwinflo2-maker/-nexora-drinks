import { useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft, Plus, Trash2, TrendingDown, Info, Package } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Team } from '@/types';

type LossFormProps = {
    _action: string;
    articles?: { id: number; name: string; stock_qty: number }[];
    loss?: any;
};

type Line = { article_id: string; quantity: number };

export default function LossForm({ _action, articles = [], loss }: LossFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const slug = team.slug;

    const isEdit = _action === 'edit';
    const existingLines = isEdit && loss?.lines
        ? loss.lines.map((l: any) => ({
            article_id: String(l.article_id || l.article?.id || ''),
            quantity: l.quantity ?? 0,
        }))
        : [{ article_id: '', quantity: 1 }];

    const { data, setData, processing, errors } = useForm({
        document_date: loss?.document_date ? String(loss.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        reason: loss?.reason ?? '',
        observation: loss?.observation ?? '',
        lines: existingLines as Line[],
    });

    const addLine = () => setData('lines', [...data.lines, { article_id: '', quantity: 1 }]);
    
    const removeLine = (idx: number) => {
        const copy = [...data.lines];
        copy.splice(idx, 1);
        setData('lines', copy);
    };

    const updateLine = (idx: number, field: keyof Line, val: any) => {
        const copy = [...data.lines];
        copy[idx] = { ...copy[idx], [field]: val };
        setData('lines', copy);
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        
        const finalLines = data.lines.filter(l => l.article_id).map(l => ({
            article_id: Number(l.article_id),
            quantity: Number(l.quantity),
        }));

        const payload = { 
            document_date: data.document_date,
            reason: data.reason,
            observation: data.observation,
            lines: finalLines 
        };

        if (isEdit) {
            router.patch(route('drinks.losses.update', { current_team: slug, loss: loss.id }), payload);
        } else {
            router.post(route('drinks.losses.store', { current_team: slug }), payload);
        }
    };

    return (
        <div className="space-y-8 animate-in fade-in duration-700">
            {/* Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white border border-border p-8 rounded-3xl shadow-sm">
                <div className="flex items-center gap-5">
                    <Button variant="ghost" onClick={() => window.history.back()} className="w-12 h-12 bg-muted rounded-2xl flex items-center justify-center text-muted-foreground hover:bg-rose-500 hover:text-white transition-all shadow-sm p-0">
                        <ArrowLeft className="h-6 w-6" />
                    </Button>
                    <div>
                        <h2 className="text-2xl font-black text-foreground tracking-tight">
                            {isEdit ? 'Modifier la perte' : 'Déclaration de Perte'}
                        </h2>
                        <p className="text-sm text-muted-foreground">Renseignez les détails des articles perdus ou cassés.</p>
                    </div>
                </div>
                <div className="flex items-center gap-4">
                    <div className="hidden sm:flex flex-col items-end px-4 border-r border-border">
                        <span className="text-xs font-bold text-rose-500">Casse / Périmé</span>
                        <span className="text-[10px] text-muted-foreground uppercase tracking-widest">Type de document</span>
                    </div>
                    <div className="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500">
                        <TrendingDown className="h-6 w-6" />
                    </div>
                </div>
            </div>

            <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Col: Info */}
                <div className="lg:col-span-1 space-y-6">
                    <div className="bg-white border border-border rounded-3xl p-8 shadow-sm space-y-6">
                        <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2">
                            <Info className="h-4 w-4" /> Détails Généraux
                        </h3>

                        <div className="space-y-4">
                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Date du document</Label>
                                <Input 
                                    type="date" 
                                    value={data.document_date} 
                                    onChange={e => setData('document_date', e.target.value)} 
                                    className="h-12 bg-muted/30 border-border rounded-2xl focus:ring-rose-500/20 focus:border-rose-500" 
                                />
                            </div>

                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Motif / Raison</Label>
                                <Input 
                                    value={data.reason} 
                                    onChange={e => setData('reason', e.target.value)} 
                                    className="h-12 bg-muted/30 border-border rounded-2xl focus:ring-rose-500/20 focus:border-rose-500" 
                                    placeholder="Ex: Cassé lors du transport, Périmé..." 
                                />
                                {errors.reason && <p className="text-[10px] text-red-500 font-bold ml-1">{errors.reason}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label className="text-[10px] font-bold uppercase tracking-widest ml-1">Observations</Label>
                                <textarea 
                                    value={data.observation} 
                                    onChange={e => setData('observation', e.target.value)} 
                                    className="w-full min-h-[120px] bg-muted/30 border-border rounded-2xl p-4 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all" 
                                    placeholder="Notes complémentaires..." 
                                />
                            </div>
                        </div>
                    </div>

                    <Button type="submit" disabled={processing} className="w-full h-14 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-3xl shadow-lg shadow-rose-500/20 gap-3 text-lg transition-all active:scale-95">
                        <Save className="h-6 w-6" />
                        {isEdit ? 'Mettre à jour' : 'Enregistrer la perte'}
                    </Button>
                </div>

                {/* Right Col: Lines */}
                <div className="lg:col-span-2">
                    <div className="bg-white border border-border rounded-3xl shadow-sm overflow-hidden">
                        <div className="p-8 border-b border-border flex items-center justify-between">
                            <h3 className="text-xs font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2">
                                <Package className="h-4 w-4" /> Articles Perdus
                            </h3>
                            <Button type="button" variant="outline" size="sm" onClick={addLine} className="rounded-xl border-2 font-bold gap-2">
                                <Plus className="h-4 w-4" /> Ajouter une ligne
                            </Button>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-sm text-left">
                                <thead className="bg-muted/30 border-b border-border">
                                    <tr>
                                        <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Désignation Article</th>
                                        <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest text-right w-32">Stock</th>
                                        <th className="px-8 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-widest w-32 text-center">Quantité</th>
                                        <th className="px-8 py-4 w-16" />
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border/40">
                                    {data.lines.map((line, idx) => {
                                        const art = articles.find(a => String(a.id) === line.article_id);

                                        return (
                                            <tr key={idx} className="hover:bg-muted/10 transition-colors">
                                                <td className="px-8 py-5">
                                                    <Select value={line.article_id} onValueChange={v => updateLine(idx, 'article_id', v)}>
                                                        <SelectTrigger className="h-11 bg-muted/30 border-transparent rounded-xl focus:ring-rose-500/20">
                                                            <SelectValue placeholder="Choisir un article..." />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {articles.map(a => <SelectItem key={a.id} value={String(a.id)}>{a.name}</SelectItem>)}
                                                        </SelectContent>
                                                    </Select>
                                                </td>
                                                <td className="px-8 py-5 text-right font-bold text-muted-foreground">
                                                    {art ? art.stock_qty : '—'}
                                                </td>
                                                <td className="px-8 py-5">
                                                    <Input 
                                                        type="number" 
                                                        min="1" 
                                                        value={line.quantity} 
                                                        onChange={e => updateLine(idx, 'quantity', Number(e.target.value))} 
                                                        className="h-11 bg-muted/30 border-transparent rounded-xl text-center font-bold focus:ring-rose-500/20" 
                                                    />
                                                </td>
                                                <td className="px-8 py-5 text-right">
                                                    {data.lines.length > 1 && (
                                                        <Button type="button" variant="ghost" size="icon" className="h-10 w-10 text-rose-500/50 hover:bg-rose-500/10 hover:text-rose-500 rounded-xl" onClick={() => removeLine(idx)}>
                                                            <Trash2 className="h-5 w-5" />
                                                        </Button>
                                                    )}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                        
                        {errors.lines && (
                            <div className="p-8 bg-red-500/5 border-t border-red-500/10">
                                <p className="text-xs text-red-500 font-bold">Erreur sur les lignes : {errors.lines}</p>
                            </div>
                        )}
                    </div>
                </div>
            </form>
        </div>
    );
}
