import { useForm, usePage, router } from '@inertiajs/react';
import { Save, ArrowLeft } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Team } from '@/types';

type PaymentFormProps = {
    _action: string;
    clients?: { id: number; name: string }[];
    sales?: { id: number; code: string; total_ttc: number; client_id: number }[];
    payment?: any;
};

export default function PaymentForm({ _action, clients = [], sales = [], payment }: PaymentFormProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const isEdit = _action === 'edit';

    const { data, setData, processing, errors } = useForm({
        document_date: payment?.document_date ? String(payment.document_date).slice(0, 10) : new Date().toISOString().slice(0, 10),
        client_id: payment?.client_id ? String(payment.client_id) : '',
        sale_id: payment?.sale_id ? String(payment.sale_id) : '',
        amount: payment?.amount ?? 0,
        method: payment?.method ?? 'cash',
        reference: payment?.reference ?? '',
        observation: payment?.observation ?? '',
    });

    // Filter sales by selected client
    const filteredSales = data.client_id
        ? sales.filter(s => String(s.client_id) === data.client_id)
        : sales;

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        const payload = {
            ...data,
            client_id: data.client_id ? Number(data.client_id) : null,
            sale_id: data.sale_id ? Number(data.sale_id) : null,
            amount: Number(data.amount),
        };
        
        if (isEdit) {
            router.put(route('drinks.payments.update', { current_team: team.slug, payment: payment.id }), payload);
        } else {
            router.post(route('drinks.payments.store', { current_team: team.slug }), payload);
        }
    };

    const fmt = (v: number) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(v);

    return (
        <div className="p-6">
            <Button variant="ghost" className="mb-6 -ml-4 text-muted-foreground" onClick={() => window.history.back()}>
                <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>
            <h2 className="text-xl font-bold tracking-tight mb-6">{isEdit ? 'Modifier le règlement' : 'Nouveau règlement'}</h2>
            <form onSubmit={handleSubmit} className="space-y-6 max-w-2xl">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-2">
                        <Label>Date</Label>
                        <Input type="date" value={data.document_date} onChange={e => setData('document_date', e.target.value)} className="bg-background/50" />
                        {errors.document_date && <p className="text-sm text-destructive">{errors.document_date}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Client</Label>
                        <Select value={data.client_id} onValueChange={v => {
                            setData('client_id', v); setData('sale_id', ''); 
                        }}>
                            <SelectTrigger className="bg-background/50"><SelectValue placeholder="Sélectionner..." /></SelectTrigger>
                            <SelectContent>
                                {clients.map(c => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}
                            </SelectContent>
                        </Select>
                        {errors.client_id && <p className="text-sm text-destructive">{errors.client_id}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Vente associée (optionnel)</Label>
                        <Select value={data.sale_id} onValueChange={v => setData('sale_id', v)}>
                            <SelectTrigger className="bg-background/50"><SelectValue placeholder="Aucune..." /></SelectTrigger>
                            <SelectContent>
                                {filteredSales.map(s => (
                                    <SelectItem key={s.id} value={String(s.id)}>
                                        {s.code} — {fmt(s.total_ttc)}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="space-y-2">
                        <Label>Montant</Label>
                        <Input type="number" min="0" value={data.amount} onChange={e => setData('amount', Number(e.target.value) as any)} className="bg-background/50" />
                        {data.amount > 0 && <p className="text-xs text-muted-foreground">{fmt(Number(data.amount))}</p>}
                        {errors.amount && <p className="text-sm text-destructive">{errors.amount}</p>}
                    </div>
                    <div className="space-y-2">
                        <Label>Mode de paiement</Label>
                        <Select value={data.method} onValueChange={v => setData('method', v)}>
                            <SelectTrigger className="bg-background/50"><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="cash">Espèces</SelectItem>
                                <SelectItem value="cheque">Chèque</SelectItem>
                                <SelectItem value="transfer">Virement</SelectItem>
                                <SelectItem value="mobile_money">Mobile Money</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="space-y-2">
                        <Label>Référence</Label>
                        <Input value={data.reference} onChange={e => setData('reference', e.target.value)} className="bg-background/50" placeholder="N° chèque, ref. virement..." />
                    </div>
                </div>
                <div className="space-y-2">
                    <Label>Observation</Label>
                    <Input value={data.observation} onChange={e => setData('observation', e.target.value)} className="bg-background/50" placeholder="Notes..." />
                </div>
                <div className="pt-4 border-t border-border/50 flex justify-end">
                    <Button type="submit" disabled={processing} className="shadow-md hover:shadow-lg transition-all">
                        <Save className="mr-2 h-4 w-4" /> Enregistrer
                    </Button>
                </div>
            </form>
        </div>
    );
}
