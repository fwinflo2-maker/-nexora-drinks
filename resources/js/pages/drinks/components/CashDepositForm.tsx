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
        bank_name: cashDeposit?.bank_name ?? '',
        account_number: cashDeposit?.account_number ?? '',
        total_amount: cashDeposit?.total_amount ?? 0,
        reference: cashDeposit?.reference ?? '',
        observation: cashDeposit?.observation ?? '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        const payload = { ...data, total_amount: Number(data.total_amount) };
        
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
            <h2 className="text-xl font-bold tracking-tight mb-6">{isEdit ? "Modifier le versement" : 'Nouveau versement banque'}</h2>
            <form onSubmit={handleSubmit} className="space-y-6 max-w-2xl">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <Label>Date</Label>
                        <Input type="date" value={data.document_date} onChange={e => setData('document_date', e.target.value)} className="bg-background/50" />
                        {errors.document_date && <p className="text-sm text-destructive">{errors.document_date}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Montant versé</Label>
                        <Input type="number" min="0" value={data.total_amount} onChange={e => setData('total_amount', Number(e.target.value) as any)} className="bg-background/50" />
                        {data.total_amount > 0 && <p className="text-xs text-muted-foreground">{fmt(Number(data.total_amount))}</p>}
                        {errors.total_amount && <p className="text-sm text-destructive">{errors.total_amount}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Banque</Label>
                        <Input value={data.bank_name} onChange={e => setData('bank_name', e.target.value)} className="bg-background/50" placeholder="Ex: Ecobank, Afriland..." />
                        {errors.bank_name && <p className="text-sm text-destructive">{errors.bank_name}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>N° de compte</Label>
                        <Input value={data.account_number} onChange={e => setData('account_number', e.target.value)} className="bg-background/50" />
                    </div>
                    <div className="space-y-2 sm:col-span-2">
                        <Label>Référence (N° Bordereau)</Label>
                        <Input value={data.reference} onChange={e => setData('reference', e.target.value)} className="bg-background/50" placeholder="Ex: BORD-123456" />
                        {errors.reference && <p className="text-sm text-destructive">{errors.reference}</p>}
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
