import { useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

type CashInputFormProps = {
    _action: string;
    cashInput?: any;
};

export default function CashInputForm({ _action, cashInput }: CashInputFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const isEdit = _action === 'edit';

    const { data, setData, processing, errors } = useForm({
        document_date: cashInput?.document_date ? String(cashInput.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        amount: cashInput?.amount ?? 0,
        source: cashInput?.source ?? '',
        observation: cashInput?.observation ?? '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        const payload = { ...data, amount: Number(data.amount) };
        
        if (isEdit) {
            router.put(route('drinks.cash-inputs.update', { current_team: team.slug, cash_input: cashInput.id }), payload);
        } else {
            router.post(route('drinks.cash-inputs.store', { current_team: team.slug }), payload);
        }
    };

    const fmt = (v: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);

    return (
        <div className="p-6">
            <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>
            <h2 className="text-xl font-bold tracking-tight mb-6">{isEdit ? "Modifier l'apport" : 'Nouvel apport de fonds'}</h2>
            <form onSubmit={handleSubmit} className="space-y-6 max-w-2xl">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <Label>Date</Label>
                        <Input type="date" value={data.document_date} onChange={e => setData('document_date', e.target.value)} className="bg-background/50" />
                        {errors.document_date && <p className="text-sm text-destructive">{errors.document_date}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Montant</Label>
                        <Input type="number" min="0" value={data.amount} onChange={e => setData('amount', Number(e.target.value) as any)} className="bg-background/50" />
                        {data.amount > 0 && <p className="text-xs text-muted-foreground">{fmt(Number(data.amount))}</p>}
                        {errors.amount && <p className="text-sm text-destructive">{errors.amount}</p>}
                    </div>
                    <div className="space-y-2 sm:col-span-2">
                        <Label>Source / Origine</Label>
                        <Input value={data.source} onChange={e => setData('source', e.target.value)} className="bg-background/50" placeholder="Ex: Apport personnel, prêt..." />
                        {errors.source && <p className="text-sm text-destructive">{errors.source}</p>}
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
