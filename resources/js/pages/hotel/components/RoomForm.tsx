import { useForm, usePage } from '@inertiajs/react';
import { Save, ArrowLeft, Edit } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

export default function RoomForm({ _action, room, roomTypes, statuses }: { _action: string; room?: any; roomTypes: any[]; statuses: any[] }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const item = room;

    const { data, setData, post, put, processing, errors } = useForm({
        room_type_id: item?.room_type_id ?? '',
        number: item?.number ?? '',
        floor: item?.floor ?? '',
        status: item?.status ?? 'available',
        notes: item?.notes ?? '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        if (_action === 'edit' && item?.id) {
            put(route('hotel.rooms.update', { current_team: team.slug, room: item.id }));
        } else {
            post(route('hotel.rooms.store', { current_team: team.slug }));
        }
    };

    const statusOptions = statuses.length > 0
        ? statuses
        : [
            { value: 'available', label: 'Disponible' },
            { value: 'occupied', label: 'Occupée' },
            { value: 'reserved', label: 'Réservée' },
            { value: 'maintenance', label: 'Maintenance' },
        ];

    return (
        <div className="p-6 max-w-2xl">
            <Button variant="ghost" className="mb-6 -ml-2 h-8 text-muted-foreground gap-1.5 text-sm" onClick={() => window.history.back()}>
                <ArrowLeft className="h-3.5 w-3.5" /> Retour
            </Button>
            <form onSubmit={handleSubmit} className="space-y-5 rounded-xl border border-border bg-card/50 p-5">
                <h3 className="text-sm font-semibold text-foreground flex items-center gap-2">
                    <Edit className="h-3.5 w-3.5 text-blue-500" />
                    {_action === 'edit' ? 'Modifier la chambre' : 'Nouvelle chambre'}
                </h3>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-1.5">
                        <Label htmlFor="room_type_id" className="text-xs">Type de chambre *</Label>
                        <select
                            id="room_type_id"
                            className="w-full h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            value={data.room_type_id}
                            onChange={e => setData('room_type_id', e.target.value)}
                        >
                            <option value="">— Sélectionner —</option>
                            {roomTypes.map(rt => (
                                <option key={rt.id} value={rt.id}>{rt.name}</option>
                            ))}
                        </select>
                        {errors.room_type_id && <p className="text-xs text-destructive">{errors.room_type_id}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="number" className="text-xs">Numéro de chambre *</Label>
                        <Input id="number" type="text" className="h-9 text-sm" placeholder="Ex: 101, 201A..." value={data.number} onChange={e => setData('number', e.target.value)} />
                        {errors.number && <p className="text-xs text-destructive">{errors.number}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="floor" className="text-xs">Étage</Label>
                        <Input id="floor" type="text" className="h-9 text-sm" placeholder="Ex: 1, RDC..." value={data.floor} onChange={e => setData('floor', e.target.value)} />
                        {errors.floor && <p className="text-xs text-destructive">{errors.floor}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="status" className="text-xs">Statut *</Label>
                        <select
                            id="status"
                            className="w-full h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            value={data.status}
                            onChange={e => setData('status', e.target.value)}
                        >
                            {statusOptions.map((s: any) => (
                                <option key={s.value ?? s} value={s.value ?? s}>{s.label ?? s}</option>
                            ))}
                        </select>
                        {errors.status && <p className="text-xs text-destructive">{errors.status}</p>}
                    </div>

                    <div className="space-y-1.5 sm:col-span-2">
                        <Label htmlFor="notes" className="text-xs">Notes</Label>
                        <textarea
                            id="notes"
                            className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm min-h-[80px] resize-none focus:outline-none focus:ring-2 focus:ring-ring"
                            value={data.notes}
                            onChange={e => setData('notes', e.target.value)}
                        />
                    </div>
                </div>

                <div className="flex justify-end pt-4 border-t border-border/50">
                    <Button type="submit" disabled={processing} className="gap-2">
                        <Save className="h-4 w-4" /> Enregistrer
                    </Button>
                </div>
            </form>
        </div>
    );
}
