import { useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Team } from '@/types';

type ExpenseFormProps = {
    _action: string;
    expenseTypes?: { id: number; name: string }[];
    expense?: any;
};

export default function ExpenseForm({ _action, expenseTypes = [], expense }: ExpenseFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const isEdit = _action === 'edit';

    const { data, setData, processing, errors } = useForm({
        expense_type_id: expense?.expense_type_id ? String(expense.expense_type_id) : '',
        document_date: expense?.document_date ? String(expense.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        label: expense?.label ?? '',
        amount: expense?.amount ?? 0,
        observation: expense?.observation ?? '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        const payload = { 
            ...data, 
            expense_type_id: Number(data.expense_type_id), 
            amount: Number(data.amount) 
        };

        if (isEdit) {
            router.put(route('drinks.expenses.update', { current_team: team.slug, expense: expense.id }), payload);
        } else {
            router.post(route('drinks.expenses.store', { current_team: team.slug }), payload);
        }
    };

    const fmt = (v: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);

    return (
        <div className="p-6">
            <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>
            <h2 className="text-xl font-bold tracking-tight mb-6">{isEdit ? 'Modifier la charge' : 'Nouvelle charge'}</h2>
            <form onSubmit={handleSubmit} className="space-y-6 max-w-2xl">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <Label>Type de charge</Label>
                        <Select value={data.expense_type_id} onValueChange={v => setData('expense_type_id', v)}>
                            <SelectTrigger className="bg-background/50"><SelectValue placeholder="Sélectionner..." /></SelectTrigger>
                            <SelectContent>
                                {expenseTypes.map(t => <SelectItem key={t.id} value={String(t.id)}>{t.name}</SelectItem>)}
                            </SelectContent>
                        </Select>
                        {errors.expense_type_id && <p className="text-sm text-destructive">{errors.expense_type_id}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Date</Label>
                        <Input type="date" value={data.document_date} onChange={e => setData('document_date', e.target.value)} className="bg-background/50" />
                        {errors.document_date && <p className="text-sm text-destructive">{errors.document_date}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Libellé</Label>
                        <Input value={data.label} onChange={e => setData('label', e.target.value)} className="bg-background/50" placeholder="Description de la charge..." />
                        {errors.label && <p className="text-sm text-destructive">{errors.label}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Montant</Label>
                        <Input type="number" min="0" value={data.amount} onChange={e => setData('amount', Number(e.target.value) as any)} className="bg-background/50" />
                        {data.amount > 0 && <p className="text-xs text-muted-foreground">{fmt(Number(data.amount))}</p>}
                        {errors.amount && <p className="text-sm text-destructive">{errors.amount}</p>}
                    </div>
                </div>
                <div className="space-y-2">
                    <Label>Observation</Label>
                    <Input value={data.observation} onChange={e => setData('observation', e.target.value)} className="bg-background/50" placeholder="Notes..." />
                </div>
                <div className="pt-4 border-t border-border/50 flex justify-end">
                    <Button type="submit" disabled={processing} className="shadow-md hover:shadow-lg transition-all">
                        <Save className="mr-2 h-4 w-4" /> {isEdit ? 'Mettre à jour' : 'Enregistrer'}
                    </Button>
                </div>
            </form>
        </div>
    );
}
