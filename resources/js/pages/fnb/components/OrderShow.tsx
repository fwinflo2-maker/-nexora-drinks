import { router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Plus, Minus, Trash2, Send, CheckCircle2, XCircle, ChefHat } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { OrderStatusBadge } from './DashboardView';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface MenuItem {
    id: number;
    name: string;
    price: number;
}

interface OrderItemModel {
    id: number;
    quantity: number;
    unit_price: number;
    status: string;
    notes: string | null;
    menu_item: MenuItem | null;
}

interface Table {
    id: number;
    name: string;
}

interface Waiter {
    name: string;
}

interface Validator {
    name: string;
}

interface OrderModel {
    id: number;
    reference: string;
    status: string;
    total: number;
    notes: string | null;
    created_at: string;
    closed_at: string | null;
    table: Table | null;
    waiter: Waiter | null;
    validator: Validator | null;
    items: OrderItemModel[];
}

interface Props {
    order: OrderModel;
}

const ITEM_STATUS_STYLES: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-700',
    sent: 'bg-blue-100 text-blue-700',
    preparing: 'bg-indigo-100 text-indigo-700',
    ready: 'bg-emerald-100 text-emerald-700',
    served: 'bg-slate-100 text-slate-500',
};

const ITEM_STATUS_LABELS: Record<string, string> = {
    pending: 'En attente', sent: 'Envoyé', preparing: 'En prép.',
    ready: 'Prêt', served: 'Servi',
};

export default function OrderShow({ order }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug;

    const [processing, setProcessing] = useState<string | null>(null);

    const canSend = order.status === 'open';
    const canClose = ['open', 'sent', 'preparing', 'ready'].includes(order.status);
    const canCancel = ['open', 'sent'].includes(order.status);
    const isEditable = order.status === 'open';

    const action = (name: string, url: string, method: 'post' | 'delete' = 'post', confirm?: string) => {
        if (confirm && !window.confirm(confirm)) return;
        setProcessing(name);
        router[method](url, {}, {
            onFinish: () => setProcessing(null),
            preserveScroll: true,
        });
    };

    const updateItemStatus = (item: OrderItemModel, status: string) => {
        setProcessing(`item-${item.id}`);
        router.post(
            route('fnb.orders.items.status', { current_team: slug, order: order.id, item: item.id }),
            { status },
            { onFinish: () => setProcessing(null), preserveScroll: true }
        );
    };

    const removeItem = (item: OrderItemModel) => {
        if (!confirm(`Retirer "${item.menu_item?.name}" ?`)) return;
        setProcessing(`remove-${item.id}`);
        router.delete(
            route('fnb.orders.items.remove', { current_team: slug, order: order.id, item: item.id }),
            { onFinish: () => setProcessing(null), preserveScroll: true }
        );
    };

    return (
        <div className="max-w-3xl space-y-4">
            {/* Header card */}
            <div className="bg-card border border-border rounded-xl p-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div className="space-y-1">
                    <div className="flex items-center gap-2">
                        <span className="font-mono text-xs text-muted-foreground">{order.reference}</span>
                        <OrderStatusBadge status={order.status} />
                    </div>
                    <p className="text-lg font-semibold text-foreground">
                        {order.table?.name ?? 'Sans table'}
                    </p>
                    <p className="text-sm text-muted-foreground">
                        Serveur : {order.waiter?.name ?? '—'} · {new Date(order.created_at).toLocaleString('fr-FR')}
                    </p>
                    {order.notes && (
                        <p className="text-xs text-muted-foreground bg-muted/40 rounded-lg px-3 py-1.5 mt-1">{order.notes}</p>
                    )}
                </div>

                <div className="flex gap-2 flex-wrap">
                    {canSend && (
                        <Button
                            size="sm"
                            variant="outline"
                            disabled={processing === 'send'}
                            onClick={() => action('send', route('fnb.orders.send', { current_team: slug, order: order.id }))}
                        >
                            <Send className="h-3.5 w-3.5 mr-1.5" />
                            Envoyer en cuisine
                        </Button>
                    )}
                    {canClose && (
                        <Button
                            size="sm"
                            disabled={processing === 'close'}
                            onClick={() => action('close', route('fnb.orders.close', { current_team: slug, order: order.id }), 'post', 'Clôturer cette commande ?')}
                        >
                            <CheckCircle2 className="h-3.5 w-3.5 mr-1.5" />
                            Clôturer
                        </Button>
                    )}
                    {canCancel && (
                        <Button
                            size="sm"
                            variant="destructive"
                            disabled={processing === 'cancel'}
                            onClick={() => action('cancel', route('fnb.orders.cancel', { current_team: slug, order: order.id }), 'post', 'Annuler cette commande ?')}
                        >
                            <XCircle className="h-3.5 w-3.5 mr-1.5" />
                            Annuler
                        </Button>
                    )}
                </div>
            </div>

            {/* Items */}
            <div className="bg-card border border-border rounded-xl overflow-hidden">
                <div className="px-4 py-3 border-b border-border">
                    <h3 className="text-sm font-semibold text-foreground">Articles ({order.items.length})</h3>
                </div>
                <div className="divide-y divide-border">
                    {order.items.map(item => (
                        <motion.div
                            key={item.id}
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            className="px-4 py-3 flex items-center gap-3"
                        >
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <span className="h-5 w-5 rounded-full bg-muted text-muted-foreground text-[10px] font-bold flex items-center justify-center flex-shrink-0">
                                        {item.quantity}
                                    </span>
                                    <p className="text-sm font-medium text-foreground truncate">
                                        {item.menu_item?.name ?? '—'}
                                    </p>
                                </div>
                                {item.notes && (
                                    <p className="text-xs text-muted-foreground mt-0.5 ml-7">{item.notes}</p>
                                )}
                            </div>

                            <p className="text-sm font-mono text-foreground flex-shrink-0">
                                {(Number(item.unit_price) * item.quantity).toLocaleString()}
                            </p>

                            <div className="flex items-center gap-2 flex-shrink-0">
                                {order.status !== 'open' && order.status !== 'closed' && order.status !== 'cancelled' && (
                                    <select
                                        value={item.status}
                                        onChange={e => updateItemStatus(item, e.target.value)}
                                        disabled={processing === `item-${item.id}`}
                                        className="text-xs h-7 rounded-md border border-input bg-transparent px-2"
                                    >
                                        {Object.entries(ITEM_STATUS_LABELS).map(([v, l]) => (
                                            <option key={v} value={v}>{l}</option>
                                        ))}
                                    </select>
                                )}
                                {order.status === 'open' && (
                                    <span className={`text-[11px] font-medium px-2 py-0.5 rounded-full ${ITEM_STATUS_STYLES[item.status] ?? 'bg-slate-100 text-slate-500'}`}>
                                        {ITEM_STATUS_LABELS[item.status] ?? item.status}
                                    </span>
                                )}
                                {order.status !== 'open' && (order.status === 'closed' || order.status === 'cancelled') && (
                                    <span className={`text-[11px] font-medium px-2 py-0.5 rounded-full ${ITEM_STATUS_STYLES[item.status] ?? 'bg-slate-100 text-slate-500'}`}>
                                        {ITEM_STATUS_LABELS[item.status] ?? item.status}
                                    </span>
                                )}
                                {isEditable && (
                                    <button
                                        onClick={() => removeItem(item)}
                                        disabled={!!processing}
                                        className="h-6 w-6 rounded-md bg-red-50 flex items-center justify-center hover:bg-red-100 transition-colors"
                                    >
                                        <Trash2 className="h-3 w-3 text-red-500" />
                                    </button>
                                )}
                            </div>
                        </motion.div>
                    ))}
                    {order.items.length === 0 && (
                        <p className="text-xs text-muted-foreground text-center py-6">Aucun article</p>
                    )}
                </div>
                <div className="px-4 py-3 border-t border-border bg-muted/20 flex justify-between items-center">
                    <span className="text-sm font-semibold text-foreground">Total</span>
                    <span className="text-sm font-semibold font-mono text-foreground">{Number(order.total).toLocaleString()}</span>
                </div>
            </div>

            {order.validator && (
                <p className="text-xs text-muted-foreground text-right">
                    Validé par {order.validator.name}
                    {order.closed_at && ` · ${new Date(order.closed_at).toLocaleString('fr-FR')}`}
                </p>
            )}
        </div>
    );
}
