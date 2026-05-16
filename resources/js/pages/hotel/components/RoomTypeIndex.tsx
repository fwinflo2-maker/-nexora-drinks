import { Link, router, usePage } from '@inertiajs/react';
import { Plus, Edit, Trash2, BedDouble } from 'lucide-react';
import React from 'react';
import type { Team } from '@/types';

export default function RoomTypeIndex({ roomTypes }: { roomTypes: any[] }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const items = Array.isArray(roomTypes) ? roomTypes : [];

    const handleDelete = (id: number, name: string) => {
        if (!confirm(`Supprimer le type "${name}" ?`)) return;
        router.delete(route('hotel.room-types.destroy', { current_team: team.slug, roomType: id }));
    };

    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-6">
                <h2 className="text-lg font-bold text-foreground">Types de chambres</h2>
                <Link
                    href={route('hotel.room-types.create', { current_team: team.slug })}
                    className="inline-flex items-center justify-center gap-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3 transition-colors"
                >
                    <Plus className="h-4 w-4" /> Nouveau type
                </Link>
            </div>

            {items.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-16 text-center">
                    <BedDouble className="h-12 w-12 text-muted-foreground/30 mb-4" />
                    <p className="text-sm text-muted-foreground">Aucun type de chambre configuré.</p>
                    <Link href={route('hotel.room-types.create', { current_team: team.slug })} className="mt-4 text-sm text-blue-500 hover:underline">
                        Créer le premier type
                    </Link>
                </div>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {items.map(item => (
                        <div key={item.id} className="rounded-xl border border-border bg-card p-5 hover:shadow-md transition-shadow">
                            <div className="flex items-start justify-between mb-3">
                                <div className="flex items-center gap-2">
                                    <div className="h-9 w-9 rounded-lg bg-blue-600/10 flex items-center justify-center">
                                        <BedDouble className="h-4 w-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <p className="text-sm font-semibold text-foreground">{item.name}</p>
                                        <p className="text-[11px] text-muted-foreground">{item.rooms_count ?? 0} chambre(s)</p>
                                    </div>
                                </div>
                                <span className={`text-[10px] px-2 py-0.5 rounded-full font-medium ${item.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-red-500/10 text-red-500'}`}>
                                    {item.is_active ? 'Actif' : 'Inactif'}
                                </span>
                            </div>

                            {item.description && (
                                <p className="text-xs text-muted-foreground mb-3 line-clamp-2">{item.description}</p>
                            )}

                            <div className="flex items-center gap-3 text-xs text-muted-foreground mb-4">
                                <span>Capacité : <strong className="text-foreground">{item.capacity}</strong></span>
                                <span>·</span>
                                <span>Prix de base : <strong className="text-foreground">{item.base_price?.toLocaleString()}</strong></span>
                            </div>

                            {Array.isArray(item.amenities) && item.amenities.length > 0 && (
                                <div className="flex flex-wrap gap-1 mb-4">
                                    {item.amenities.slice(0, 4).map((a: string, i: number) => (
                                        <span key={i} className="text-[10px] bg-muted px-2 py-0.5 rounded-full text-muted-foreground">{a}</span>
                                    ))}
                                    {item.amenities.length > 4 && (
                                        <span className="text-[10px] bg-muted px-2 py-0.5 rounded-full text-muted-foreground">+{item.amenities.length - 4}</span>
                                    )}
                                </div>
                            )}

                            <div className="flex gap-2 pt-3 border-t border-border/50">
                                <Link
                                    href={route('hotel.room-types.edit', { current_team: team.slug, roomType: item.id })}
                                    className="flex-1 inline-flex items-center justify-center gap-1.5 text-xs font-medium h-8 rounded-md border border-border hover:bg-muted transition-colors text-foreground"
                                >
                                    <Edit className="h-3.5 w-3.5" /> Modifier
                                </Link>
                                <button
                                    onClick={() => handleDelete(item.id, item.name)}
                                    className="inline-flex items-center justify-center h-8 w-8 rounded-md border border-red-200 text-red-500 hover:bg-red-50 transition-colors"
                                >
                                    <Trash2 className="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
