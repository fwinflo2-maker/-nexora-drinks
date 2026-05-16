import { Link, usePage, router } from '@inertiajs/react';
import { Plus, Search, Eye, Pencil, Trash2, Printer, ChevronUp, ChevronDown, ChevronsUpDown } from 'lucide-react';
import React, { useState, useEffect } from 'react';
import { Badge } from '@/components/ui/badge';
import type { Team } from '@/types';

type Filters = {
    search: string;
    status: string;
    sort: string;
    direction: 'asc' | 'desc';
};

type Props = {
    sales: any;
    filters: Filters;
};

const STATUS_OPTIONS = [
    { value: '', label: 'Tous' },
    { value: 'validated', label: 'Validées' },
    { value: 'draft', label: 'Brouillons' },
    { value: 'cancelled', label: 'Annulées' },
];

function SortIcon({ col, sort, direction }: { col: string; sort: string; direction: string }) {
    if (sort !== col) return <ChevronsUpDown className="h-3 w-3 opacity-30" />;
    return direction === 'asc'
        ? <ChevronUp className="h-3 w-3 text-amber-500" />
        : <ChevronDown className="h-3 w-3 text-amber-500" />;
}

function getStatusBadge(status: string) {
    switch (status) {
        case 'validated': return <Badge className="bg-emerald-500/10 text-emerald-600 border-emerald-500/20 font-bold">Validée</Badge>;
        case 'draft': return <Badge variant="secondary" className="bg-muted text-muted-foreground border-border font-bold">Brouillon</Badge>;
        case 'cancelled': return <Badge className="bg-red-500/10 text-red-500 border-red-500/20 font-bold">Annulée</Badge>;
        default: return <Badge>{status}</Badge>;
    }
}

const formatCurrency = (val: number) =>
    new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XAF', maximumFractionDigits: 0 }).format(val);

export default function SaleIndex({ sales, filters: initialFilters }: Props) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const [search, setSearch] = useState(initialFilters?.search ?? '');
    const [status, setStatus] = useState(initialFilters?.status ?? '');
    const sort = initialFilters?.sort ?? 'document_date';
    const direction = initialFilters?.direction ?? 'desc';

    // Debounce search
    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get(
                window.location.pathname,
                { search, status, sort, direction },
                { preserveState: true, replace: true },
            );
        }, 400);
        return () => clearTimeout(timeout);
    }, [search]);

    function applyStatus(val: string) {
        setStatus(val);
        router.get(
            window.location.pathname,
            { search, status: val, sort, direction },
            { preserveState: true, replace: true },
        );
    }

    function applySort(col: string) {
        const newDir = sort === col && direction === 'desc' ? 'asc' : 'desc';
        router.get(
            window.location.pathname,
            { search, status, sort: col, direction: newDir },
            { preserveState: true, replace: true },
        );
    }

    const total = sales?.total ?? 0;

    return (
        <div className="flex flex-col space-y-5 p-6">
            {/* Header */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div className="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                    {/* Search */}
                    <div className="relative w-full sm:w-72">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Référence, client..."
                            className="w-full pl-9 pr-3 py-2 bg-card border border-border rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500/50 transition-shadow"
                        />
                    </div>
                    {/* Status chips */}
                    <div className="flex items-center gap-1.5 flex-wrap">
                        {STATUS_OPTIONS.map((opt) => (
                            <button
                                key={opt.value}
                                onClick={() => applyStatus(opt.value)}
                                className={`px-3 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wide transition-all ${
                                    status === opt.value
                                        ? 'bg-amber-500 text-white shadow-sm'
                                        : 'bg-muted text-muted-foreground hover:bg-muted/70'
                                }`}
                            >
                                {opt.label}
                            </button>
                        ))}
                    </div>
                </div>
                <Link
                    href={route('drinks.sales.create', { current_team: team.slug })}
                    className="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-white text-sm font-bold rounded-lg transition-colors shadow-sm shrink-0"
                >
                    <Plus className="h-4 w-4" /> Nouvelle Vente
                </Link>
            </div>

            {/* Result count */}
            <p className="text-[11px] font-bold text-muted-foreground uppercase tracking-widest px-1">
                {total.toLocaleString('fr-FR')} vente{total !== 1 ? 's' : ''}
                {status ? ` · filtre: ${STATUS_OPTIONS.find(o => o.value === status)?.label}` : ''}
            </p>

            {/* Table */}
            <div className="bg-card border border-border rounded-xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/30">
                            <tr className="border-b border-border">
                                <th
                                    className="px-5 py-3.5 text-left cursor-pointer select-none group"
                                    onClick={() => applySort('code')}
                                >
                                    <span className="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground group-hover:text-foreground transition-colors">
                                        Référence <SortIcon col="code" sort={sort} direction={direction} />
                                    </span>
                                </th>
                                <th
                                    className="px-5 py-3.5 text-left cursor-pointer select-none group"
                                    onClick={() => applySort('document_date')}
                                >
                                    <span className="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground group-hover:text-foreground transition-colors">
                                        Date <SortIcon col="document_date" sort={sort} direction={direction} />
                                    </span>
                                </th>
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Client</th>
                                <th className="px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Statut</th>
                                <th
                                    className="px-5 py-3.5 text-right cursor-pointer select-none group"
                                    onClick={() => applySort('total_ttc')}
                                >
                                    <span className="flex items-center justify-end gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground group-hover:text-foreground transition-colors">
                                        Total TTC <SortIcon col="total_ttc" sort={sort} direction={direction} />
                                    </span>
                                </th>
                                <th className="px-5 py-3.5 text-right text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/50">
                            {sales?.data?.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="py-12 text-center text-muted-foreground text-sm">
                                        Aucune vente trouvée.
                                    </td>
                                </tr>
                            ) : (
                                sales?.data?.map((sale: any) => (
                                    <tr
                                        key={sale.id}
                                        onClick={(e) => {
                                            if ((e.target as HTMLElement).closest('.actions-cell')) return;
                                            router.visit(route('drinks.sales.show', { current_team: team.slug, sale: sale.id }));
                                        }}
                                        className="hover:bg-muted/40 transition-colors group cursor-pointer"
                                    >
                                        <td className="px-5 py-3.5 font-bold text-foreground whitespace-nowrap">{sale.code}</td>
                                        <td className="px-5 py-3.5 text-muted-foreground whitespace-nowrap">{sale.document_date}</td>
                                        <td className="px-5 py-3.5 text-foreground font-medium whitespace-nowrap">{sale.client?.name || 'Client divers'}</td>
                                        <td className="px-5 py-3.5 whitespace-nowrap">{getStatusBadge(sale.status)}</td>
                                        <td className="px-5 py-3.5 text-right font-bold text-foreground whitespace-nowrap">{formatCurrency(sale.total_ttc)}</td>
                                        <td className="px-5 py-3.5 actions-cell">
                                            <div className="flex items-center justify-end gap-1 opacity-100 sm:opacity-40 group-hover:opacity-100 transition-opacity">
                                                <a
                                                    href={route('drinks.sales.pdf', { current_team: team.slug, sale: sale.id })}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="p-2 text-muted-foreground hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors"
                                                    title="Imprimer"
                                                    onClick={(e) => e.stopPropagation()}
                                                >
                                                    <Printer className="h-4 w-4" />
                                                </a>
                                                <Link
                                                    href={route('drinks.sales.show', { current_team: team.slug, sale: sale.id })}
                                                    className="p-2 text-muted-foreground hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                    title="Détails"
                                                    onClick={(e) => e.stopPropagation()}
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>
                                                {sale.status === 'draft' && (
                                                    <>
                                                        <Link
                                                            href={route('drinks.sales.edit', { current_team: team.slug, sale: sale.id })}
                                                            className="p-2 text-muted-foreground hover:text-amber-500 hover:bg-amber-500/10 rounded-lg transition-colors"
                                                            title="Modifier"
                                                            onClick={(e) => e.stopPropagation()}
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Link>
                                                        <button
                                                            onClick={(e) => {
                                                                e.stopPropagation();
                                                                if (confirm('Supprimer cette vente ?')) {
                                                                    router.delete(route('drinks.sales.destroy', { current_team: team.slug, sale: sale.id }));
                                                                }
                                                            }}
                                                            className="p-2 text-muted-foreground hover:text-red-500 hover:bg-red-500/10 rounded-lg transition-colors"
                                                            title="Supprimer"
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </button>
                                                    </>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Pagination */}
            {sales?.last_page > 1 && (
                <div className="flex items-center justify-between px-1">
                    <p className="text-[11px] text-muted-foreground font-bold">
                        Page {sales.current_page} sur {sales.last_page}
                        {' '}·{' '}
                        {sales.from}–{sales.to} sur {total.toLocaleString('fr-FR')}
                    </p>
                    <div className="flex gap-1 bg-card p-1 rounded-lg border border-border">
                        {sales.links.map((link: any, i: number) => (
                            <Link
                                key={i}
                                href={link.url || '#'}
                                className={`px-3 py-1.5 text-xs font-bold rounded-md transition-colors ${
                                    link.active
                                        ? 'bg-amber-500 text-white'
                                        : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                } ${!link.url ? 'opacity-30 cursor-not-allowed pointer-events-none' : ''}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
