import { Link, router, usePage } from '@inertiajs/react';
import { Plus, Edit, Trash2, Home } from 'lucide-react';
import React from 'react';
import type { Team } from '@/types';

const STATUS_STYLES: Record<string, string> = {
    available:   'bg-emerald-500/10 text-emerald-600',
    occupied:    'bg-blue-500/10 text-blue-600',
    reserved:    'bg-amber-500/10 text-amber-600',
    maintenance: 'bg-red-500/10 text-red-500',
};

const STATUS_LABELS: Record<string, string> = {
    available:   'Disponible',
    occupied:    'Occupée',
    reserved:    'Réservée',
    maintenance: 'Maintenance',
};

export default function RoomIndex({ rooms }: { rooms: any[] }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const items = Array.isArray(rooms) ? rooms : [];

    const handleDelete = (id: number, number: string) => {
        if (!confirm(`Supprimer la chambre ${number} ?`)) return;
        router.delete(route('hotel.rooms.destroy', { current_team: team.slug, room: id }));
    };

    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-6">
                <h2 className="text-lg font-bold text-foreground">Chambres</h2>
                <Link
                    href={route('hotel.rooms.create', { current_team: team.slug })}
                    className="inline-flex items-center justify-center gap-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3 transition-colors"
                >
                    <Plus className="h-4 w-4" /> Nouvelle chambre
                </Link>
            </div>

            {items.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-16 text-center">
                    <Home className="h-12 w-12 text-muted-foreground/30 mb-4" />
                    <p className="text-sm text-muted-foreground">Aucune chambre configurée.</p>
                    <Link href={route('hotel.rooms.create', { current_team: team.slug })} className="mt-4 text-sm text-blue-500 hover:underline">
                        Ajouter une chambre
                    </Link>
                </div>
            ) : (
                <div className="rounded-xl border border-border bg-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">N°</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Étage</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Type</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Statut</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Notes</th>
                                    <th className="h-10 px-4 text-right font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map(item => (
                                    <tr key={item.id} className="border-b border-border/50 hover:bg-muted/20 transition-colors">
                                        <td className="p-4 font-semibold text-foreground">{item.number}</td>
                                        <td className="p-4 text-muted-foreground">{item.floor ?? '—'}</td>
                                        <td className="p-4 text-foreground">{item.room_type?.name ?? '—'}</td>
                                        <td className="p-4">
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium ${STATUS_STYLES[item.status] ?? 'bg-muted text-muted-foreground'}`}>
                                                {STATUS_LABELS[item.status] ?? item.status}
                                            </span>
                                        </td>
                                        <td className="p-4 text-muted-foreground text-xs max-w-[200px] truncate">{item.notes ?? '—'}</td>
                                        <td className="p-4 text-right">
                                            <div className="flex items-center justify-end gap-1">
                                                <Link
                                                    href={route('hotel.rooms.edit', { current_team: team.slug, room: item.id })}
                                                    className="inline-flex items-center justify-center rounded-md h-8 w-8 hover:bg-muted transition-colors"
                                                >
                                                    <Edit className="h-4 w-4 text-blue-500" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(item.id, item.number)}
                                                    className="inline-flex items-center justify-center rounded-md h-8 w-8 hover:bg-red-50 transition-colors"
                                                >
                                                    <Trash2 className="h-4 w-4 text-red-400" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
}
