import { Link, usePage } from '@inertiajs/react';
import { Plus, Edit } from 'lucide-react';
import React from 'react';
import type { Team } from '@/types';

export default function ClientIndex({ clients }: { clients: any[] }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const items = Array.isArray(clients) ? clients : (clients?.data || []);

    return (
        <div className="p-6">
            <div className="flex items-center justify-between mb-6">
                <h2 className="text-lg font-bold text-foreground">Liste</h2>
                <Link
                    href={route('drinks.clients.create', { current_team: team.slug })}
                    className="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium transition-colors focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 rounded-md px-3"
                >
                    <Plus className="h-4 w-4" /> Nouveau
                </Link>
            </div>
            
            <div className="rounded-xl border border-border bg-card overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/50 border-b border-border">
                            <tr>
                                <th className="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Code</th>
                                    <th className="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Nom</th>
                                    <th className="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Téléphone</th>
                                    <th className="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Email</th>
                                    <th className="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Adresse</th>
                                    <th className="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Actif</th>
                                <th className="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.length === 0 ? (
                                <tr>
                                    <td colSpan={7} className="p-8 text-center text-muted-foreground">Aucune donnée trouvée.</td>
                                </tr>
                            ) : items.map(item => (
                                <tr key={item.id} className="border-b border-border/50 hover:bg-muted/20 transition-colors">
                                    <td className="p-4 align-middle">{item.code}</td>
                                        <td className="p-4 align-middle">{item.name}</td>
                                        <td className="p-4 align-middle">{item.phone}</td>
                                        <td className="p-4 align-middle">{item.email}</td>
                                        <td className="p-4 align-middle">{item.address}</td>
                                        <td className="p-4 align-middle">
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium ${item.is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500'}`}>
                                                {item.is_active ? 'Oui' : 'Non'}
                                            </span>
                                        </td>
                                    <td className="p-4 align-middle text-right">
                                        <Link
                                            href={route('drinks.clients.edit', { current_team: team.slug, client: item.id })}
                                            className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-8 w-8"
                                        >
                                            <Edit className="h-4 w-4 text-amber-500" />
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
