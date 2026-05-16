import { router, useForm, usePage } from '@inertiajs/react';
import { AnimatePresence, motion } from 'framer-motion';
import { AlertTriangle, CheckCircle2, CreditCard, Download, Loader2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

interface Folio {
    id: number;
    label: string;
    amount: number;
    type: string;
}

interface Balance {
    room: number;
    restaurant: number;
    extras: number;
    discounts: number;
    total: number;
    paid: number;
    balance: number;
}

interface Reservation {
    id: number;
    reference: string;
    nights: number;
    room: { number: string };
    guest: { name: string };
}

interface Props {
    reservation: Reservation;
    balance: Balance;
    folios: Folio[];
}

const PAYMENT_METHODS = [
    { value: 'especes', label: 'Espèces' },
    { value: 'carte', label: 'Carte bancaire' },
    { value: 'virement', label: 'Virement' },
    { value: 'mobile_money', label: 'Mobile Money' },
];

const fmt = (n: number) => n.toLocaleString('fr-FR') + ' FCFA';

export default function CheckoutWizard({ reservation, balance, folios }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;
    const [step, setStep] = useState<1 | 2 | 3>(1);
    const openOrders = folios.filter(f => f.type === 'restaurant' && Number(f.amount) > 0).length;

    const { data, setData, post, processing, errors } = useForm({
        amount: String(balance.balance),
        method: 'especes',
        discount: '',
    });

    const handleCheckout = () => {
        post(route('hotel.reservations.checkout', { current_team: team.slug, reservation: reservation.id }), {
            onSuccess: () => setStep(3),
        });
    };

    const slideVariants = {
        enter: { opacity: 0, x: 30 },
        center: { opacity: 1, x: 0 },
        exit: { opacity: 0, x: -30 },
    };

    return (
        <div className="space-y-5">
            {/* Stepper */}
            <div className="flex items-center gap-2">
                {[1, 2, 3].map(s => (
                    <div key={s} className="flex items-center gap-2">
                        <div className={`w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold transition-colors ${
                            step >= s ? 'bg-blue-600 text-white' : 'bg-muted text-muted-foreground'
                        }`}>
                            {s === 3 && step === 3 ? <CheckCircle2 className="h-4 w-4" /> : s}
                        </div>
                        {s < 3 && <div className={`h-0.5 w-12 transition-colors ${step > s ? 'bg-blue-600' : 'bg-muted'}`} />}
                    </div>
                ))}
                <span className="text-xs text-muted-foreground ml-2">
                    {step === 1 ? 'Vérification folio' : step === 2 ? 'Paiement' : 'Confirmation'}
                </span>
            </div>

            <AnimatePresence mode="wait">
                {step === 1 && (
                    <motion.div key="step1" variants={slideVariants} initial="enter" animate="center" exit="exit" transition={{ duration: 0.2 }} className="space-y-4">
                        <h3 className="text-sm font-semibold">Récapitulatif du folio</h3>

                        {openOrders > 0 && (
                            <div className="flex items-start gap-2 rounded-lg bg-amber-500/10 border border-amber-500/30 p-3 text-xs text-amber-600">
                                <AlertTriangle className="h-4 w-4 flex-shrink-0 mt-0.5" />
                                <p>Des commandes restaurant sont encore ouvertes. Clôturez-les avant le check-out.</p>
                            </div>
                        )}

                        <div className="space-y-1.5 text-xs">
                            <div className="flex justify-between text-muted-foreground"><span>Hébergement ({reservation.nights} nuit{reservation.nights > 1 ? 's' : ''})</span><span>{fmt(balance.room)}</span></div>
                            {balance.restaurant > 0 && <div className="flex justify-between text-orange-600"><span>Restaurant</span><span>{fmt(balance.restaurant)}</span></div>}
                            {balance.extras > 0 && <div className="flex justify-between text-muted-foreground"><span>Services</span><span>{fmt(balance.extras)}</span></div>}
                            {balance.discounts > 0 && <div className="flex justify-between text-green-600"><span>Remises</span><span>−{fmt(balance.discounts)}</span></div>}
                            <div className="flex justify-between font-semibold border-t border-border pt-1.5 text-sm"><span>Total</span><span>{fmt(balance.total)}</span></div>
                            {balance.paid > 0 && <div className="flex justify-between text-green-600"><span>Déjà payé</span><span>−{fmt(balance.paid)}</span></div>}
                            <div className="flex justify-between font-bold text-base border-t-2 border-foreground/20 pt-1.5">
                                <span>Solde à régler</span>
                                <span className={balance.balance > 0 ? 'text-destructive' : 'text-green-600'}>{fmt(balance.balance)}</span>
                            </div>
                        </div>

                        <Button className="w-full h-9 text-sm" onClick={() => setStep(2)}>
                            Procéder au paiement →
                        </Button>
                    </motion.div>
                )}

                {step === 2 && (
                    <motion.div key="step2" variants={slideVariants} initial="enter" animate="center" exit="exit" transition={{ duration: 0.2 }} className="space-y-4">
                        <h3 className="text-sm font-semibold">Mode de paiement</h3>

                        <div className="space-y-1">
                            <Label className="text-xs">Mode *</Label>
                            <div className="grid grid-cols-2 gap-2">
                                {PAYMENT_METHODS.map(m => (
                                    <button
                                        key={m.value}
                                        onClick={() => setData('method', m.value)}
                                        className={`h-9 rounded-lg border text-xs font-medium transition-colors ${
                                            data.method === m.value
                                                ? 'border-blue-600 bg-blue-600/10 text-blue-600'
                                                : 'border-border text-muted-foreground hover:border-foreground/40'
                                        }`}
                                    >
                                        {m.label}
                                    </button>
                                ))}
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-3">
                            <div className="space-y-1">
                                <Label className="text-xs">Montant perçu *</Label>
                                <Input type="number" step="1" className="h-9 text-sm" value={data.amount} onChange={e => setData('amount', e.target.value)} />
                                {errors.amount && <p className="text-xs text-destructive">{errors.amount}</p>}
                            </div>
                            <div className="space-y-1">
                                <Label className="text-xs">Remise (optionnel)</Label>
                                <Input type="number" step="1" min="0" className="h-9 text-sm" placeholder="0" value={data.discount} onChange={e => setData('discount', e.target.value)} />
                            </div>
                        </div>

                        <div className="flex gap-2">
                            <Button variant="outline" className="flex-1 h-9 text-sm" onClick={() => setStep(1)}>← Retour</Button>
                            <Button className="flex-1 h-9 text-sm gap-1.5" onClick={handleCheckout} disabled={processing}>
                                {processing ? <Loader2 className="h-4 w-4 animate-spin" /> : <CreditCard className="h-4 w-4" />}
                                Confirmer
                            </Button>
                        </div>
                    </motion.div>
                )}

                {step === 3 && (
                    <motion.div key="step3" variants={slideVariants} initial="enter" animate="center" exit="exit" transition={{ duration: 0.2 }} className="flex flex-col items-center gap-4 py-8">
                        <CheckCircle2 className="h-12 w-12 text-green-500" />
                        <h3 className="font-semibold">Check-out effectué</h3>
                        <p className="text-xs text-muted-foreground text-center">
                            {reservation.guest.name} — Chambre {reservation.room.number} libérée.
                        </p>
                        <a
                            href={route('hotel.reservations.folio.pdf', { current_team: team.slug, reservation: reservation.id })}
                            target="_blank"
                            rel="noreferrer"
                        >
                            <Button size="sm" variant="outline" className="gap-1.5 h-8 text-xs">
                                <Download className="h-3.5 w-3.5" /> Télécharger le folio PDF
                            </Button>
                        </a>
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
}
