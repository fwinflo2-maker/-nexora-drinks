import { Link, useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft, Plus, Trash2 } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Team } from '@/types';

type ProcurementFormProps = {
    _action: string;
    suppliers?: { id: number; name: string }[];
    articles?: { id: number; name: string; cost_price: number }[];
    kinds?: { value: string; label: string }[];
    procurement?: any;
};

type Line = { article_id: string; quantity: number; unit_price: number };

export default function ProcurementForm({ _action, suppliers = [], articles = [], kinds = [], procurement }: ProcurementFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const isEdit = _action === 'edit';
    const existingLines = isEdit && procurement?.article_lines
        ? procurement.article_lines.map((l: any) => ({
            article_id: String(l.article_id || l.article?.id || ''),
            quantity: l.quantity_received ?? l.quantity ?? 0,
            unit_price: l.unit_price ?? l.article?.cost_price ?? 0,
        }))
        : [{ article_id: '', quantity: 1, unit_price: 0 }];

    const { data, setData, processing, errors } = useForm({
        kind: procurement?.kind ?? kinds[0]?.value ?? '',
        document_date: procurement?.document_date ? String(procurement.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        supplier_id: procurement?.supplier_id ? String(procurement.supplier_id) : (suppliers.length === 1 ? String(suppliers[0].id) : ''),
        observation: procurement?.observation ?? '',
        lines: existingLines as Line[],
    });

    React.useEffect(() => {
        if (!isEdit && !data.supplier_id && suppliers.length === 1) {
            setData('supplier_id', String(suppliers[0].id));
        }
    }, [suppliers]);

    const addLine = () => setData('lines', [...data.lines, { article_id: '', quantity: 1, unit_price: 0 }]);
    
    const removeLine = (idx: number) => {
        const copy = [...data.lines];
        copy.splice(idx, 1);
        setData('lines', copy);
    };

    const updateLine = (idx: number, field: keyof Line, val: any) => {
        const copy = [...data.lines];
        copy[idx] = { ...copy[idx], [field]: val };

        if (field === 'article_id') {
            const art = articles.find(a => String(a.id) === String(val));
            if (art) {
                copy[idx].unit_price = art.cost_price;
            }
        }

        setData('lines', copy);
    };

    const totalHt = data.lines.reduce((sum, l) => sum + l.quantity * l.unit_price, 0);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        
        const finalLines = data.lines.filter(l => l.article_id).map(l => ({
            article_id: Number(l.article_id),
            quantity: Number(l.quantity),
            unit_price: Number(l.unit_price),
        }));

        const payload = {
            ...data,
            lines: finalLines,
        };

        if (isEdit) {
            router.put(route('drinks.procurements.update', { current_team: team.slug, procurement: procurement.id }), payload);
        } else {
            router.post(route('drinks.procurements.store', { current_team: team.slug }), payload);
        }
    };

    const fmt = (v: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);

    return (
        <div className="p-6">
            <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>

            <h2 className="text-xl font-bold tracking-tight mb-6">
                {isEdit ? "Modifier l'approvisionnement" : 'Nouvel approvisionnement'}
            </h2>

            <form onSubmit={handleSubmit} className="space-y-8 max-w-4xl">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {kinds.length > 0 && (
                        <div className="space-y-2">
                            <Label>Type</Label>
                            <Select value={data.kind} onValueChange={v => setData('kind', v)}>
                                <SelectTrigger className="bg-background/50"><SelectValue placeholder="Type..." /></SelectTrigger>
                                <SelectContent>
                                    {kinds.map(k => <SelectItem key={k.value} value={k.value}>{k.label}</SelectItem>)}
                                </SelectContent>
                            </Select>
                        </div>
                    )}
                    <div className="space-y-2">
                        <Label>Date</Label>
                        <Input type="date" value={data.document_date} onChange={e => setData('document_date', e.target.value)} className="bg-background/50" />
                    </div>
                    <div className="space-y-2">
                        <div className="flex items-center justify-between">
                            <Label>Fournisseur</Label>
                            <Link 
                                href={route('drinks.suppliers.index', { current_team: team.slug })}
                                className="text-[10px] font-bold text-indigo-500 uppercase hover:underline"
                            >
                                Gérer
                            </Link>
                        </div>
                        <Select value={data.supplier_id} onValueChange={v => setData('supplier_id', v)}>
                            <SelectTrigger className="bg-background/50"><SelectValue placeholder="Sélectionner..." /></SelectTrigger>
                            <SelectContent>
                                {suppliers.length === 0 && (
                                    <div className="p-4 text-center">
                                        <p className="text-xs text-muted-foreground mb-2">Aucun fournisseur</p>
                                        <Link 
                                            href={route('drinks.suppliers.index', { current_team: team.slug, _action: 'create' })}
                                            className="text-xs font-bold text-indigo-500"
                                        >
                                            + Ajouter un fournisseur
                                        </Link>
                                    </div>
                                )}
                                {suppliers.map(s => <SelectItem key={s.id} value={String(s.id)}>{s.name}</SelectItem>)}
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="space-y-2">
                        <Label>Observation</Label>
                        <Input value={data.observation} onChange={e => setData('observation', e.target.value)} className="bg-background/50" placeholder="Notes..." />
                    </div>
                </div>

                {/* Lines */}
                <div>
                    <div className="flex items-center justify-between mb-3">
                        <h3 className="text-sm font-semibold uppercase text-muted-foreground tracking-wide">Lignes d'articles</h3>
                        <Button type="button" variant="outline" size="sm" onClick={addLine}>
                            <Plus className="mr-1 h-3 w-3" /> Ajouter
                        </Button>
                    </div>
                    <div className="rounded-xl border border-border/50 overflow-hidden">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 text-muted-foreground text-xs uppercase tracking-wider">
                                <tr>
                                    <th className="px-3 py-2 text-left font-medium">Article</th>
                                    <th className="px-3 py-2 text-right font-medium w-28">Qté reçue</th>
                                    <th className="px-3 py-2 text-right font-medium w-36">Prix unit.</th>
                                    <th className="px-3 py-2 text-right font-medium w-36">Montant</th>
                                    <th className="px-3 py-2 w-12" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/50">
                                {data.lines.map((line, idx) => (
                                    <tr key={idx} className="hover:bg-muted/20">
                                        <td className="px-3 py-2">
                                            <Select value={line.article_id} onValueChange={v => updateLine(idx, 'article_id', v)}>
                                                <SelectTrigger className="bg-background/50 border-0 h-8"><SelectValue placeholder="Article..." /></SelectTrigger>
                                                <SelectContent>
                                                    {articles.map(a => <SelectItem key={a.id} value={String(a.id)}>{a.name}</SelectItem>)}
                                                </SelectContent>
                                            </Select>
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input type="number" min="1" value={line.quantity} onChange={e => updateLine(idx, 'quantity', Number(e.target.value))} className="bg-background/50 text-right h-8 border-0" />
                                        </td>
                                        <td className="px-3 py-2">
                                            <Input type="number" min="0" value={line.unit_price} onChange={e => updateLine(idx, 'unit_price', Number(e.target.value))} className="bg-background/50 text-right h-8 border-0" />
                                        </td>
                                        <td className="px-3 py-2 text-right font-medium text-foreground/80">
                                            {fmt(line.quantity * line.unit_price)}
                                        </td>
                                        <td className="px-3 py-2">
                                            {data.lines.length > 1 && (
                                                <Button type="button" variant="ghost" size="icon" className="h-7 w-7 text-destructive/70 hover:text-destructive" onClick={() => removeLine(idx)}>
                                                    <Trash2 className="h-3.5 w-3.5" />
                                                </Button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot className="bg-muted/30 font-semibold">
                                <tr>
                                    <td colSpan={3} className="px-3 py-3 text-right text-sm text-muted-foreground">Total HT</td>
                                    <td className="px-3 py-3 text-right text-primary font-bold">{fmt(totalHt)}</td>
                                    <td />
                                </tr>
                            </tfoot>
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
