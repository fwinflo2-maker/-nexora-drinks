import { router, usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import { Download, FileText, TrendingUp, TrendingDown, Wallet, Landmark, Calendar, Filter, BarChart3, ChevronRight } from 'lucide-react';
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

// ─── Types ────────────────────────────────────────────────────────────────────

type CashEntry = { amount: number; count: number };

type BrouillardData = {
    cash_inputs: CashEntry;
    cash_deposits: CashEntry;
    expenses: CashEntry;
    payments: CashEntry;
    sales_total: number;
};

type BrouillardProps = {
    data: BrouillardData;
    dateFrom: string;
    dateTo: string;
};

type SalesRow = {
    article_id: number;
    article_name: string;
    total_qty: number;
    total_amount_ht: number;
};

type SalesReportProps = {
    rows: SalesRow[];
    dateFrom: string;
    dateTo: string;
};

type StockRow = {
    article_id: number;
    article_name: string;
    stock_qty: number;
    cost_price: number;
};

type StockReportProps = {
    rows: StockRow[];
    date?: string;
};

type ClientRow = {
    client_id: number;
    client_name: string;
    total_ttc: number;
    sale_count: number;
};

type ClientReportProps = {
    rows: ClientRow[];
    dateFrom: string;
    dateTo: string;
};

type RoadmapProps = {
    sales: any[];
    date: string;
};

type ReportViewProps = {
    _action: string;
} & Partial<BrouillardProps> &
    Partial<SalesReportProps> &
    Partial<StockReportProps> &
    Partial<ClientReportProps> &
    Partial<RoadmapProps>;

// ─── Helpers ──────────────────────────────────────────────────────────────────

const formatCurrency = (amount: number) =>
    new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XAF',
        maximumFractionDigits: 0,
    }).format(amount);

const PDF_ROUTES: Record<string, string> = {
    brouillard: 'drinks.reports.brouillard.pdf',
    'sales-report': 'drinks.reports.sales.pdf',
    'stock-report': 'drinks.reports.stock.pdf',
    'client-report': 'drinks.reports.clients.pdf',
    roadmap: 'drinks.reports.roadmap.pdf',
};

const TITLES: Record<string, string> = {
    brouillard: 'Brouillard de Caisse',
    'sales-report': 'Rapport des Ventes',
    'stock-report': 'État du Stock',
    'client-report': "Chiffre d'Affaires Client",
    roadmap: 'Feuille de Route Chauffeur',
};

// ─── Date filter form ─────────────────────────────────────────────────────────

function DateFilters({
    action,
    slug,
    dateFrom,
    dateTo,
    date,
}: {
    action: string;
    slug: string;
    dateFrom?: string;
    dateTo?: string;
    date?: string;
}) {
    const isStockReport = action === 'stock-report';

    const routeNameMap: Record<string, string> = {
        brouillard: 'drinks.reports.brouillard',
        'sales-report': 'drinks.reports.sales',
        'stock-report': 'drinks.reports.stock',
        'client-report': 'drinks.reports.clients',
        roadmap: 'drinks.reports.roadmap',
    };

    const [localFrom, setLocalFrom] = useState(dateFrom ?? '');
    const [localTo, setLocalTo] = useState(dateTo ?? '');
    const [localDate, setLocalDate] = useState(date ?? '');

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        const params: Record<string, string> = { current_team: slug };

        if (isStockReport || action === 'roadmap') {
            if (localDate) {
params.date = localDate;
}
        } else {
            if (localFrom) {
params.date_from = localFrom;
}

            if (localTo) {
params.date_to = localTo;
}
        }

        router.get(route(routeNameMap[action], { current_team: slug }), params);
    };

    return (
        <form onSubmit={handleSubmit} className="flex flex-wrap items-center gap-3 bg-white p-2 rounded-xl border border-border shadow-sm">
            {isStockReport || action === 'roadmap' ? (
                <div className="flex items-center gap-2 px-3">
                    <Calendar className="h-4 w-4 text-muted-foreground" />
                    <input
                        type="date"
                        value={localDate}
                        onChange={(e) => setLocalDate(e.target.value)}
                        className="bg-transparent border-none text-sm font-medium focus:ring-0 w-32"
                    />
                </div>
            ) : (
                <>
                    <div className="flex items-center gap-2 px-3 border-r border-border/50">
                        <span className="text-[10px] font-bold text-muted-foreground uppercase tracking-tighter">Du</span>
                        <input
                            type="date"
                            value={localFrom}
                            onChange={(e) => setLocalFrom(e.target.value)}
                            className="bg-transparent border-none text-sm font-medium focus:ring-0 w-32"
                        />
                    </div>
                    <div className="flex items-center gap-2 px-3 border-r border-border/50">
                        <span className="text-[10px] font-bold text-muted-foreground uppercase tracking-tighter">Au</span>
                        <input
                            type="date"
                            value={localTo}
                            onChange={(e) => setLocalTo(e.target.value)}
                            className="bg-transparent border-none text-sm font-medium focus:ring-0 w-32"
                        />
                    </div>
                </>
            )}
            <Button type="submit" size="sm" variant="ghost" className="h-8 hover:bg-amber-500/10 hover:text-amber-500 font-bold">
                <Filter className="h-3.5 w-3.5 mr-2" />
                Filtrer
            </Button>
        </form>
    );
}

// ─── Brouillard ───────────────────────────────────────────────────────────────

function BrouillardView({ data }: { data: BrouillardData }) {
    const solde =
        (data.cash_inputs?.amount ?? 0) +
        (data.sales_total ?? 0) -
        (data.expenses?.amount ?? 0) -
        (data.cash_deposits?.amount ?? 0);

    const cards = [
        {
            label: 'Apports de Fonds',
            amount: data.cash_inputs?.amount ?? 0,
            count: data.cash_inputs?.count ?? 0,
            icon: <TrendingUp className="h-4 w-4 text-emerald-500" />,
            color: 'emerald',
        },
        {
            label: 'Ventes (Total)',
            amount: data.sales_total ?? 0,
            count: null,
            icon: <Wallet className="h-4 w-4 text-amber-500" />,
            color: 'amber',
        },
        {
            label: 'Dépenses & Charges',
            amount: data.expenses?.amount ?? 0,
            count: data.expenses?.count ?? 0,
            icon: <TrendingDown className="h-4 w-4 text-rose-500" />,
            color: 'rose',
        },
        {
            label: 'Versements Banque',
            amount: data.cash_deposits?.amount ?? 0,
            count: data.cash_deposits?.count ?? 0,
            icon: <Landmark className="h-4 w-4 text-blue-500" />,
            color: 'blue',
        },
    ];

    return (
        <div className="space-y-6">
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {cards.map((c) => (
                    <div key={c.label} className="bg-card border border-border p-5 rounded-2xl shadow-sm">
                        <div className="flex items-center justify-between mb-4">
                            <span className="text-[10px] font-bold text-muted-foreground uppercase tracking-[0.1em]">{c.label}</span>
                            <div className={`w-8 h-8 rounded-lg bg-${c.color}-500/10 flex items-center justify-center`}>
                                {c.icon}
                            </div>
                        </div>
                        <p className="text-xl font-bold text-foreground">{formatCurrency(c.amount)}</p>
                        {c.count !== null && (
                            <p className="text-[10px] text-muted-foreground mt-1">
                                {c.count} opération{c.count > 1 ? 's' : ''}
                            </p>
                        )}
                    </div>
                ))}
            </div>

            <div className="bg-card border border-border p-6 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                <div>
                    <h4 className="text-sm font-bold text-foreground">Solde Théorique de Caisse</h4>
                    <p className="text-xs text-muted-foreground mt-1">Estimation basée sur les flux enregistrés sur la période.</p>
                </div>
                <div className="text-right">
                    <p className={`text-3xl font-black tabular-nums ${solde >= 0 ? 'text-emerald-500' : 'text-rose-500'}`}>
                        {formatCurrency(solde)}
                    </p>
                    <p className="text-[10px] text-muted-foreground uppercase tracking-widest mt-1">Disponible en caisse</p>
                </div>
            </div>
        </div>
    );
}

// ─── Roadmap ──────────────────────────────────────────────────────────────────

function RoadmapView({ sales }: { sales: any[] }) {
    return (
        <div className="space-y-4">
            {sales.map((sale, idx) => (
                <div key={sale.id} className="bg-card border border-border rounded-2xl overflow-hidden group hover:border-amber-500/50 transition-all">
                    <div className="bg-muted/30 p-4 border-b border-border flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                {idx + 1}
                            </div>
                            <div>
                                <h4 className="font-bold text-foreground">{sale.client?.name || 'Client Inconnu'}</h4>
                                <p className="text-xs text-muted-foreground flex items-center gap-1">
                                    <Landmark className="h-3 w-3" />
                                    {sale.client?.address || 'Pas d\'adresse'}
                                </p>
                            </div>
                        </div>
                        <div className="text-right">
                            <p className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Bon N°</p>
                            <p className="text-xs font-black text-foreground">{sale.code}</p>
                        </div>
                    </div>
                    <div className="p-4">
                        <table className="w-full text-xs">
                            <thead>
                                <tr className="text-muted-foreground border-b border-border/50">
                                    <th className="py-2 text-left font-semibold">Article</th>
                                    <th className="py-2 text-right font-semibold">Quantité</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/30">
                                {sale.article_lines?.map((line: any) => (
                                    <tr key={line.id} className="group/line">
                                        <td className="py-2 text-foreground font-medium">{line.article?.name}</td>
                                        <td className="py-2 text-right font-black text-amber-500">{line.quantity}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            ))}
            {sales.length === 0 && (
                <div className="flex flex-col items-center justify-center py-20 text-muted-foreground space-y-3">
                    <Calendar className="h-12 w-12 opacity-20" />
                    <p className="text-sm italic">Aucune livraison prévue pour cette date.</p>
                </div>
            )}
        </div>
    );
}

// ─── Main View ────────────────────────────────────────────────────────────────

export default function ReportView({
    _action,
    data,
    rows,
    sales,
    dateFrom,
    dateTo,
    date,
}: ReportViewProps) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const slug = team.slug;

    const title = TITLES[_action] ?? 'Rapport';
    const pdfRoute = PDF_ROUTES[_action];

    // Build PDF Export Link with current filters
    const getPdfUrl = () => {
        if (!pdfRoute) {
return '#';
}

        const params: Record<string, string> = { current_team: slug };

        if (_action === 'stock-report' || _action === 'roadmap') {
            if (date) {
params.date = date;
}
        } else {
            if (dateFrom) {
params.date_from = dateFrom;
}

            if (dateTo) {
params.date_to = dateTo;
}
        }

        return route(pdfRoute, params);
    };

    return (
        <div className="p-6 space-y-8 max-w-7xl mx-auto">
            {/* Header */}
            <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div className="flex items-center gap-4">
                    <div className="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shadow-inner">
                        <BarChart3 className="h-7 w-7" />
                    </div>
                    <div>
                        <h2 className="text-2xl font-black text-foreground tracking-tight">{title}</h2>
                        <div className="flex items-center gap-2 text-xs text-muted-foreground">
                            <Calendar className="h-3.5 w-3.5" />
                            {(_action === 'stock-report' || _action === 'roadmap') ? `Date: ${date || 'Aujourd\'hui'}` : `${dateFrom || '?'} au ${dateTo || '?'}`}
                        </div>
                    </div>
                </div>

                <div className="flex flex-wrap items-center gap-4">
                    <DateFilters
                        action={_action}
                        slug={slug}
                        dateFrom={dateFrom}
                        dateTo={dateTo}
                        date={date}
                    />

                    {pdfRoute && (
                        <a
                            href={getPdfUrl()}
                            target="_blank"
                            rel="noreferrer"
                            className="inline-flex h-11 items-center gap-2.5 rounded-xl bg-foreground text-background px-6 text-sm font-bold hover:opacity-90 transition-all shadow-lg active:scale-95"
                        >
                            <Download className="h-4 w-4" />
                            Exporter PDF
                        </a>
                    )}
                </div>
            </div>

            {/* Reports Tabs */}
            <div className="flex items-center gap-1 p-1 bg-white rounded-2xl border border-border w-fit max-w-full overflow-x-auto shadow-sm">
                {Object.keys(TITLES).map((key) => (
                    <Button
                        key={key}
                        variant="ghost"
                        size="sm"
                        onClick={() => router.visit(route(key === 'roadmap' ? 'drinks.reports.roadmap' : (key === 'brouillard' ? 'drinks.reports.brouillard' : `drinks.reports.${key.split('-')[0]}`), { current_team: slug }))}
                        className={`h-9 px-5 text-xs font-bold rounded-xl transition-all ${
                            _action === key 
                            ? 'bg-card text-foreground shadow-sm ring-1 ring-border' 
                            : 'text-muted-foreground hover:text-foreground hover:bg-card/50'
                        }`}
                    >
                        {TITLES[key]}
                    </Button>
                ))}
            </div>

            {/* Content Area */}
            <div className="bg-white border border-border rounded-3xl p-6 min-h-[400px] shadow-sm">
                {_action === 'brouillard' && data && <BrouillardView data={data} />}
                {_action === 'roadmap' && sales && <RoadmapView sales={sales} />}

                {(_action === 'sales-report' || _action === 'stock-report' || _action === 'client-report') && rows && (
                    <div className="overflow-hidden rounded-2xl border border-border shadow-sm">
                        <table className="w-full text-sm text-left">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-[0.15em]">Élément</th>
                                    <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-[0.15em] text-center">
                                        {_action === 'stock-report' ? 'Quantité Stock' : 'Volume Ventes'}
                                    </th>
                                    <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-[0.15em] text-right">
                                        {_action === 'stock-report' ? 'Prix de Revient' : 'Valeur Totale'}
                                    </th>
                                    <th className="px-6 py-4 text-[10px] font-bold text-muted-foreground uppercase tracking-[0.15em] text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/50">
                                {rows.map((row: any, i: number) => (
                                    <tr key={i} className="hover:bg-muted/20 transition-colors group">
                                        <td className="px-6 py-4 font-bold text-foreground">
                                            {row.article_name || row.client_name}
                                        </td>
                                        <td className="px-6 py-4 text-center tabular-nums font-medium">
                                            {row.total_qty || row.stock_qty || row.sale_count}
                                        </td>
                                        <td className="px-6 py-4 text-right tabular-nums font-black text-amber-500">
                                            {formatCurrency(row.total_amount_ht || row.total_ttc || (row.stock_qty * row.cost_price))}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <Button variant="ghost" size="sm" className="h-8 w-8 p-0 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                <ChevronRight className="h-4 w-4" />
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                                {rows.length === 0 && (
                                    <tr>
                                        <td colSpan={4} className="px-6 py-12 text-center text-muted-foreground italic">
                                            Aucune donnée disponible pour cette période.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
}
