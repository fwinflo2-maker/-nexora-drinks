import { Link, router, usePage } from '@inertiajs/react';
import { Plus, Eye, CalendarCheck, Search } from 'lucide-react';
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { StatusBadge } from './DashboardView';
import type { Team } from '@/types';

export default function ReservationIndex({ reservations, filters, statuses }: { reservations: any; filters?: any; statuses?: any[] }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const items = reservations?.data ?? (Array.isArray(reservations) ? reservations : []);
    const pagination = reservations?.links;

    const [search, setSearch] = useState(filters?.search ?? '');
    const [status, setStatus] = useState(filters?.status ?? '');

    const applyFilters = (newSearch?: string, newStatus?: string) => {
        router.get(
            route('hotel.reservations.index', { current_team: team.slug }),
            { search: newSearch ?? search, status: newStatus ?? status },
            { preserveState: true }
        );
    };

    const handleSearch = (e: React.FormEvent) => { e.preventDefault(); applyFilters(); };

    const statusOptions = [
        { value: 'pending', label: 'En attente' },
        { value: 'confirmed', label: 'Confirmée' },
        { value: 'checked_in', label: 'En cours' },
        { value: 'checked_out', label: 'Départ effectué' },
        { value: 'cancelled', label: 'Annulée' },
    ];

    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-6">
                <h2 className="text-lg font-bold text-foreground">Réservations</h2>
                <Link
                    href={route('hotel.reservations.create', { current_team: team.slug })}
                    className="inline-flex items-center justify-center gap-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3 transition-colors"
                >
                    <Plus className="h-4 w-4" /> Nouvelle réservation
                </Link>
            </div>

            <div className="mb-4 flex flex-wrap gap-2">
                <form onSubmit={handleSearch} className="flex gap-2">
                    <div className="relative">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            type="text"
                            className="h-9 text-sm pl-9 w-48"
                            placeholder="Référence, client..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                        />
                    </div>
                    <Button type="submit" variant="outline" size="sm">Chercher</Button>
                </form>

                <select
                    className="h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none"
                    value={status}
                    onChange={e => { setStatus(e.target.value); applyFilters(search, e.target.value); }}
                >
                    <option value="">Tous les statuts</option>
                    {statusOptions.map(s => (
                        <option key={s.value} value={s.value}>{s.label}</option>
                    ))}
                </select>
            </div>

            {items.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-16 text-center">
                    <CalendarCheck className="h-12 w-12 text-muted-foreground/30 mb-4" />
                    <p className="text-sm text-muted-foreground">Aucune réservation trouvée.</p>
                </div>
            ) : (
                <div className="rounded-xl border border-border bg-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Référence</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Client</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Chambre</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Arrivée</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Départ</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Nuits</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Statut</th>
                                    <th className="h-10 px-4 text-right font-medium text-muted-foreground">Montant</th>
                                    <th className="h-10 px-4 text-right font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map((item: any) => (
                                    <tr key={item.id} className="border-b border-border/50 hover:bg-muted/20 transition-colors">
                                        <td className="p-4 font-mono text-xs text-muted-foreground">{item.reference}</td>
                                        <td className="p-4 font-medium text-foreground">{item.guest?.name ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{item.room?.number ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{item.check_in ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{item.check_out ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{item.nights ?? '—'}</td>
                                        <td className="p-4"><StatusBadge status={item.status} /></td>
                                        <td className="p-4 text-right font-medium text-foreground">
                                            {item.total_price != null ? item.total_price.toLocaleString() : '—'}
                                        </td>
                                        <td className="p-4 text-right">
                                            <Link
                                                href={route('hotel.reservations.show', { current_team: team.slug, reservation: item.id })}
                                                className="inline-flex items-center justify-center rounded-md h-8 w-8 hover:bg-muted transition-colors"
                                            >
                                                <Eye className="h-4 w-4 text-blue-500" />
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {pagination && (
                        <div className="px-4 py-3 border-t border-border flex gap-1 flex-wrap">
                            {pagination.map((link: any, i: number) => (
                                link.url ? (
                                    <Link
                                        key={i}
                                        href={link.url}
                                        className={`px-3 py-1 rounded text-xs font-medium transition-colors ${link.active ? 'bg-blue-600 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80'}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ) : (
                                    <span key={i} className="px-3 py-1 rounded text-xs font-medium text-muted-foreground/40" dangerouslySetInnerHTML={{ __html: link.label }} />
                                )
                            ))}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
