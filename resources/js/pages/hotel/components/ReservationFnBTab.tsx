import { Link, usePage } from '@inertiajs/react';
import { ExternalLink, ShoppingBag, UtensilsCrossed } from 'lucide-react';
import type { Team } from '@/types';

interface OrderItem {
    id: number;
    quantity: number;
    unit_price: number;
    menu_item: { name: string } | null;
}

interface FnBOrder {
    id: number;
    reference: string;
    status: string;
    total: number;
    order_type: string;
    created_at: string;
    items: OrderItem[];
}

interface Props {
    reservationId: number;
    fnbOrders: FnBOrder[];
}

const STATUS_LABELS: Record<string, string> = {
    open: 'Ouverte',
    sent: 'Envoyée',
    preparing: 'En préparation',
    ready: 'Prête',
    closed: 'Clôturée',
    cancelled: 'Annulée',
};

const STATUS_COLORS: Record<string, string> = {
    open: 'bg-amber-500/15 text-amber-600',
    sent: 'bg-blue-500/15 text-blue-600',
    preparing: 'bg-purple-500/15 text-purple-600',
    ready: 'bg-green-500/15 text-green-600',
    closed: 'bg-muted text-muted-foreground',
    cancelled: 'bg-red-500/15 text-red-500',
};

export default function ReservationFnBTab({ reservationId, fnbOrders }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team;

    const totalRestaurant = fnbOrders.filter(o => o.status !== 'cancelled').reduce((sum, o) => sum + Number(o.total), 0);

    if (fnbOrders.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center py-12 text-muted-foreground gap-2">
                <UtensilsCrossed className="h-8 w-8 opacity-30" />
                <p className="text-sm">Aucune commande restaurant rattachée</p>
            </div>
        );
    }

    return (
        <div className="space-y-3">
            <div className="flex items-center justify-between text-xs text-muted-foreground mb-1">
                <span>{fnbOrders.length} commande(s)</span>
                <span className="font-semibold text-foreground">Total restaurant : {totalRestaurant.toLocaleString('fr-FR')} FCFA</span>
            </div>
            {fnbOrders.map(order => (
                <div key={order.id} className="rounded-lg border border-border bg-muted/20 p-3">
                    <div className="flex items-center justify-between mb-2">
                        <div className="flex items-center gap-2">
                            <ShoppingBag className="h-3.5 w-3.5 text-muted-foreground" />
                            <span className="text-xs font-medium">{order.reference}</span>
                            {order.order_type === 'room_service' && (
                                <span className="text-[10px] bg-blue-500/15 text-blue-600 px-1.5 py-0.5 rounded">🛏 Room service</span>
                            )}
                        </div>
                        <div className="flex items-center gap-2">
                            <span className={`text-[10px] px-1.5 py-0.5 rounded font-medium ${STATUS_COLORS[order.status] ?? 'bg-muted text-muted-foreground'}`}>
                                {STATUS_LABELS[order.status] ?? order.status}
                            </span>
                            <Link
                                href={route('fnb.orders.show', { current_team: team.slug, order: order.id })}
                                className="text-muted-foreground hover:text-foreground"
                            >
                                <ExternalLink className="h-3 w-3" />
                            </Link>
                        </div>
                    </div>
                    <div className="text-xs text-muted-foreground space-y-0.5">
                        {order.items.map(item => (
                            <div key={item.id} className="flex justify-between">
                                <span>{item.quantity}× {item.menu_item?.name ?? '—'}</span>
                                <span>{(item.quantity * item.unit_price).toLocaleString('fr-FR')} FCFA</span>
                            </div>
                        ))}
                    </div>
                    <div className="flex justify-end mt-2 pt-2 border-t border-border/50">
                        <span className="text-xs font-semibold">{Number(order.total).toLocaleString('fr-FR')} FCFA</span>
                    </div>
                </div>
            ))}
        </div>
    );
}
