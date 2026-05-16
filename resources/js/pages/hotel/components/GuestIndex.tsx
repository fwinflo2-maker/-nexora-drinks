import { Link, router, usePage } from '@inertiajs/react';
import { Plus, Eye, Edit, Search, Users } from 'lucide-react';
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Team } from '@/types';

export default function GuestIndex({ guests, filters }: { guests: any; filters?: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const items = guests?.data ?? (Array.isArray(guests) ? guests : []);
    const pagination = guests?.links;

    const [search, setSearch] = useState(filters?.search ?? '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(route('hotel.guests.index', { current_team: team.slug }), { search }, { preserveState: true });
    };

    const ID_LABELS: Record<string, string> = { cni: 'CNI', passport: 'Passeport', autre: 'Autre' };

    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-6">
                <h2 className="text-lg font-bold text-foreground">Clients</h2>
                <Link
                    href={route('hotel.guests.create', { current_team: team.slug })}
                    className="inline-flex items-center justify-center gap-2 text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3 transition-colors"
                >
                    <Plus className="h-4 w-4" /> Nouveau client
                </Link>
            </div>

            <form onSubmit={handleSearch} className="mb-4 flex gap-2 max-w-sm">
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    <Input
                        type="text"
                        className="h-9 text-sm pl-9"
                        placeholder="Rechercher..."
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                    />
                </div>
                <Button type="submit" variant="outline" size="sm">Chercher</Button>
            </form>

            {items.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-16 text-center">
                    <Users className="h-12 w-12 text-muted-foreground/30 mb-4" />
                    <p className="text-sm text-muted-foreground">Aucun client trouvé.</p>
                </div>
            ) : (
                <div className="rounded-xl border border-border bg-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-muted/50 border-b border-border">
                                <tr>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Nom</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Téléphone</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Email</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Pièce d'identité</th>
                                    <th className="h-10 px-4 text-left font-medium text-muted-foreground">Séjours</th>
                                    <th className="h-10 px-4 text-right font-medium text-muted-foreground">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map((item: any) => (
                                    <tr key={item.id} className="border-b border-border/50 hover:bg-muted/20 transition-colors">
                                        <td className="p-4 font-medium text-foreground">{item.name}</td>
                                        <td className="p-4 text-muted-foreground">{item.phone ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground">{item.email ?? '—'}</td>
                                        <td className="p-4 text-muted-foreground text-xs">
                                            {item.id_type ? `${ID_LABELS[item.id_type] ?? item.id_type} • ${item.id_number ?? ''}` : '—'}
                                        </td>
                                        <td className="p-4 text-center">
                                            <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-500/10 text-blue-600">
                                                {item.reservations_count ?? 0}
                                            </span>
                                        </td>
                                        <td className="p-4 text-right">
                                            <div className="flex items-center justify-end gap-1">
                                                <Link
                                                    href={route('hotel.guests.show', { current_team: team.slug, guest: item.id })}
                                                    className="inline-flex items-center justify-center rounded-md h-8 w-8 hover:bg-muted transition-colors"
                                                >
                                                    <Eye className="h-4 w-4 text-muted-foreground" />
                                                </Link>
                                                <Link
                                                    href={route('hotel.guests.edit', { current_team: team.slug, guest: item.id })}
                                                    className="inline-flex items-center justify-center rounded-md h-8 w-8 hover:bg-muted transition-colors"
                                                >
                                                    <Edit className="h-4 w-4 text-blue-500" />
                                                </Link>
                                            </div>
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
