import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Edit, User, CalendarCheck } from 'lucide-react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { StatusBadge } from './DashboardView';
import type { Team } from '@/types';

export default function GuestShow({ guest }: { guest: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const item = guest ?? {};
    const reservations = item.reservations ?? [];

    const ID_LABELS: Record<string, string> = { cni: 'CNI', passport: 'Passeport', autre: 'Autre' };

    return (
        <div className="p-6 max-w-3xl">
            <div className="flex items-center justify-between mb-6">
                <Button variant="ghost" className="-ml-2 h-8 text-muted-foreground gap-1.5 text-sm" onClick={() => window.history.back()}>
                    <ArrowLeft className="h-3.5 w-3.5" /> Retour
                </Button>
                <Link
                    href={route('hotel.guests.edit', { current_team: team.slug, guest: item.id })}
                    className="inline-flex items-center justify-center gap-2 text-sm font-medium border border-border hover:bg-muted h-9 rounded-md px-3 transition-colors"
                >
                    <Edit className="h-4 w-4" /> Modifier
                </Link>
            </div>

            <div className="rounded-xl border border-border bg-card p-5 mb-4">
                <div className="flex items-center gap-4 mb-4">
                    <div className="h-14 w-14 rounded-2xl bg-blue-600/10 flex items-center justify-center">
                        <User className="h-7 w-7 text-blue-600" />
                    </div>
                    <div>
                        <h2 className="text-lg font-bold text-foreground">{item.name}</h2>
                        <p className="text-sm text-muted-foreground">
                            {item.id_type ? `${ID_LABELS[item.id_type] ?? item.id_type} · ${item.id_number ?? ''}` : 'Pièce non renseignée'}
                        </p>
                    </div>
                </div>
                <div className="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p className="text-xs text-muted-foreground mb-1">Téléphone</p>
                        <p className="font-medium text-foreground">{item.phone ?? '—'}</p>
                    </div>
                    <div>
                        <p className="text-xs text-muted-foreground mb-1">Email</p>
                        <p className="font-medium text-foreground">{item.email ?? '—'}</p>
                    </div>
                </div>
            </div>

            <div className="rounded-xl border border-border bg-card overflow-hidden">
                <div className="px-5 py-4 border-b border-border flex items-center gap-2">
                    <CalendarCheck className="h-4 w-4 text-blue-500" />
                    <h3 className="text-sm font-semibold text-foreground">Historique des séjours</h3>
                    <span className="ml-auto text-xs text-muted-foreground">{reservations.length}</span>
                </div>
                {reservations.length === 0 ? (
                    <div className="py-8 text-center text-sm text-muted-foreground">Aucun séjour enregistré.</div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Référence</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Chambre</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Arrivée</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Départ</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Statut</th>
                                    <th className="h-10 px-4 text-right font-medium text-muted-foreground">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                {reservations.map((r: any) => (
                                    <tr key={r.id} className="border-b border-border/50 hover:bg-muted/20 transition-colors">
                                        <td className="p-4">
                                            <Link
                                                href={route('hotel.reservations.show', { current_team: team.slug, reservation: r.id })}
                                                className="font-mono text-xs text-blue-500 hover:underline"
                                            >
                                                {r.reference}
                                            </Link>
                                        </td>
                                        <td className="p-4 text-muted-foreground">{r.room?.number ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{r.check_in ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{r.check_out ?? '—'}</td>
                                        <td className="p-4"><StatusBadge status={r.status} /></td>
                                        <td className="p-4 text-right font-medium text-foreground">
                                            {r.total_price != null ? r.total_price.toLocaleString() : '—'}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
}
