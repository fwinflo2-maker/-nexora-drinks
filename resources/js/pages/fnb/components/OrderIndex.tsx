import { Link, router } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Plus, Eye, Search } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { OrderStatusBadge } from './DashboardView';
import { usePage } from '@inertiajs/react';
import type { Team } from '@/types';

interface OrderItem {
    id: number;
    status: string;
}

interface Table {
    name: string;
}

interface Waiter {
    name: string;
}

interface Order {
    id: number;
    reference: string;
    status: string;
    total: number;
    created_at: string;
    table: Table | null;
    waiter: Waiter | null;
    items: OrderItem[];
}

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Props {
    orders: Paginated<Order>;
    filters: { status: string };
    statuses: { value: string; name: string }[];
}

export default function OrderIndex({ orders, filters, statuses }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug;

    const [status, setStatus] = useState(filters.status ?? '');

    const filter = (newStatus: string) => {
        setStatus(newStatus);
        router.get(
            route('fnb.orders.index', { current_team: slug }),
            { status: newStatus || undefined },
            { preserveState: true, replace: true }
        );
    };

    return (
        <div className="space-y-4">
            <div className="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                <div className="flex gap-2">
                    <button
                        onClick={() => filter('')}
                        className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${!status ? 'bg-emerald-600 text-white' : 'bg-muted text-muted-foreground hover:text-foreground'}`}
                    >
                        Toutes
                    </button>
                    {statuses.map(s => (
                        <button
                            key={s.value}
                            onClick={() => filter(s.value)}
                            className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${status === s.value ? 'bg-emerald-600 text-white' : 'bg-muted text-muted-foreground hover:text-foreground'}`}
                        >
                            {s.name}
                        </button>
                    ))}
                </div>
                <Link href={route('fnb.orders.create', { current_team: slug })}>
                    <Button size="sm">
                        <Plus className="h-3.5 w-3.5 mr-1.5" />
                        Nouvelle commande
                    </Button>
                </Link>
            </div>

            {orders.data.length === 0 ? (
                <div className="text-center py-12 text-muted-foreground text-sm">
                    Aucune commande trouvée.
                </div>
            ) : (
                <div className="bg-card border border-border rounded-xl overflow-hidden">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border bg-muted/30">
                                <th className="text-left px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider">Référence</th>
                                <th className="text-left px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider hidden sm:table-cell">Table</th>
                                <th className="text-left px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider hidden md:table-cell">Serveur</th>
                                <th className="text-left px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider">Statut</th>
                                <th className="text-right px-4 py-3 font-medium text-muted-foreground text-xs uppercase tracking-wider">Total</th>
                                <th className="px-4 py-3 hidden lg:table-cell font-medium text-muted-foreground text-xs uppercase tracking-wider text-right">Date</th>
                                <th className="px-4 py-3" />
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border">
                            {orders.data.map((o) => (
                                <motion.tr
                                    key={o.id}
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    className="hover:bg-muted/20 transition-colors"
                                >
                                    <td className="px-4 py-3">
                                        <span className="font-mono text-xs text-muted-foreground">{o.reference}</span>
                                    </td>
                                    <td className="px-4 py-3 hidden sm:table-cell text-foreground">
                                        {o.table?.name ?? <span className="text-muted-foreground">—</span>}
                                    </td>
                                    <td className="px-4 py-3 hidden md:table-cell text-muted-foreground">
                                        {o.waiter?.name ?? '—'}
                                    </td>
                                    <td className="px-4 py-3">
                                        <OrderStatusBadge status={o.status} />
                                    </td>
                                    <td className="px-4 py-3 text-right font-mono text-foreground">
                                        {Number(o.total).toLocaleString()}
                                    </td>
                                    <td className="px-4 py-3 hidden lg:table-cell text-right text-xs text-muted-foreground">
                                        {new Date(o.created_at).toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })}
                                    </td>
                                    <td className="px-4 py-3">
                                        <Link href={route('fnb.orders.show', { current_team: slug, order: o.id })}>
                                            <button className="h-7 w-7 rounded-md bg-muted flex items-center justify-center hover:bg-muted/80 transition-colors">
                                                <Eye className="h-3.5 w-3.5 text-muted-foreground" />
                                            </button>
                                        </Link>
                                    </td>
                                </motion.tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            {orders.last_page > 1 && (
                <div className="flex items-center justify-center gap-1">
                    {orders.links.map((link, i) => (
                        <button
                            key={i}
                            disabled={!link.url}
                            onClick={() => link.url && router.visit(link.url)}
                            className={`px-3 py-1.5 rounded-lg text-xs transition-colors ${
                                link.active
                                    ? 'bg-emerald-600 text-white'
                                    : link.url
                                        ? 'bg-muted text-muted-foreground hover:text-foreground'
                                        : 'opacity-30 cursor-not-allowed bg-muted text-muted-foreground'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
