import { Link, usePage } from '@inertiajs/react';
import { Download, FileText, ReceiptText } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

interface Folio {
    id: number;
    label: string;
    amount: number;
    type: string;
    created_at: string;
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
    check_in: string;
    check_out: string;
    nights: number;
    room: { number: string; room_type?: { name: string } };
    guest: { name: string };
}

interface Props {
    reservation: Reservation;
    balance: Balance;
    folios: Folio[];
}

const TYPE_LABELS: Record<string, string> = {
    room: 'Hébergement',
    service: 'Service',
    extra: 'Extra',
    discount: 'Remise',
    restaurant: 'Restaurant',
    payment: 'Paiement',
};

const TYPE_COLORS: Record<string, string> = {
    room: 'text-blue-600',
    service: 'text-purple-600',
    extra: 'text-amber-600',
    discount: 'text-green-600',
    restaurant: 'text-orange-600',
    payment: 'text-emerald-600',
};

export default function FolioSummary({ reservation, balance, folios }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;

    const fmt = (n: number) => n.toLocaleString('fr-FR') + ' FCFA';

    return (
        <div className="space-y-5">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <ReceiptText className="h-4 w-4 text-muted-foreground" />
                    <span className="text-sm font-semibold">Folio — {reservation.reference}</span>
                </div>
                <a
                    href={route('hotel.reservations.folio.pdf', { current_team: team.slug, reservation: reservation.id })}
                    target="_blank"
                    rel="noreferrer"
                >
                    <Button size="sm" variant="outline" className="h-8 text-xs gap-1.5">
                        <Download className="h-3.5 w-3.5" /> PDF
                    </Button>
                </a>
            </div>

            <div className="rounded-lg border border-border overflow-hidden">
                <table className="w-full text-xs">
                    <thead>
                        <tr className="bg-muted/50">
                            <th className="text-left px-4 py-2 font-medium text-muted-foreground">Date</th>
                            <th className="text-left px-4 py-2 font-medium text-muted-foreground">Catégorie</th>
                            <th className="text-left px-4 py-2 font-medium text-muted-foreground">Libellé</th>
                            <th className="text-right px-4 py-2 font-medium text-muted-foreground">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        {folios.map(f => (
                            <tr key={f.id} className="border-t border-border/50">
                                <td className="px-4 py-2 text-muted-foreground">{f.created_at.slice(0, 10)}</td>
                                <td className={`px-4 py-2 font-medium ${TYPE_COLORS[f.type] ?? ''}`}>{TYPE_LABELS[f.type] ?? f.type}</td>
                                <td className="px-4 py-2">{f.label}</td>
                                <td className={`px-4 py-2 text-right font-mono ${f.type === 'discount' || f.type === 'payment' ? 'text-green-600' : ''}`}>
                                    {f.type === 'discount' || f.type === 'payment' ? '−' : ''}{fmt(Number(f.amount))}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <div className="rounded-lg bg-muted/30 border border-border p-4 space-y-2">
                <div className="flex justify-between text-xs text-muted-foreground">
                    <span>Hébergement</span><span>{fmt(balance.room)}</span>
                </div>
                {balance.restaurant > 0 && (
                    <div className="flex justify-between text-xs text-orange-600">
                        <span>Restaurant</span><span>{fmt(balance.restaurant)}</span>
                    </div>
                )}
                {balance.extras > 0 && (
                    <div className="flex justify-between text-xs text-muted-foreground">
                        <span>Services & extras</span><span>{fmt(balance.extras)}</span>
                    </div>
                )}
                {balance.discounts > 0 && (
                    <div className="flex justify-between text-xs text-green-600">
                        <span>Remises</span><span>−{fmt(balance.discounts)}</span>
                    </div>
                )}
                <div className="flex justify-between text-sm font-semibold border-t border-border pt-2">
                    <span>Total séjour</span><span>{fmt(balance.total)}</span>
                </div>
                {balance.paid > 0 && (
                    <div className="flex justify-between text-xs text-green-600">
                        <span>Déjà payé</span><span>−{fmt(balance.paid)}</span>
                    </div>
                )}
                <div className="flex justify-between text-base font-bold border-t-2 border-foreground/20 pt-2">
                    <span>Solde à régler</span>
                    <span className={balance.balance > 0 ? 'text-destructive' : 'text-green-600'}>{fmt(balance.balance)}</span>
                </div>
            </div>

            <Link
                href={route('hotel.reservations.show', { current_team: team.slug, reservation: reservation.id })}
                className="text-xs text-muted-foreground hover:text-foreground flex items-center gap-1"
            >
                ← Retour à la réservation
            </Link>
        </div>
    );
}
