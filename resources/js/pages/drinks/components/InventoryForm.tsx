import { useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft, Plus, Trash2 } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Team } from '@/types';

type InventoryFormProps = {
    _action: string;
    articles?: { id: number; name: string; stock_qty: number }[];
    inventory?: any;
};

type Line = { article_id: string; counted_qty: number };

export default function InventoryForm({ _action, articles = [], inventory }: InventoryFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const isEdit = _action === 'edit';
    const existingLines = isEdit && inventory?.lines
        ? inventory.lines.map((l: any) => ({
            article_id: String(l.article_id || l.article?.id || ''),
            counted_qty: l.counted_qty ?? 0,
        }))
        : [{ article_id: '', counted_qty: 0 }];

    const { data, setData, processing, errors } = useForm({
        document_date: inventory?.document_date ? String(inventory.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        observation: inventory?.observation ?? '',
        lines: existingLines as Line[],
    });

    const addLine = () => setData('lines', [...data.lines, { article_id: '', counted_qty: 0 }]);
    
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

    const getStockQty = (articleId: string) => {
        const art = articles.find(a => String(a.id) === articleId);
        return art?.stock_qty ?? 0;
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        
        const payload = {
            ...data,
            lines: data.lines.filter(l => l.article_id).map(l => ({
                article_id: Number(l.article_id),
                counted_qty: Number(l.counted_qty),
            })),
        };

        if (isEdit) {
            router.put(route('drinks.inventories.update', { current_team: team.slug, inventory: inventory.id }), payload);
        } else {
            router.post(route('drinks.inventories.store', { current_team: team.slug }), payload);
        }
    };

    return (
        <div className="p-6">
            <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>

            <h2 className="text-xl font-bold tracking-tight mb-6">
                {isEdit ? "Modifier l'inventaire" : 'Nouvel inventaire'}
            </h2>

            <form onSubmit={handleSubmit} className="space-y-8 max-w-4xl">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <Label>Date</Label>
                        <Input type="date" value={data.document_date} onChange={e => setData('document_date', e.target.value)} className="bg-background/50" />
                    </div>
                    <div className="space-y-2">
                        <Label>Observation</Label>
                        <Input value={data.observation} onChange={e => setData('observation', e.target.value)} className="bg-background/50" placeholder="Notes..." />
                    </div>
                </div>

                {/* Lines */}
                <div>
                    <div className="flex items-center justify-between mb-3">
                        <h3 className="text-sm font-semibold uppercase text-muted-foreground tracking-wide">Lignes d'inventaire</h3>
                        <Button type="button" variant="outline" size="sm" onClick={addLine}>
                            <Plus className="mr-1 h-3 w-3" /> Ajouter
                        </Button>
                    </div>
                    <div className="rounded-xl border border-border/50 overflow-hidden">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 text-muted-foreground text-xs uppercase tracking-wider">
                                <tr>
                                    <th className="px-3 py-2 text-left font-medium">Article</th>
                                    <th className="px-3 py-2 text-right font-medium w-32">Stock théorique</th>
                                    <th className="px-3 py-2 text-right font-medium w-32">Qté comptée</th>
                                    <th className="px-3 py-2 text-right font-medium w-28">Écart</th>
                                    <th className="px-3 py-2 w-12" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/50">
                                {data.lines.map((line, idx) => {
                                    const stockQty = getStockQty(line.article_id);
                                    const ecart = line.counted_qty - stockQty;

                                    return (
                                        <tr key={idx} className="hover:bg-muted/20">
                                            <td className="px-3 py-2">
                                                <Select value={line.article_id} onValueChange={v => updateLine(idx, 'article_id', v)}>
                                                    <SelectTrigger className="bg-background/50 border-0 h-8"><SelectValue placeholder="Article..." /></SelectTrigger>
                                                    <SelectContent>
                                                        {articles.map(a => <SelectItem key={a.id} value={String(a.id)}>{a.name}</SelectItem>)}
                                                    </SelectContent>
                                                </Select>
                                            </td>
                                            <td className="px-3 py-2 text-right text-muted-foreground">
                                                {line.article_id ? stockQty : '—'}
                                            </td>
                                            <td className="px-3 py-2">
                                                <Input type="number" min="0" value={line.counted_qty} onChange={e => updateLine(idx, 'counted_qty', Number(e.target.value))} className="bg-background/50 text-right h-8 border-0" />
                                            </td>
                                            <td className={`px-3 py-2 text-right font-medium ${ecart < 0 ? 'text-destructive' : ecart > 0 ? 'text-emerald-600' : 'text-muted-foreground'}`}>
                                                {line.article_id ? (ecart > 0 ? `+${ecart}` : ecart) : '—'}
                                            </td>
                                            <td className="px-3 py-2">
                                                {data.lines.length > 1 && (
                                                    <Button type="button" variant="ghost" size="icon" className="h-7 w-7 text-destructive/70 hover:text-destructive" onClick={() => removeLine(idx)}>
                                                        <Trash2 className="h-3.5 w-3.5" />
                                                    </Button>
                                                )}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                    {errors.lines && <p className="text-sm text-destructive mt-1">{errors.lines}</p>}
                </div>

                <div className="pt-4 border-t border-border/50 flex justify-end">
                    <Button type="submit" disabled={processing} className="shadow-md hover:shadow-lg transition-all">
                        <Save className="mr-2 h-4 w-4" />
                        {isEdit ? 'Mettre à jour' : 'Enregistrer'}
                    </Button>
                </div>
            </form>
        </div>
    );
}
