import { useForm, usePage } from '@inertiajs/react';
import { Save, ArrowLeft, CalendarCheck } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

export default function ReservationForm({ _action, rooms, guests }: { _action: string; rooms: any[]; guests: any[] }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;

    const { data, setData, post, processing, errors } = useForm({
        room_id: '',
        guest_id: '',
        check_in: '',
        check_out: '',
        total_price: '',
        notes: '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(route('hotel.reservations.store', { current_team: team.slug }));
    };

    const selectedRoom = rooms.find(r => String(r.id) === String(data.room_id));

    return (
        <div className="p-6 max-w-2xl">
            <Button variant="ghost" className="mb-6 -ml-2 h-8 text-muted-foreground gap-1.5 text-sm" onClick={() => window.history.back()}>
                <ArrowLeft className="h-3.5 w-3.5" /> Retour
            </Button>
            <form onSubmit={handleSubmit} className="space-y-5 rounded-xl border border-border bg-card/50 p-5">
                <h3 className="text-sm font-semibold text-foreground flex items-center gap-2">
                    <CalendarCheck className="h-3.5 w-3.5 text-blue-500" />
                    Nouvelle réservation
                </h3>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-1.5">
                        <Label htmlFor="room_id" className="text-xs">Chambre *</Label>
                        <select
                            id="room_id"
                            className="w-full h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            value={data.room_id}
                            onChange={e => setData('room_id', e.target.value)}
                        >
                            <option value="">— Sélectionner une chambre —</option>
                            {rooms.map(r => (
                                <option key={r.id} value={r.id}>
                                    {r.number} — {r.room_type?.name ?? 'Type inconnu'}
                                </option>
                            ))}
                        </select>
                        {errors.room_id && <p className="text-xs text-destructive">{errors.room_id}</p>}
                        {selectedRoom && (
                            <p className="text-[11px] text-muted-foreground">Prix de base : {selectedRoom.room_type?.base_price?.toLocaleString() ?? '—'}</p>
                        )}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="guest_id" className="text-xs">Client *</Label>
                        <select
                            id="guest_id"
                            className="w-full h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            value={data.guest_id}
                            onChange={e => setData('guest_id', e.target.value)}
                        >
                            <option value="">— Sélectionner un client —</option>
                            {guests.map(g => (
                                <option key={g.id} value={g.id}>{g.name}</option>
                            ))}
                        </select>
                        {errors.guest_id && <p className="text-xs text-destructive">{errors.guest_id}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="check_in" className="text-xs">Date d'arrivée *</Label>
                        <Input id="check_in" type="date" className="h-9 text-sm" value={data.check_in} onChange={e => setData('check_in', e.target.value)} />
                        {errors.check_in && <p className="text-xs text-destructive">{errors.check_in}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="check_out" className="text-xs">Date de départ *</Label>
                        <Input id="check_out" type="date" className="h-9 text-sm" value={data.check_out} onChange={e => setData('check_out', e.target.value)} />
                        {errors.check_out && <p className="text-xs text-destructive">{errors.check_out}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="total_price" className="text-xs">Montant total *</Label>
                        <Input id="total_price" type="number" step="0.01" className="h-9 text-sm" value={data.total_price} onChange={e => setData('total_price', e.target.value)} />
                        {errors.total_price && <p className="text-xs text-destructive">{errors.total_price}</p>}
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
                        <Save className="h-4 w-4" /> Créer la réservation
                    </Button>
                </div>
            </form>
        </div>
    );
}
