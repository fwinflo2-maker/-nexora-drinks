import { router, usePage } from '@inertiajs/react';
import { Download, FileText, TrendingUp, ShoppingBag, BookOpen, Filter } from 'lucide-react';
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

type OrderRow = {
    reference: string;
    table_name: string;
    server_name: string;
    items_count: number;
    total: number;
    closed_at: string;
};

type RevenueRow = {
    date: string;
    count: number;
    revenue: number;
};

type MenuRow = {
    name: string;
    category: string;
    quantity_sold: number;
    revenue: number;
};

type Props = {
    _action: string;
    dateFrom?: string;
    dateTo?: string;
    rows?: OrderRow[] | RevenueRow[] | MenuRow[];
};

const fmt = (n: number) => new Intl.NumberFormat('fr-FR').format(n);

function DateFilters({
    dateFrom,
    dateTo,
    onFilter,
}: {
    dateFrom: string;
    dateTo: string;
    onFilter: (from: string, to: string) => void;
}) {
    const [from, setFrom] = useState(dateFrom);
    const [to, setTo] = useState(dateTo);

    return (
        <div className="flex flex-wrap items-end gap-3 mb-5">
            <div className="space-y-1">
                <Label className="text-xs">Du</Label>
                <Input type="date" className="h-8 text-xs w-36" value={from} onChange={e => setFrom(e.target.value)} />
            </div>
            <div className="space-y-1">
                <Label className="text-xs">Au</Label>
                <Input type="date" className="h-8 text-xs w-36" value={to} onChange={e => setTo(e.target.value)} />
            </div>
            <Button size="sm" className="h-8 gap-1.5" onClick={() => onFilter(from, to)}>
                <Filter className="h-3.5 w-3.5" /> Filtrer
            </Button>
        </div>
    );
}

function OrdersReport({
    rows,
    dateFrom,
    dateTo,
    slug,
}: {
    rows: OrderRow[];
    dateFrom: string;
    dateTo: string;
    slug: string;
}) {
    const total = rows.reduce((s, r) => s + r.total, 0);

    const filter = (from: string, to: string) => {
        router.get(route('fnb.reports.orders', { current_team: slug }), { date_from: from, date_to: to });
    };

    const downloadPdf = () => {
        window.open(
            route('fnb.reports.orders.pdf', { current_team: slug }) + `?date_from=${dateFrom}&date_to=${dateTo}`,
            '_blank',
        );
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h2 className="text-sm font-semibold text-foreground flex items-center gap-2">
                    <ShoppingBag className="h-4 w-4 text-emerald-500" /> Commandes
                </h2>
                <Button size="sm" variant="outline" className="h-8 gap-1.5 text-xs" onClick={downloadPdf}>
                    <Download className="h-3.5 w-3.5" /> PDF
                </Button>
            </div>
            <DateFilters dateFrom={dateFrom} dateTo={dateTo} onFilter={filter} />
            {rows.length === 0 ? (
                <p className="text-center py-10 text-sm text-muted-foreground">Aucune commande sur la période.</p>
            ) : (
                <div className="rounded-xl border border-border bg-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    {['Référence', 'Table', 'Serveur', 'Articles', 'Total', 'Clôturée le'].map(h => (
                                        <th key={h} className="h-9 px-3 text-left font-medium text-muted-foreground">{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((r, i) => (
                                    <tr key={i} className="border-b border-border/50 hover:bg-muted/20">
                                        <td className="px-3 py-2 font-mono">{r.reference}</td>
                                        <td className="px-3 py-2">{r.table_name}</td>
                                        <td className="px-3 py-2 text-muted-foreground">{r.server_name}</td>
                                        <td className="px-3 py-2 text-right">{r.items_count}</td>
                                        <td className="px-3 py-2 text-right font-medium">{fmt(r.total)}</td>
                                        <td className="px-3 py-2 text-muted-foreground">{r.closed_at}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr className="bg-muted/30">
                                    <td colSpan={4} className="px-3 py-2 font-semibold text-foreground">
                                        Total ({rows.length} commande{rows.length > 1 ? 's' : ''})
                                    </td>
                                    <td className="px-3 py-2 text-right font-bold text-foreground">{fmt(total)}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
}

function RevenueReport({
    rows,
    dateFrom,
    dateTo,
    slug,
}: {
    rows: RevenueRow[];
    dateFrom: string;
    dateTo: string;
    slug: string;
}) {
    const totalRevenue = rows.reduce((s, r) => s + r.revenue, 0);
    const totalCount = rows.reduce((s, r) => s + r.count, 0);

    const filter = (from: string, to: string) => {
        router.get(route('fnb.reports.revenue', { current_team: slug }), { date_from: from, date_to: to });
    };

    const downloadPdf = () => {
        window.open(
            route('fnb.reports.revenue.pdf', { current_team: slug }) + `?date_from=${dateFrom}&date_to=${dateTo}`,
            '_blank',
        );
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h2 className="text-sm font-semibold text-foreground flex items-center gap-2">
                    <TrendingUp className="h-4 w-4 text-emerald-500" /> Revenus
                </h2>
                <Button size="sm" variant="outline" className="h-8 gap-1.5 text-xs" onClick={downloadPdf}>
                    <Download className="h-3.5 w-3.5" /> PDF
                </Button>
            </div>
            <div className="grid grid-cols-2 gap-3 mb-2">
                <div className="rounded-xl border border-border bg-card p-4">
                    <p className="text-xs text-muted-foreground mb-1">CA période</p>
                    <p className="text-xl font-bold text-foreground">{fmt(totalRevenue)}</p>
                </div>
                <div className="rounded-xl border border-border bg-card p-4">
                    <p className="text-xs text-muted-foreground mb-1">Commandes</p>
                    <p className="text-xl font-bold text-foreground">{fmt(totalCount)}</p>
                </div>
            </div>
            <DateFilters dateFrom={dateFrom} dateTo={dateTo} onFilter={filter} />
            {rows.length === 0 ? (
                <p className="text-center py-10 text-sm text-muted-foreground">Aucune donnée sur la période.</p>
            ) : (
                <div className="rounded-xl border border-border bg-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    {['Date', 'Commandes', 'CA (FCFA)'].map(h => (
                                        <th key={h} className="h-9 px-4 text-left font-medium text-muted-foreground">{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((r, i) => (
                                    <tr key={i} className="border-b border-border/50 hover:bg-muted/20">
                                        <td className="px-4 py-2">{r.date}</td>
                                        <td className="px-4 py-2 text-right">{r.count}</td>
                                        <td className="px-4 py-2 text-right font-medium">{fmt(r.revenue)}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr className="bg-muted/30">
                                    <td className="px-4 py-2 font-semibold text-foreground">Total</td>
                                    <td className="px-4 py-2 text-right font-bold text-foreground">{fmt(totalCount)}</td>
                                    <td className="px-4 py-2 text-right font-bold text-foreground">{fmt(totalRevenue)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
}

function MenuReport({
    rows,
    dateFrom,
    dateTo,
    slug,
}: {
    rows: MenuRow[];
    dateFrom: string;
    dateTo: string;
    slug: string;
}) {
    const totalRevenue = rows.reduce((s, r) => s + r.revenue, 0);
    const totalQty = rows.reduce((s, r) => s + r.quantity_sold, 0);

    const filter = (from: string, to: string) => {
        router.get(route('fnb.reports.menu', { current_team: slug }), { date_from: from, date_to: to });
    };

    const downloadPdf = () => {
        window.open(
            route('fnb.reports.menu.pdf', { current_team: slug }) + `?date_from=${dateFrom}&date_to=${dateTo}`,
            '_blank',
        );
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h2 className="text-sm font-semibold text-foreground flex items-center gap-2">
                    <BookOpen className="h-4 w-4 text-emerald-500" /> Performance Menu
                </h2>
                <Button size="sm" variant="outline" className="h-8 gap-1.5 text-xs" onClick={downloadPdf}>
                    <Download className="h-3.5 w-3.5" /> PDF
                </Button>
            </div>
            <DateFilters dateFrom={dateFrom} dateTo={dateTo} onFilter={filter} />
            {rows.length === 0 ? (
                <p className="text-center py-10 text-sm text-muted-foreground">Aucune vente sur la période.</p>
            ) : (
                <div className="rounded-xl border border-border bg-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    {['Article', 'Catégorie', 'Qté vendue', 'CA (FCFA)'].map(h => (
                                        <th key={h} className="h-9 px-4 text-left font-medium text-muted-foreground">{h}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((r, i) => (
                                    <tr key={i} className="border-b border-border/50 hover:bg-muted/20">
                                        <td className="px-4 py-2 font-medium">{r.name}</td>
                                        <td className="px-4 py-2 text-muted-foreground">{r.category}</td>
                                        <td className="px-4 py-2 text-right">{r.quantity_sold}</td>
                                        <td className="px-4 py-2 text-right font-medium">{fmt(r.revenue)}</td>
                                    </tr>
                                ))}
                            </tbody>
                            <tfoot>
                                <tr className="bg-muted/30">
                                    <td colSpan={2} className="px-4 py-2 font-semibold text-foreground">Total</td>
                                    <td className="px-4 py-2 text-right font-bold text-foreground">{fmt(totalQty)}</td>
                                    <td className="px-4 py-2 text-right font-bold text-foreground">{fmt(totalRevenue)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
}

export default function ReportView({ _action, dateFrom = '', dateTo = '', rows = [] }: Props) {
    const { currentTeam } = usePage().props as any;
    const team = currentTeam as Team & { slug?: string };
    const slug = team?.slug ?? '';

    const REPORT_TABS = [
        { id: 'orders', label: 'Commandes', icon: <ShoppingBag className="h-3.5 w-3.5" /> },
        { id: 'revenue', label: 'Revenus', icon: <TrendingUp className="h-3.5 w-3.5" /> },
        { id: 'menu', label: 'Menu', icon: <BookOpen className="h-3.5 w-3.5" /> },
    ];

    const navigateTo = (id: string) => {
        const routeMap: Record<string, string> = {
            orders: route('fnb.reports.orders', { current_team: slug }),
            revenue: route('fnb.reports.revenue', { current_team: slug }),
            menu: route('fnb.reports.menu', { current_team: slug }),
        };
        router.get(routeMap[id]);
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center gap-1 border-b border-border pb-3">
                <FileText className="h-4 w-4 text-muted-foreground mr-1" />
                {REPORT_TABS.map(tab => (
                    <button
                        key={tab.id}
                        onClick={() => navigateTo(tab.id)}
                        className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${
                            _action === tab.id
                                ? 'bg-emerald-600/15 text-emerald-500'
                                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                        }`}
                    >
                        {tab.icon}
                        {tab.label}
                    </button>
                ))}
            </div>

            {_action === 'orders' && (
                <OrdersReport rows={rows as OrderRow[]} dateFrom={dateFrom} dateTo={dateTo} slug={slug} />
            )}
            {_action === 'revenue' && (
                <RevenueReport rows={rows as RevenueRow[]} dateFrom={dateFrom} dateTo={dateTo} slug={slug} />
            )}
            {_action === 'menu' && (
                <MenuReport rows={rows as MenuRow[]} dateFrom={dateFrom} dateTo={dateTo} slug={slug} />
            )}
        </div>
    );
}
