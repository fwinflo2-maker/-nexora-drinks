import { useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft, Plus, Trash2, CheckCircle, AlertTriangle, UserPlus, X } from 'lucide-react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Team } from '@/types';

type SaleFormProps = {
    _action: string;
    clients?: { id: number; name: string }[];
    articles?: { id: number; name: string; sale_price: number; stock_qty: number }[];
    kinds?: { value: string; label: string }[];
    sale?: any;
};

type Line = { article_id: string; quantity: number; unit_price: number };

export default function SaleForm({ _action, clients: initialClients = [], articles = [], kinds = [], sale }: SaleFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const [clients, setClients] = useState(initialClients);
    const [showAddClient, setShowAddClient] = useState(false);
    const [newClientName, setNewClientName] = useState('');
    const [newClientPhone, setNewClientPhone] = useState('');
    const [addingClient, setAddingClient] = useState(false);
    const [addClientError, setAddClientError] = useState('');

    const handleAddClient = async () => {
        if (!newClientName.trim()) {
            setAddClientError('Le nom est requis.');
            return;
        }
        setAddingClient(true);
        setAddClientError('');
        try {
            const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';
            const res = await fetch(route('drinks.clients.quick-store', { current_team: team.slug }), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ name: newClientName.trim(), phone: newClientPhone.trim() || null }),
            });
            if (!res.ok) {
                const err = await res.json();
                setAddClientError(err.message ?? 'Erreur lors de la création.');
                return;
            }
            const created = await res.json();
            setClients(prev => [...prev, { id: created.id, name: created.name }].sort((a, b) => a.name.localeCompare(b.name)));
            setData('client_id', String(created.id));
            setNewClientName('');
            setNewClientPhone('');
            setShowAddClient(false);
        } catch {
            setAddClientError('Erreur réseau. Réessayez.');
        } finally {
            setAddingClient(false);
        }
    };

    const isEdit = _action === 'edit';
    const existingLines = isEdit && sale?.article_lines
        ? sale.article_lines.map((l: any) => ({
            article_id: String(l.article_id || l.article?.id || ''),
            quantity: l.quantity || l.quantity_received || 0,
            unit_price: l.unit_price ?? l.article?.sale_price ?? 0,
        }))
        : [{ article_id: '', quantity: 1, unit_price: 0 }];

    const { data, setData, processing, errors, setError, clearErrors } = useForm({
        kind: sale?.kind ?? kinds[0]?.value ?? '',
        document_date: sale?.document_date ? String(sale.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        client_id: sale?.client_id ? String(sale.client_id) : '',
        observation: sale?.observation ?? '',
        lines: existingLines as Line[],
    });

    const addLine = () => setData('lines', [...data.lines, { article_id: '', quantity: 1, unit_price: 0 }]);
    const removeLine = (idx: number) => {
        const copy = data.lines.filter((_, i) => i !== idx);
        setData('lines', copy);
    };

    const updateLine = (idx: number, field: keyof Line, val: any) => {
        const copy = [...data.lines];
        copy[idx] = { ...copy[idx], [field]: val };

        if (field === 'article_id') {
            const art = articles.find(a => String(a.id) === String(val));
            if (art) {
                copy[idx].unit_price = art.sale_price;
            }
        }

        setData('lines', copy);
        
        // Clear line error when modified
        if (errors[`lines.${idx}.quantity` as any]) {
            clearErrors(`lines.${idx}.quantity` as any);
        }
    };

    const totalHt = data.lines.reduce((sum, l) => sum + l.quantity * l.unit_price, 0);
    const totalTtc = Math.round(totalHt * 1.1925);

    const validateStock = (): boolean => {
        let isValid = true;
        clearErrors();

        data.lines.forEach((line, idx) => {
            if (line.article_id) {
                const art = articles.find(a => String(a.id) === String(line.article_id));
                if (art && line.quantity > art.stock_qty) {
                    setError(`lines.${idx}.quantity` as any, `Stock insuffisant (${art.stock_qty} dispo)`);
                    isValid = false;
                }
            }
        });

        return isValid;
    };

    const handleSubmitAction = (shouldValidate: boolean) => {
        if (shouldValidate && !validateStock()) {
            return;
        }

        const payload = {
            ...data,
            validate: shouldValidate,
            lines: data.lines.filter(l => l.article_id).map(l => ({
                article_id: Number(l.article_id),
                quantity: Number(l.quantity),
                unit_price: Number(l.unit_price),
            })),
        };

        if (isEdit) {
            router.put(route('drinks.sales.update', { current_team: team.slug, sale: sale.id }), payload);
        } else {
            router.post(route('drinks.sales.store', { current_team: team.slug }), payload);
        }
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        handleSubmitAction(false);
    };

    const fmt = (v: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);

    return (
        <div className="p-6">
            <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>

            <h2 className="text-xl font-bold tracking-tight mb-6">
                {isEdit ? 'Modifier la vente' : 'Nouvelle vente'}
            </h2>

            <form onSubmit={handleSubmit} className="space-y-8 max-w-4xl">
                {(errors as any).sale && (
                    <div className="bg-rose-500/10 border border-rose-500/20 text-rose-500 p-4 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                        <AlertTriangle className="h-5 w-5 shrink-0" />
                        <p className="text-sm font-medium">{(errors as any).sale}</p>
                    </div>
                )}
                {/* Header fields */}
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
                        <Label>Client</Label>
                        <div className="flex gap-2">
                            <Select value={data.client_id} onValueChange={v => setData('client_id', v)}>
                                <SelectTrigger className="bg-background/50 flex-1"><SelectValue placeholder="Sélectionner..." /></SelectTrigger>
                                <SelectContent>
                                    {clients.map(c => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}
                                    {clients.length === 0 && (
                                        <div className="px-3 py-2 text-xs text-muted-foreground italic">Aucun client</div>
                                    )}
                                </SelectContent>
                            </Select>
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                className="h-10 w-10 flex-shrink-0 border-amber-500/30 hover:bg-amber-500/10 hover:text-amber-500"
                                title="Ajouter un client"
                                onClick={() => setShowAddClient(true)}
                            >
                                <UserPlus className="h-4 w-4" />
                            </Button>
                        </div>
                        {showAddClient && (
                            <div className="mt-2 rounded-xl border border-amber-500/30 bg-amber-500/5 p-4 space-y-3 animate-in fade-in slide-in-from-top-2">
                                <div className="flex items-center justify-between">
                                    <p className="text-xs font-semibold text-amber-600 uppercase tracking-wider">Nouveau client</p>
                                    <button type="button" onClick={() => { setShowAddClient(false); setAddClientError(''); }} className="text-muted-foreground hover:text-foreground">
                                        <X className="h-3.5 w-3.5" />
                                    </button>
                                </div>
                                <div className="grid grid-cols-2 gap-2">
                                    <div>
                                        <Label className="text-xs">Nom *</Label>
                                        <Input
                                            value={newClientName}
                                            onChange={e => setNewClientName(e.target.value)}
                                            placeholder="Nom du client..."
                                            className="h-8 text-sm mt-1"
                                        />
                                    </div>
                                    <div>
                                        <Label className="text-xs">Téléphone</Label>
                                        <Input
                                            value={newClientPhone}
                                            onChange={e => setNewClientPhone(e.target.value)}
                                            placeholder="+243..."
                                            className="h-8 text-sm mt-1"
                                        />
                                    </div>
                                </div>
                                {addClientError && <p className="text-xs text-red-500">{addClientError}</p>}
                                <div className="flex justify-end gap-2">
                                    <Button type="button" variant="ghost" size="sm" onClick={() => { setShowAddClient(false); setAddClientError(''); }}>
                                        Annuler
                                    </Button>
                                    <Button type="button" size="sm" onClick={handleAddClient} disabled={addingClient} className="bg-amber-500 hover:bg-amber-600 text-white">
                                        <UserPlus className="h-3.5 w-3.5 mr-1.5" />
                                        {addingClient ? 'Création...' : 'Créer & sélectionner'}
                                    </Button>
                                </div>
                            </div>
                        )}
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
                                    <th className="px-3 py-2 text-right font-medium w-32">Qté</th>
                                    <th className="px-3 py-2 text-right font-medium w-36">Prix unit.</th>
                                    <th className="px-3 py-2 text-right font-medium w-36">Montant</th>
                                    <th className="px-3 py-2 w-12" />
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/50">
                                {data.lines.map((line, idx) => {
                                    const art = articles.find(a => String(a.id) === String(line.article_id));
                                    const isOutOfStock = art && line.quantity > art.stock_qty;

                                    return (
                                        <tr key={idx} className={`hover:bg-muted/20 ${isOutOfStock ? 'bg-red-500/5' : ''}`}>
                                            <td className="px-3 py-2">
                                                <Select value={line.article_id} onValueChange={v => updateLine(idx, 'article_id', v)}>
                                                    <SelectTrigger className="bg-background/50 border-0 h-8">
                                                        <SelectValue placeholder="Article..." />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {articles.map(a => (
                                                            <SelectItem key={a.id} value={String(a.id)}>
                                                                <div className="flex items-center justify-between w-full min-w-[200px]">
                                                                    <span>{a.name}</span>
                                                                    <span className={`text-[10px] ml-2 ${a.stock_qty <= 0 ? 'text-rose-500 font-bold' : 'text-muted-foreground'}`}>
                                                                        Stock: {a.stock_qty}
                                                                    </span>
                                                                </div>
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </td>
                                            <td className="px-3 py-2">
                                                <div className="space-y-1">
                                                    <Input 
                                                        type="number" 
                                                        min="1" 
                                                        value={line.quantity} 
                                                        onChange={e => updateLine(idx, 'quantity', Number(e.target.value))} 
                                                        className={`bg-background/50 text-right h-8 border-0 ${isOutOfStock ? 'text-rose-500 font-bold' : ''}`} 
                                                    />
                                                    {isOutOfStock && (
                                                        <div className="flex items-center justify-end gap-1 text-[10px] text-rose-500 font-medium">
                                                            <AlertTriangle className="h-3 w-3" />
                                                            Dispo: {art.stock_qty}
                                                        </div>
                                                    )}
                                                </div>
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
                                    );
                                })}
                            </tbody>
                            <tfoot className="bg-muted/30 font-semibold">
                                <tr>
                                    <td colSpan={3} className="px-3 py-3 text-right text-sm text-muted-foreground">Total HT</td>
                                    <td className="px-3 py-3 text-right">{fmt(totalHt)}</td>
                                    <td />
                                </tr>
                                <tr>
                                    <td colSpan={3} className="px-3 py-2 text-right text-sm text-muted-foreground">Total TTC</td>
                                    <td className="px-3 py-2 text-right text-primary font-bold">{fmt(totalTtc)}</td>
                                    <td />
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {(errors as any)['lines'] && <p className="text-sm text-destructive mt-1">{(errors as any)['lines']}</p>}
                </div>

                <div className="pt-4 border-t border-border/50 flex justify-end gap-3">
                    <Button 
                        type="button" 
                        variant="outline"
                        disabled={processing} 
                        onClick={() => handleSubmitAction(false)}
                        className="border-border hover:bg-muted"
                    >
                        <Save className="mr-2 h-4 w-4" />
                        Enregistrer Brouillon
                    </Button>
                    <Button 
                        type="button" 
                        disabled={processing} 
                        onClick={() => handleSubmitAction(true)}
                        className="bg-emerald-600 hover:bg-emerald-700 text-white shadow-md hover:shadow-lg transition-all"
                    >
                        <CheckCircle className="mr-2 h-4 w-4" />
                        Valider la Vente
                    </Button>
                </div>
            </form>
        </div>
    );
}
