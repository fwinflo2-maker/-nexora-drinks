import { useForm, usePage } from '@inertiajs/react';
import { Save, ArrowLeft, Edit } from 'lucide-react';
import type { FormEvent } from 'react';
import React from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

export default function GuestForm({ _action, guest }: { _action: string; guest?: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const item = guest;

    const { data, setData, post, put, processing, errors } = useForm({
        name: item?.name ?? '',
        email: item?.email ?? '',
        phone: item?.phone ?? '',
        id_type: item?.id_type ?? 'cni',
        id_number: item?.id_number ?? '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        if (_action === 'edit' && item?.id) {
            put(route('hotel.guests.update', { current_team: team.slug, guest: item.id }));
        } else {
            post(route('hotel.guests.store', { current_team: team.slug }));
        }
    };

    return (
        <div className="p-6 max-w-2xl">
            <Button variant="ghost" className="mb-6 -ml-2 h-8 text-muted-foreground gap-1.5 text-sm" onClick={() => window.history.back()}>
                <ArrowLeft className="h-3.5 w-3.5" /> Retour
            </Button>
            <form onSubmit={handleSubmit} className="space-y-5 rounded-xl border border-border bg-card/50 p-5">
                <h3 className="text-sm font-semibold text-foreground flex items-center gap-2">
                    <Edit className="h-3.5 w-3.5 text-blue-500" />
                    {_action === 'edit' ? 'Modifier le client' : 'Nouveau client'}
                </h3>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-1.5 sm:col-span-2">
                        <Label htmlFor="name" className="text-xs">Nom complet *</Label>
                        <Input id="name" type="text" className="h-9 text-sm" value={data.name} onChange={e => setData('name', e.target.value)} />
                        {errors.name && <p className="text-xs text-destructive">{errors.name}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="phone" className="text-xs">Téléphone</Label>
                        <Input id="phone" type="tel" className="h-9 text-sm" value={data.phone} onChange={e => setData('phone', e.target.value)} />
                        {errors.phone && <p className="text-xs text-destructive">{errors.phone}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="email" className="text-xs">Email</Label>
                        <Input id="email" type="email" className="h-9 text-sm" value={data.email} onChange={e => setData('email', e.target.value)} />
                        {errors.email && <p className="text-xs text-destructive">{errors.email}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="id_type" className="text-xs">Type de pièce d'identité</Label>
                        <select
                            id="id_type"
                            className="w-full h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            value={data.id_type}
                            onChange={e => setData('id_type', e.target.value)}
                        >
                            <option value="cni">CNI</option>
                            <option value="passport">Passeport</option>
                            <option value="autre">Autre</option>
                        </select>
                        {errors.id_type && <p className="text-xs text-destructive">{errors.id_type}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="id_number" className="text-xs">Numéro de pièce</Label>
                        <Input id="id_number" type="text" className="h-9 text-sm" value={data.id_number} onChange={e => setData('id_number', e.target.value)} />
                        {errors.id_number && <p className="text-xs text-destructive">{errors.id_number}</p>}
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
