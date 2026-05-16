import { useForm, usePage } from '@inertiajs/react';
import { Save, ArrowLeft, Edit, X, Plus } from 'lucide-react';
import type { FormEvent } from 'react';
import React, { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

export default function RoomTypeForm({ _action, roomType }: { _action: string; roomType?: any }) {
    const { currentTeam } = usePage().props;
    const team = currentTeam as Team;
    const item = roomType;

    const { data, setData, post, put, processing, errors } = useForm({
        name: item?.name ?? '',
        description: item?.description ?? '',
        base_price: item?.base_price ?? '',
        capacity: item?.capacity ?? 2,
        amenities: item?.amenities ?? [],
        is_active: item?.is_active ?? true,
    });

    const [amenityInput, setAmenityInput] = useState('');

    const addAmenity = () => {
        const val = amenityInput.trim();
        if (!val || data.amenities.includes(val)) return;
        setData('amenities', [...data.amenities, val]);
        setAmenityInput('');
    };

    const removeAmenity = (a: string) => {
        setData('amenities', data.amenities.filter((x: string) => x !== a));
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        if (_action === 'edit' && item?.id) {
            put(route('hotel.room-types.update', { current_team: team.slug, roomType: item.id }));
        } else {
            post(route('hotel.room-types.store', { current_team: team.slug }));
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
                    {_action === 'edit' ? 'Modifier le type' : 'Nouveau type de chambre'}
                </h3>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-1.5 sm:col-span-2">
                        <Label htmlFor="name" className="text-xs">Nom *</Label>
                        <Input id="name" type="text" className="h-9 text-sm" value={data.name} onChange={e => setData('name', e.target.value)} />
                        {errors.name && <p className="text-xs text-destructive">{errors.name}</p>}
                    </div>

                    <div className="space-y-1.5 sm:col-span-2">
                        <Label htmlFor="description" className="text-xs">Description</Label>
                        <textarea
                            id="description"
                            className="w-full rounded-md border border-input bg-background px-3 py-2 text-sm min-h-[80px] resize-none focus:outline-none focus:ring-2 focus:ring-ring"
                            value={data.description}
                            onChange={e => setData('description', e.target.value)}
                        />
                        {errors.description && <p className="text-xs text-destructive">{errors.description}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="base_price" className="text-xs">Prix de base *</Label>
                        <Input id="base_price" type="number" step="0.01" className="h-9 text-sm" value={data.base_price} onChange={e => setData('base_price', e.target.value)} />
                        {errors.base_price && <p className="text-xs text-destructive">{errors.base_price}</p>}
                    </div>

                    <div className="space-y-1.5">
                        <Label htmlFor="capacity" className="text-xs">Capacité (personnes) *</Label>
                        <Input id="capacity" type="number" min={1} className="h-9 text-sm" value={data.capacity} onChange={e => setData('capacity', parseInt(e.target.value))} />
                        {errors.capacity && <p className="text-xs text-destructive">{errors.capacity}</p>}
                    </div>

                    <div className="space-y-1.5 sm:col-span-2">
                        <Label className="text-xs">Équipements</Label>
                        <div className="flex gap-2">
                            <Input
                                type="text"
                                className="h-9 text-sm flex-1"
                                placeholder="Ex: WiFi, Climatisation..."
                                value={amenityInput}
                                onChange={e => setAmenityInput(e.target.value)}
                                onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); addAmenity(); } }}
                            />
                            <Button type="button" variant="outline" size="icon" className="h-9 w-9" onClick={addAmenity}>
                                <Plus className="h-4 w-4" />
                            </Button>
                        </div>
                        {data.amenities.length > 0 && (
                            <div className="flex flex-wrap gap-1.5 mt-2">
                                {data.amenities.map((a: string) => (
                                    <span key={a} className="inline-flex items-center gap-1 bg-blue-500/10 text-blue-600 text-xs px-2 py-0.5 rounded-full">
                                        {a}
                                        <button type="button" onClick={() => removeAmenity(a)} className="hover:text-red-500 transition-colors">
                                            <X className="h-3 w-3" />
                                        </button>
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>

                    <label className="flex items-center gap-2 cursor-pointer">
                        <Checkbox checked={data.is_active} onCheckedChange={v => setData('is_active', !!v)} />
                        <span className="text-sm font-medium">Actif</span>
                    </label>
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
