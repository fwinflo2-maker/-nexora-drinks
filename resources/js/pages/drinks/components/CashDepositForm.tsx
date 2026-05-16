import { useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

type CashDepositFormProps = {
    _action: string;
    cashDeposit?: any;
};

export default function CashDepositForm({ _action, cashDeposit }: CashDepositFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const isEdit = _action === 'edit';

    const { data, setData, processing, errors } = useForm({
        document_date: cashDeposit?.document_date ? String(cashDeposit.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        amount_cash: cashDeposit?.amount_cash ?? 0,
        amount_cheque: cashDeposit?.amount_cheque ?? 0,
        amount_other: cashDeposit?.amount_other ?? 0,
        observation: cashDeposit?.observation ?? '',
    });

    const total = Number(data.amount_cash) + Number(data.amount_cheque) + Number(data.amount_other);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        const payload = {
            ...data,
            amount_cash: Number(data.amount_cash),
            amount_cheque: Number(data.amount_cheque),
            amount_other: Number(data.amount_other),
        };

        if (isEdit) {
            router.put(route('drinks.cash-deposits.update', { current_team: team.slug, cash_deposit: cashDeposit.id }), payload);
        } else {
            router.post(route('drinks.cash-deposits.store', { current_team: team.slug }), payload);
        }
    };

    const fmt = (v: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);

    return (
        <div className="p-6">
            <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>
            <h2 className="text-xl font-bold tracking-tight mb-6">{isEdit ? 'Modifier le versement' : 'Nouveau versement banque'}</h2>
            <form onSubmit={handleSubmit} className="space-y-6 max-w-2xl">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-2 sm:col-span-2">
                        <Label>Date</Label>
                        <Input type="date" value={data.document_date} onChange={e => setData('document_date', e.target.value)} className="bg-background/50" />
                        {errors.document_date && <p className="text-sm text-destructive">{errors.document_date}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Espèces</Label>
                        <Input
                            type="number"
                            min="0"
                            value={data.amount_cash}
                            onChange={e => setData('amount_cash', Number(e.target.value) as any)}
                            className="bg-background/50"
                        />
                        {errors.amount_cash && <p className="text-sm text-destructive">{errors.amount_cash}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Chèques</Label>
                        <Input
                            type="number"
                            min="0"
                            value={data.amount_cheque}
                            onChange={e => setData('amount_cheque', Number(e.target.value) as any)}
                            className="bg-background/50"
                        />
                        {errors.amount_cheque && <p className="text-sm text-destructive">{errors.amount_cheque}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Autres</Label>
                        <Input
                            type="number"
                            min="0"
                            value={data.amount_other}
                            onChange={e => setData('amount_other', Number(e.target.value) as any)}
                            className="bg-background/50"
                        />
                        {errors.amount_other && <p className="text-sm text-destructive">{errors.amount_other}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label className="text-muted-foreground">Total calculé</Label>
                        <div className="h-9 px-3 py-2 rounded-md border border-border/50 bg-muted/30 text-sm font-medium">
                            {fmt(total)}
                        </div>
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
